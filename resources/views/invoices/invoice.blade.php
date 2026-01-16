<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            direction: rtl;
            text-align: right;
        }
    </style>

    <meta charset="utf-8">
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px 12px; border: 1px solid #ddd; }
        h2 { margin-bottom: 20px; }
    </style>
</head>
<body>
    <h2>Invoice #{{ $order->order_number }}</h2>
    <table>
        <tr>
            <th>Invoice ID</th>
            <td>{{ $order->invoice_number }}</td>
        </tr>
        <tr>
            <th>Total</th>
            <td>{{ number_format($order->total, 2) }}</td>
        </tr>
        <tr>
            <th>Payment Method</th>
            <td>{{ $order->paymentMethod?->getTranslation('name', 'en') ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Payment Reference</th>
            <td>{{ $order->payment_reference ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Payment Due Date</th>
            <td>{{ $order->payment_due_date?->format('Y-m-d') ?? 'N/A' }}</td>
        </tr>
    </table>
</body>
</html>
