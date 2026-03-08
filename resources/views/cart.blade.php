@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Shopping Cart</h1>

    <div class="cart-items">
        <div>
            <p>Brake Pads</p>
            <p>$50</p>
            <button>Remove</button>
        </div>
        <div>
            <p>Oil Filter</p>
            <p>$10</p>
            <button>Remove</button>
        </div>
    </div>

    <div class="cart-summary">
        <p>Subtotal: $60</p>
        <button>Continue Shopping</button>
        <button>Checkout</button>
    </div>
</div>
@endsection