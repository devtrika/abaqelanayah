<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductOption;
use Illuminate\Support\Facades\Session;
use App\Repositories\CartRepository; // for VAT settings via SiteSetting

class SessionCartService
{
    private string $sessionKey = 'session_cart';

    public function getCart(): array
    {
        $cart = Session::get($this->sessionKey, [
            'items' => [],
            'subtotal' => 0,
            'total' => 0,
        ]);

        // Recalculate totals to be safe
        return $this->recalculate($cart);
    }

    public function addToCart(array $data): array
    {
        $cart = $this->getCart();
        $key = $this->makeKey($data);

        $quantity = max(1, (int)($data['quantity'] ?? 1));
        if (isset($cart['items'][$key])) {
            $cart['items'][$key]['quantity'] += $quantity;
        } else {
            $product = Product::findOrFail($data['product_id']);
            $unitPrice = $this->computeUnitPrice($product, $data);
            $cart['items'][$key] = [
                'id' => null,
                'key' => $key,
                'product_id' => $product->id,
                'name' => $product->name,
                'image_url' => $product->image_url,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total' => round($unitPrice * $quantity, 2),
                'returnable' => false,
            ];
        }

        $cart = $this->recalculate($cart);
        Session::put($this->sessionKey, $cart);
        return $cart;
    }

    public function updateCartItem(array $payload): array
    {
        $cart = $this->getCart();
        $key = $payload['key'];
        $quantity = max(1, (int)$payload['quantity']);
        if (isset($cart['items'][$key])) {
            $cart['items'][$key]['quantity'] = $quantity;
            $cart['items'][$key]['total'] = round($cart['items'][$key]['unit_price'] * $quantity, 2);
        }
        $cart = $this->recalculate($cart);
        Session::put($this->sessionKey, $cart);
        return $cart;
    }

    public function removeFromCart(string $key): array
    {
        $cart = $this->getCart();
        unset($cart['items'][$key]);
        $cart = $this->recalculate($cart);
        Session::put($this->sessionKey, $cart);
        return $cart;
    }

    public function clear(): void
    {
        Session::forget($this->sessionKey);
    }

    private function makeKey(array $data): string
    {
        $parts = [
            (int)($data['product_id'] ?? 0),
            (int)($data['weight_option_id'] ?? 0),
            (int)($data['cutting_option_id'] ?? 0),
            (int)($data['packaging_option_id'] ?? 0),
        ];
        return implode(':', $parts);
    }

    private function computeUnitPrice(Product $product, array $data): float
    {
        // base price after discount
        $base = (float)($product->final_price ?? $product->base_price ?? 0);
        $weightPrice = 0;
        $cuttingPrice = 0;
        $packagingPrice = 0;

        if (!empty($data['weight_option_id'])) {
            $opt = ProductOption::find($data['weight_option_id']);
            $weightPrice = (float)($opt?->additional_price ?? 0);
        }
        if (!empty($data['cutting_option_id'])) {
            $opt = ProductOption::find($data['cutting_option_id']);
            $cuttingPrice = (float)($opt?->additional_price ?? 0);
        }
        if (!empty($data['packaging_option_id'])) {
            $opt = ProductOption::find($data['packaging_option_id']);
            $packagingPrice = (float)($opt?->additional_price ?? 0);
        }

        return round($base + $weightPrice + $cuttingPrice + $packagingPrice, 2);
    }

    private function recalculate(array $cart): array
    {
        $subtotal = 0.0;
        foreach ($cart['items'] as &$item) {
            $item['total'] = round($item['unit_price'] * $item['quantity'], 2);
            $subtotal += $item['total'];
        }
        unset($item);

        // VAT similar to CartService
        $repo = app(CartRepository::class);
        $appSettings = $repo->getAppSettings();
        $vatSetting = isset($appSettings['vat_amount']) ? (float)$appSettings['vat_amount'] : 15.0;
        $vatRate = $vatSetting > 1 ? ($vatSetting / 100) : $vatSetting;
        $vatRate = max(0, $vatRate);

        $vatAmount = round($subtotal * $vatRate, 2);
        $total = round($subtotal + $vatAmount, 2);

        $cart['subtotal'] = round($subtotal, 2);
        $cart['total'] = $total;
        $cart['count'] = array_sum(array_map(fn($i) => (int)$i['quantity'], $cart['items']));

        return $cart;
    }
}

