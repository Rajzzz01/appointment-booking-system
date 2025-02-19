<!DOCTYPE html>
<html>

<head>
    <title>Appointment Cancelled</title>
</head>

<body>
    <p>Dear {{ $name }},</p>

    <p>The appointment titled "<strong>{{ $title }}</strong>" scheduled for <strong>{{ $date_time }}
            ({{ $timezone }})</strong> has been cancelled.</p>

    <p>We regret any inconvenience caused.</p>

    <p>Thank you!</p>
</body>

</html>
