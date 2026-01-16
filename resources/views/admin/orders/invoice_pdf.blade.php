<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $order->order_number ?? $order->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .section { margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background: #f2f2f2; }
        .totals { text-align: right; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Invoice</h2>
        <h4>#{{ $order->order_number ?? $order->id }}</h4>
        <div>{{ $order->created_at->format('Y-m-d H:i') }}</div>
    </div>

    <div class="section">
        <strong>User:</strong> {{ optional($order->user)->name }}<br>
        <strong>Phone:</strong> {{ optional($order->user)->phone }}<br>
        <strong>Provider:</strong> {{ optional($order->provider)->commercial_name ?? '-' }}<br>
        <strong>Address:</strong> {{ optional($order->address)->address ?? '-' }}
    </div>

    <div class="section">
        <table class="table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Type</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ class_basename($item->item_type) }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->price, 2) }}</td>
                        <td>{{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="totals">
        <div><strong>Subtotal:</strong> {{ number_format($order->subtotal, 2) }}</div>
        <div><strong>Discount:</strong> {{ number_format($order->discount_amount, 2) }}</div>
        <div><strong>Delivery Fee:</strong> {{ number_format($order->delivery_fee, 2) }}</div>
        <div><strong>Total:</strong> {{ number_format($order->total, 2) }}</div>
    </div>

</body>
</html> 