@extends('layouts.app')

@section('title', 'Payment | Laravel Commerce')

@section('content')
<section class="auth-shell">
    <div class="auth-panel">
        <h1>{{ ucfirst($provider) }} payment</h1>
        <p class="text-secondary">Order {{ $order->order_number }} is ready for provider confirmation.</p>
        <form action="{{ route('payments.complete', [$order, $provider]) }}" method="post">
            @csrf
            <button class="btn btn-brand w-100" type="submit">Confirm payment</button>
        </form>
    </div>
</section>
@endsection
