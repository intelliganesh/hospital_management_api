<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $subject }}</title>
</head>

<body>
    @if (str_contains($body, '<'))
    {!! $body !!}
    @else
        {!! nl2br(e($body)) !!}
    @endif
</body>

</html>
