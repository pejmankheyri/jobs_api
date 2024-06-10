<!DOCTYPE html>
<html>

<head>
    <title>You have applied for a job</title>
</head>

<body>
    <h1>Hello, {{ $user->email }}</h1>
    <p>Thank you for applying to the job "{{ $jobItem->title }}" at {{ config('app.name') }}.</p>
    <p>Your application has been received and is currently being reviewed.</p>
</body>

</html>
