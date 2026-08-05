<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Forgot Password - {{ env('APP_NAME') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f9fc;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #28a745;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .content {
            padding: 20px;
            color: #333333;
            font-size: 16px;
            line-height: 1.6;
        }

        .content p {
            margin: 15px 0;
        }

        .content a {
            display: inline-block;
            margin-top: 10px;
            padding: 12px 20px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
        }

        .footer {
            background-color: #f1f1f1;
            color: #777777;
            padding: 15px;
            text-align: center;
            font-size: 14px;
        }

        .footer a {
            color: #007bff;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Password Reset Request</h1>
        </div>
        <div class="content">
            <p>Hello, {{ $name }}</p>
            <p>We received a request to reset the password associated with your account on
                <strong>{{ env('APPLICATION_NAME') }}</strong>.
            </p>
            <p>Click the button below to reset your password:</p>
            <p><a target="_blank" href="{{ $verificationUrl }}">Reset Password</a></p>
            <p>This link will expire on <strong>{{ $expiration }}</strong> for your security.</p>
            <p>If you did not request a password reset, please ignore this email or contact support if you have any
                concerns.</p>
            <p>Thank you,<br>The {{ env('APPLICATION_NAME') }} Team</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ env('APPLICATION_NAME') }}. All rights reserved.</p>
            {{-- <p>Need help? <a href="mailto:support@hospitalapp.com">Contact Support</a></p> --}}
        </div>
    </div>
</body>

</html>
