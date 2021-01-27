<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Forgot Password</title>
</head>

<body>
    <h1>Forgot Password</h1>

    @if (session('status'))
        <p> {{ session('status') }}</p>
    @endif

    <form action="{{ url('/forgot-password') }}" method="POST">
        @csrf
        <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" autofocus><br><br>

        @error('email')
            <p>{{ $message }}</p>
        @enderror

        <button type="submit">Forgot password</button>
    </form>
</body>

</html>
