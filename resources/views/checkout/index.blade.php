<!-- Checkout Page -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Checkout</h1>
    <form method="POST" action="{{ route('checkout.process') }}">
        @csrf
        <div>
            <label for="address">Shipping Address:</label>
            <input type="text" name="address" id="address" required>
        </div>
        <div>
            <label for="discount_code">Discount Code:</label>
            <input type="text" name="discount_code" id="discount_code">
        </div>
        <button type="submit">Place Order</button>
    </form>
</div>
@endsection