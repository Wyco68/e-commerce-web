<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Summary</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <h1>Order Summary</h1>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>${{ $item->price }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>${{ $item->price * $item->quantity }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <h2>Order Total: ${{ $order->total }}</h2>
</body>
</html>