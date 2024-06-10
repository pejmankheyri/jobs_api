<!DOCTYPE html>
<html>

<head>
    <title>Job Application Received</title>

</head>

<body>
    <h1>Hello, {{ $jobItem->company->user->email }}</h1>
    <p>Thank you for applying to the job "{{ $jobItem->title }}" at {{ config('app.name') }}.</p>
    <p>Your application has been received and is currently being reviewed.</p>
</body>

</html>
