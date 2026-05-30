@extends('layouts.store')

@section('content')
<script>
const siteUrl = '<?php echo SITE_URL; ?>';
const csrfToken = '<?php echo generateCSRFToken(); ?>';
</script>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h1><?php echo $search ? 'Search Results' : ($categorySlug ? e(ucfirst(str_replace('-', ' ', $categorySlug))) : 'All Products'); ?></h1>
        <div class="breadcrumb">
            <a href="<?php echo SITE_URL; ?>/">Home</a>
            <span>/</span>
            <span>Products</span>
            <?php if ($categorySlug): ?>
                <span>/</span>
                <span><?php echo e(ucfirst(str_replace('-', ' ', $categorySlug))); ?></span>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="container">
    <div class="products-layout">
        <!-- Sidebar Filters -->
        <aside class="filters-sidebar">
            <form method="GET" action="">
                <?php if ($search): ?>
                    <input type="hidden" name="q" value="<?php echo e($search); ?>">
                <?php endif; ?>

                <!-- Categories -->
                <div class="filter-group">
                    <h3>Categories</h3>
                    <?php foreach ($allCategories as $cat): ?>
                    <label class="filter-option">
                        <input type="checkbox" name="category" value="<?php echo e($cat['slug']); ?>"
                            <?php echo $categorySlug === $cat['slug'] ? 'checked' : ''; ?>
                            onchange="this.form.submit()">
                        <?php echo e($cat['name']); ?> (<?php echo $cat['product_count']; ?>)
                    </label>
                    <?php endforeach; ?>
                </div>

                <!-- Price Range -->
                <div class="filter-group">
                    <h3>Price Range</h3>
                    <div class="price-range">
                        <input type="range" name="price_max" min="0" max="500" step="10"
                            value="<?php echo $priceMax ?: 500; ?>"
                            oninput="updatePriceLabel(this.value)" onchange="this.form.submit()">
                        <div class="price-values">
                            <span>$0</span>
                            <span id="priceMaxLabel">$<?php echo $priceMax ?: 500; ?></span>
                        </div>
                    </div>
                </div>

                <!-- Rating -->
                <div class="filter-group">
                    <h3>Minimum Rating</h3>
                    <?php for ($r = 4; $r >= 1; $r--): ?>
                    <label class="filter-option">
                        <input type="radio" name="rating" value="<?php echo $r; ?>"
                            <?php echo $ratingMin === $r ? 'checked' : ''; ?>
                            onchange="this.form.submit()">
                        <?php echo str_repeat('&#9733;', $r) . str_repeat('&#9734;', 5 - $r); ?> & up
                    </label>
                    <?php endfor; ?>
                    <label class="filter-option">
                        <input type="radio" name="rating" value="0" <?php echo $ratingMin === 0 ? 'checked' : ''; ?> onchange="this.form.submit()">
                        All ratings
                    </label>
                </div>
            </form>
        </aside>

        <!-- Products -->
        <div>
            <!-- Sort Bar -->
            <div class="sort-bar">
                <span>Showing <?php echo count($products); ?> of <?php echo $totalProducts; ?> products</span>
                <div>
                    <label>Sort by: </label>
                    <select onchange="window.location.href=updateUrlParam('sort', this.value)">
                        <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest</option>
                        <option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>Price: High to Low</option>
                        <option value="popular" <?php echo $sort === 'popular' ? 'selected' : ''; ?>>Most Popular</option>
                    </select>
                </div>
            </div>

            <?php if (empty($products)): ?>
                <div class="empty-cart">
                    <h2>No products found</h2>
                    <p>Try adjusting your filters or search terms.</p>
                    <a href="<?php echo SITE_URL; ?>/pages/products.php" class="btn btn-accent">View All Products</a>
                </div>
            <?php else: ?>
                <div class="products-grid">
                    <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-card-image">
                            <img src="<?php echo productImage($product['image']); ?>" alt="<?php echo e($product['name']); ?>">
                            <?php if ($product['discount_price']): ?>
                                <span class="product-badge badge-sale">-<?php echo round((1 - $product['discount_price'] / $product['price']) * 100); ?>%</span>
                            <?php endif; ?>
                            <button class="product-wishlist-btn" onclick="addToWishlist(<?php echo $product['id']; ?>)">
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

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">&laquo;</a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php if ($i === $page): ?>
                            <span class="active"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    <?php if ($page < $totalPages): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">&raquo;</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function updateUrlParam(key, value) {
    const url = new URL(window.location);
    url.searchParams.set(key, value);
    url.searchParams.delete('page');
    return url.toString();
}
</script>
@endsection
