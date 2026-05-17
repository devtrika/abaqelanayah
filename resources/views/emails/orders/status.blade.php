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
    $statusLabel = __('site.order_status_' . $order->status);
    $trackUrl = route('website.track-order.show', ['orderNumber' => $orderNumber]);
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
                        <td style="padding: 18px 18px 12px;">
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
                                    <td align="center" style="padding: 10px 16px 6px;">
                                        <div style="font-family: Arial, Helvetica, sans-serif; font-size: 20px; font-weight: 700; line-height: 1.25; color: #111827;">
                                            {{ __('site.order_status_updated') }}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding: 0 16px 14px;">
                                        <div style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #6B7280; line-height: 1.6;">
                                            {{ __('site.hello') }} {{ $customerName }}<br>
                                            {{ __('site.your_order_status_updated') }}
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; margin-top: 8px;">
                                <tr>
                                    <td style="padding: 10px 0;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; background: #F3F4F6; border-radius: 10px;">
                                            <tr>
                                                <td style="padding: 16px;">
                                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                                                        <tr>
                                                            <td style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #6B7280; padding: 2px 0;">
                                                                {{ __('site.order_number') }}
                                                            </td>
                                                            <td align="{{ $isRtl ? 'left' : 'right' }}" style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; font-weight: 800; color: #111827; padding: 2px 0;">
                                                                #{{ $orderNumber }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #6B7280; padding: 2px 0;">
                                                                {{ __('site.new_status') }}
                                                            </td>
                                                            <td align="{{ $isRtl ? 'left' : 'right' }}" style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; font-weight: 800; color: #111827; padding: 2px 0;">
                                                                {{ $statusLabel }}
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; margin-top: 12px;">
                                <tr>
                                    <td align="center" style="padding: 10px 0 6px;">
                                        <a href="{{ $trackUrl }}" style="display: inline-block; padding: 12px 22px; border: 1px solid #D1D5DB; border-radius: 6px; text-decoration: none; font-weight: 700; font-size: 12px; letter-spacing: 0.6px; color: #6B7280;">
                                            {{ __('site.track_order') }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding: 6px 0 0;">
                                        <div style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #6B7280;">
                                            {{ __('site.thanks') }}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
