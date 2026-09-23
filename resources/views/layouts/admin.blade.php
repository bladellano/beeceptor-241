<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Mock API') — {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ \App\Support\AssetVersion::url('css/admin.css') }}">
</head>
<body>
<header class="topbar">
    <div class="container topbar-inner">
        <a href="{{ route('admin.dashboard') }}" class="brand">{{ config('app.name', 'Mock API') }}</a>
        <nav>
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a href="{{ route('admin.endpoints.index') }}">Endpoints</a>
        </nav>
        <form method="POST" action="{{ route('logout') }}" class="logout-form">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </div>
</header>
<main class="container">
    @if (session('status'))
        <div class="alert">{{ session('status') }}</div>
    @endif
    @yield('content')
</main>
@stack('scripts')
</body>
</html>
