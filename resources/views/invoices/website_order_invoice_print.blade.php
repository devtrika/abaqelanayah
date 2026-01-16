<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>فاتورة الطلب #{{ $order->id ?? '-' }}</title>
    <!-- Website styles -->
    <link rel="stylesheet" href="{{ asset('website/css/bootstrap.rtl.min.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/main.css') }}">
    <style>
        /* Print tuning to match site look while staying clean */
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }
        body { background: #fff; }
        .invoice-wrapper { width: 100%; max-width: 980px; margin: 0 auto; padding: 24px; }
        .invoice-header { display: flex; gap: 16px; align-items: center; justify-content: space-between; margin-bottom: 16px; }
        .invoice-header .brand { display: flex; align-items: center; gap: 12px; }
        .invoice-header .brand img { width: 48px; height: 48px; object-fit: contain; }
        .invoice-header .brand h1 { font-size: 20px; margin: 0; }
        .invoice-meta { text-align: left; font-size: 14px; }
        .section-title { font-size: 16px; font-weight: 700; margin: 18px 0 10px; }
        .order-products table { width: 100%; border-collapse: collapse; }
        .order-products th, .order-products td { border: 1px solid #eee; padding: 8px; font-size: 14px; }
        .order-products th { background: #f7f7f7; font-weight: 600; }
        .order-totals { margin-top: 14px; }
        .order-totals .total-item { display: flex; justify-content: space-between; padding: 6px 0; }
        .order-totals .total-item .title { color: #666; }
        .order-final { margin-top: 10px; padding-top: 10px; border-top: 1px dashed #ddd; font-weight: 700; display: flex; justify-content: space-between; }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body>
    <div class="invoice-wrapper">
        <div class="invoice-header">
            <div class="brand">
                <img src="{{ asset('website/images/favicon.png') }}" alt="Logo">
                <h1>{{ config('app.name', 'المتجر') }}</h1>
            </div>
            <div class="invoice-meta">
                <div>رقم الطلب: {{ $order->id }}</div>
                <div>تاريخ: {{ $order->created_at?->format('Y-m-d H:i') }}</div>
                @php $effectiveStatus = $order->refund_status ?? $order->status; @endphp
                <div>الحالة: {{ __('admin.' . $effectiveStatus) }}</div>
                @if($order->refund_status)
                    <div>حالة الاسترجاع: {{ __('admin.' . $order->refund_status) }}</div>
                @endif
            </div>
        </div>

        <div class="order-information">
            <div class="section-title">بيانات الطلب</div>
            <div>العميل: {{ $order->user?->name ?? '-' }}</div>
            <div>الهاتف: {{ $order->user?->phone ?? '-' }}</div>
            <div>نوع التوصيل: {{ $order->delivery_type_label ?? $order->deliveryTypeLabel ?? $order->delivery_type ?? '-' }}</div>
            <div>مدة التحضير المتوقعة: {{ $order->expected_preparation_duration ?? '-' }}</div>
            <div>الكوبون: {{ $order->coupon_code ?? '-' }}</div>
        </div>

        <div class="order-products">
            <div class="section-title">العناصر</div>
            <table>
                <thead>
                    <tr>
                        <th>المنتج</th>
                        <th>الكمية</th>
                        <th>السعر</th>
                        <th>الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(($order->items ?? []) as $item)
                        <tr>
                            <td>{{ $item->product?->name ?? $item->name ?? '-' }}</td>
                            <td>{{ $item->qty ?? $item->quantity ?? 1 }}</td>
                            <td>{{ number_format($item->price ?? 0, 2) }}</td>
                            <td>{{ number_format(($item->price ?? 0) * ($item->qty ?? $item->quantity ?? 1), 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="order-totals">
            <div class="section-title">ملخص الطلب</div>
            <div class="total-item">
                <span class="title">المجموع الفرعي بدون ضريبة</span>
                <strong class="value">{{ number_format(($order->subtotal ?? $order->total_before_discount ?? 0), 2) }}</strong>
            </div>
            <div class="total-item">
                <span class="title">قيمة الخصم ({{ $order->coupon_code ?? '-' }})</span>
                <strong class="value">{{ number_format(($order->coupon_amount ?? $order->discount_amount ?? $order->discount ?? 0), 2) }}</strong>
            </div>
            <div class="total-item">
                <span class="title">المجموع بعد الخصم</span>
                <strong class="value">{{ number_format((($order->subtotal ?? $order->total_before_discount ?? 0) - ($order->coupon_amount ?? $order->discount_amount ?? $order->discount ?? 0)), 2) }}</strong>
            </div>
            <div class="total-item">
                <span class="title">رسوم التوصيل</span>
                <strong class="value">{{ number_format(($order->delivery_fee ?? $order->shipping_cost ?? 0), 2) }}</strong>
            </div>
            <div class="total-item">
                <span class="title">الإجمالي بدون ضريبة</span>
                <strong class="value">{{ number_format(((($order->subtotal ?? $order->total_before_discount ?? 0) - ($order->coupon_amount ?? $order->discount_amount ?? $order->discount ?? 0)) + ($order->delivery_fee ?? $order->shipping_cost ?? 0)), 2) }}</strong>
            </div>
            <div class="total-item">
                <span class="title">قيمة الضريبة ({{ $order->vat_percent ?? 15 }}٪)</span>
                <strong class="value">{{ number_format(($order->vat_amount ?? 0), 2) }}</strong>
            </div>
            <div class="total-item">
                <span class="title">الإجمالي بعد الضريبة</span>
                <strong class="value">{{ number_format((((($order->subtotal ?? $order->total_before_discount ?? 0) - ($order->coupon_amount ?? $order->discount_amount ?? $order->discount ?? 0)) + ($order->delivery_fee ?? $order->shipping_cost ?? 0)) + ($order->vat_amount ?? 0)), 2) }}</strong>
            </div>
            <div class="total-item">
                <span class="title">خصم المحفظة</span>
                <strong class="value">{{ number_format(($order->wallet_deduction ?? 0), 2) }}</strong>
            </div>
        </div>

        <div class="order-final">
            <span>الإجمالي النهائي</span>
            <strong>{{ number_format(($order->final_total ?? $order->total ?? $order->grand_total ?? 0), 2) }}</strong>
        </div>
    </div>

    <script>
        // Auto-trigger printing on load (website only) - no visible controls
        window.addEventListener('load', function () {
            try { window.print(); } catch (e) {}
        });
    </script>
</body>
</html>