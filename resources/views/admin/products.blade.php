<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pageTitle); ?> | <?php echo SITE_NAME; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
</head>
<body>
<div class="admin-layout">
    @include('admin.sidebar')
    <div class="admin-main">
        <?php $flash = getFlash(); if ($flash): ?>
            <div class="flash-message flash-<?php echo $flash['type']; ?>" id="flashMessage">
                <span><?php echo e($flash['message']); ?></span>
                <button class="flash-close" onclick="this.parentElement.remove()">&times;</button>
            </div>
        <?php endif; ?>

        <div class="admin-header">
            <h1>Products</h1>
            <a href="?add=1" class="btn btn-accent">+ Add Product</a>
        </div>

        <?php if ($showForm): ?>
        <!-- Add/Edit Form -->
        <div class="admin-card">
            <h2 style="margin-bottom:20px;"><?php echo $editProduct ? 'Edit Product' : 'Add New Product'; ?></h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <input type="hidden" name="action" value="<?php echo $editProduct ? 'edit' : 'add'; ?>">
                <?php if ($editProduct): ?><input type="hidden" name="id" value="<?php echo $editProduct['id']; ?>"><?php endif; ?>
                <input type="hidden" name="existing_image" value="<?php echo e($editProduct['image'] ?? ''); ?>">

                <div class="form-row">
                    <div class="form-group">
                        <label>Product Name *</label>
                        <input type="text" name="name" class="form-control" value="<?php echo e($editProduct['name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Category *</label>
                        <select name="category_id" class="form-control" required>
                            <option value="">Select category</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo ($editProduct['category_id'] ?? '') == $cat['id'] ? 'selected' : ''; ?>><?php echo e($cat['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="4"><?php echo e($editProduct['description'] ?? ''); ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Price *</label>
                        <input type="number" name="price" class="form-control" step="0.01" value="<?php echo $editProduct['price'] ?? ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Discount Price</label>
                        <input type="number" name="discount_price" class="form-control" step="0.01" value="<?php echo $editProduct['discount_price'] ?? ''; ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Stock Quantity *</label>
                        <input type="number" name="stock_quantity" class="form-control" value="<?php echo $editProduct['stock_quantity'] ?? 0; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Product Image</label>
                        <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/webp">
                        <?php if (!empty($editProduct['image'])): ?>
                            <p style="font-size:.8rem;color:var(--text-muted);margin-top:4px;">Current: <?php echo e($editProduct['image']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="active" <?php echo ($editProduct['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo ($editProduct['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                    <div class="form-group" style="padding-top:28px;">
                        <label class="form-check">
                            <input type="checkbox" name="is_featured" <?php echo ($editProduct['is_featured'] ?? 0) ? 'checked' : ''; ?>>
                            Featured Product
                        </label>
                    </div>
                </div>

                <div style="display:flex;gap:8px;">
                    <button type="submit" class="btn btn-accent"><?php echo $editProduct ? 'Update Product' : 'Add Product'; ?></button>
                    <a href="<?php echo SITE_URL; ?>/admin/products.php" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <!-- Search/Filter -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h2>All Products (<?php echo count($products); ?>)</h2>
                <form class="admin-search" method="GET">
                    <input type="text" name="search" placeholder="Search products..." value="<?php echo e($search); ?>">
                    <select name="category" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo e($cat['slug']); ?>" <?php echo $catFilter === $cat['slug'] ? 'selected' : ''; ?>><?php echo e($cat['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary">Search</button>
                </form>
            </div>
            <div class="table-responsive">
                <table>
                    <thead><tr><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($products as $p): ?>
                    <tr>
                        <td><img src="<?php echo productImage($p['image']); ?>" class="table-img" alt=""></td>
                        <td>
                            <strong><?php echo e($p['name']); ?></strong>
                            <?php if ($p['is_featured']): ?><span class="badge badge-pending" style="margin-left:4px;">Featured</span><?php endif; ?>
                        </td>
                        <td><?php echo e($p['category_name']); ?></td>
                        <td>
                            <?php echo formatPrice($p['discount_price'] ?? $p['price']); ?>
                            <?php if ($p['discount_price']): ?><br><small style="text-decoration:line-through;color:var(--text-muted);"><?php echo formatPrice($p['price']); ?></small><?php endif; ?>
                        </td>
                        <td><span style="color:<?php echo $p['stock_quantity'] < 5 ? 'var(--danger)' : 'var(--success)'; ?>;font-weight:600;"><?php echo $p['stock_quantity']; ?></span></td>
                        <td><span class="badge badge-<?php echo $p['status']; ?>"><?php echo ucfirst($p['status']); ?></span></td>
                        <td>
                            <div style="display:flex;gap:4px;">
                                <a href="?edit=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline">Edit</a>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this product?')">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>setTimeout(()=>{const f=document.getElementById('flashMessage');if(f){f.style.opacity='0';setTimeout(()=>f.remove(),500);}},4000);</script>
</body></html>
