<!DOCTYPE html>
<html>

<head>
    <title>Appointment Reminder</title>
</head>

<body>
    <p>Dear {{ $name }},</p>

    <p>This is a reminder for your upcoming appointment titled "<strong>{{ $title }}</strong>".</p>

    <p><strong>Scheduled for</strong> {{ $date_time }} ({{ $timezone }})</p>

    <p>Thank you!</p>
</body>

</html>
