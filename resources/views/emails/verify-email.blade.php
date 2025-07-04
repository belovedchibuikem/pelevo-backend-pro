<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email Address</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            padding: 20px 0;
            background-color: #4F46E5;
            color: white;
        }
        .content {
            padding: 30px 20px;
            background-color: #ffffff;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #4F46E5;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #666;
            background-color: #f9fafb;
        }
        .warning {
            background-color: #FEF2F2;
            border-left: 4px solid #DC2626;
            padding: 15px;
            margin: 20px 0;
        }
        .expiry {
            color: #DC2626;
            font-weight: bold;
        }
        .benefits {
            background-color: #F0FDF4;
            border-left: 4px solid #059669;
            padding: 15px;
            margin: 20px 0;
        }
        .benefits ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .benefits li {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Verify Your Email Address</h1>
        </div>
        
        <div class="content">
            <p>Hello {{ $user->name }},</p>

            <p>Thank you for registering with {{ config('app.name') }}! To complete your registration and access all features, please verify your email address by clicking the button below:</p>

            <div style="text-align: center;">
                <a href="{{ $verificationUrl }}" class="button">Verify Email Address</a>
            </div>

            <p>Or copy and paste this link into your browser:</p>
            <p style="word-break: break-all;">{{ $verificationUrl }}</p>

            <div class="warning">
                <p><strong>Important:</strong> This verification link will expire in <span class="expiry">24 hours</span>.</p>
            </div>

            <div class="benefits">
                <p><strong>Benefits of verifying your email:</strong></p>
                <ul>
                    <li>Access to all features of {{ config('app.name') }}</li>
                    <li>Receive important notifications about your account</li>
                    <li>Reset your password if needed</li>
                    <li>Enhanced account security</li>
                </ul>
            </div>

            <p>If you did not create an account with {{ config('app.name') }}, you can safely ignore this email.</p>

            <p>If you're having trouble clicking the button, copy and paste the URL above into your web browser.</p>

            <p>Best regards,<br>
            The {{ config('app.name') }} Team</p>
        </div>

        <div class="footer">
            <p>This email was sent to {{ $user->email }}. If you didn't create an account with us, please ignore this email or contact support if you have concerns.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html> 