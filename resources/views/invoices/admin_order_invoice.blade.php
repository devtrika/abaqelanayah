<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->order_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  
</head><style>
    @page {
        margin: 20px;
    }

    body {
        font-family: 'Cairo', 'Arial', sans-serif;
        direction: rtl;
        text-align: right;
        margin: 0;
        padding: 0;
        background-color: #fff;
        font-size: 12px;
    }

    .invoice-container {
        padding: 20px;
        background-color: #fff;
        page-break-inside: avoid;
    }

    .header {
        text-align: center;
        border-bottom: 2px solid #007bff;
        padding-bottom: 15px;
        margin-bottom: 20px;
    }

    .header h1 {
        color: #007bff;
        margin: 0;
        font-size: 22px;
    }

    .header h2 {
        color: #6c757d;
        margin: 5px 0;
        font-size: 16px;
    }

    .admin-badge {
        background-color: #007bff;
        color: white;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 10px;
        display: inline-block;
        margin-top: 5px;
    }

    .info-section {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .info-box {
        flex: 1 1 45%;
        margin-bottom: 15px;
        padding: 10px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
    }

    .info-box h3 {
        font-size: 14px;
        color: #007bff;
        margin-bottom: 10px;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 5px;
    }

    .info-item {
        margin-bottom: 6px;
        font-size: 12px;
    }

    .info-label {
        font-weight: bold;
        color: #343a40;
    }

    .info-value {
        color: #495057;
    }

    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        page-break-inside: avoid;
    }

    .items-table th,
    .items-table td {
        border: 1px solid #dee2e6;
        padding: 8px;
        font-size: 12px;
        text-align: right;
    }

    .items-table th {
        background-color: #007bff;
        color: white;
    }

    .totals-section {
        margin-top: 20px;
        padding-top: 10px;
        border-top: 2px solid #dee2e6;
        page-break-inside: avoid;
    }

    .total-row {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        margin-bottom: 8px;
    }

    .total-row.final {
        font-weight: bold;
        font-size: 14px;
        color: #007bff;
        border-top: 1px solid #007bff;
        padding-top: 6px;
    }

    .footer {
        margin-top: 30px;
        text-align: center;
        color: #6c757d;
        font-size: 11px;
        border-top: 1px solid #dee2e6;
        padding-top: 10px;
        page-break-inside: avoid;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 10px;
        font-weight: bold;
        font-size: 11px;
        color: #fff;
    }

    .status-completed { background-color: #28a745; }
    .status-processing { background-color: #ffc107; color: #000; }
    .status-cancelled { background-color: #dc3545; }

    /* Avoid table or section breaking mid-page */
    tr, td, th, .info-box, .totals-section, .footer {
        page-break-inside: avoid;
    }
</style>

<body>
<div class="invoice-container">
    <!-- Header -->
    <div class="header">
        <h1>فاتورة الطلب - إدارة النظام</h1>
        <h2>Invoice #{{ $order->order_number }}</h2>
        <p>تاريخ الطلب: {{ $order->created_at->format('Y-m-d H:i') }}</p>
    </div>

    <!-- Customer Info -->
    <div class="info-section">
        <div class="info-box">
            <h3>معلومات العميل</h3>
            <div class="info-item"><span class="info-label">الاسم:</span> <span class="info-value">{{ $customer->name }}</span></div>
            <div class="info-item"><span class="info-label">رقم الهاتف:</span> <span class="info-value">{{ $customer->country_code }}{{ $customer->phone }}</span></div>
            <div class="info-item"><span class="info-label">البريد الإلكتروني:</span> <span class="info-value">{{ $customer->email ?? 'غير محدد' }}</span></div>
            <div class="info-item"><span class="info-label">نوع المستخدم:</span> <span class="info-value">{{ $customer->type === 'client' ? 'عميل' : 'مزود' }}</span></div>
        </div>
    </div>

    <!-- Order Info -->
    <div class="info-section">
        <div class="info-box">
            <h3>معلومات الطلب</h3>
            <div class="info-item"><span class="info-label">رقم الطلب:</span> <span class="info-value">{{ $order->order_number }}</span></div>
            @php
                $statusKey = $order->status;
                $statusEnum = \App\Enums\OrderStatus::tryFrom($statusKey);
                $statusLabel = $statusEnum?->label();
                if (!$statusLabel) {
                    $statusLabel = __('admin.' . $statusKey);
                    if ($statusLabel === 'admin.' . $statusKey) {
                        $statusLabel = __('admin.' . str_replace('_','-',$statusKey));
                    }
                }
                if (str_starts_with($statusLabel, 'admin.')) {
                    $statusLabel = $statusKey;
                }
                $statusColorName = $statusEnum?->color() ?? null;
                $bgColor = match ($statusColorName) {
                    'primary' => '#007bff',
                    'success' => '#28a745',
                    'warning' => '#ffc107',
                    'danger'  => '#dc3545',
                    'info'    => '#17a2b8',
                    'secondary' => '#6c757d',
                    default   => '#6c757d',
                };
                $textColor = $statusColorName === 'warning' ? '#000' : '#fff';
            @endphp
            <div class="info-item"><span class="info-label">حالة الطلب:</span> <span class="status-badge" style="background-color: {{ $bgColor }}; color: {{ $textColor }};">{{ $statusLabel }}</span></div>
            <div class="info-item"><span class="info-label">طريقة الدفع:</span> <span class="info-value">{{ $payment_method->name ?? 'غير محدد' }}</span></div>
            <div class="info-item"><span class="info-label">حالة الدفع:</span> <span class="info-value">{{ __('admin.' . $order->payment_status) }}</span></div>
            <div class="info-item"><span class="info-label">نوع التوصيل:</span> <span class="info-value">{{ __('admin.' . $order->delivery_type) }}</span></div>
        </div>

        <!-- Address Info -->
        <div class="info-box">
            <h3>معلومات العنوان</h3>
            @if($address)
                @php
                    $addrPhone = trim(($address->country_code ?? '') . ($address->phone ?? ''));
                    if (empty($addrPhone)) {
                        $addrPhone = trim(($customer->country_code ?? '') . ($customer->phone ?? ''));
                    }
                @endphp
                <div class="info-item"><span class="info-label">رقم الهاتف:</span> <span class="info-value">{{ $addrPhone ?: 'غير محدد' }}</span></div>
                <div class="info-item"><span class="info-label">المدينة:</span> <span class="info-value">{{ $address->city->name ?? 'غير محدد' }}</span></div>
            @else
                <div class="info-item"><span class="info-value">لا يوجد عنوان محدد</span></div>
            @endif
        </div>

        {{-- <div class="info-box">
            <h3>معلومات الفرع</h3>
            @if($branch)
                <div class="info-item"><span class="info-label">اسم الفرع:</span> <span class="info-value">{{ $branch->name }}</span></div>
                <div class="info-item"><span class="info-label">المدينة:</span> <span class="info-value">{{ $branch->city->name ?? 'غير محدد' }}</span></div>
            @else
                <div class="info-item"><span class="info-value">لا يوجد عنوان محدد</span></div>
            @endif
        </div> --}}
    </div>

    <!-- Order Items -->
    <h3>المنتجات</h3>
    <table class="items-table">
        <thead>
            <tr>
                <th>الاسم</th>
                <th>الكمية</th>
                <th>السعر</th>
                <th>الإجمالي</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->price, 2) }} ريال</td>
                    <td>{{ number_format($item->total, 2) }} ريال</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals -->
    <div class="totals-section">
        @php
            $discount = (float) ($order->coupon_amount ?? $order->discount_amount ?? 0);
            $deliveryFee = (float) ($order->delivery_fee ?? 0);
            $homeServiceFee = (float) ($order->home_service_fee ?? 0);
            $bookingFee = (float) ($order->booking_fee ?? 0);
            $cancelFees = (float) ($order->cancel_fees ?? 0);
            $subtotalAfterDiscount = max(0, (float) $order->subtotal - $discount);
            $beforeVat = $subtotalAfterDiscount + $deliveryFee + $homeServiceFee + $bookingFee + $cancelFees;
            $vat = (float) ($order->vat_amount ?? 0);
            $afterVat = $beforeVat + $vat;
            $wallet = (float) ($order->wallet_deduction ?? 0);
            $loyalty = (float) ($order->loyalty_deduction ?? 0);
            $vatPercent = $beforeVat > 0 ? round(($vat / $beforeVat) * 100, 2) : 0;
        @endphp
        <div class="total-row"><span>إجمالي المنتجات بدون ضريبة:</span> <span>{{ number_format((float) $order->subtotal, 2) }} ريال</span></div>
        @if($discount > 0)
            <div class="total-row"><span>كود الخصم {{ $order->coupon_code ? '(' . $order->coupon_code . ')' : '' }}:</span> <span>-{{ number_format($discount, 2) }} ريال</span></div>
            <div class="total-row"><span>إجمالي المنتجات بعد الخصم:</span> <span>{{ number_format($subtotalAfterDiscount, 2) }} ريال</span></div>
        @endif
        <div class="total-row"><span>رسوم التوصيل:</span> <span>{{ number_format($deliveryFee, 2) }} ريال</span></div>
        @if($homeServiceFee > 0)
            <div class="total-row"><span>رسوم الخدمة المنزلية:</span> <span>{{ number_format($homeServiceFee, 2) }} ريال</span></div>
        @endif
        @if($bookingFee > 0)
            <div class="total-row"><span>رسوم الحجز:</span> <span>{{ number_format($bookingFee, 2) }} ريال</span></div>
        @endif
        @if($cancelFees > 0)
            <div class="total-row"><span>رسوم الإلغاء:</span> <span>{{ number_format($cancelFees, 2) }} ريال</span></div>
        @endif
        <div class="total-row"><span>إجمالي المستحق بدون ضريبة:</span> <span>{{ number_format($beforeVat, 2) }} ريال</span></div>
        <div class="total-row"><span>ضريبة القيمة المضافة ({{ $vatPercent }}%):</span> <span>{{ number_format($vat, 2) }} ريال</span></div>
        <div class="total-row"><span>إجمالي المستحق بعد الضريبة:</span> <span>{{ number_format($afterVat, 2) }} ريال</span></div>
        @if($wallet > 0)
            <div class="total-row"><span>خصم المحفظة:</span> <span>-{{ number_format($wallet, 2) }} ريال</span></div>
        @endif
        @if($loyalty > 0)
            <div class="total-row"><span>خصم النقاط:</span> <span>-{{ number_format($loyalty, 2) }} ريال</span></div>
        @endif
        <div class="total-row final"><span>الإجمالي النهائي:</span> <span>{{ number_format((float) $order->total, 2) }} ريال</span></div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>تم إنشاء هذه الفاتورة في {{ now()->format('Y-m-d H:i:s') }}</p>
        <p>فاتورة إدارية - نظام إدارة الطلبات</p>
    </div>
</div>
</body>
</html> 