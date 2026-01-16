<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactUsRequest;
use App\Models\ContactUs;
use App\Models\Fqs;
use App\Models\Social;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class StaticPageController extends Controller
{
    /**
     * Show About page
     *
     * @return View
     */
    public function about(): View
    {
        $content = $this->getSettingContent('about');
        return view('website.pages.static_pages.about', compact('content'));
    }

    /**
     * Show FAQ page
     *
     * @return View
     */
    public function faq(): View
    {
        $faqs = Fqs::orderBy('id')->get();
        return view('website.pages.static_pages.faq', compact('faqs'));
    }

    /**
     * Show Terms page
     *
     * @return View
     */
    public function terms(): View
    {
        $content = $this->getSettingContent('terms');
        return view('website.pages.static_pages.terms', compact('content'));
    }

    /**
     * Show Privacy page
     *
     * @return View
     */
    public function privacy(): View
    {
        $content = $this->getSettingContent('privacy');
        return view('website.pages.static_pages.privacy', compact('content'));
    }

    /**
     * Show Returns page
     *
     * @return View
     */
    public function returns(): View
    {
        $content = $this->getSettingContent('returns');
        return view('website.pages.static_pages.returns', compact('content'));
    }

    /**
     * Show Contact page
     *
     * @return View
     */
    public function contact(): View
    {
        $socials = Social::all();
        $contactInfo = $this->getContactInfo();
        
        return view('website.pages.static_pages.contact', compact('socials', 'contactInfo'));
    }

    /**
     * Handle contact form submission
     *
     * @param ContactUsRequest $request
     * @return RedirectResponse
     */
    public function submitContact(ContactUsRequest $request): RedirectResponse
    {
        $data = $request->validated();
        
        // Add user_id if authenticated
        if (auth()->check()) {
            $data['user_id'] = auth()->id();
        }

        ContactUs::create($data);

        return redirect()
            ->back()
            ->with('success', __('site.contact_message_sent'));
    }

    /**
     * Get setting content based on locale
     *
     * @param string $key
     * @return string
     */
    private function getSettingContent(string $key): string
    {
        $settingKey = $key . '_' . app()->getLocale();
        $setting = SiteSetting::where('key', $settingKey)->first();
        
        return $setting ? $setting->value : '';
    }

    /**
     * Get contact information from settings
     *
     * @return array
     */
    private function getContactInfo(): array
    {
        $keys = ['email', 'phone', 'whatsapp'];
        $settings = SiteSetting::whereIn('key', $keys)->pluck('value', 'key');
        
        return [
            'email' => $settings['email'] ?? '',
            'phone' => $settings['phone'] ?? '',
            'whatsapp' => $settings['whatsapp'] ?? '',
        ];
    }
}

