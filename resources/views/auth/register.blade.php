@extends('layouts.store')

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <h1>Create Account</h1>
        <p class="auth-subtitle">Join us and start shopping today</p>

        <?php if (!empty($errors)): ?>
            <div style="background:var(--danger-bg);color:#991b1b;padding:12px;border-radius:var(--radius);margin-bottom:16px;font-size:.9rem;">
                <?php foreach ($errors as $err): ?>
                    <div><?php echo e($err); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="registerForm" onsubmit="return validateForm('registerForm')">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" name="full_name" id="full_name" class="form-control" value="<?php echo e($_POST['full_name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" value="<?php echo e($_POST['email'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" name="phone" id="phone" class="form-control" value="<?php echo e($_POST['phone'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" required minlength="6">
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-accent btn-block btn-lg">Create Account</button>
        </form>

        <div class="auth-footer">
            Already have an account? <a href="<?php echo SITE_URL; ?>/pages/login.php">Log in</a>
        </div>
    </div>
</div>
@endsection
