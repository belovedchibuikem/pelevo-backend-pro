<!DOCTYPE html>
<html>
<head>
    <title>Verify Your Email Address</title>
</head>
<body>
    <h2>Welcome to {{ config('app.name') }}!</h2>
    <p>Thank you for registering. Please click the button below to verify your email address:</p>
    
    <a href="{{ url('/email/verify/' . $user->id . '/' . sha1($user->getEmailForVerification())) }}" 
       style="background-color: #4CAF50; color: white; padding: 14px 20px; text-decoration: none; border-radius: 4px; display: inline-block;">
        Verify Email Address
    </a>

    <p>If you did not create an account, no further action is required.</p>
    
    <p>Regards,<br>{{ config('app.name') }} Team</p>
</body>
</html> 