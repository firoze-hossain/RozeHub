<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Developer Account · RozeHub Marketplace</title>
    <link rel="icon" href="{{ asset('images/rozehub-icon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/rozehub.css') }}">
    <style>
        body.dev-login-body {
            background: #06110e;
            color: #e2ebe5;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            margin: 0;
            background-image: radial-gradient(circle at 50% 10%, rgba(20, 114, 85, 0.22) 0%, transparent 60%);
        }
        .dev-login-card {
            width: 100%;
            max-width: 480px;
            background: #0a1b16;
            border: 1px solid #19382e;
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7), 0 0 30px rgba(56, 220, 156, 0.08);
            overflow: hidden;
        }
        .dev-login-header {
            padding: 32px 32px 24px;
            border-bottom: 1px solid #162f27;
            text-align: center;
        }
        .dev-logo-mark {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: linear-gradient(135deg, #147255, #38dc9c);
            color: #06110e;
            font-size: 26px;
            font-weight: 900;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
            box-shadow: 0 8px 20px rgba(56, 220, 156, 0.3);
        }
        .dev-eyebrow {
            display: block;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #38dc9c;
            margin-bottom: 6px;
        }
        .dev-login-title {
            font-size: 20px;
            font-weight: 700;
            color: #ffffff;
            margin: 0 0 8px;
        }
        .dev-login-sub {
            font-size: 13px;
            color: #80968e;
            line-height: 1.5;
            margin: 0;
        }
        .dev-login-body-content {
            padding: 28px 32px;
        }
        .role-notice-banner {
            background: rgba(20, 114, 85, 0.15);
            border: 1px solid rgba(56, 220, 156, 0.25);
            border-radius: 10px;
            padding: 12px 14px;
            margin-bottom: 20px;
            font-size: 12px;
            color: #a4c2b7;
            line-height: 1.5;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        .role-notice-banner .icon {
            color: #38dc9c;
            font-size: 16px;
            flex-shrink: 0;
        }
        .role-notice-banner a {
            color: #38dc9c;
            text-decoration: underline;
            font-weight: 600;
        }
        .form-group {
            margin-bottom: 16px;
        }
        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #bce5d4;
            margin-bottom: 6px;
        }
        .form-control {
            width: 100%;
            box-sizing: border-box;
            background: #06110e;
            border: 1px solid #19382e;
            border-radius: 8px;
            padding: 10px 14px;
            color: #e2ebe5;
            font-size: 14px;
            outline: none;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .form-control:focus {
            border-color: #38dc9c;
            box-shadow: 0 0 0 3px rgba(56, 220, 156, 0.15);
        }
        .btn-dev-submit {
            display: block;
            width: 100%;
            padding: 12px 20px;
            background: #147255;
            color: #ffffff;
            font-size: 14px;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            margin-top: 20px;
            transition: background 0.15s ease, transform 0.1s ease;
            box-shadow: 0 4px 14px rgba(20, 114, 85, 0.4);
        }
        .btn-dev-submit:hover {
            background: #0f5b43;
        }
        .dev-login-footer {
            padding: 16px 32px 24px;
            border-top: 1px solid #162f27;
            font-size: 12px;
            color: #80968e;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .dev-login-footer a {
            color: #38dc9c;
            text-decoration: none;
        }
        .dev-login-footer a:hover {
            text-decoration: underline;
        }
        .alert-error {
            background: rgba(220, 38, 38, 0.15);
            border: 1px solid rgba(220, 38, 38, 0.35);
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 18px;
            font-size: 13px;
            color: #fca5a5;
        }
    </style>
</head>
<body class="dev-login-body">
    <div class="dev-login-card">
        <div class="dev-login-header">
            <div class="dev-logo-mark">R</div>
            <span class="dev-eyebrow">RozeHub Developer Center</span>
            <h1 class="dev-login-title">Create Publisher Account</h1>
            <p class="dev-login-sub">Register to build and publish plugins for Lumina, DBNavigator, and the RozeHub marketplace.</p>
        </div>

        <div class="dev-login-body-content">
            <div class="role-notice-banner">
                <span class="icon">ℹ</span>
                <div>
                    <strong>Normal desktop app user?</strong> You do not need a developer account to use ThunderCall or other apps. Create a <a href="{{ route('register') }}">Normal User Account →</a>
                </div>
            </div>

            @if($errors->any())
                <div class="alert-error">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('developer.register.submit') }}">
                @csrf
                <div class="form-group">
                    <label for="name">Developer / Publisher Name</label>
                    <input id="name" type="text" name="name" class="form-control" value="{{ old('name') }}" required autofocus placeholder="e.g. Acme Tools or Your Name">
                </div>

                <div class="form-group">
                    <label for="email">Developer Email</label>
                    <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="dev@example.com">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" class="form-control" required placeholder="Minimum 8 characters">
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required placeholder="Repeat password">
                </div>

                <button type="submit" class="btn-dev-submit">
                    Register Publisher Account →
                </button>
            </form>
        </div>

        <div class="dev-login-footer">
            <span>Already a publisher? <a href="{{ route('developer.login') }}">Sign in</a></span>
            <a href="{{ route('hub') }}">← Back to Hub</a>
        </div>
    </div>
</body>
</html>
