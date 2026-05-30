@extends('layouts.app')

@section('title', 'Invoice '.$order->order_number)

@section('content')
<section class="container py-4 invoice">
    <div class="d-flex justify-content-between">
        <div><h1>Invoice</h1><p>{{ $order->order_number }}</p></div>
        <button class="btn btn-outline-dark print-hide" onclick="window.print()"><i data-lucide="printer"></i> Print</button>
    </div>
    <div class="tool-panel table-responsive">
        <table class="table">
            <thead><tr><th>Item</th><th>Qty</th><th>Unit</th><th>Total</th></tr></thead>
            <tbody>@foreach($order->items as $item)<tr><td>{{ $item->product_name }}</td><td>{{ $item->quantity }}</td><td>${{ number_format((float) $item->unit_price, 2) }}</td><td>${{ number_format((float) $item->total, 2) }}</td></tr>@endforeach</tbody>
        </table>
        <h2 class="text-end">${{ number_format((float) $order->total, 2) }}</h2>
    </div>
</section>
@endsection
