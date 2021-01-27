<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Verify email to proceed</title>
</head>

<body>
    <h1>Verify email to proceed</h1>

    @if (session('status') == 'verification-link-sent')
        <p> A new email verification link has been emailed to you!</p>
    @endif

    <form action="{{ url('/email/verification-notification') }}" method="POST">
        @csrf
        <button type="submit">Resend verification code</button>
    </form>
</body>

</html>
