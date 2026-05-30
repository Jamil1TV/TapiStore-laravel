@extends('layouts.store')

@section('content')
<script>const siteUrl = '<?php echo SITE_URL; ?>'; const csrfToken = '<?php echo generateCSRFToken(); ?>';</script>

<div class="page-header">
    <div class="container">
        <h1>My Profile</h1>
        <div class="breadcrumb"><a href="<?php echo SITE_URL; ?>/">Home</a><span>/</span><span>Profile</span></div>
    </div>
</div>

<div class="container">
    <div class="profile-layout">
        <aside class="profile-sidebar">
            <div class="profile-avatar"><?php echo strtoupper(substr($user['full_name'], 0, 1)); ?></div>
            <h3><?php echo e($user['full_name']); ?></h3>
            <p><?php echo e($user['email']); ?></p>
            <div style="display:flex;justify-content:center;gap:20px;margin-bottom:16px;font-size:.85rem;">
                <div><strong><?php echo $totalOrders; ?></strong><br><span style="color:var(--text-muted)">Orders</span></div>
                <div><strong><?php echo $totalWishlist; ?></strong><br><span style="color:var(--text-muted)">Wishlist</span></div>
            </div>
            <nav class="profile-nav">
                <a href="<?php echo SITE_URL; ?>/pages/profile.php" class="active">Profile Info</a>
                <a href="<?php echo SITE_URL; ?>/pages/orders.php">Order History</a>
                <a href="<?php echo SITE_URL; ?>/pages/wishlist.php">Wishlist</a>
                <a href="<?php echo SITE_URL; ?>/pages/logout.php" style="color:var(--danger)">Logout</a>
            </nav>
        </aside>

        <div class="profile-content">
            <h2>Personal Information</h2>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <input type="hidden" name="action" value="update_profile">
                <div class="form-row">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="full_name" class="form-control" value="<?php echo e($user['full_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email (cannot change)</label>
                        <input type="email" class="form-control" value="<?php echo e($user['email']); ?>" disabled>
                    </div>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="tel" name="phone" class="form-control" value="<?php echo e($user['phone'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" class="form-control" rows="3"><?php echo e($user['address'] ?? ''); ?></textarea>
                </div>
                <button type="submit" class="btn btn-accent">Save Changes</button>
            </form>

            <h2 style="margin-top:40px;">Change Password</h2>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <input type="hidden" name="action" value="change_password">
                <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" class="form-control" required minlength="6">
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Change Password</button>
            </form>
        </div>
    </div>
</div>
@endsection
