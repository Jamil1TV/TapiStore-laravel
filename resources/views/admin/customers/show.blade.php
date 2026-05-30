@extends('layouts.admin')

@section('title', $customer->name.' | Admin')

@section('admin')
<div class="section-heading"><h1>{{ $customer->name }}</h1><a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a></div>
<div class="row g-4">
    <div class="col-lg-7 tool-panel">
        <h2>Orders</h2>
        @foreach($customer->orders as $order)
            <div class="order-row"><span>{{ $order->order_number }} · {{ $order->status }}</span><strong>${{ number_format((float) $order->total, 2) }}</strong></div>
        @endforeach
    </div>
    <div class="col-lg-5 tool-panel">
        <h2>Addresses</h2>
        @foreach($customer->addresses as $address)
            <p><strong>{{ ucfirst($address->type) }}</strong><br>{{ $address->address_line_1 }}, {{ $address->city }}</p>
        @endforeach
    </div>
</div>
@endsection
