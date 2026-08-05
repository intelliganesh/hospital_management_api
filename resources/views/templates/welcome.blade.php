<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Welcome Email</title>
</head>

<body>
    <h1>Welcome, {{ $name }}!</h1>
    <p>Thank you for registering with us.</p>
    <p>Email: {{ $email }}</p>
    <p>Registered on: {{ $registered_at }}</p>
    <p>We look forward to serving you!</p>
</body>

</html>
