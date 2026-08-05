<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Appointment Rescheduled - {{ env('APP_NAME') }}</title>
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
            background-color: #fd7e14;
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
            background-color: #ffe5d1;
            padding: 10px;
            border-left: 4px solid #fd7e14;
            margin: 15px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Appointment Rescheduled</h1>
        </div>
        <div class="content">
            <p>Hello {{ $patientName }},</p>
            <p>Your appointment with Dr. {{ $doctorName }} has been rescheduled. Here are the updated details:</p>

            <div class="highlight">
                <p><strong>New Date & Time:</strong> {{ $newAppointmentDateTime }}</p>
            </div>

            <p><strong>Reason:</strong> {{ $reason ?? 'N/A' }}</p>
            <p><strong>Location:</strong> {{ $location }}</p>

            <p>We apologize for any inconvenience and appreciate your understanding.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ env('APP_NAME') }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
