<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
<div class="auth-wrap">
    <div class="card auth-card">
        <h1>Admin Login</h1>
        @if ($errors->any())
            <div class="alert" style="background:#fef2f2;border-color:#fca5a5;">{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
            <label for="password">Password</label>
            <input id="password" type="password" name="password" required>
            <label><input type="checkbox" name="remember"> Remember me</label>
            <button class="btn btn-primary" type="submit">Sign in</button>
        </form>
    </div>
</div>
</body>
</html>
