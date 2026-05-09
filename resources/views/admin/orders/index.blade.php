@extends('layouts.admin')

@section('content')
<div class="toolbar">
    <h1>Orders</h1>
    <form method="get" class="actions">
        <input class="search" name="q" value="{{ request('q') }}" placeholder="Search order, customer, phone">
        <select name="status"><option value="">All statuses</option>@foreach ($statuses as $status)<option value="{{ $status }}" @selected(request('status')===$status)>{{ $status }}</option>@endforeach</select>
        <button type="submit">Filter</button>
    </form>
</div>
<div class="table-wrap">
<table class="table">
    <thead><tr><th>Order</th><th>Customer</th><th>Status</th><th>Payment</th><th>Total</th><th></th></tr></thead>
    <tbody>
    @foreach ($orders as $order)
        <tr>
            <td>{{ $order->order_number }}<br><span class="muted">{{ $order->created_at->format('Y-m-d H:i') }}</span></td>
            <td>{{ $order->customer_name }}<br><span class="muted">{{ $order->customer_phone }}</span></td>
            <td><span class="badge">{{ $order->status }}</span></td>
            <td>{{ $order->payment_status }}</td>
            <td>LKR {{ number_format((float) $order->total, 2) }}</td>
            <td><a class="btn" href="{{ route('admin.orders.show', $order) }}">Open</a></td>
        </tr>
    @endforeach
    </tbody>
</table>
</div>
{{ $orders->links() }}
@endsection
