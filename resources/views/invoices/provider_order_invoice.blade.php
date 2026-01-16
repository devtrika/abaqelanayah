<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $sub_order->sub_order_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', 'Arial', sans-serif;
            direction: rtl;
            text-align: right;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .invoice-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 28px;
        }
        .header h2 {
            color: #6c757d;
            margin: 5px 0;
            font-size: 18px;
        }
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .info-box {
            flex: 1;
            margin: 0 10px;
        }
        .info-box h3 {
            color: #007bff;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        .info-item {
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            color: #495057;
        }
        .info-value {
            color: #6c757d;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .items-table th,
        .items-table td {
            border: 1px solid #dee2e6;
            padding: 12px;
            text-align: right;
        }
        .items-table th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        .items-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .totals-section {
            margin-top: 30px;
            border-top: 2px solid #dee2e6;
            padding-top: 20px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
        }
        .total-row.final {
            border-top: 2px solid #007bff;
            font-weight: bold;
            font-size: 18px;
            color: #007bff;
        }
        .total-row.provider-share {
            background-color: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
            border-top: 1px solid #dee2e6;
            padding-top: 20px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
        }
        .status-completed { background-color: #d4edda; color: #155724; }
        .status-processing { background-color: #fff3cd; color: #856404; }
        .status-cancelled { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <h1>فاتورة الطلب</h1>
            <h2>Invoice #{{ $sub_order->sub_order_number }}</h2>
            <p>تاريخ الطلب: {{ $order->created_at->format('Y-m-d H:i') }}</p>
        </div>

        <!-- Information Sections -->
        <div class="info-section">
            <!-- Provider Information -->
            <div class="info-box">
                <h3>معلومات المزود</h3>
                <div class="info-item">
                    <span class="info-label">الاسم التجاري:</span>
                    <span class="info-value">{{ $provider->commercial_name ?? $provider->user->name }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">رقم الهاتف:</span>
                    <span class="info-value">{{ $provider->user->country_code }}{{ $provider->user->phone }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">البريد الإلكتروني:</span>
                    <span class="info-value">{{ $provider->user->email ?? 'غير محدد' }}</span>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="info-box">
                <h3>معلومات العميل</h3>
                <div class="info-item">
                    <span class="info-label">الاسم:</span>
                    <span class="info-value">{{ $customer->name }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">رقم الهاتف:</span>
                    <span class="info-value">{{ $customer->country_code }}{{ $customer->phone }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">البريد الإلكتروني:</span>
                    <span class="info-value">{{ $customer->email ?? 'غير محدد' }}</span>
                </div>
            </div>
        </div>

        <!-- Order Information -->
        <div class="info-section">
            <div class="info-box">
                <h3>معلومات الطلب</h3>
                <div class="info-item">
                    <span class="info-label">رقم الطلب الرئيسي:</span>
                    <span class="info-value">{{ $order->order_number }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">رقم الطلب الفرعي:</span>
                    <span class="info-value">{{ $sub_order->sub_order_number }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">حالة الطلب:</span>
                    <span class="status-badge status-{{ $sub_order->status }}">
                        {{ __('admin.' . $sub_order->status) }}
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">طريقة الدفع:</span>
                    <span class="info-value">{{ $payment_method->name ?? 'غير محدد' }}</span>
                </div>
            </div>

            <!-- Address Information -->
            <div class="info-box">
                <h3>معلومات العنوان</h3>
                @if($address)
                    <div class="info-item">
                        <span class="info-label">العنوان:</span>
                        <span class="info-value">{{ $address->details }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">المدينة:</span>
                        <span class="info-value">{{ $address->city->name ?? 'غير محدد' }}</span>
                    </div>
                @else
                    <div class="info-item">
                        <span class="info-value">لا يوجد عنوان محدد</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Order Items -->
        <h3>المنتجات والخدمات</h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th>الاسم</th>
                    <th>النوع</th>
                    <th>الكمية</th>
                    <th>السعر</th>
                    <th>الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>
                            @if($item->item_type === 'App\Models\Service')
                                خدمة
                            @else
                                منتج
                            @endif
                        </td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->price, 2) }} ريال</td>
                        <td>{{ number_format($item->total, 2) }} ريال</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals-section">
            <div class="total-row">
                <span>إجمالي المنتجات:</span>
                <span>{{ number_format($totals['products_total'], 2) }} ريال</span>
            </div>
            <div class="total-row">
                <span>إجمالي الخدمات:</span>
                <span>{{ number_format($totals['services_total'], 2) }} ريال</span>
            </div>
            <div class="total-row">
                <span>المجموع الفرعي:</span>
                <span>{{ number_format($totals['subtotal'], 2) }} ريال</span>
            </div>
            
            @if($totals['booking_fee'] > 0)
                <div class="total-row">
                    <span>رسوم الحجز:</span>
                    <span>{{ number_format($totals['booking_fee'], 2) }} ريال</span>
                </div>
            @endif
            
            @if($totals['home_service_fee'] > 0)
                <div class="total-row">
                    <span>رسوم الخدمة المنزلية:</span>
                    <span>{{ number_format($totals['home_service_fee'], 2) }} ريال</span>
                </div>
            @endif
            
            @if($totals['delivery_fee'] > 0)
                <div class="total-row">
                    <span>رسوم التوصيل:</span>
                    <span>{{ number_format($totals['delivery_fee'], 2) }} ريال</span>
                </div>
            @endif
            
            @if($totals['discount_amount'] > 0)
                <div class="total-row">
                    <span>الخصم:</span>
                    <span>-{{ number_format($totals['discount_amount'], 2) }} ريال</span>
                </div>
            @endif
            
            <div class="total-row final">
                <span>الإجمالي النهائي:</span>
                <span>{{ number_format($totals['total'], 2) }} ريال</span>
            </div>
            
          
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>تم إنشاء هذه الفاتورة في {{ now()->format('Y-m-d H:i:s') }}</p>
            <p>شكراً لتعاملكم معنا</p>
        </div>
    </div>
</body>
</html> 