<article class="product-card h-100">
    <a href="{{ route('products.show', $product) }}" class="product-media">
        <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
        @if($product->discount_percent)
            <span class="product-badge">{{ $product->discount_percent }}% {{ __('messages.off') }}</span>
        @endif
    </a>
    <div class="product-card-body">
        <div class="d-flex justify-content-between gap-2 small text-secondary">
            <span>{{ $product->brand?->name ?? __('messages.independent') }}</span>
            <span><i data-lucide="star" class="tiny-icon fill-warning"></i> {{ number_format((float) $product->rating, 1) }}</span>
        </div>
        <h2 class="product-title"><a href="{{ route('products.show', $product) }}">{{ $product->name }}</a></h2>
        <p class="text-secondary small mb-2">{{ Str::limit($product->summary, 72) }}</p>
        <div class="d-flex align-items-center justify-content-between gap-2 mt-auto">
            <div>
                <span class="price">${{ number_format((float) $product->price, 2) }}</span>
                @if($product->compare_at_price)
                    <span class="compare">${{ number_format((float) $product->compare_at_price, 2) }}</span>
                @endif
            </div>
            <form action="{{ route('cart.store', $product) }}" method="post" class="ajax-cart-form">
                @csrf
                <input type="hidden" name="quantity" value="1">
                <button class="btn icon-btn btn-brand" type="submit" data-bs-toggle="tooltip" title="{{ __('messages.add_to_cart') }}" @disabled(! $product->inStock())>
                    <i data-lucide="shopping-cart"></i>
                </button>
            </form>
        </div>
    </div>
</article>
