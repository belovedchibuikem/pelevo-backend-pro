@extends('layouts.verification')

@section('content')
<div class="container error">
    <div class="icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
    </div>
    <h1>Verification Failed</h1>
    <p>The verification link is invalid or has expired. Please request a new verification email from the login page.</p>
    <a href="{{ config('app.frontend_url') }}/auth" class="button">Return to Login</a>
</div>
@endsection 