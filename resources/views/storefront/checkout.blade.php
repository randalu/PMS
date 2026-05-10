@extends('layouts.storefront')

@section('content')
<section class="section">
    <div class="container split">
        <div>
            <h1>Checkout</h1>
            <form method="post" action="{{ route('checkout.store') }}">
                @csrf
                <div class="form-grid">
                    <div class="field"><label>Name</label><input name="customer_name" value="{{ old('customer_name') }}" required></div>
                    <div class="field"><label>Phone</label><input name="customer_phone" value="{{ old('customer_phone') }}" required></div>
                    <div class="field"><label>Email</label><input name="customer_email" type="email" value="{{ old('customer_email') }}"></div>
                </div>
                <div class="field"><label>Delivery address</label><textarea name="delivery_address" required>{{ old('delivery_address') }}</textarea></div>
                <div class="field"><label>Notes</label><textarea name="customer_notes">{{ old('customer_notes') }}</textarea></div>
                <button class="primary" type="submit">Place COD Order</button>
            </form>
        </div>
        <aside class="card"><div class="card-body">
            <h2>Order Summary</h2>
            @foreach ($cart['items'] as $item)
                <p>{{ $item['quantity'] }} x {{ $item['variant']->product->sku }} {{ $item['variant']->size }} - LKR {{ number_format($item['line_total'], 2) }}<br><span class="muted">2 matching pillow cases included</span></p>
            @endforeach
            <h3>Subtotal: LKR {{ number_format($cart['subtotal'], 2) }}</h3>
            <p class="muted">Delivery fee is confirmed by admin.</p>
        </div></aside>
    </div>
</section>
@endsection
