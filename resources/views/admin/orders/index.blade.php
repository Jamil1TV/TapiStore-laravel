@extends('layouts.admin')

@section('title', 'Orders | Admin')

@section('admin')
<div class="section-heading"><h1>Orders</h1></div>
<form method="get" class="admin-search"><select class="form-select" name="status" onchange="this.form.submit()"><option value="">All statuses</option>@foreach(['pending','processing','shipped','delivered','cancelled','refunded'] as $status)<option value="{{ $status }}" @selected(request('status')===$status)>{{ ucfirst($status) }}</option>@endforeach</select></form>
<div class="tool-panel table-responsive">
    <table class="table align-middle">
        <thead><tr><th>Order</th><th>Customer</th><th>Status</th><th>Payment</th><th>Total</th><th></th></tr></thead>
        <tbody>
        @foreach($orders as $order)
            <tr>
                <td>{{ $order->order_number }}</td>
                <td>{{ $order->user?->name ?? 'Guest' }}</td>
                <td><span class="badge text-bg-secondary">{{ $order->status }}</span></td>
                <td>{{ $order->payment_status }}</td>
                <td>${{ number_format((float) $order->total, 2) }}</td>
                <td class="text-end"><a class="btn btn-outline-dark btn-sm" href="{{ route('admin.orders.show', $order) }}">Open</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $orders->links() }}
</div>
@endsection
