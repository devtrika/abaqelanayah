<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Repositories\AddressRepository;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;


class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected AddressRepository $addressRepository
    ) {}

    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('website.login');
        }

        $cart = $this->cartService->getCart(Auth::user())->load('items.product');
        $data = $this->transformDbCart($cart);

        $addresses = $this->addressRepository->getUserAddresses(Auth::user());
        $cities = City::orderBy('name')->get();

        return view('website.pages.cart', [
            'cartData' => $data,
            'addresses' => $addresses,
            'cities' => $cities,
        ]);
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'nullable|integer|min:1|max:999',
           
        ]);
        $validated['quantity'] = (int)($validated['quantity'] ?? 1);

        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => __('الرجاء تسجيل الدخول لإضافة إلى السلة')], 401);
        }

        $cart = $this->cartService->addToCart(Auth::user(), $validated);
        $data = $this->transformDbCart($cart);

        return response()->json([
            'success' => true,
            'message' => __('تمت إضافة المنتج إلى السلة'),
            'count' => $data['count'],
            'total' => $data['total'],
            'html' => View::make('website.partials.cart_items_list', ['cartData' => $data])->render(),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'cart_item_id' => 'nullable|integer',
            'cart_item_key' => 'nullable|string',
            'quantity' => 'required|integer|min:1|max:999',
        ]);

        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => __('الرجاء تسجيل الدخول')], 401);
        }

        $cart = $this->cartService->updateCartItem(Auth::user(), [
            'cart_item_id' => $validated['cart_item_id'],
            'quantity' => $validated['quantity'],
        ]);
        $data = $this->transformDbCart($cart);

        return response()->json([
            'success' => true,
            'message' => __('تم تحديث السلة'),
            'count' => $data['count'],
            'total' => $data['total'],
            'html' => View::make('website.partials.cart_items_list', ['cartData' => $data])->render(),
        ]);
    }

    public function remove(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'nullable|integer',
            'cart_item_key' => 'nullable|string',
        ]);

        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => __('الرجاء تسجيل الدخول')], 401);
        }

        $cart = $this->cartService->removeFromCart(Auth::user(), (int) $validated['product_id']);
        $data = $this->transformDbCart($cart);

        return response()->json([
            'success' => true,
            'message' => __('تم حذف المنتج من السلة'),
            'count' => $data['count'],
            'total' => $data['total'],
            'html' => View::make('website.partials.cart_items_list', ['cartData' => $data])->render(),
        ]);
    }

    public function summary()
    {
        if (!Auth::check()) {
            return response()->json(['count' => 0, 'total' => 0, 'gift_fee' => 0]);
        }

        $cart = $this->cartService->getCart(Auth::user())->load('items.product');
        $data = $this->transformDbCart($cart);

        return response()->json([
            'count' => $data['count'],
            'total' => $data['total'],
            'gift_fee' => ((session('checkout.temp.order_type') === 'gift') ? (float) (\App\Models\SiteSetting::where('key','gift_fee')->value('value') ?? 0) : 0),
        ]);
    }

    private function transformDbCart($cart): array
    {
        $items = [];
        foreach ($cart->items as $item) {
            $items[] = [
                'id' => $item->id,
                'key' => null,
                'product_id' => $item->product_id,
                'name' => $item->product->name,
                'image_url' => $item->product->image_url,
                'quantity' => (int) $item->quantity,
                'unit_price' => (float) $item->price,
                'total' => (float) $item->total,
                'returnable' => false,
                'is_refunded' => (bool) ($item->product->is_refunded ?? false),
            ];
        }

        return [
            'items' => $items,
            'count' => array_sum(array_map(fn($i) => $i['quantity'], $items)),
            'subtotal' => (float) $cart->subtotal,
            'total' => (float) $cart->total,
        ];
    }
}

