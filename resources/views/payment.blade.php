<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Payment</title>
</head>

<body>
    <h1>Payment Page</h1>
    <p>Order ID: {{ $order->order_id }}</p>
    <p>Total Amount: Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
</body>

</html>