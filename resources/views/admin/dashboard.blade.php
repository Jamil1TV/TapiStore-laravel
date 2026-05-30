@extends('layouts.admin')

@section('title', 'Admin Dashboard | Laravel Commerce')

@section('admin')
<div class="section-heading">
    <div>
        <p class="eyebrow text-brand">Overview</p>
        <h1>Dashboard</h1>
    </div>
</div>
<div class="admin-stats">
    <div class="stat"><span>Revenue</span><strong>${{ number_format((float) $revenue, 2) }}</strong></div>
    <div class="stat"><span>Orders</span><strong>{{ $ordersCount }}</strong></div>
    <div class="stat"><span>Customers</span><strong>{{ $customersCount }}</strong></div>
    <div class="stat"><span>Low stock</span><strong>{{ $lowStockCount }}</strong></div>
</div>
<div class="row g-4 mt-1">
    <div class="col-lg-7">
        <div class="tool-panel">
            <h2>Latest orders</h2>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Order</th><th>Customer</th><th>Status</th><th>Total</th></tr></thead>
                    <tbody>
                    @foreach($latestOrders as $order)
                        <tr>
                            <td><a href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }}</a></td>
                            <td>{{ $order->user?->name ?? 'Guest' }}</td>
                            <td><span class="badge text-bg-secondary">{{ $order->status }}</span></td>
                            <td>${{ number_format((float) $order->total, 2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="tool-panel">
            <h2>Pending reviews</h2>
            @forelse($pendingReviews as $review)
                <div class="review-row">
                    <strong>{{ $review->product->name }}</strong>
                    <p>{{ Str::limit($review->body, 100) }}</p>
                </div>
            @empty
                <p class="text-secondary mb-0">No pending reviews.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
