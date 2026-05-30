@extends('layouts.store')

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <h1>Welcome Back</h1>
        <p class="auth-subtitle">Log in to your account</p>

        <?php if ($error): ?>
            <div style="background:var(--danger-bg);color:#991b1b;padding:12px;border-radius:var(--radius);margin-bottom:16px;font-size:.9rem;">
                <?php echo e($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="loginForm">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" value="<?php echo e($_POST['email'] ?? $_COOKIE['remember_email'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-check">
                    <input type="checkbox" name="remember" <?php echo isset($_COOKIE['remember_email']) ? 'checked' : ''; ?>>
                    Remember me
                </label>
            </div>
            <button type="submit" class="btn btn-accent btn-block btn-lg">Log In</button>
        </form>

        <div class="auth-footer">
            Don't have an account? <a href="<?php echo SITE_URL; ?>/pages/register.php">Register</a>
        </div>

        <div style="margin-top:20px;text-align:center;">
            <a href="<?php echo SITE_URL; ?>/pages/forgot_password.php" class="text-accent" style="text-decoration:none;font-weight:500;">Forgot Password?</a>
        </div>
    </div>
</div>
@endsection
