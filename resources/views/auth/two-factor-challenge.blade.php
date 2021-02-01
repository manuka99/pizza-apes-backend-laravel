<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Two factor challenge</title>
</head>

<body>
    <form action="{{ url('/two-factor-challenge') }}" method="POST">
        @csrf
        <h4>Enter TOTP code</h4>
        <input type="text" name="code">

        <h4>Or enter recovery code</h4>
        <input type="text" name="recovery_code">
        @error('recovery_code')
            <br><br>{{ $message }}
        @enderror
        <br><br>
        <button type="submit">Validate</button>
    </form>

    <form action="{{ url('/forget/two-factor-login') }}" method="POST">
        @csrf
        <button type="submit">Logout</button>
    </form>
</body>

</html>
