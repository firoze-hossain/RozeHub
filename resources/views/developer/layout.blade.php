<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Developer Center · RozeHub' }}</title>
    <link rel="icon" href="{{ asset('images/rozehub-icon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/rozehub.css').'?v=20260829' }}">
    <link rel="stylesheet" href="{{ asset('css/developer-marketplace.css') }}?v={{ time() }}">
</head>
<body class="developer-body">
<header class="dev-top">
    <div class="dev-top-inner">
        <a href="{{ route('hub') }}" class="dev-brand" title="RozeHub Platform">
            <span class="dev-brand-icon">
                <img src="{{ asset('images/rozehub-icon.png') }}" alt="RozeHub" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                <span class="dev-brand-fallback" style="display:none;">R</span>
            </span>
            <div class="dev-brand-text">
                <strong class="dev-brand-name">RozeHub</strong>
                <span class="dev-brand-sub">DEVELOPER CENTER</span>
            </div>
        </a>

        <nav class="dev-nav">
            <a href="{{ route('developer.dashboard') }}" class="{{ request()->routeIs('developer.dashboard') ? 'is-active' : '' }}">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                Dashboard
            </a>
            <a href="{{ route('developer.marketplace.submissions') }}" class="{{ request()->routeIs('developer.marketplace.submission*') ? 'is-active' : '' }}">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                Submissions
            </a>
            <a href="{{ route('marketplace.index') }}" target="_blank" class="dev-nav-external">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                Marketplace <span class="ext-icon">↗</span>
            </a>
            <a href="{{ route('developer.notifications') }}" class="{{ request()->routeIs('developer.notifications') ? 'is-active' : '' }}">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                Notifications
                @if(($unread ?? 0) > 0)
                    <span class="dev-nav-badge">{{ $unread }}</span>
                @endif
            </a>
        </nav>

        <div class="dev-account">
            <div class="dev-user-pill">
                <span class="dev-user-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
                <span class="dev-user-name">{{ auth()->user()->name }}</span>
            </div>
            <form method="POST" action="{{ route('developer.logout') }}" class="dev-logout-form">
                @csrf
                <button type="submit" class="dev-btn-logout" title="Sign out of developer portal">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                    Sign out
                </button>
            </form>
        </div>
    </div>
</header>

<main class="dev-main">
    @if(session('success'))
        <div class="dev-alert success">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            <div>{{ session('success') }}</div>
        </div>
    @endif
    @if(isset($errors) && $errors->any())
        <div class="dev-alert error">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            <div>
                <strong>Please fix the following:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @yield('content')
</main>
</body>
</html>
