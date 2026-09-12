<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmation</title>
</head>
<body>
    <h1>Hi {{ $order->user->name }},</h1>
    <p>Thank you for your order! We are processing it right now.</p>
    
    <h3>Order Summary:</h3>
    <ul>
        <li>Order ID: #{{ $order->id }}</li>
        <li>Total Price: ${{ number_format($order->total_price, 2) }}</li>
        <li>Status: {{ ucfirst($order->status) }}</li>
    </ul>
</body>
</html>