@extends('layouts.store')

@section('content')
<script>const siteUrl = '<?php echo SITE_URL; ?>'; const csrfToken = '<?php echo generateCSRFToken(); ?>';</script>

<div class="page-header">
    <div class="container">
        <h1>Search Results</h1>
        <div class="breadcrumb"><a href="<?php echo SITE_URL; ?>/">Home</a><span>/</span><span>Search: "<?php echo e($query); ?>"</span></div>
    </div>
</div>

<div class="container" style="padding:40px 20px;">
    <?php if (!$query): ?>
        <div class="empty-cart">
            <h2>Enter a search term</h2>
            <p>Use the search bar above to find products.</p>
        </div>
    <?php elseif (empty($products)): ?>
        <div class="empty-cart">
            <h2>No results found</h2>
            <p>No products match "<?php echo e($query); ?>". Try different keywords.</p>
            <a href="<?php echo SITE_URL; ?>/pages/products.php" class="btn btn-accent">Browse All Products</a>
        </div>
    <?php else: ?>
        <p style="color:var(--text-light);margin-bottom:20px;"><?php echo $totalProducts; ?> result(s) found for "<?php echo e($query); ?>"</p>
        <div class="products-grid">
            <?php foreach ($products as $product): ?>
            <div class="product-card">
                <div class="product-card-image">
                    <img src="<?php echo productImage($product['image']); ?>" alt="<?php echo e($product['name']); ?>">
                    <?php if ($product['discount_price']): ?>
                        <span class="product-badge badge-sale">-<?php echo round((1 - $product['discount_price'] / $product['price']) * 100); ?>%</span>
                    <?php endif; ?>
                </div>
                <div class="product-card-body">
                    <div class="product-category"><?php echo e($product['category_name']); ?></div>
                    <h3><a href="<?php echo SITE_URL; ?>/pages/product.php?id=<?php echo $product['id']; ?>"><?php echo e($product['name']); ?></a></h3>
                    <div class="product-price">
                        <span class="price-current"><?php echo formatPrice($product['discount_price'] ?? $product['price']); ?></span>
                        <?php if ($product['discount_price']): ?><span class="price-original"><?php echo formatPrice($product['price']); ?></span><?php endif; ?>
                    </div>
                </div>
                <div class="product-card-footer">
                    <?php echo renderStars($product['avg_rating'] ?? 0); ?>
                    <button class="btn-add-cart" onclick="addToCart(<?php echo $product['id']; ?>)">Add to Cart</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?q=<?php echo urlencode($query); ?>&page=<?php echo $i; ?>" class="<?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
@endsection
