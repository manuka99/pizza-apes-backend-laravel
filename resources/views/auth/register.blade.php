<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register</title>
</head>

<body>
    <h1>Register</h1>
    <form action="{{ url('register') }}" method="POST">
        @csrf
        <input type="text" name="name" placeholder="Name" value="{{ old('name') }}" autofocus><br><br>

        @error('name')
            <p>{{ $message }}</p>
        @enderror

        <input type="number" name="age" placeholder="Age" value="{{ old('age') }}" autofocus><br><br>

        <input type="text" name="email" placeholder="Email" value="{{ old('email') }}" autofocus><br><br>

        @error('email')
            <p>{{ $message }}</p>
        @enderror

        <input type="password" name="password" placeholder="Password" autocomplete="current-password"><br><br>

        @error('password')
            <p>{{ $message }}</p>
        @enderror

        <input type="password" name="password_confirmation" placeholder="Confirm Password"
            autocomplete="current-password"><br><br>

        @error('password_confirmation')
            <p>{{ $message }}</p>
        @enderror

        <button type="submit">Login</button>
    </form>
</body>

</html>
