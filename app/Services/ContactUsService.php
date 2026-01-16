<?php
namespace App\Services;

use App\Models\ContactUs;
use Illuminate\Support\Facades\Auth;

class ContactUsService
{
    public function store(array $data)
    {
        $data['user_id'] = Auth::id();
        $contact = ContactUs::create($data);

        // Send notification to all admins
        $user = Auth::user();
        if ($user) {
            $admins = \App\Models\Admin::all();
            $userType = $user->type;
            $userName = $user->name;
            if ($userType === 'client') {
                $message = 'تم اراسال رساله جديده من نموذج تواصل بنا للعملاء ' . $userName;
            } elseif ($userType === 'provider') {
                $message = 'تم اراسال رساله جديده من نموذج تواصل بنا لمقدمي الخدمه ' . $userName;
            } else {
                $message = 'تم ارسال رسالة جديدة من نموذج تواصل بنا من المستخدم ' . $userName;
            }
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\NotifyAdmin([
                    'title' => [
                        'ar' => 'رسالة تواصل جديدة',
                        'en' => 'New Contact Us Message'
                    ],
                    'body' => [
                        'ar' => $message,
                        'en' => $message
                    ],
                    'type' => 'contact_us',
                    'contact_id' => $contact->id
                ]));
            }
        }

        return $contact;
    }
}
