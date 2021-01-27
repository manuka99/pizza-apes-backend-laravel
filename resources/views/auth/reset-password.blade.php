<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>New Password reset</title>
</head>

<body>
    <h1>New Password reset</h1>
    <form action="{{ url('/reset-password') }}" method="POST">
        @csrf

        <input type="hidden"  name="token" value="{{request()->route('token')}}">

        <input type="text" name="email" placeholder="Email" value="{{request('email')}}" autofocus><br><br>

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

        <button type="submit">Reset</button>
    </form>
</body>

</html>
