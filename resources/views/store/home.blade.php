@extends('layouts.store')

@section('content')
<script>
const siteUrl = '<?php echo SITE_URL; ?>';
const csrfToken = '<?php echo generateCSRFToken(); ?>';
</script>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <span class="hero-badge">&#9889; New Season Collection</span>
            <h1>Discover Premium<br>Products for <span>Every</span> Lifestyle</h1>
            <p>Shop the latest trends in electronics, fashion, home decor and more. Premium quality at unbeatable prices with fast, free delivery.</p>
            <div class="hero-buttons">
                <a href="<?php echo SITE_URL; ?>/pages/products.php" class="btn btn-accent btn-lg">Shop Now &#8594;</a>
                <a href="#categories" class="btn btn-lg" style="background:rgba(255,255,255,.15);color:#fff;backdrop-filter:blur(10px);">Browse Categories</a>
            </div>
            <div class="hero-stats">
                <div class="hero-stat">
                    <div class="stat-number">500+</div>
                    <div class="stat-label">Products</div>
                </div>
                <div class="hero-stat">
                    <div class="stat-number">10K+</div>
                    <div class="stat-label">Customers</div>
                </div>
                <div class="hero-stat">
                    <div class="stat-number">4.9</div>
                    <div class="stat-label">Rating</div>
                </div>
            </div>
        </div>
        <div class="hero-image">
            <img src="<?php echo SITE_URL; ?>/assets/images/hero-product.png" alt="Featured Products" onerror="this.style.display='none'">
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>Featured Products</h2>
            <p>Handpicked items just for you</p>
            <div class="accent-line"></div>
        </div>
        <div class="products-grid">
            <?php foreach ($featured as $product): ?>
            <div class="product-card">
                <div class="product-card-image">
                    <img src="<?php echo productImage($product['image']); ?>" alt="<?php echo e($product['name']); ?>">
                    <?php if ($product['discount_price']): ?>
                        <span class="product-badge badge-sale">
                            -<?php echo round((1 - $product['discount_price'] / $product['price']) * 100); ?>%
                        </span>
                    <?php else: ?>
                        <span class="product-badge badge-featured">Featured</span>
                    <?php endif; ?>
                    <button class="product-wishlist-btn" onclick="addToWishlist(<?php echo $product['id']; ?>)" title="Add to Wishlist">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                    </button>
                </div>
                <div class="product-card-body">
                    <div class="product-category"><?php echo e($product['category_name']); ?></div>
                    <h3><a href="<?php echo SITE_URL; ?>/pages/product.php?id=<?php echo $product['id']; ?>"><?php echo e($product['name']); ?></a></h3>
                    <div class="product-price">
                        <span class="price-current"><?php echo formatPrice($product['discount_price'] ?? $product['price']); ?></span>
                        <?php if ($product['discount_price']): ?>
                            <span class="price-original"><?php echo formatPrice($product['price']); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="product-card-footer">
                    <?php echo renderStars($product['avg_rating'] ?? 0); ?>
                    <button class="btn-add-cart" onclick="addToCart(<?php echo $product['id']; ?>)">Add to Cart</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Categories -->
<section class="section section-alt" id="categories">
    <div class="container">
        <div class="section-header">
            <h2>Shop by Category</h2>
            <p>Find exactly what you're looking for</p>
            <div class="accent-line"></div>
        </div>
        <div class="categories-grid">
            <?php foreach ($categories as $cat): ?>
            <a href="<?php echo SITE_URL; ?>/pages/products.php?category=<?php echo e($cat['slug']); ?>" class="category-card">
                <img src="<?php echo categoryImage($cat['image']); ?>" alt="<?php echo e($cat['name']); ?>">
                <div class="category-overlay">
                    <h3><?php echo e($cat['name']); ?></h3>
                    <p><?php echo e(substr($cat['description'] ?? '', 0, 60)); ?></p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Promo Banners -->
<section class="section">
    <div class="container">
        <div class="promo-grid">
            <div class="promo-card">
                <h3>Free Shipping</h3>
                <p>On all orders over $50. No code needed — applied automatically at checkout.</p>
                <a href="<?php echo SITE_URL; ?>/pages/products.php" class="btn btn-accent btn-sm">Shop Now</a>
            </div>
            <div class="promo-card accent-promo">
                <h3>New Member Discount</h3>
                <p>Sign up today and get 15% off your first order. Limited time offer!</p>
                <a href="<?php echo SITE_URL; ?>/pages/register.php" class="btn btn-sm" style="background:#fff;color:var(--accent);">Join Now</a>
            </div>
        </div>
    </div>
</section>

<!-- New Arrivals -->
<section class="section section-alt">
    <div class="container">
        <div class="section-header">
            <h2>New Arrivals</h2>
            <p>The latest additions to our store</p>
            <div class="accent-line"></div>
        </div>
        <div class="products-grid">
            <?php foreach ($newArrivals as $product): ?>
            <div class="product-card">
                <div class="product-card-image">
                    <img src="<?php echo productImage($product['image']); ?>" alt="<?php echo e($product['name']); ?>">
                    <span class="product-badge badge-new">New</span>
                    <button class="product-wishlist-btn" onclick="addToWishlist(<?php echo $product['id']; ?>)" title="Add to Wishlist">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                    </button>
                </div>
                <div class="product-card-body">
                    <div class="product-category"><?php echo e($product['category_name']); ?></div>
                    <h3><a href="<?php echo SITE_URL; ?>/pages/product.php?id=<?php echo $product['id']; ?>"><?php echo e($product['name']); ?></a></h3>
                    <div class="product-price">
                        <span class="price-current"><?php echo formatPrice($product['discount_price'] ?? $product['price']); ?></span>
                        <?php if ($product['discount_price']): ?>
                            <span class="price-original"><?php echo formatPrice($product['price']); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="product-card-footer">
                    <?php echo renderStars($product['avg_rating'] ?? 0); ?>
                    <button class="btn-add-cart" onclick="addToCart(<?php echo $product['id']; ?>)">Add to Cart</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-3">
            <a href="<?php echo SITE_URL; ?>/pages/products.php" class="btn btn-outline">View All Products &#8594;</a>
        </div>
    </div>
</section>
@endsection
