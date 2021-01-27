<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
</head>

<body>
    {{-- reset password success --}}
    @if (session('status'))
        <p> {{ session('status') }}</p>
    @endif
    {{-- reset password success end --}}
    
    <h1>Login</h1>
    <form action="{{ route('login') }}" method="POST">
        @csrf
        <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" autofocus><br><br>

        @error('email')
            <p>{{ $message }}</p>
        @enderror

        <input type="password" name="password" placeholder="Password" autocomplete="current-password"><br><br>

        @error('password')
            <p>{{ $message }}</p>
        @enderror


        <input type="checkbox" value="forever" name="remember"> Remember me<br><br>

        <button type="submit">Login</button>
    </form>
</body>

</html>
