@extends('layouts.app')

@section('title', __('messages.cart').' | '.__('messages.app_name'))

@section('content')
<section class="container py-4">
    <h1>{{ __('messages.your_cart') }}</h1>
    <div class="row g-4">
        <div class="col-lg-8">
            @forelse($items as $item)
                <div class="cart-row">
                    <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}">
                    <div class="flex-grow-1">
                        <h2><a href="{{ route('products.show', $item->product) }}">{{ $item->product->name }}</a></h2>
                        <p class="text-secondary mb-1">{{ $item->product->brand?->name }}</p>
                        <strong>${{ number_format((float) $item->product->price, 2) }}</strong>
                    </div>
                    <form action="{{ route('cart.update', $item) }}" method="post" class="quantity-form">
                        @csrf @method('PATCH')
                        <input class="form-control" type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock_quantity }}">
                        <button class="btn icon-btn ghost" type="submit" data-bs-toggle="tooltip" title="{{ __('messages.update') }}"><i data-lucide="refresh-cw"></i></button>
                    </form>
                    <form action="{{ route('cart.destroy', $item) }}" method="post">
                        @csrf @method('DELETE')
                        <button class="btn icon-btn ghost text-danger" data-bs-toggle="tooltip" title="{{ __('messages.remove') }}"><i data-lucide="trash-2"></i></button>
                    </form>
                </div>
            @empty
                <div class="empty-state">{{ __('messages.cart_empty') }}</div>
            @endforelse
        </div>
        <div class="col-lg-4">
            <div class="summary-panel">
                <h2>{{ __('messages.order_summary') }}</h2>
                <div class="summary-line"><span>{{ __('messages.subtotal') }}</span><strong>${{ number_format($subtotal, 2) }}</strong></div>
                <a class="btn btn-brand w-100 mt-3 {{ $items->isEmpty() ? 'disabled' : '' }}" href="{{ route('checkout.index') }}">{{ __('messages.checkout') }}</a>
            </div>
        </div>
    </div>
</section>
@endsection
