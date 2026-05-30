@extends('layouts.app')

@section('title', $product->meta_title ?: $product->name)
@section('meta_description', $product->meta_description ?: $product->summary)

@section('content')
<section class="container py-4">
    <div class="product-detail">
        <div class="gallery">
            <img class="gallery-main" src="{{ $product->image_url }}" alt="{{ $product->name }}">
            <div class="gallery-thumbs">
                @foreach($product->images as $image)
                    <img src="{{ $image->image_url }}" alt="{{ $image->alt_text ?: $product->name }}">
                @endforeach
            </div>
        </div>
        <div class="detail-panel">
            <p class="eyebrow text-brand">{{ $product->category->name }} {{ $product->brand ? ' / '.$product->brand->name : '' }}</p>
            <h1>{{ $product->name }}</h1>
            <div class="rating-row"><i data-lucide="star" class="fill-warning"></i> {{ number_format((float) $product->rating, 1) }} ({{ $product->reviews_count }} reviews)</div>
            <p class="lead text-secondary">{{ $product->summary }}</p>
            <div class="price-row">
                <span class="detail-price">${{ number_format((float) $product->price, 2) }}</span>
                @if($product->compare_at_price)
                    <span class="compare">${{ number_format((float) $product->compare_at_price, 2) }}</span>
                @endif
            </div>
            <p class="{{ $product->inStock() ? 'text-success' : 'text-danger' }}">{{ $product->inStock() ? $product->stock_quantity.' in stock' : 'Out of stock' }}</p>
            <form action="{{ route('cart.store', $product) }}" method="post" class="ajax-cart-form detail-actions">
                @csrf
                <input class="form-control qty-input" type="number" name="quantity" value="1" min="1" max="{{ max(1, $product->stock_quantity) }}">
                <button class="btn btn-brand btn-lg" type="submit" @disabled(! $product->inStock())><i data-lucide="shopping-cart"></i> Add to cart</button>
            </form>
            @auth
                <form action="{{ route('wishlist.toggle', $product) }}" method="post">
                    @csrf
                    <button class="btn btn-outline-dark mt-2" type="submit"><i data-lucide="heart"></i> Wishlist</button>
                </form>
            @endauth
            <hr>
            <div class="prose">{!! nl2br(e($product->description)) !!}</div>
        </div>
    </div>
</section>

<section class="container py-4">
    <div class="row g-4">
        <div class="col-lg-7">
            <h2>Reviews</h2>
            @forelse($product->approvedReviews as $review)
                <div class="review-row">
                    <strong>{{ $review->title ?: 'Customer review' }}</strong>
                    <span>{{ str_repeat('★', $review->rating) }}</span>
                    <p>{{ $review->body }}</p>
                    <small class="text-secondary">{{ $review->user->name }}</small>
                </div>
            @empty
                <p class="text-secondary">No approved reviews yet.</p>
            @endforelse
        </div>
        @auth
            <div class="col-lg-5">
                <div class="tool-panel">
                    <h3>Leave a review</h3>
                    <form method="post" action="{{ route('reviews.store', $product) }}" class="stacked-form">
                        @csrf
                        <label>Rating<select class="form-select" name="rating">@for($i=5;$i>=1;$i--)<option value="{{ $i }}">{{ $i }}</option>@endfor</select></label>
                        <label>Title<input class="form-control" name="title"></label>
                        <label>Review<textarea class="form-control" name="body" rows="4" required></textarea></label>
                        <button class="btn btn-brand" type="submit">Submit review</button>
                    </form>
                </div>
            </div>
        @endauth
    </div>
</section>

@if($relatedProducts->isNotEmpty())
<section class="container py-5">
    <div class="section-heading"><h2>Related products</h2></div>
    <div class="product-grid compact">
        @foreach($relatedProducts as $product)
            @include('partials.product-card', ['product' => $product])
        @endforeach
    </div>
</section>
@endif
@endsection
