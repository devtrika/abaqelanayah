@php
    $isRtl = app()->getLocale() === 'ar';
    $appName = $settings['name_' . app()->getLocale()]
        ?? $settings['name_en']
        ?? config('app.name');
    $logoValue = $settings['logo'] ?? null;
    $logoUrl = null;
    if (is_string($logoValue) && $logoValue !== '') {
        $logoUrl = preg_match('#^https?://#i', $logoValue) ? $logoValue : url($logoValue);
    }
    $customerName = $order->address->recipient_name
        ?? $order->recipient_name
        ?? optional($order->user)->name
        ?? __('site.guest');
    $orderNumber = $order->order_number ?? $order->id;
    $currency = __('site.currency');
    $subtotal = $order->subtotal ?? 0;
    $deliveryFee = $order->delivery_fee ?? 0;
    $discountAmount = $order->discount_amount ?? 0;
    $giftFee = $order->gift_fee ?? 0;
    $total = $order->total ?? 0;
    $paymentMethodName = optional($order->paymentMethod)->name;
    $paymentReference = $order->payment_reference;
@endphp

<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="margin: 0; padding: 0; background: #F9FAFB;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; width: 100%; background: #F9FAFB;">
        <tr>
            <td align="center" style="padding: 24px 16px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; width: 100%; max-width: 640px; background: #ffffff; border-radius: 12px; overflow: hidden;">
                    <tr>
                        <td style="padding: 18px 18px 6px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                                <tr>
                                    <td align="center" style="padding: 12px 0 6px;">
                                        @if($logoUrl)
                                            <img src="{{ $logoUrl }}" alt="{{ $appName }}" style="max-height: 44px; display: block;">
                                        @endif
                                        <div style="font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-weight: 800; color: #111827; line-height: 1.2; margin-top: 6px;">
                                            {{ $appName }}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding: 10px 16px 4px;">
                                        <div style="font-family: Arial, Helvetica, sans-serif; font-size: 20px; font-weight: 700; line-height: 1.25; color: #111827;">
                                            {{ __('site.thank_you_name_order_received', ['name' => $customerName]) }}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding: 6px 16px 14px;">
                                        <div style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #6B7280;">
                                            {{ __('site.order_number') }} #{{ $orderNumber }}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 0 18px 18px;">
                            <div style="font-family: Arial, Helvetica, sans-serif; color: #111827;">

                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
        <tr>
            <td style="padding: 10px 0;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; background: #F3F4F6; border-radius: 10px;">
                    <tr>
                        <td style="padding: 16px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                                <tr>
                                    <td valign="top" style="width: 55%; padding-{{ $isRtl ? 'left' : 'right' }}: 12px;">
                                        <div style="font-size: 13px; font-weight: 700; color: #111827; margin-bottom: 6px;">
                                            {{ $customerName }}
                                        </div>
                                        @if($order->address)
                                            <div style="font-size: 12px; color: #374151; line-height: 1.6;">
                                                @if($order->address->phone)
                                                    {{ $order->address->phone }}<br>
                                                @endif
                                                @if($order->address->address_name)
                                                    {{ $order->address->address_name }}<br>
                                                @endif
                                                @if($order->address->district)
                                                    {{ $order->address->district->name }}<br>
                                                @endif
                                                @if($order->address->city)
                                                    {{ $order->address->city->name }}<br>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td valign="top" align="{{ $isRtl ? 'left' : 'right' }}" style="width: 45%;">
                                        <div style="font-size: 26px; font-weight: 800; color: #111827; line-height: 1;">
                                            {{ number_format((float) $total, 2) }}
                                        </div>
                                        <div style="font-size: 12px; color: #6B7280; margin-top: 6px;">
                                            {{ $currency }}
                                        </div>
                                        @if($paymentMethodName)
                                            <div style="font-size: 12px; color: #374151; margin-top: 10px;">
                                                {{ $paymentMethodName }}
                                            </div>
                                        @endif
                                        @if($paymentReference)
                                            <div style="font-size: 12px; color: #6B7280; margin-top: 4px;">
                                                {{ __('site.payment_reference') }}: {{ $paymentReference }}
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
                                </table>

                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
        <tr>
            <td style="padding: 12px 0 6px;">
                <div style="font-size: 13px; color: #6B7280;">
                    {{ __('site.order_date') }}: {{ $order->created_at->format('Y-m-d h:i A') }}
                </div>
            </td>
        </tr>
                                </table>

                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
        @foreach($order->items as $item)
            @php
                $productName = optional($item->product)->name ?? __('site.product');
                $productImage = optional($item->product)->image_url ?: null;
                $optionText = optional($item->weightOption)->name ?: null;
                $linePrice = $item->total ?? ($item->price * $item->quantity);
            @endphp
            <tr>
                <td style="padding: 14px 0; border-bottom: 1px solid #E5E7EB;">
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                        <tr>
                            <td valign="middle" style="width: 64px; padding-{{ $isRtl ? 'left' : 'right' }}: 12px;">
                                @if($productImage)
                                    <img src="{{ $productImage }}" alt="{{ $productName }}" width="56" height="56" style="border-radius: 10px; display: block; object-fit: cover;">
                                @else
                                    <div style="width: 56px; height: 56px; border-radius: 10px; background: #F3F4F6;"></div>
                                @endif
                            </td>
                            <td valign="middle">
                                <div style="font-size: 13px; font-weight: 700; color: #111827; line-height: 1.35;">
                                    {{ $productName }}
                                </div>
                                <div style="font-size: 12px; color: #6B7280; margin-top: 3px;">
                                    @if($optionText)
                                        {{ $optionText }}
                                    @endif
                                    <span style="margin-{{ $isRtl ? 'right' : 'left' }}: 8px;">
                                        {{ __('site.quantity') }}: {{ $item->quantity }}
                                    </span>
                                </div>
                            </td>
                            <td valign="middle" align="{{ $isRtl ? 'left' : 'right' }}" style="width: 110px;">
                                <div style="font-size: 13px; font-weight: 700; color: #111827;">
                                    {{ number_format((float) $linePrice, 2) }}
                                </div>
                                <div style="font-size: 12px; color: #6B7280; margin-top: 3px;">
                                    {{ $currency }}
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        @endforeach
                                </table>

                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; margin-top: 10px;">
        <tr>
            <td style="padding: 12px 0;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                    <tr>
                        <td style="font-size: 13px; color: #6B7280; padding: 2px 0;">{{ __('site.subtotal') }}</td>
                        <td align="{{ $isRtl ? 'left' : 'right' }}" style="font-size: 13px; color: #111827; padding: 2px 0;">
                            {{ number_format((float) $subtotal, 2) }} {{ $currency }}
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 13px; color: #6B7280; padding: 2px 0;">{{ __('site.delivery_fee') }}</td>
                        <td align="{{ $isRtl ? 'left' : 'right' }}" style="font-size: 13px; color: #111827; padding: 2px 0;">
                            {{ number_format((float) $deliveryFee, 2) }} {{ $currency }}
                        </td>
                    </tr>
                    @if($giftFee > 0)
                        <tr>
                            <td style="font-size: 13px; color: #6B7280; padding: 2px 0;">{{ __('site.gift_fee') }}</td>
                            <td align="{{ $isRtl ? 'left' : 'right' }}" style="font-size: 13px; color: #111827; padding: 2px 0;">
                                {{ number_format((float) $giftFee, 2) }} {{ $currency }}
                            </td>
                        </tr>
                    @endif
                    @if($discountAmount > 0)
                        <tr>
                            <td style="font-size: 13px; color: #6B7280; padding: 2px 0;">{{ __('site.discount') }}</td>
                            <td align="{{ $isRtl ? 'left' : 'right' }}" style="font-size: 13px; color: #111827; padding: 2px 0;">
                                -{{ number_format((float) $discountAmount, 2) }} {{ $currency }}
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="2" style="padding: 10px 0 6px; border-top: 1px solid #E5E7EB;"></td>
                    </tr>
                    <tr>
                        <td style="font-size: 13px; font-weight: 800; color: #111827; padding: 2px 0;">{{ __('site.total') }}</td>
                        <td align="{{ $isRtl ? 'left' : 'right' }}" style="font-size: 13px; font-weight: 800; color: #111827; padding: 2px 0;">
                            {{ number_format((float) $total, 2) }} {{ $currency }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
                                </table>

                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; margin-top: 16px;">
        <tr>
            <td align="center" style="padding: 12px 0 6px;">
                <a href="{{ url('/') }}" style="display: inline-block; padding: 12px 22px; border: 1px solid #D1D5DB; border-radius: 6px; text-decoration: none; font-weight: 700; font-size: 12px; letter-spacing: 0.6px; color: #6B7280;">
                    {{ __('site.return_to_shop') }}
                </a>
            </td>
        </tr>
        <tr>
            <td align="center" style="padding: 6px 0 0;">
                <a href="{{ route('website.track-order.show', ['orderNumber' => ($order->order_number ?? $order->id)]) }}" style="font-size: 12px; color: #6B7280; text-decoration: underline;">
                    {{ __('site.track_order') }}
                </a>
            </td>
        </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
