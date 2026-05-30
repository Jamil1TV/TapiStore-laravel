@extends('layouts.store')

@section('content')
<script>const siteUrl = '<?php echo SITE_URL; ?>'; const csrfToken = '<?php echo generateCSRFToken(); ?>';</script>

<div class="page-header">
    <div class="container">
        <h1>My Wishlist</h1>
        <div class="breadcrumb"><a href="<?php echo SITE_URL; ?>/">Home</a><span>/</span><span>Wishlist</span></div>
    </div>
</div>

<div class="container">
    <?php if (empty($wishlistItems)): ?>
        <div class="empty-cart">
            <h2>Your wishlist is empty</h2>
            <p>Save items you love to your wishlist and come back to them later.</p>
            <a href="<?php echo SITE_URL; ?>/pages/products.php" class="btn btn-accent">Browse Products</a>
        </div>
    <?php else: ?>
        <div class="wishlist-grid">
            <?php foreach ($wishlistItems as $item): ?>
            <div class="product-card" data-wishlist-id="<?php echo $item['product_id']; ?>">
                <div class="product-card-image">
                    <img src="<?php echo productImage($item['image']); ?>" alt="<?php echo e($item['name']); ?>">
                    <button class="product-wishlist-btn" onclick="removeFromWishlist(<?php echo $item['product_id']; ?>)" title="Remove" style="background:var(--danger);color:#fff;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                    </button>
                </div>
                <div class="product-card-body">
                    <div class="product-category"><?php echo e($item['category_name']); ?></div>
                    <h3><a href="<?php echo SITE_URL; ?>/pages/product.php?id=<?php echo $item['product_id']; ?>"><?php echo e($item['name']); ?></a></h3>
                    <div class="product-price">
                        <span class="price-current"><?php echo formatPrice($item['discount_price'] ?? $item['price']); ?></span>
                        <?php if ($item['discount_price']): ?>
                            <span class="price-original"><?php echo formatPrice($item['price']); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="product-card-footer">
                    <?php echo renderStars($item['avg_rating'] ?? 0); ?>
                    <button class="btn-add-cart" onclick="addToCart(<?php echo $item['product_id']; ?>)">Add to Cart</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
@endsection
