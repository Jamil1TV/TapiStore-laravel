@extends('layouts.app')

@section('title', 'Dashboard | Laravel Commerce')

@section('content')
<section class="container py-4">
    <div class="section-heading">
        <div>
            <p class="eyebrow text-brand">Account</p>
            <h1>Dashboard</h1>
        </div>
        <a class="btn btn-outline-dark" href="{{ route('wishlist.index') }}"><i data-lucide="heart"></i> Wishlist</a>
    </div>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="tool-panel">
                <h2>Order history</h2>
                @forelse($orders as $order)
                    <div class="order-row">
                        <div>
                            <strong>{{ $order->order_number }}</strong>
                            <p class="mb-0 text-secondary">{{ $order->created_at->format('M d, Y') }} · {{ ucfirst($order->status) }}</p>
                        </div>
                        <strong>${{ number_format((float) $order->total, 2) }}</strong>
                    </div>
                @empty
                    <p class="text-secondary mb-0">No orders yet.</p>
                @endforelse
                <div class="mt-3">{{ $orders->links() }}</div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="tool-panel mb-4">
                <h2>Profile</h2>
                <form method="post" action="{{ route('dashboard.profile.update') }}" class="stacked-form">
                    @csrf @method('PATCH')
                    <label>Name<input class="form-control" name="name" value="{{ auth()->user()->name }}" required></label>
                    <label>Phone<input class="form-control" name="phone" value="{{ auth()->user()->phone }}"></label>
                    <button class="btn btn-brand" type="submit">Save profile</button>
                </form>
            </div>
            <div class="tool-panel">
                <h2>Saved addresses</h2>
                @foreach($addresses as $address)
                    <div class="address-row">
                        <strong>{{ ucfirst($address->type) }} · {{ $address->full_name }}</strong>
                        <p>{{ $address->address_line_1 }}, {{ $address->city }}</p>
                        <form method="post" action="{{ route('dashboard.addresses.destroy', $address) }}">
                            @csrf @method('DELETE')
                            <button class="btn btn-link text-danger p-0" type="submit">Remove</button>
                        </form>
                    </div>
                @endforeach
                <form method="post" action="{{ route('dashboard.addresses.store') }}" class="stacked-form mt-3">
                    @csrf
                    <select class="form-select" name="type"><option value="shipping">Shipping</option><option value="billing">Billing</option></select>
                    <input class="form-control" name="full_name" placeholder="Full name" required>
                    <input class="form-control" name="phone" placeholder="Phone" required>
                    <input class="form-control" name="address_line_1" placeholder="Address line 1" required>
                    <input class="form-control" name="address_line_2" placeholder="Address line 2">
                    <div class="row g-2">
                        <div class="col"><input class="form-control" name="city" placeholder="City" required></div>
                        <div class="col"><input class="form-control" name="state" placeholder="State"></div>
                    </div>
                    <div class="row g-2">
                        <div class="col"><input class="form-control" name="postal_code" placeholder="Postal code" required></div>
                        <div class="col"><input class="form-control" name="country" value="United States" required></div>
                    </div>
                    <label class="form-check"><input class="form-check-input" type="checkbox" name="is_default" value="1"><span class="form-check-label">Default address</span></label>
                    <button class="btn btn-outline-dark" type="submit">Add address</button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
