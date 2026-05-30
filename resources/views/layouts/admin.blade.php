@extends('layouts.app')

@section('content')
<section class="admin-shell">
    <aside class="admin-sidebar">
        <strong>{{ __('messages.admin') }}</strong>
        <nav>
            <a href="{{ route('admin.dashboard') }}"><i data-lucide="layout-dashboard"></i> {{ __('messages.admin_dashboard') }}</a>
            <a href="{{ route('admin.products.index') }}"><i data-lucide="package"></i> {{ __('messages.admin_products') }}</a>
            <a href="{{ route('admin.categories.index') }}"><i data-lucide="tags"></i> {{ __('messages.admin_categories') }}</a>
            <a href="{{ route('admin.brands.index') }}"><i data-lucide="badge"></i> {{ __('messages.admin_brands') }}</a>
            <a href="{{ route('admin.orders.index') }}"><i data-lucide="receipt-text"></i> {{ __('messages.admin_orders') }}</a>
            <a href="{{ route('admin.customers.index') }}"><i data-lucide="users"></i> {{ __('messages.admin_customers') }}</a>
            <a href="{{ route('admin.coupons.index') }}"><i data-lucide="ticket-percent"></i> {{ __('messages.admin_coupons') }}</a>
            <a href="{{ route('admin.banners.index') }}"><i data-lucide="image"></i> {{ __('messages.admin_banners') }}</a>
            <a href="{{ route('admin.reviews.index') }}"><i data-lucide="message-square"></i> {{ __('messages.admin_reviews') }}</a>
        </nav>
    </aside>
    <div class="admin-content">
        @yield('admin')
    </div>
</section>
@endsection
