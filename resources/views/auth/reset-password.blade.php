@extends('layouts.app')

@section('title', 'Choose Password | Laravel Commerce')

@section('content')
<section class="auth-shell">
    <div class="auth-panel">
        <h1>Choose password</h1>
        <form method="post" action="{{ route('password.update') }}" class="stacked-form">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <label>Email<input class="form-control" type="email" name="email" value="{{ old('email', $email) }}" required></label>
            <label>Password<input class="form-control" type="password" name="password" required></label>
            <label>Confirm password<input class="form-control" type="password" name="password_confirmation" required></label>
            <button class="btn btn-brand w-100" type="submit">Update password</button>
        </form>
    </div>
</section>
@endsection
