<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Password Updated - {{ env('APP_NAME') }}</title>
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
            <h1>Password Updated</h1>
        </div>
        <div class="content">
            <p>Hello, {{ $name }}</p>
            <p>Your password for <strong>{{ env('APPLICATION_NAME') }}</strong> has been successfully updated.</p>
            <p>If you made this change, no further action is needed.</p>
            <p>If you did not request this change, please contact our support team immediately as this could indicate
                unauthorized access to your account.</p>
            <p>Thank you,<br>The {{ env('APPLICATION_NAME') }} Team</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ env('APPLICATION_NAME') }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
