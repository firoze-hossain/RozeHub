<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Admin Login · RozeHub Platform</title>
    <link rel="icon" href="{{ asset('images/rozehub-icon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/rozehub.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <style>
        .role-switcher-links {
            margin-top: 18px;
            padding-top: 14px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            font-size: 12px;
            color: #718096;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .role-switcher-links a {
            color: #a0aec0;
            text-decoration: none;
        }
        .role-switcher-links a:hover {
            color: #f97316;
            text-decoration: underline;
        }
    </style>
</head>
<body class="admin-login">
    <div class="login-card">
        <a class="admin-brand login-brand" href="{{ route('hub') }}">
            <span class="brand-mark">R</span>
            <span>RozeHub <small>ADMIN</small></span>
        </a>
        <div class="login-heading">
            <span>SECURE PLATFORM ACCESS</span>
            <h1>Administrator Sign In</h1>
            <p>Manage official software releases, reviews, roadmaps, and marketplace moderation.</p>
        </div>

        @if($errors->any())
            <div class="admin-alert error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf
            <label>
                Email Address
                <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            </label>
            <label>
                Password
                <input type="password" name="password" required autocomplete="current-password">
            </label>
            <label class="remember">
                <input type="checkbox" name="remember" value="1"> Keep me signed in
            </label>
            <button class="admin-primary" type="submit">
                Sign in to Admin Dashboard <span>→</span>
            </button>
        </form>

        <div class="role-switcher-links">
            <span>Desktop User? <a href="{{ route('login') }}">User Sign In</a></span>
            <a href="{{ route('developer.login') }}">Developer Center ↗</a>
        </div>

        <a class="login-back" href="{{ route('hub') }}" style="margin-top: 14px; display: inline-block;">← Back to RozeHub</a>
    </div>
</body>
</html>
