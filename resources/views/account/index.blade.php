<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My RozeHub Account · Single Account Ecosystem</title>
    <link rel="stylesheet" href="{{ asset('css/rozehub.css') }}">
    <style>
        body.account-body {
            background: #090d16;
            color: #f1f5f9;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }
        .account-navbar {
            background: #0f172a;
            border-bottom: 1px solid #1e293b;
            padding: 14px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .nav-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            color: white;
            text-decoration: none;
            font-weight: 700;
            font-size: 17px;
        }
        .nav-logo-badge {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #f97316;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .nav-link {
            color: #94a3b8;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: color 0.15s ease;
        }
        .nav-link:hover { color: #f8fafc; }
        .main-container {
            max-width: 1080px;
            margin: 36px auto;
            padding: 0 24px;
        }
        .hero-banner {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border: 1px solid #334155;
            border-radius: 16px;
            padding: 28px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 32px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4);
        }
        .profile-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .big-avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f97316, #ea580c);
            color: white;
            font-size: 24px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 14px rgba(249, 115, 22, 0.4);
        }
        .profile-name {
            font-size: 22px;
            font-weight: 700;
            color: white;
            margin: 0 0 4px;
        }
        .profile-sub {
            font-size: 13px;
            color: #94a3b8;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .pill-badge {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 12px;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        .section-header {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 17px;
            font-weight: 700;
            color: #f8fafc;
            margin: 0 0 6px;
        }
        .section-desc {
            font-size: 13px;
            color: #64748b;
            margin: 0;
        }
        .grid-apps {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .app-card {
            background: #111827;
            border: 1px solid #1f2937;
            border-radius: 14px;
            padding: 22px;
            transition: transform 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .app-card:hover {
            border-color: #38bdf8;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }
        .app-card-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 12px;
        }
        .app-card-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: #1e293b;
            border: 1px solid #334155;
            color: #38bdf8;
            font-size: 18px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .app-card-title {
            font-size: 15px;
            font-weight: 600;
            color: white;
            margin: 0 0 2px;
        }
        .app-card-tag {
            font-size: 11px;
            color: #64748b;
            margin: 0;
        }
        .app-card-desc {
            font-size: 12px;
            color: #94a3b8;
            line-height: 1.5;
            margin: 0 0 16px;
        }
        .app-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 14px;
            border-top: 1px solid #1e293b;
        }
        .btn-connect {
            background: #1e293b;
            border: 1px solid #334155;
            color: #38bdf8;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
            transition: background 0.15s ease, color 0.15s ease;
        }
        .btn-connect:hover {
            background: #38bdf8;
            color: #0f172a;
        }
        .connected-badge {
            color: #10b981;
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .settings-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 40px;
        }
        .settings-card {
            background: #111827;
            border: 1px solid #1f2937;
            border-radius: 14px;
            padding: 24px;
        }
        .settings-card h3 {
            font-size: 15px;
            font-weight: 600;
            color: white;
            margin: 0 0 16px;
        }
        .form-group {
            margin-bottom: 16px;
        }
        label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #cbd5e1;
            margin-bottom: 6px;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            box-sizing: border-box;
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 8px;
            padding: 9px 12px;
            color: white;
            font-size: 13px;
        }
        .btn-save {
            background: #2563eb;
            color: white;
            font-size: 13px;
            font-weight: 600;
            border: none;
            border-radius: 6px;
            padding: 8px 16px;
            cursor: pointer;
        }
        .btn-save:hover { background: #1d4ed8; }
        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid #10b981;
            color: #6ee7b7;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 20px;
            font-size: 13px;
        }
    </style>
</head>
<body class="account-body">
    <nav class="account-navbar">
        <a href="{{ route('hub') }}" class="nav-brand">
            <span class="nav-logo-badge">R</span>
            <span>RozeHub Ecosystem</span>
        </a>
        <div class="nav-links">
            <a href="{{ route('hub') }}" class="nav-link">Explore Hub</a>
            <a href="{{ route('marketplace.index') }}" class="nav-link">Marketplace</a>
            <a href="{{ route('docs.index') }}" class="nav-link">Documentation</a>
            @if($user->isDeveloper())
                <a href="{{ route('developer.dashboard') }}" class="nav-link" style="color: #38dc9c; font-weight: 600;">Developer Center ↗</a>
            @else
                <a href="{{ route('developer.dashboard') }}" class="nav-link" style="color: #94a3b8;">Publisher Portal ↗</a>
            @endif
            @if($user->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="nav-link" style="color: #f97316; font-weight: 600;">Admin Panel ↗</a>
            @endif
            <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" style="background: none; border: none; color: #ef4444; font-size: 13px; cursor: pointer; padding: 0;">Sign out</button>
            </form>
        </div>
    </nav>

    <div class="main-container">
        @if(session('status'))
            <div class="alert-success">{{ session('status') }}</div>
        @endif

        <div class="hero-banner">
            <div class="profile-left">
                <div class="big-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                <div>
                    <h1 class="profile-name">{{ $user->name }}</h1>
                    <p class="profile-sub">
                        <span>{{ $user->email }}</span>
                        @if($user->isAdmin())
                            <span class="pill-badge" style="background: rgba(249, 115, 22, 0.15); color: #f97316; border-color: rgba(249, 115, 22, 0.3);">Administrator</span>
                        @elseif($user->isDeveloper())
                            <span class="pill-badge" style="background: rgba(56, 220, 156, 0.15); color: #38dc9c; border-color: rgba(56, 220, 156, 0.3);">Developer / Publisher</span>
                        @else
                            <span class="pill-badge" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8; border-color: rgba(56, 189, 248, 0.3);">Standard User Account</span>
                        @endif
                    </p>
                </div>
            </div>
            <div>
                <a href="{{ route('auth.desktop', ['app' => 'thundercall']) }}" class="btn-connect" style="padding: 10px 18px; font-size: 13px; background: #f97316; color: white; border-color: #ea580c;">
                    ⚡ Connect ThunderCall
                </a>
            </div>
        </div>

        <div class="section-header">
            <h2 class="section-title">Connected Desktop Applications</h2>
            <p class="section-desc">Your single RozeHub account syncs workspaces, settings, and marketplace extensions across all 7 projects.</p>
        </div>

        <div class="grid-apps">
            @foreach($projects as $proj)
                @php
                    $connected = isset($interactions[$proj->id]);
                @endphp
                <div class="app-card">
                    <div>
                        <div class="app-card-header">
                            <div class="app-card-icon">{{ substr($proj->name, 0, 1) }}</div>
                            <div>
                                <h3 class="app-card-title">{{ $proj->name }}</h3>
                                <p class="app-card-tag">{{ $proj->category ?? 'Ecosystem Application' }}</p>
                            </div>
                        </div>
                        <p class="app-card-desc">{{ $proj->tagline ?? $proj->description }}</p>
                    </div>

                    <div class="app-card-footer">
                        @if($connected)
                            <span class="connected-badge">● Connected & Active</span>
                        @else
                            <span style="font-size: 12px; color: #64748b;">Ready to link</span>
                        @endif

                        <a href="{{ route('auth.desktop', ['app' => $proj->slug]) }}" class="btn-connect">
                            {{ $connected ? 'Re-authorize ↗' : 'Connect SSO ↗' }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="section-header">
            <h2 class="section-title">Account & Security Settings</h2>
            <p class="section-desc">Manage your master credentials and profile.</p>
        </div>

        <div class="settings-grid">
            <div class="settings-card">
                <h3>Profile Information</h3>
                <form method="POST" action="{{ route('account.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                    </div>
                    <button type="submit" class="btn-save">Save Profile</button>
                </form>
            </div>

            <div class="settings-card">
                <h3>Change Password</h3>
                <form method="POST" action="{{ route('account.password') }}">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label>New Password (min. 8 characters)</label>
                        <input type="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" name="password_confirmation" required>
                    </div>
                    <button type="submit" class="btn-save">Update Password</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
