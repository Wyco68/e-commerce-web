@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Find Parts for Your Car</h1>

    <form method="GET" action="{{ route('parts.search') }}">
        <label>Brand:</label>
        <select name="brand">
            <option>Tovola</option>
        </select>

        <label>Model:</label>
        <select name="model">
            <option>Camry</option>
        </select>

        <label>Year:</label>
        <select name="year">
            <option>2030</option>
        </select>

        <button type="submit">Search Parts</button>
    </form>

    <h2>Compatible Parts</h2>
    <div class="compatible-parts">
        <div>Brake Pads</div>
        <div>Oil Filter</div>
        <div>Air Filter</div>
        <div>Spark Plugs</div>
    </div>
</div>
@endsection