@extends('layouts.admin')

@section('title', 'Order '.$order->order_number.' | Admin')

@section('admin')
<div class="section-heading">
    <h1>{{ $order->order_number }}</h1>
    <a class="btn btn-outline-dark" href="{{ route('admin.orders.invoice', $order) }}"><i data-lucide="printer"></i> Invoice</a>
</div>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="tool-panel table-responsive">
            <table class="table align-middle">
                <thead><tr><th>Item</th><th>Qty</th><th>Total</th></tr></thead>
                <tbody>@foreach($order->items as $item)<tr><td>{{ $item->product_name }}<br><small>{{ $item->sku }}</small></td><td>{{ $item->quantity }}</td><td>${{ number_format((float) $item->total, 2) }}</td></tr>@endforeach</tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-4">
        <form class="tool-panel stacked-form" method="post" action="{{ route('admin.orders.update', $order) }}">
            @csrf @method('PATCH')
            <h2>Status</h2>
            <select class="form-select" name="status">@foreach(['pending','processing','shipped','delivered','cancelled','refunded'] as $status)<option value="{{ $status }}" @selected($order->status===$status)>{{ ucfirst($status) }}</option>@endforeach</select>
            <select class="form-select" name="payment_status">@foreach(['unpaid','pending','paid','refunded','failed'] as $status)<option value="{{ $status }}" @selected($order->payment_status===$status)>{{ ucfirst($status) }}</option>@endforeach</select>
            <button class="btn btn-brand" type="submit">Update order</button>
            <hr>
            <div class="summary-line"><span>Subtotal</span><strong>${{ number_format((float) $order->subtotal, 2) }}</strong></div>
            <div class="summary-line"><span>Tax</span><strong>${{ number_format((float) $order->tax_amount, 2) }}</strong></div>
            <div class="summary-line"><span>Shipping</span><strong>${{ number_format((float) $order->shipping_amount, 2) }}</strong></div>
            <div class="summary-line"><span>Discount</span><strong>-${{ number_format((float) $order->discount_amount, 2) }}</strong></div>
            <div class="summary-line total"><span>Total</span><strong>${{ number_format((float) $order->total, 2) }}</strong></div>
        </form>
    </div>
</div>
@endsection
