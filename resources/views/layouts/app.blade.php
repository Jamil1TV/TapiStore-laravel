<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ session('theme', 'light') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('messages.app_name'))</title>
    <meta name="description" content="@yield('meta_description', __('messages.meta_description'))">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/store.css') }}" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg sticky-top bg-body border-bottom">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ route('home') }}">
            <span class="brand-mark">LC</span>
            {{ __('messages.app_name') }}
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="{{ __('messages.toggle_navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <form action="{{ route('products.index') }}" class="d-flex ms-lg-4 my-3 my-lg-0 flex-grow-1" role="search">
                <input class="form-control search-input" type="search" name="q" value="{{ request('q') }}" placeholder="{{ __('messages.search_products') }}">
                <button class="btn btn-dark icon-btn ms-2" type="submit" data-bs-toggle="tooltip" title="{{ __('messages.search') }}">
                    <i data-lucide="search"></i>
                </button>
            </form>
            <ul class="navbar-nav ms-lg-4 align-items-lg-center gap-lg-2">
                <li class="nav-item"><a class="nav-link" href="{{ route('products.index') }}">{{ __('messages.shop') }}</a></li>
                <li class="nav-item">
                    <button class="btn icon-btn ghost" id="themeToggle" type="button" data-bs-toggle="tooltip" title="{{ __('messages.toggle_theme') }}">
                        <i data-lucide="sun-moon"></i>
                    </button>
                </li>
                <li class="nav-item">
                    <a class="btn icon-btn ghost position-relative" href="{{ route('cart.index') }}" data-bs-toggle="tooltip" title="{{ __('messages.cart') }}">
                        <i data-lucide="shopping-bag"></i>
                        <span class="cart-count badge rounded-pill text-bg-warning">{{ app(\App\Services\CartService::class)->count(request()) }}</span>
                    </a>
                </li>
                @guest
                    <li class="nav-item"><a class="btn btn-outline-dark" href="{{ route('login') }}">{{ __('messages.login') }}</a></li>
                    <li class="nav-item"><a class="btn btn-brand" href="{{ route('register') }}">{{ __('messages.register') }}</a></li>
                @else
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">{{ auth()->user()->name }}</a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('dashboard') }}">{{ __('messages.dashboard') }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('wishlist.index') }}">{{ __('messages.wishlist') }}</a></li>
                            @if(auth()->user()->isAdmin())
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">{{ __('messages.admin_panel') }}</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="post">
                                    @csrf
                                    <button class="dropdown-item" type="submit">{{ __('messages.logout') }}</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

<main>
    <div class="container pt-3">
        @if (session('status'))
            <div class="alert alert-success border-0 shadow-sm">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm">
                <strong>{{ __('messages.check_form') }}</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
    @yield('content')
</main>

<footer class="border-top py-5 mt-5">
    <div class="container d-flex flex-column flex-md-row justify-content-between gap-3">
        <div>
            <strong>{{ __('messages.app_name') }}</strong>
            <p class="text-secondary mb-0">{{ __('messages.footer_tagline') }}</p>
        </div>
        <div class="d-flex gap-3">
            <a href="{{ route('products.index') }}">{{ __('messages.shop') }}</a>
            <a href="{{ url('/api/products') }}">{{ __('messages.api') }}</a>
            <a href="{{ route('cart.index') }}">{{ __('messages.cart') }}</a>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<script src="{{ asset('js/store.js') }}"></script>
@stack('scripts')
</body>
</html>
