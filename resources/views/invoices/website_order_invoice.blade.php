<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8" />
    <title>فاتورة الطلب #{{ $order->order_number }}</title>
    <style>
        @page { margin: 24px; }
        body {
            font-family: 'DejaVu Sans', 'Cairo', 'Arial', sans-serif;
            background: #ffffff;
            color: #222;
            margin: 0;
            padding: 0;
            direction: rtl;
            text-align: right;
            font-size: 12px;
        }
        .container {
            padding: 16px 12px;
            page-break-inside: avoid;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #34b7ea;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }
        .brand {
            display: flex; align-items: center;
        }
        .brand img { height: 40px; margin-left: 10px; }
        .brand-title { font-size: 18px; color: #34b7ea; margin: 0; }
        .invoice-meta { text-align: left; }
        .invoice-meta h2 { margin: 0; font-size: 16px; color: #222; }
        .invoice-meta p { margin: 3px 0 0; color: #6b7280; font-size: 12px; }

        .section {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 10px;
        }
        .section-title {
            margin: 0 0 8px;
            font-size: 14px;
            color: #34b7ea;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 6px;
        }
        .grid { display: flex; flex-wrap: wrap; }
        .grid .col { flex: 1 1 45%; }
        .item { margin-bottom: 6px; }
        .item .label { font-weight: 600; color: #222; }
        .item .value { color: #6b7280; }

        .status-badge {
            display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 700; color: #fff;
        }
        .status-delivered { background: #22c55e; }
        .status-pending, .status-processing, .status-new, .status-confirmed { background: #34b7ea; }
        .status-cancelled, .status-request_cancel { background: #ef4444; }
        .status-problem { background: #f59e0b; color: #111; }

        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: right; font-size: 12px; }
        thead th { background: #34b7ea; color: #fff; }
        tbody tr:nth-child(even) { background: #f9fafb; }

        .totals { margin-top: 10px; border-top: 2px solid #e5e7eb; padding-top: 8px; }
        .total-row { display: flex; justify-content: space-between; margin-bottom: 6px; }
        .total-row.final { border-top: 1px solid #34b7ea; padding-top: 6px; font-weight: 700; color: #34b7ea; }

        .footer { text-align: center; color: #6b7280; font-size: 11px; margin-top: 14px; border-top: 1px solid #e5e7eb; padding-top: 8px; }
    </style>
</head>
<body>
<div class="container">
    <!-- Header -->
    <div class="header">
        <div class="brand">
            <img src="{{ public_path('website/images/favicon.png') }}" alt="logo">
            <h1 class="brand-title">ليا - فاتورة الطلب</h1>
        </div>
        <div class="invoice-meta">
            <h2>رقم الطلب: {{ $order->order_number }}</h2>
            <p>تاريخ الإنشاء: {{ $order->created_at?->format('Y/m/d - H:i') }}</p>
        </div>
    </div>

    <!-- Order & Status -->
    <div class="section">
        <h3 class="section-title">معلومات الطلب</h3>
        <div class="grid">
            <div class="col">
                <div class="item"><span class="label">حالة الطلب:</span>
                    <span class="value">
                        @php $st = $order->refund_status ?? $order->status; @endphp
                        <span class="status-badge status-{{ $st }}">{{ __('admin.' . $st) }}</span>
                    </span>
                </div>
                @if($order->refund_status)
                <div class="item"><span class="label">حالة الاسترجاع:</span>
                    <span class="value">
                        <span class="status-badge status-{{ $order->refund_status }}">{{ __('admin.' . $order->refund_status) }}</span>
                    </span>
                </div>
                @endif
                <div class="item"><span class="label">نوع الطلب:</span>
                    <span class="value">{{ $order->original_order_id ? 'طلب مرتجع' : 'عادي' }}</span>
                </div>
                <div class="item"><span class="label">نوع التوصيل:</span>
                    <span class="value">
                        {{ $order->delivery_type === 'immediate' ? 'فوري' : ($order->delivery_type === 'schedule' ? 'مجدول' : ($order->delivery_type === 'pickup' ? 'استلام' : 'منزل')) }}
                    </span>
                </div>
            </div>
            <div class="col">
                <div class="item"><span class="label">طريقة الدفع:</span>
                    <span class="value">{{ $order->paymentMethod?->name ?? '-' }}</span>
                </div>
                <div class="item"><span class="label">تاريخ ووقت الدفع:</span>
                    <span class="value">{{ optional($order->payment_date)->format('Y/m/d - H:i') ?? '-' }}</span>
                </div>
                <div class="item"><span class="label">زمن تجهيز الطلب المتوقع:</span>
                    <span class="value">{{ optional($order->branch)->expected_duration ? optional($order->branch)->expected_duration . ' دقيقة' : '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Delivery Info -->
    <div class="section">
        <h3 class="section-title">معلومات التوصيل</h3>
        <div class="grid">
            <div class="col">
                <div class="item"><span class="label">العنوان:</span> <span class="value">{{ $order->address?->address_name ?? $order->address_name ?? '-' }}</span></div>
                <div class="item"><span class="label">اسم المستلم:</span> <span class="value">{{ $order->address?->recipient_name ?? $order->recipient_name ?? $order->reciver_name ?? '-' }}</span></div>
                <div class="item"><span class="label">رقم الجوال:</span> <span class="value">{{ $order->address?->phone ?? $order->recipient_phone ?? $order->reciver_phone ?? '-' }}</span></div>
            </div>
            <div class="col">
                <div class="item"><span class="label">المدينة:</span> <span class="value">{{ optional(optional($order->address)->city)->name ?? optional($order->city)->name ?? '-' }}</span></div>
                <div class="item"><span class="label">الحي:</span> <span class="value">{{ optional(optional($order->address)->district)->getTranslation('name', app()->getLocale()) ?? '-' }}</span></div>
                <div class="item"><span class="label">وصف العنوان:</span> <span class="value">{{ $order->address?->description ?? $order->message ?? '-' }}</span></div>
            </div>
        </div>
    </div>

    <!-- Items -->
    <div class="section">
        <h3 class="section-title">تفاصيل المنتجات</h3>
        <table>
            <thead>
                <tr>
                    <th>الاسم</th>
                    <th>الكمية</th>
                    <th>السعر</th>
                    <th>الإجمالي</th>
                </tr>
            </thead>
            <tbody>
            @foreach($order->items as $item)
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
        @php
            $discount = $order->coupon_amount ?? $order->discount_amount ?? 0;
            $deliveryFee = (float) ($order->delivery_fee ?? 0);
            $subtotalAfterDiscount = (float) $order->subtotal - (float) $discount;
            $beforeVat = $subtotalAfterDiscount + $deliveryFee;
            $vat = (float) ($order->vat_amount ?? 0);
            $afterVat = $beforeVat + $vat;
            $wallet = (float) ($order->wallet_deduction ?? 0);
        @endphp
        <div class="totals">
            <div class="total-row"><span>إجمالي المنتجات بدون ضريبة:</span> <span>{{ number_format($order->subtotal, 2) }} ريال</span></div>
            <div class="total-row"><span>كود الخصم {{ $order->coupon_code ? '(' . $order->coupon_code . ')' : '' }}:</span> <span>-{{ number_format($discount, 2) }} ريال</span></div>
            <div class="total-row"><span>إجمالي المنتجات بعد الخصم:</span> <span>{{ number_format($subtotalAfterDiscount, 2) }} ريال</span></div>
            <div class="total-row"><span>رسوم التوصيل:</span> <span>{{ number_format($deliveryFee, 2) }} ريال</span></div>
            <div class="total-row"><span>إجمالي المستحق بدون ضريبة:</span> <span>{{ number_format($beforeVat, 2) }} ريال</span></div>
            <div class="total-row"><span>ضريبة القيمة المضافة 15%:</span> <span>{{ number_format($vat, 2) }} ريال</span></div>
            <div class="total-row"><span>إجمالي المستحق بعد الضريبة:</span> <span>{{ number_format($afterVat, 2) }} ريال</span></div>
            <div class="total-row"><span>خصم المحفظة:</span> <span>-{{ number_format($wallet, 2) }} ريال</span></div>
            <div class="total-row final"><span>الإجمالي النهائي:</span> <span>{{ number_format($order->total, 2) }} ريال</span></div>
        </div>
    </div>

    <!-- Conditional Notes -->
    @if($order->status === 'cancelled' || $order->status === 'request_cancel')
        <div class="section">
            <h3 class="section-title">سبب الإلغاء</h3>
            <div class="item"><span class="value">{{ $order->cancelReason ? $order->cancelReason->getTranslation('reason', app()->getLocale()) : ($order->notes ?? '-') }}</span></div>
        </div>
    @endif

    @if($order->status === 'problem')
        <div class="section">
            <h3 class="section-title">سبب المشكلة</h3>
            <div class="item"><span class="value">{{ $order->problem?->getTranslation('problem', app()->getLocale()) ?? '-' }}@if(!empty($order->notes))<br>{{ $order->notes }}@endif</span></div>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>تم إنشاء هذه الفاتورة في {{ now()->format('Y-m-d H:i:s') }}</p>
        <p>ليا &mdash; شكراً لتعاملكم معنا</p>
    </div>
</div>
</body>
</html>