<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Appointment Updated - {{ env('APP_NAME') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f9fc;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 30px auto;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #ffc107;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .content {
            padding: 20px;
            color: #333;
            font-size: 16px;
            line-height: 1.6;
        }

        .footer {
            background-color: #f1f1f1;
            color: #777;
            padding: 15px;
            text-align: center;
            font-size: 14px;
        }

        .btn {
            background: #007bff;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            display: inline-block;
            border-radius: 5px;
            margin-top: 10px;
        }

        .highlight {
            background-color: #fff3cd;
            padding: 10px;
            border-left: 4px solid #ffc107;
            margin: 15px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Appointment Updated</h1>
        </div>
        <div class="content">
            <p>Hello Dr. {{ $doctorName }},</p>
            <p>An appointment with <strong>{{ $patientName }}</strong> has been updated. Please review the new details
                below:</p>

            <div class="highlight">
                <p><strong>New Date & Time:</strong> {{ $newAppointmentDateTime }}</p>
            </div>
            <p>Make sure to adjust your schedule accordingly.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ env('APP_NAME') }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
