@extends('layouts.app')

@section('title', 'Checkout | Laravel Commerce')

@section('content')
<section class="container py-4">
    <h1>{{ __('messages.checkout') }}</h1>
    <form action="{{ route('checkout.store') }}" method="post" class="row g-4">
        @csrf
        <div class="col-lg-7">
            <div class="tool-panel mb-4">
                <h2>{{ __('messages.billing_address') }}</h2>
                @include('checkout.partials.address-fields', ['prefix' => 'billing'])
            </div>
            <div class="tool-panel mb-4">
                <h2>{{ __('messages.shipping_address') }}</h2>
                @include('checkout.partials.address-fields', ['prefix' => 'shipping'])
            </div>
            <div class="tool-panel">
                <h2>{{ __('messages.payment') }}</h2>
                <div class="payment-options">
                    <label><input type="radio" name="payment_method" value="cod" checked> {{ __('messages.cod') }}</label>
                    <label><input type="radio" name="payment_method" value="stripe"> {{ __('messages.stripe') }}</label>
                    <label><input type="radio" name="payment_method" value="paypal"> {{ __('messages.paypal') }}</label>
                </div>
                <label class="mt-3 d-block">{{ __('messages.notes') }}<textarea class="form-control" name="notes" rows="3"></textarea></label>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="summary-panel sticky-lg-top checkout-summary">
                <h2>{{ __('messages.order_summary') }}</h2>
                @foreach($items as $item)
                    <div class="summary-product"><span>{{ $item->product->name }} × {{ $item->quantity }}</span><strong>${{ number_format($item->line_total, 2) }}</strong></div>
                @endforeach
                <hr>
                <div class="summary-line"><span>{{ __('messages.subtotal') }}</span><strong>${{ number_format($totals['subtotal'], 2) }}</strong></div>
                <div class="summary-line"><span>{{ __('messages.discount') }}</span><strong>-${{ number_format($totals['discount'], 2) }}</strong></div>
                <div class="summary-line"><span>{{ __('messages.tax') }}</span><strong>${{ number_format($totals['tax'], 2) }}</strong></div>
                <div class="summary-line"><span>{{ __('messages.shipping') }}</span><strong>${{ number_format($totals['shipping'], 2) }}</strong></div>
                <div class="summary-line total"><span>{{ __('messages.total') }}</span><strong>${{ number_format($totals['total'], 2) }}</strong></div>
                <button class="btn btn-brand w-100 mt-3" type="submit">{{ __('messages.place_order') }}</button>
            </div>
        </div>
    </form>
    <div class="coupon-box mt-4">
        <form action="{{ route('checkout.coupon.apply') }}" method="post" class="d-flex gap-2">
            @csrf
            <input class="form-control" name="code" placeholder="{{ __('messages.coupon_code') }}" value="{{ $coupon?->code }}">
            <button class="btn btn-outline-dark" type="submit">{{ __('messages.apply') }}</button>
        </form>
        @if($coupon)
            <form action="{{ route('checkout.coupon.remove') }}" method="post" class="mt-2">
                @csrf @method('DELETE')
                <button class="btn btn-link p-0" type="submit">{{ __('messages.remove_coupon', ['code' => $coupon->code]) }}</button>
            </form>
        @endif
    </div>
</section>
@endsection
