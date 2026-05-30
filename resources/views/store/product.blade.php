@extends('layouts.store')

@section('content')
<script>
const siteUrl = '<?php echo SITE_URL; ?>';
const csrfToken = '<?php echo generateCSRFToken(); ?>';
</script>

<!-- Breadcrumb -->
<div class="page-header" style="padding:20px 0;">
    <div class="container">
        <div class="breadcrumb">
            <a href="<?php echo SITE_URL; ?>/">Home</a><span>/</span>
            <a href="<?php echo SITE_URL; ?>/pages/products.php">Products</a><span>/</span>
            <a href="<?php echo SITE_URL; ?>/pages/products.php?category=<?php echo e($product['category_slug']); ?>"><?php echo e($product['category_name']); ?></a><span>/</span>
            <span><?php echo e($product['name']); ?></span>
        </div>
    </div>
</div>

<div class="container">
    <div class="product-detail">
        <!-- Gallery -->
        <div class="product-gallery">
            <div class="product-main-image">
                <img src="<?php echo productImage($product['image']); ?>" alt="<?php echo e($product['name']); ?>" id="mainImage">
            </div>
        </div>

        <!-- Info -->
        <div class="product-info">
            <h1><?php echo e($product['name']); ?></h1>

            <div class="product-meta">
                <?php echo renderStars($avgRating); ?>
                <span style="color:var(--text-muted);font-size:.9rem;">(<?php echo count($reviews); ?> reviews)</span>
                <span class="badge badge-active"><?php echo e($product['category_name']); ?></span>
            </div>

            <div class="product-detail-price">
                <span class="price-current"><?php echo formatPrice($product['discount_price'] ?? $product['price']); ?></span>
                <?php if ($product['discount_price']): ?>
                    <span class="price-original"><?php echo formatPrice($product['price']); ?></span>
                    <span class="price-discount">Save <?php echo round((1 - $product['discount_price'] / $product['price']) * 100); ?>%</span>
                <?php endif; ?>
            </div>

            <!-- Stock Status -->
            <?php if ($product['stock_quantity'] > 10): ?>
                <span class="stock-status stock-in">&#10003; In Stock</span>
            <?php elseif ($product['stock_quantity'] > 0): ?>
                <span class="stock-status stock-low">&#9888; Only <?php echo $product['stock_quantity']; ?> left</span>
            <?php else: ?>
                <span class="stock-status stock-out">&#10007; Out of Stock</span>
            <?php endif; ?>

            <!-- Actions -->
            <div class="product-actions">
                <div class="qty-selector">
                    <button data-action="minus">&#8722;</button>
                    <input type="number" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>" id="productQty" data-max="<?php echo $product['stock_quantity']; ?>">
                    <button data-action="plus">+</button>
                </div>
                <button class="btn btn-accent btn-lg" onclick="addToCart(<?php echo $product['id']; ?>, document.getElementById('productQty').value)" <?php echo $product['stock_quantity'] < 1 ? 'disabled' : ''; ?>>
                    Add to Cart
                </button>
                <button class="btn btn-outline" onclick="addToWishlist(<?php echo $product['id']; ?>)">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                </button>
            </div>

            <!-- Tabs -->
            <div class="product-tabs">
                <div class="tab-buttons">
                    <button class="tab-btn active" data-tab="tabDescription">Description</button>
                    <button class="tab-btn" data-tab="tabReviews">Reviews (<?php echo count($reviews); ?>)</button>
                </div>

                <div class="tab-content active" id="tabDescription">
                    <p><?php echo nl2br(e($product['description'])); ?></p>
                </div>

                <div class="tab-content" id="tabReviews">
                    <?php if (empty($reviews)): ?>
                        <p style="color:var(--text-muted);">No reviews yet. Be the first to review this product!</p>
                    <?php else: ?>
                        <?php foreach ($reviews as $review): ?>
                        <div class="review-card">
                            <div class="review-header">
                                <div class="review-avatar"><?php echo strtoupper(substr($review['full_name'], 0, 1)); ?></div>
                                <div>
                                    <div class="review-author"><?php echo e($review['full_name']); ?></div>
                                    <div class="review-date"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></div>
                                </div>
                                <div style="margin-left:auto;"><?php echo renderStars($review['rating']); ?></div>
                            </div>
                            <p><?php echo nl2br(e($review['comment'])); ?></p>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if ($canReview): ?>
                    <div class="review-form">
                        <h3>Write a Review</h3>
                        <form method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                            <div class="star-rating-input">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <label>&#9733;<input type="radio" name="rating" value="<?php echo $i; ?>" <?php echo $i === 5 ? 'checked' : ''; ?>></label>
                                <?php endfor; ?>
                            </div>
                            <div class="form-group">
                                <textarea name="comment" class="form-control" placeholder="Share your experience..." rows="4" required></textarea>
                            </div>
                            <button type="submit" name="submit_review" class="btn btn-accent">Submit Review</button>
                        </form>
                    </div>
                    <?php elseif (isLoggedIn()): ?>
                        <p class="mt-2" style="color:var(--text-muted);font-size:.9rem;">You can review this product after purchasing it.</p>
                    <?php else: ?>
                        <p class="mt-2"><a href="<?php echo SITE_URL; ?>/pages/login.php" class="btn btn-outline btn-sm">Log in to write a review</a></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Related Products -->
<?php if (!empty($related)): ?>
<section class="section section-alt">
    <div class="container">
        <div class="section-header">
            <h2>Related Products</h2>
            <div class="accent-line"></div>
        </div>
        <div class="products-grid">
            <?php foreach ($related as $rp): ?>
            <div class="product-card">
                <div class="product-card-image">
                    <img src="<?php echo productImage($rp['image']); ?>" alt="<?php echo e($rp['name']); ?>">
                </div>
                <div class="product-card-body">
                    <div class="product-category"><?php echo e($rp['category_name']); ?></div>
                    <h3><a href="<?php echo SITE_URL; ?>/pages/product.php?id=<?php echo $rp['id']; ?>"><?php echo e($rp['name']); ?></a></h3>
                    <div class="product-price">
                        <span class="price-current"><?php echo formatPrice($rp['discount_price'] ?? $rp['price']); ?></span>
                        <?php if ($rp['discount_price']): ?>
                            <span class="price-original"><?php echo formatPrice($rp['price']); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="product-card-footer">
                    <?php echo renderStars($rp['avg_rating'] ?? 0); ?>
                    <button class="btn-add-cart" onclick="addToCart(<?php echo $rp['id']; ?>)">Add to Cart</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
@endsection
