@extends('layouts.admin')

@section('content')
<h1>Dashboard</h1>
<div class="stats">
    <div class="stat"><span>New orders</span><strong>{{ $newOrders }}</strong></div>
    <div class="stat"><span>Pending delivery</span><strong>{{ $pendingDelivery }}</strong></div>
    <div class="stat"><span>Low stock</span><strong>{{ $lowStock }}</strong></div>
    <div class="stat"><span>Products</span><strong>{{ $products }}</strong></div>
</div>
<h2>Recent Orders</h2>
<div class="table-wrap">
    <table class="table">
        <thead><tr><th>Order</th><th>Customer</th><th>Status</th><th>Total</th><th></th></tr></thead>
        <tbody>
        @foreach ($recentOrders as $order)
            <tr>
                <td>{{ $order->order_number }}</td>
                <td>{{ $order->customer_name }}<br><span class="muted">{{ $order->customer_phone }}</span></td>
                <td><span class="badge">{{ $order->status }}</span></td>
                <td>LKR {{ number_format((float) $order->total, 2) }}</td>
                <td><a class="btn" href="{{ route('admin.orders.show', $order) }}">Open</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
