@extends('layouts.app')

@section('title', 'Wishlist | Laravel Commerce')

@section('content')
<section class="container py-4">
    <div class="section-heading">
        <h1>Wishlist</h1>
        <a href="{{ route('products.index') }}">Continue shopping</a>
    </div>
    <div class="product-grid">
        @forelse($wishlist as $item)
            @include('partials.product-card', ['product' => $item->product])
        @empty
            <div class="empty-state">No saved products yet.</div>
        @endforelse
    </div>
    <div class="mt-4">{{ $wishlist->links() }}</div>
</section>
@endsection
