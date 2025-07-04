@extends('layouts.verification')

@section('content')
<div class="container success">
    <div class="icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>
    </div>
    <h1>Email Verified Successfully!</h1>
    <p>Your email has been verified. You can now log in to your account and start using all features.</p>
    <a href="{{ config('app.frontend_url') }}/auth" class="button">Proceed to Login</a>
</div>
@endsection 