<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Requests\Website\Checkout\CreateOrderRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use App\Services\CartService;
use App\Services\Order\OrderCheckoutService;
use App\Services\Order\DeliveryCalculationService;
use App\Services\Order\InventoryService;
use App\Services\OrderService;
use App\Repositories\AddressRepository;
use App\Models\PaymentMethod;
use App\Models\Address;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected OrderCheckoutService $checkoutService,
        protected DeliveryCalculationService $deliveryCalcService,
        protected InventoryService $inventoryService,
        protected OrderService $orderService,
        protected AddressRepository $addressRepository
    ) {}

    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('website.login');
        }

        $user = Auth::user();
        $cart = $this->cartService->getCart($user)->load('items.product');
        $cartData = $this->transformDbCart($cart);

        $addresses = $this->addressRepository->getUserAddresses($user);
        $paymentMethods = PaymentMethod::where('is_active', 1)->get();

        // Cache incoming cart address/gift payload into session for seamless checkout
        $req = request();
        $sessionPayload = [
            'order_type' => $req->input('order_type'),
            'delivery_type' => $req->input('delivery_type'),
            'address_id' => $req->input('address_id'),
            'address_name' => $req->input('address_name'),
            'recipient_name' => $req->input('recipient_name'),
            'phone' => $req->input('phone'),
            'country_code' => $req->input('country_code'),
            'city_id' => $req->input('city_id'),
            'districts_id' => $req->input('districts_id'),
            'description' => $req->input('description'),
            'latitude' => $req->input('latitude') ?? $req->input('lat'),
            'longitude' => $req->input('longitude') ?? $req->input('lng'),
            'lat' => $req->input('lat') ?? $req->input('latitude'),
            'lng' => $req->input('lng') ?? $req->input('longitude'),
            'reciver_name' => $req->input('reciver_name'),
            'reciver_phone' => $req->input('reciver_phone'),
            'gift_address_name' => $req->input('gift_address_name'),
            'gift_latitude' => $req->input('gift_latitude') ?? $req->input('gift_lat'),
            'gift_longitude' => $req->input('gift_longitude') ?? $req->input('gift_lng'),
            'message' => $req->input('message'),
            'whatsapp' => $req->input('whatsapp'),
            'hide_sender' => $req->input('hide_sender'),
        ];
        $sessionPayload = array_filter($sessionPayload, function($v){ return !is_null($v) && $v !== ''; });
        if (!empty($sessionPayload)) {
            session()->put('checkout.temp', $sessionPayload);
            Log::info('checkout.index session stored', [
                'incoming' => $sessionPayload,
                'stored' => session('checkout.temp')
            ]);
        } else {
            Log::info('checkout.index no incoming payload', [
                'stored' => session('checkout.temp')
            ]);
        }

        return view('website.pages.checkout', compact('cartData', 'addresses', 'paymentMethods'));
    }

    /**
     * Prepare checkout by storing order data in session (called from cart page via POST)
     */
    public function prepare(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'الرجاء تسجيل الدخول'], 401);
        }

        $user = Auth::user();
        
        $latitude = $request->input('latitude') ?? $request->input('lat');
        $longitude = $request->input('longitude') ?? $request->input('lng');
        $giftLatitude = $request->input('gift_latitude') ?? $request->input('gift_lat');
        $giftLongitude = $request->input('gift_longitude') ?? $request->input('gift_lng');
        $orderType = $request->input('order_type', 'ordinary');

        // Determine which coordinates to use based on order type
        $lat = ($orderType === 'gift') ? $giftLatitude : $latitude;
        $lng = ($orderType === 'gift') ? $giftLongitude : $longitude;


        // Store all incoming data in session
        $sessionPayload = [
            'order_type' => $request->input('order_type'),
            'delivery_type' => $request->input('delivery_type'),
            'address_id' => $request->input('address_id'),
            'address_name' => $request->input('address_name'),
            'recipient_name' => $request->input('recipient_name'),
            'phone' => $request->input('phone'),
            'country_code' => $request->input('country_code'),
            'city_id' => $request->input('city_id'),
            'districts_id' => $request->input('districts_id'),
            'description' => $request->input('description'),
            'latitude' => $request->input('latitude') ?? $request->input('lat'),
            'longitude' => $request->input('longitude') ?? $request->input('lng'),
            'lat' => $request->input('lat') ?? $request->input('latitude'),
            'lng' => $request->input('lng') ?? $request->input('longitude'),
            'reciver_name' => $request->input('reciver_name'),
            'reciver_phone' => $request->input('reciver_phone'),
            'gift_city_id' => $request->input('gift_city_id'),
            'gift_districts_id' => $request->input('gift_districts_id'),
            'gift_address_name' => $request->input('gift_address_name'),
            'gift_latitude' => $request->input('gift_latitude') ?? $request->input('gift_lat'),
            'gift_longitude' => $request->input('gift_longitude') ?? $request->input('gift_lng'),
            'message' => $request->input('message'),
            'whatsapp' => $request->input('whatsapp'),
            'hide_sender' => $request->input('hide_sender'),
        ];

        // Remove null/empty values
        $sessionPayload = array_filter($sessionPayload, function($v){ return !is_null($v) && $v !== ''; });

        // Store in session
        session()->put('checkout.temp', $sessionPayload);

        Log::info('checkout.prepare session stored', [
            'user_id' => Auth::id(),
            'payload' => $sessionPayload,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم حفظ البيانات بنجاح',
            'redirect_url' => route('website.checkout'),
        ]);
    }

    public function store(CreateOrderRequest $request)
    {
        $user = $request->user();
        $validated = $request->validated();
        // Create an address if not provided but coordinates exist (ordinary orders only)
        if (
            ($validated['order_type'] ?? 'ordinary') !== 'gift'
            && empty($validated['address_id'])
            && (!empty($validated['latitude']) || !empty($validated['lat']))
            && (!empty($validated['longitude']) || !empty($validated['lng']))
        ) {
            $address = $this->addressRepository->create([
                'user_id' => $user->id,
                'address_name' => $validated['address_name'] ?? null,
                'recipient_name' => $validated['recipient_name'] ?? $user->name,
                'city_id' => $validated['city_id'] ?? null,
                'districts_id' => $validated['districts_id'] ?? null,
                'latitude' => isset($validated['latitude']) ? (float) $validated['latitude'] : (float) ($validated['lat'] ?? 0),
                'longitude' => isset($validated['longitude']) ? (float) $validated['longitude'] : (float) ($validated['lng'] ?? 0),
                'phone' => $validated['phone'] ?? $user->phone,
                'country_code' => $validated['country_code'] ?? $user->country_code,
                'description' => $validated['description'] ?? null,
                'is_default' => false,
            ]);
            $validated['address_id'] = $address->id;
            session()->put('checkout.temp.address_id', $address->id);
            Log::info('checkout.store created address', [
                'new_address_id' => $address->id,
                'session_address_id' => session('checkout.temp.address_id')
            ]);
        } else if (!empty($validated['address_id'])) {
            session()->put('checkout.temp.address_id', (int) $validated['address_id']);
            Log::info('checkout.store using existing address', [
                'address_id' => (int) $validated['address_id'],
                'session_address_id' => session('checkout.temp.address_id')
            ]);
        } else {
            Log::warning('checkout.store no address resolved', [
                'validated' => $validated
            ]);
        }

        $payload = [
            'order_type' => $validated['order_type'] ?? 'ordinary',
            'delivery_type' => $validated['delivery_type'],
            'schedule_date' => $validated['schedule_date'] ?? null,
            'schedule_time' => $validated['schedule_time'] ?? null,
            'address_id' => $validated['address_id'] ?? null,
            'latitude' => $validated['latitude'] ?? ($validated['lat'] ?? null),
            'longitude' => $validated['longitude'] ?? ($validated['lng'] ?? null),
            'lat' => $validated['lat'] ?? ($validated['latitude'] ?? null),
            'lng' => $validated['lng'] ?? ($validated['longitude'] ?? null),
            'reciver_name' => $validated['reciver_name'] ?? null,
            'reciver_phone' => $validated['reciver_phone'] ?? null,
            'gift_city_id' => $validated['gift_city_id'] ?? null,
            'gift_districts_id' => $validated['gift_districts_id'] ?? null,
            'gift_address_name' => $validated['gift_address_name'] ?? null,
            'gift_latitude' => $validated['gift_latitude'] ?? null,
            'gift_longitude' => $validated['gift_longitude'] ?? null,
            'message' => $validated['message'] ?? null,
            'whatsapp' => $validated['whatsapp'] ?? null,
            'hide_sender' => $validated['hide_sender'] ?? null,
            'payment_method_id' => (int) $validated['payment_method_id'],
            'notes' => $validated['notes'] ?? null,
        ];

        try {
            $result = $this->checkoutService->createOrderFromCart($user, $payload, ['origin' => 'website-checkout']);
            $order = $result['order'];
            $paymentUrl = $result['payment_url'] ?? null;

            return $paymentUrl
                ? Redirect::to($paymentUrl)
                : redirect()->route('website.checkout.success', ['orderNumber' => $order->order_number]);
        } catch (\Exception $e) {
            return back()->withErrors(['checkout' => $e->getMessage()])->withInput();
        }
    }

    public function success(Request $request, string $orderNumber)
    {
        Log::info('checkout.success clearing session', [
            'before_clear' => [
                'temp' => session('checkout.temp'),
                'order_type' => session('checkout.order_type')
            ]
        ]);
        session()->forget('checkout.temp');
        session()->forget('checkout.order_type');
        Log::info('checkout.success cleared session');

        return view('website.pages.success', [
            'orderNumber' => $orderNumber,
        ]);
    }

    // Apply coupon to current cart (website checkout)
    public function applyCoupon(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'coupon_code' => 'required|string|exists:coupons,coupon_num',
        ]);

        try {
            $cart = $this->cartService->applyCoupon($user, $data['coupon_code']);
            Log::info('checkout.applyCoupon success', [
                'user_id' => $user->id,
                'coupon_code' => $data['coupon_code'],
                'cart_id' => $cart->id,
            ]);

            $cart = $cart->load('items.product');
            return response()->json([
                'success' => true,
                'message' => __('apis.coupon_applied'),
                'cart' => $this->transformDbCart($cart),
            ]);
        } catch (\Throwable $e) {
            Log::warning('checkout.applyCoupon failed', [
                'user_id' => $user->id,
                'coupon_code' => $data['coupon_code'] ?? null,
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    // Remove coupon from current cart (website checkout)
    public function removeCoupon(Request $request)
    {
        $user = $request->user();

        try {
            $cart = $this->cartService->removeCoupon($user);
            Log::info('checkout.removeCoupon success', [
                'user_id' => $user->id,
                'cart_id' => $cart->id,
            ]);

            $cart = $cart->load('items.product');
            return response()->json([
                'success' => true,
                'message' => __('apis.coupon_removed'),
                'cart' => $this->transformDbCart($cart),
            ]);
        } catch (\Throwable $e) {
            Log::warning('checkout.removeCoupon failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    // Apply wallet deduction to current cart (website checkout)
    public function applyWalletDeduction(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'amount' => 'required|integer|min:1',
        ]);

        try {
            $cart = $this->cartService->applyWalletDeduction($user, (int) $data['amount']);
            Log::info('checkout.applyWalletDeduction success', [
                'user_id' => $user->id,
                'amount' => (int) $data['amount'],
                'cart_id' => $cart->id,
            ]);

            $cart = $cart->load('items.product');
            // Refresh user to get updated wallet balance
            $user->refresh();

            return response()->json([
                'success' => true,
                'message' => __('apis.wallet_deduction_applied'),
                'cart' => $this->transformDbCart($cart),
                'wallet_balance' => (float) $user->wallet_balance,
            ]);
        } catch (\Throwable $e) {
            Log::warning('checkout.applyWalletDeduction failed', [
                'user_id' => $user->id,
                'amount' => $data['amount'] ?? null,
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    // Remove wallet deduction from current cart (website checkout)
    public function removeWalletDeduction(Request $request)
    {
        $user = $request->user();

        try {
            $cart = $this->cartService->removeWalletDeduction($user);
            Log::info('checkout.removeWalletDeduction success', [
                'user_id' => $user->id,
                'cart_id' => $cart->id,
            ]);

            $cart = $cart->load('items.product');
            // Refresh user to get updated wallet balance
            $user->refresh();

            return response()->json([
                'success' => true,
                'message' => __('apis.wallet_deduction_removed'),
                'cart' => $this->transformDbCart($cart),
                'wallet_balance' => (float) $user->wallet_balance,
            ]);
        } catch (\Throwable $e) {
            Log::warning('checkout.removeWalletDeduction failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function calculate(Request $request)
    {
        $user = $request->user();
        $cart = $this->cartService->getCart($user)->load('items.product');

        $data = [
            'order_type'    => $request->input('order_type', 'ordinary'),
            'delivery_type' => $request->input('delivery_type', 'immediate'),
        ];
        if ($request->filled('address_id')) {
            $data['address_id'] = (int) $request->input('address_id');
        }
        // Accept both naming variants for coordinates
        if ($request->filled('lat') && $request->filled('lng')) {
            $data['lat'] = (float) $request->input('lat');
            $data['lng'] = (float) $request->input('lng');
        } elseif ($request->filled('latitude') && $request->filled('longitude')) {
            $data['lat'] = (float) $request->input('latitude');
            $data['lng'] = (float) $request->input('longitude');
        }
        if ($request->filled('gift_latitude') && $request->filled('gift_longitude')) {
            $data['gift_latitude']  = (float) $request->input('gift_latitude');
            $data['gift_longitude'] = (float) $request->input('gift_longitude');
        }
        try {

        
            // 2) Build an Address model for distance/fee calc (existing address or ephemeral with given coords)
            $addressModel = null;
            if (!empty($data['address_id'])) {
                $addressModel = $this->addressRepository->getUserAddress($user, (int) $data['address_id']);
            }
            if (!$addressModel) {
                // Prefer gift coordinates if provided and order_type is gift
                $lat = $data['lat'] ?? $data['gift_latitude'] ?? null;
                $lng = $data['lng'] ?? $data['gift_longitude'] ?? null;
                if ($lat !== null && $lng !== null) {
                    $addressModel = new Address(['latitude' => (float) $lat, 'longitude' => (float) $lng]);
                }
            }
            if (!$addressModel) {
                throw new \Exception(__('apis.location_required'));
            }

       
            // 5) Totals assembly
            $subtotal        = (float) $cart->subtotal;
            $discount        = (float) ($cart->discount ?? 0);
            $coupon          = (float) ($cart->coupon_value ?? 0);
            $wallet          = (float) ($cart->wallet_deduction ?? 0);
            $vat             = (float) ($cart->vat_amount ?? 0);
            $beforeDelivery  = (float) $cart->total;
            $orderType       = $data['order_type'] ?? 'ordinary';
            $deliveryFee     = ($orderType === 'pickup') ? 0 : (float) getSiteSetting('delivery_fee', 15);
            $giftFee         = (float) ($orderType === 'gift' ? getSiteSetting('gift_fee', 0) : 0);
            $final           = $beforeDelivery + $deliveryFee + $giftFee;

            // Immediate info
            $today            = now()->format('Y/m/d');

            return response()->json([
                'success'      => true,
                'distance'     => 0,
                'delivery_fee' => $deliveryFee,
                'gift_fee'     => $giftFee,
                'totals'       => [
                    'subtotal'         => $subtotal,
                    'discount'         => $discount,
                    'coupon_value'     => $coupon,
                    'wallet_deduction' => $wallet,
                    'vat_amount'       => $vat,
                    'amount_before_vat'=> $subtotal - $discount,
                    'before_delivery'  => $beforeDelivery,
                    'final'            => $final,
                ],
                'immediate' => [
                    'last_pickup_string' => "اليوم - {$today}",
                    'expected_duration'  => '3-4 ساعات',
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
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

        $subtotal = (float) $cart->subtotal;
        $discount = (float) ($cart->discount ?? 0);
        $amountBeforeVat = $subtotal - $discount; // pre-calculated on server to avoid frontend math

        return [
            'items' => $items,
            'count' => array_sum(array_map(fn($i) => $i['quantity'], $items)),
            'subtotal' => $subtotal,
            'discount' => $discount,
            'coupon_code' => $cart->coupon_code ?? null,
            'coupon_value' => (float) ($cart->coupon_value ?? 0),
            'wallet_deduction' => (float) ($cart->wallet_deduction ?? 0),
            'vat_amount' => (float) ($cart->vat_amount ?? 0),
            'amount_before_vat' => (float) $amountBeforeVat,
            'total' => (float) $cart->total, // total before delivery fee
        ];
    }
}


