@extends('layouts.app')

@section('title', 'Order Confirmed | Laravel Commerce')

@section('content')
<section class="auth-shell">
    <div class="auth-panel wide">
        <i data-lucide="circle-check" class="success-icon"></i>
        <h1>Order confirmed</h1>
        <p class="text-secondary">Order {{ $order->order_number }} total: ${{ number_format((float) $order->total, 2) }}</p>
        <a class="btn btn-brand" href="{{ route('dashboard') }}">View dashboard</a>
        <a class="btn btn-outline-dark" href="{{ route('products.index') }}">Continue shopping</a>
    </div>
</section>
@endsection
