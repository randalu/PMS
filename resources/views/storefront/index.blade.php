@extends('layouts.storefront')

@section('content')
<section class="hero">
    <div class="container hero-grid">
        <div>
            <h1>Order bedsheet sets from Priyanthi Multi Stores</h1>
            <p>Browse collections, choose a size, send a WhatsApp inquiry, or place an online order for admin confirmation and delivery.</p>
            <div class="actions">
                <a class="btn primary" href="#products">Shop Bedsheets</a>
                <a class="btn" href="{{ route('cart.show') }}">View Cart</a>
            </div>
        </div>
        <div class="hero-image"><img src="{{ asset('images/24084.png') }}" alt="Priyanthi Multi Stores bedsheet set"></div>
    </div>
</section>
<section class="section" id="products">
    <div class="container">
        <div class="toolbar">
            <form method="get"><input class="search" name="s" value="{{ $search }}" placeholder="Search by SKU, name, or collection"></form>
            <div class="filters">
                <a class="filter" href="{{ route('home') }}">All</a>
                @foreach ($categories as $category)
                    <a class="filter" href="{{ route('collections.show', $category) }}">{{ $category->name }}</a>
                @endforeach
            </div>
        </div>
        <div class="grid">
            @foreach ($products as $product)
                @include('storefront.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endsection
