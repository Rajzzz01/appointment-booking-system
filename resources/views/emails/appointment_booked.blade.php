<!DOCTYPE html>
<html>

<head>
    <title>Appointment Confirmation</title>
</head>

<body>
    <p>Dear {{ $name }},</p>

    <p>Your appointment titled "<strong>{{ $title }}</strong>" has been successfully booked for
        <strong>{{ $date_time }} ({{ $timezone }})</strong>.
    </p>

    <p><strong>Description:</strong> {{ $description }}</p>

    <p><strong>Guests:</strong></p>
    <ul>
        @if (!empty($guests))
            @foreach ($guests as $guest)
                <li>{{ $guest }}</li>
            @endforeach
        @else
            <li>No guests invited.</li>
        @endif
    </ul>

    <p>Thank you!</p>
</body>

</html>
