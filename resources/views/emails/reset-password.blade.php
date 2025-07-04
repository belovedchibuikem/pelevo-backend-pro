<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Reset Your Password</h1>
        </div>
        
        <div class="content">
            <p>Hello {{ $user->name }},</p>

            <p>We received a request to reset your password for your {{ config('app.name') }} account. If you didn't make this request, you can safely ignore this email.</p>

            <p>To reset your password, click the button below:</p>

            <div style="text-align: center;">
                <a href="{{ $resetUrl }}" class="button">Reset Password</a>
            </div>

            <p>Or copy and paste this link into your browser:</p>
            <p style="word-break: break-all;">{{ $resetUrl }}</p>

            <div class="warning">
                <p><strong>Important:</strong> This password reset link will expire in <span class="expiry">24 hours</span>.</p>
            </div>

            <p>For security reasons, this link can only be used once. If you need to reset your password again, please request another password reset.</p>

            <p>If you didn't request this password reset, please contact our support team immediately.</p>

            <p>Best regards,<br>
            The {{ config('app.name') }} Team</p>
        </div>

        <div class="footer">
            <p>This email was sent to {{ $user->email }}. If you didn't request this password reset, please ignore this email or contact support if you have concerns.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html> 