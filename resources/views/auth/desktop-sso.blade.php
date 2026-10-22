<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connect to {{ $project->name ?? ucfirst($appSlug) }} · RozeHub SSO</title>
    <link rel="stylesheet" href="{{ asset('css/rozehub.css') }}">
    <link rel="stylesheet" href="{{ asset('css/developer-marketplace.css') }}">
    <style>
        body.sso-bg {
            background: #090d16;
            color: #f1f5f9;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            margin: 0;
        }
        .sso-container {
            width: 100%;
            max-width: 480px;
            background: #111827;
            border: 1px solid #1f2937;
            border-radius: 16px;
            box-shadow: 0 20px 35px -5px rgba(0, 0, 0, 0.6), 0 0 20px rgba(56, 189, 248, 0.1);
            overflow: hidden;
        }
        .sso-header {
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.9), rgba(15, 23, 42, 0.95));
            border-bottom: 1px solid #1f2937;
            padding: 28px 32px 24px;
            text-align: center;
        }
        .app-badge-box {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            margin-bottom: 16px;
        }
        .app-icon-circle {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            background: #1e293b;
            border: 1px solid #334155;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: bold;
            color: #38bdf8;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
        .app-chain-arrow {
            color: #64748b;
            font-size: 18px;
        }
        .rozehub-logo-circle {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            background: #f97316;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
            color: white;
            box-shadow: 0 4px 14px rgba(249, 115, 22, 0.4);
        }
        .sso-title {
            font-size: 19px;
            font-weight: 700;
            color: #ffffff;
            margin: 0 0 6px;
        }
        .sso-subtitle {
            font-size: 13px;
            color: #94a3b8;
            margin: 0;
            line-height: 1.4;
        }
        .sso-body {
            padding: 28px 32px;
        }
        .user-pill {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 10px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 22px;
        }
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #0ea5e9;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
        }
        .user-details {
            flex: 1;
            overflow: hidden;
        }
        .user-name {
            font-size: 14px;
            font-weight: 600;
            color: #f8fafc;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .user-email {
            font-size: 12px;
            color: #94a3b8;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .permissions-box {
            margin-bottom: 24px;
        }
        .permissions-title {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            margin-bottom: 12px;
        }
        .permission-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 13px;
            color: #cbd5e1;
            margin-bottom: 10px;
            line-height: 1.4;
        }
        .permission-check {
            color: #10b981;
            font-weight: bold;
        }
        .btn-auth-primary {
            display: block;
            width: 100%;
            padding: 12px 20px;
            background: #f97316;
            color: white;
            font-size: 14px;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            transition: background 0.15s ease, transform 0.1s ease;
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
        }
        .btn-auth-primary:hover {
            background: #ea580c;
        }
        .sso-footer {
            padding: 16px 32px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 1px solid #1f2937;
            font-size: 12px;
        }
        .text-muted-link {
            color: #64748b;
            text-decoration: none;
        }
        .text-muted-link:hover {
            color: #94a3b8;
            text-decoration: underline;
        }
    </style>
</head>
<body class="sso-bg">
    <div class="sso-container">
        <div class="sso-header">
            <div class="app-badge-box">
                <div class="rozehub-logo-circle">R</div>
                <div class="app-chain-arrow">⟷</div>
                <div class="app-icon-circle">{{ substr($project->name ?? ucfirst($appSlug), 0, 1) }}</div>
            </div>
            <h1 class="sso-title">Connect {{ $project->name ?? ucfirst($appSlug) }}</h1>
            <p class="sso-subtitle">Single sign-on authentication for your RozeHub user account.</p>
        </div>

        @if($user)
            <div class="sso-body">
                <div class="user-pill">
                    <div class="user-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                    <div class="user-details">
                        <div class="user-name">{{ $user->name }}</div>
                        <div class="user-email">{{ $user->email }}</div>
                    </div>
                </div>

                <div class="permissions-box">
                    <div class="permissions-title">Permissions requested:</div>
                    <div class="permission-item">
                        <span class="permission-check">✓</span>
                        <span>Synchronize workspaces, collections, and team environments with RozeHub cloud</span>
                    </div>
                    <div class="permission-item">
                        <span class="permission-check">✓</span>
                        <span>Access RozeHub marketplace extensions and verified plugins</span>
                    </div>
                    <div class="permission-item">
                        <span class="permission-check">✓</span>
                        <span>Receive instant updates and notification alerts</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('auth.desktop.authorize') }}">
                    @csrf
                    <input type="hidden" name="app" value="{{ $appSlug }}">
                    <input type="hidden" name="port" value="{{ $port }}">
                    <input type="hidden" name="state" value="{{ $state }}">
                    <input type="hidden" name="redirect_uri" value="{{ $redirectUri }}">

                    <button type="submit" class="btn-auth-primary">
                        Authorize & Open {{ $project->name ?? ucfirst($appSlug) }} →
                    </button>
                </form>
            </div>

            <div class="sso-footer">
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-muted-link">
                    Sign in with a different account
                </a>
                <a href="{{ route('hub') }}" class="text-muted-link">Cancel</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
            </div>
        @else
            <div class="sso-body">
                <p style="font-size: 13px; color: #94a3b8; margin: 0 0 20px; line-height: 1.5;">
                    Sign in with your single RozeHub account to connect your desktop application.
                </p>

                @if($errors->any())
                    <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; border-radius: 8px; padding: 10px 14px; margin-bottom: 16px; font-size: 13px; color: #fca5a5;">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.submit') }}">
                    @csrf
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #cbd5e1; margin-bottom: 6px;">Email address</label>
                    <input type="email" name="email" required value="{{ old('email') }}" style="width: 100%; box-sizing: border-box; background: #1e293b; border: 1px solid #334155; border-radius: 8px; padding: 10px 12px; color: white; margin-bottom: 16px; font-size: 14px;">

                    <label style="display: block; font-size: 12px; font-weight: 600; color: #cbd5e1; margin-bottom: 6px;">Password</label>
                    <input type="password" name="password" required style="width: 100%; box-sizing: border-box; background: #1e293b; border: 1px solid #334155; border-radius: 8px; padding: 10px 12px; color: white; margin-bottom: 20px; font-size: 14px;">

                    <button type="submit" class="btn-auth-primary">
                        Sign In & Connect →
                    </button>
                </form>
            </div>

            <div class="sso-footer">
                <span style="color: #64748b;">New to RozeHub? <a href="{{ route('register') }}" style="color: #38bdf8; text-decoration: none;">Create an account</a></span>
                <a href="{{ route('hub') }}" class="text-muted-link">Cancel</a>
            </div>
        @endif
    </div>
</body>
</html>
