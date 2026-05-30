@extends('layouts.app')

@section('title', 'Reset Password | Laravel Commerce')

@section('content')
<section class="auth-shell">
    <div class="auth-panel">
        <h1>Reset password</h1>
        <form method="post" action="{{ route('password.email') }}" class="stacked-form">
            @csrf
            <label>Email<input class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus></label>
            <button class="btn btn-brand w-100" type="submit">Send reset link</button>
        </form>
    </div>
</section>
@endsection
