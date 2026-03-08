@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Products Page</h1>

    <div class="filters">
        <label>Category</label>
        <select>
            <option>A - Z</option>
        </select>

        <label>Brand</label>
        <select>
            <option>Brand A</option>
        </select>

        <label>Price Range</label>
        <input type="text" placeholder="Price Range">
    </div>

    <div class="products">
        @foreach ($products as $product)
            <div class="product">
                <h2>{{ $product->name }}</h2>
                <p>{{ $product->description }}</p>
                <p>Price: ${{ $product->price }}</p>
                <a href="{{ route('product.detail', $product->id) }}">View Details</a>
            </div>
        @endforeach
    </div>
</div>
@endsection