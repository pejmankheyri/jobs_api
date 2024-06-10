<!DOCTYPE html>
<html>

<head>
    <title>
        New user registered
    </title>
</head>

<body>
    <h1>
        New user registered
    </h1>
    <p>
        A new user has registered at {{ config('app.name') }}.
    </p>

    <p>
        <strong>Name:</strong> {{ $user->name }}
    </p>
    <p>
        <strong>Email:</strong> {{ $user->email }}
    </p>

</body>

</html>
