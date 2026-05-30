@extends('layouts.store')

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <h1>Forgot Password</h1>
        <p class="auth-subtitle">Enter your email to receive a reset link</p>

        <?php if ($error): ?>
            <div style="background:var(--danger-bg);color:#991b1b;padding:12px;border-radius:var(--radius);margin-bottom:16px;font-size:.9rem;">
                <?php echo e($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div style="background:#dcfce7;color:#166534;padding:12px;border-radius:var(--radius);margin-bottom:16px;font-size:.9rem;">
                <?php echo e($success); ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="forgotPasswordForm">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" value="<?php echo e($_POST['email'] ?? ''); ?>" required>
            </div>

            <button type="submit" class="btn btn-accent btn-block btn-lg">Send Reset Link</button>
        </form>

        <div class="auth-footer">
            Remembered your password? <a href="<?php echo SITE_URL; ?>/pages/login.php">Back to Login</a>
        </div>
    </div>
</div>
@endsection
