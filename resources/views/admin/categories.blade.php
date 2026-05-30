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
            <h1>Categories</h1>
            <a href="?add=1" class="btn btn-accent">+ Add Category</a>
        </div>

        <?php if ($showForm): ?>
        <div class="admin-card">
            <h2 style="margin-bottom:20px;"><?php echo $editCat ? 'Edit Category' : 'Add New Category'; ?></h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <input type="hidden" name="action" value="<?php echo $editCat ? 'edit' : 'add'; ?>">
                <?php if ($editCat): ?><input type="hidden" name="id" value="<?php echo $editCat['id']; ?>"><?php endif; ?>
                <input type="hidden" name="existing_image" value="<?php echo e($editCat['image'] ?? ''); ?>">

                <div class="form-row">
                    <div class="form-group">
                        <label>Category Name *</label>
                        <input type="text" name="name" class="form-control" value="<?php echo e($editCat['name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Image</label>
                        <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/webp">
                        <?php if (!empty($editCat['image'])): ?>
                            <p style="font-size:.8rem;color:var(--text-muted);margin-top:4px;">Current: <?php echo e($editCat['image']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="3"><?php echo e($editCat['description'] ?? ''); ?></textarea>
                </div>
                <div style="display:flex;gap:8px;">
                    <button type="submit" class="btn btn-accent"><?php echo $editCat ? 'Update' : 'Add'; ?> Category</button>
                    <a href="<?php echo SITE_URL; ?>/admin/categories.php" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <div class="admin-card">
            <div class="admin-card-header"><h2>All Categories (<?php echo count($categories); ?>)</h2></div>
            <div class="table-responsive">
                <table>
                    <thead><tr><th>Image</th><th>Name</th><th>Slug</th><th>Products</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><img src="<?php echo categoryImage($cat['image']); ?>" class="table-img" alt=""></td>
                        <td><strong><?php echo e($cat['name']); ?></strong></td>
                        <td style="color:var(--text-muted);"><?php echo e($cat['slug']); ?></td>
                        <td><?php echo $cat['product_count']; ?></td>
                        <td>
                            <div style="display:flex;gap:4px;">
                                <a href="?edit=<?php echo $cat['id']; ?>" class="btn btn-sm btn-outline">Edit</a>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this category?')">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
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
