<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Detail</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <h1>{{ $product->name }}</h1>
    <p>{{ $product->description }}</p>
    <p>Price: ${{ $product->price }}</p>
    <p>Discount: {{ $product->discount ? $product->discount->percentage . '%' : 'No discount available' }}</p>
    <form action="{{ route('cart.add', $product->id) }}" method="POST">
        @csrf
        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" id="quantity" value="1" min="1">
        <button type="submit">Add to Cart</button>
    </form>
</body>
</html>