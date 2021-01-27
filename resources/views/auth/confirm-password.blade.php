<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Confirm Password</title>
</head>

<body>
    <h1>Confirm Password</h1>
    <form action="{{ url('/user/confirm-password') }}" method="POST">
        @csrf

        <input type="password" name="password" placeholder="Password" autocomplete="current-password"><br><br>

        @error('password')
            <p>{{ $message }}</p>
        @enderror

        <button type="submit">Confirm password</button>
    </form>
</body>

</html>
