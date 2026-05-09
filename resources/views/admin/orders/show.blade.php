@extends('layouts.admin')

@section('content')
<h1>{{ $order->order_number }}</h1>
<div class="split">
    <div>
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>SKU</th><th>Product</th><th>Variant</th><th>Qty</th><th>Total</th></tr></thead>
                <tbody>
                @foreach ($order->items as $item)
                    <tr><td>{{ $item->sku }}</td><td>{{ $item->product_name }}</td><td>{{ $item->size }} / {{ $item->color }}</td><td>{{ $item->quantity }}</td><td>LKR {{ number_format((float) $item->line_total, 2) }}</td></tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <h2>Customer</h2>
        <p><strong>{{ $order->customer_name }}</strong><br>{{ $order->customer_phone }}<br>{{ $order->customer_email }}</p>
        <p>{{ $order->delivery_address }}</p>
        <p class="muted">{{ $order->customer_notes }}</p>
    </div>
    <form method="post" action="{{ route('admin.orders.update', $order) }}" class="card"><div class="card-body">
        @csrf @method('PUT')
        <div class="field"><label>Status</label><select name="status">@foreach ($statuses as $status)<option value="{{ $status }}" @selected(old('status', $order->status)===$status)>{{ $status }}</option>@endforeach</select></div>
        <div class="field"><label>Payment status</label><input name="payment_status" value="{{ old('payment_status', $order->payment_status) }}"></div>
        <div class="field"><label>Delivery fee</label><input name="delivery_fee" type="number" step="0.01" min="0" value="{{ old('delivery_fee', $order->delivery_fee) }}"></div>
        <div class="field"><label>Courier</label><input name="courier_name" value="{{ old('courier_name', $order->courier_name) }}"></div>
        <div class="field"><label>Tracking number</label><input name="tracking_number" value="{{ old('tracking_number', $order->tracking_number) }}"></div>
        <div class="field"><label>Delivery notes</label><textarea name="delivery_notes">{{ old('delivery_notes', $order->delivery_notes) }}</textarea></div>
        <p>Subtotal: LKR {{ number_format((float) $order->subtotal, 2) }}</p>
        <p>Total: LKR {{ number_format((float) $order->total, 2) }}</p>
        <button class="primary" type="submit">Update Order</button>
    </div></form>
</div>
@endsection
