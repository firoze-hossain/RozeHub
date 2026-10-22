<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In · RozeHub User Account</title>
    <link rel="icon" href="{{ asset('images/rozehub-icon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/rozehub.css') }}">
    <style>
        body.auth-page {
            background: #090d16;
            color: #f1f5f9;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            margin: 0;
            background-image: radial-gradient(circle at 50% 15%, rgba(56, 189, 248, 0.12) 0%, transparent 60%);
        }
        .auth-container {
            width: 100%;
            max-width: 440px;
            background: #111827;
            border: 1px solid #1f2937;
            border-radius: 16px;
            padding: 36px 32px;
            box-shadow: 0 20px 35px -5px rgba(0, 0, 0, 0.6);
        }
        .auth-header {
            text-align: center;
            margin-bottom: 24px;
        }
        .brand-logo-circle {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: #f97316;
            color: white;
            font-size: 26px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 14px;
            box-shadow: 0 4px 14px rgba(249, 115, 22, 0.4);
        }
        .account-type-tag {
            display: inline-block;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #38bdf8;
            background: rgba(56, 189, 248, 0.12);
            border: 1px solid rgba(56, 189, 248, 0.25);
            padding: 4px 10px;
            border-radius: 20px;
            margin-bottom: 8px;
        }
        h1 {
            font-size: 21px;
            font-weight: 700;
            margin: 0 0 6px;
            color: white;
        }
        p.subtitle {
            font-size: 13px;
            color: #94a3b8;
            margin: 0;
            line-height: 1.4;
        }
        .apps-preview-strip {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-top: 10px;
            font-size: 11px;
            color: #64748b;
        }
        .form-group {
            margin-bottom: 18px;
        }
        label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #cbd5e1;
            margin-bottom: 6px;
        }
        input[type="email"], input[type="password"] {
            width: 100%;
            box-sizing: border-box;
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 8px;
            padding: 10px 14px;
            color: white;
            font-size: 14px;
            transition: border-color 0.15s ease;
        }
        input:focus {
            outline: none;
            border-color: #38bdf8;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.15);
        }
        .btn-submit {
            display: block;
            width: 100%;
            padding: 12px;
            background: #f97316;
            color: white;
            font-size: 14px;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.15s ease;
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
            margin-top: 24px;
        }
        .btn-submit:hover {
            background: #ea580c;
        }
        .auth-footer {
            margin-top: 24px;
            text-align: center;
            font-size: 13px;
            color: #64748b;
        }
        .auth-footer a {
            color: #38bdf8;
            text-decoration: none;
        }
        .auth-footer a:hover {
            text-decoration: underline;
        }
        .role-switcher-box {
            margin-top: 22px;
            padding-top: 16px;
            border-top: 1px solid #1f2937;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
            color: #64748b;
        }
        .role-switcher-box a {
            color: #94a3b8;
            text-decoration: none;
        }
        .role-switcher-box a:hover {
            color: #38dc9c;
            text-decoration: underline;
        }
        .error-alert {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid #ef4444;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 18px;
            font-size: 13px;
            color: #fca5a5;
        }
    </style>
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-header">
            <div class="brand-logo-circle">R</div>
            <span class="account-type-tag">Standard User Account</span>
            <h1>Sign In to RozeHub</h1>
            <p class="subtitle">Single user account for your desktop applications.</p>
            <div class="apps-preview-strip">
                ThunderCall · DBNavigator · Lumina · StratosDB · TrackEye
            </div>
        </div>

        @if($errors->any())
            <div class="error-alert">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login.submit') }}">
            @csrf
            <div class="form-group">
                <label>Email address</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="user@example.com">
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Enter your password">
            </div>

            <div style="display: flex; align-items: center; justify-content: space-between; font-size: 12px; margin-bottom: 4px;">
                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; color: #94a3b8; font-weight: normal; margin: 0;">
                    <input type="checkbox" name="remember"> Remember me
                </label>
            </div>

            <button type="submit" class="btn-submit">Sign In to My Account →</button>
        </form>

        <div class="auth-footer">
            Don't have an account? <a href="{{ route('register') }}">Create user account</a> · <a href="{{ route('hub') }}">Back to Hub</a>
        </div>

        <div class="role-switcher-box">
            <span>Plugin Publisher? <a href="{{ route('developer.login') }}" style="color: #38dc9c;">Developer Center ↗</a></span>
            <a href="{{ route('admin.login') }}">Admin Login ↗</a>
        </div>
    </div>
</body>
</html>
