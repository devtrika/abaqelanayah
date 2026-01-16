<?php

namespace App\Http\View\Composers;

use App\Models\Category;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HeaderComposer
{
    public function __construct(private CartService $cartService) {}

    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // Get all active parent categories with their children
        $categories = Category::with('children')
            ->whereNull('parent_id')
            ->where('is_active', 1)
            ->get();

        $cartCount = 0;
        $notificationsUnreadCount = 0;
        if (Auth::check()) {
            try {
                $cart = $this->cartService->getCart(Auth::user())->load('items');
                $cartCount = (int) $cart->items->sum('quantity');
            } catch (\Throwable $e) {
                $cartCount = 0;
            }

            try {
                $notificationsUnreadCount = (int) Auth::user()->unreadNotifications()->count();
            } catch (\Throwable $e) {
                $notificationsUnreadCount = 0;
            }
        }

        $view->with([
            'headerCategories' => $categories,
            'headerCartCount' => $cartCount,
            'headerNotificationsUnreadCount' => $notificationsUnreadCount,
        ]);
    }
}

