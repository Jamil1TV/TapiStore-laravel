@extends('layouts.store')

@section('content')
<script>const siteUrl = '<?php echo SITE_URL; ?>'; const csrfToken = '<?php echo generateCSRFToken(); ?>';</script>

<div class="page-header">
    <div class="container">
        <h1>Contact Us</h1>
        <div class="breadcrumb"><a href="<?php echo SITE_URL; ?>/">Home</a><span>/</span><span>Contact</span></div>
    </div>
</div>

<div class="container">
    <div class="contact-layout">
        <div>
            <h2 style="margin-bottom:8px;">Get in Touch</h2>
            <p style="color:var(--text-light);margin-bottom:24px;">Have a question or feedback? We'd love to hear from you.</p>

            <div class="contact-info-cards">
                <div class="contact-info-card">
                    <div class="icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    </div>
                    <div><h3>Address</h3><p>123 Commerce Street, Business City, BC 10001</p></div>
                </div>
                <div class="contact-info-card">
                    <div class="icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    </div>
                    <div><h3>Phone</h3><p>+1 (234) 567-890</p></div>
                </div>
                <div class="contact-info-card">
                    <div class="icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </div>
                    <div><h3>Email</h3><p>support@tapistore.com</p></div>
                </div>
            </div>
        </div>

        <div class="checkout-form">
            <h2>Send a Message</h2>
            <form method="POST" id="contactForm" onsubmit="return validateForm('contactForm')">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <div class="form-row">
                    <div class="form-group">
                        <label>Your Name *</label>
                        <input type="text" name="name" class="form-control" required value="<?php echo e($_POST['name'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Email Address *</label>
                        <input type="email" name="email" class="form-control" required value="<?php echo e($_POST['email'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Subject *</label>
                    <input type="text" name="subject" class="form-control" required value="<?php echo e($_POST['subject'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Message *</label>
                    <textarea name="message" class="form-control" rows="5" required><?php echo e($_POST['message'] ?? ''); ?></textarea>
                </div>
                <button type="submit" class="btn btn-accent btn-lg">Send Message</button>
            </form>
        </div>
    </div>
</div>
@endsection
