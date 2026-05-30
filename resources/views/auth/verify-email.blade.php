@extends('layouts.app')

@section('title', 'Verify Email | Laravel Commerce')

@section('content')
<section class="auth-shell">
    <div class="auth-panel">
        <h1>Verify your email</h1>
        <p class="text-secondary">Open the verification link sent to {{ auth()->user()->email }}.</p>
        <form method="post" action="{{ route('verification.send') }}">
            @csrf
            <button class="btn btn-brand w-100" type="submit">Send another link</button>
        </form>
    </div>
</section>
@endsection
