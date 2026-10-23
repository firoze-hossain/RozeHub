<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $project->name }} Support Center · RozeHub</title>
    <link rel="icon" href="{{ asset('images/rozehub-icon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/rozehub.css') }}">
    <style>
        .support-hero {
            background: linear-gradient(180deg, #f8fbf9 0%, #edf5f0 100%);
            border-bottom: 1px solid var(--line);
            padding: 50px 20px 55px;
            text-align: center;
        }
        .support-hero-inner {
            max-width: 820px;
            margin: 0 auto;
        }
        .project-switch-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1180px;
            margin: 0 auto;
            padding: 12px 24px 0;
            font-size: 13px;
        }
        .back-link {
            color: #207e5d;
            text-decoration: none;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .project-selector {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
            font-size: 13px;
        }
        .project-selector select {
            padding: 6px 12px;
            border: 1px solid var(--line);
            border-radius: 6px;
            background: #fff;
            color: var(--ink);
            font-size: 13px;
            font-weight: 600;
            outline: none;
            cursor: pointer;
        }
        .support-hero h1 {
            font-size: 44px;
            letter-spacing: -1.5px;
            margin: 12px 0 12px;
            color: var(--ink);
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
        }
        .support-hero .hero-icon {
            display: inline-grid;
            place-items: center;
            width: 46px;
            height: 46px;
            background: var(--dark);
            color: var(--mint);
            font-size: 24px;
            font-weight: 800;
            border-radius: 10px;
        }
        .support-hero p {
            font-size: 17px;
            color: var(--muted);
            margin: 0 0 24px;
            line-height: 1.5;
        }
        .support-search-box {
            position: relative;
            max-width: 580px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            background: #fff;
            border: 1px solid #c2d3ca;
            border-radius: 8px;
            box-shadow: 0 6px 18px rgba(18, 60, 53, 0.06);
            overflow: hidden;
        }
        .support-search-box span {
            font-size: 20px;
            padding: 0 14px 0 18px;
            color: #7b8e88;
        }
        .support-search-box input {
            height: 50px;
            border: 0;
            outline: 0;
            width: 100%;
            font-size: 15px;
            color: var(--ink);
            background: transparent;
        }
        .support-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(102, 221, 174, 0.18);
            border: 1px solid rgba(47, 157, 118, 0.35);
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            color: #176648;
        }
        .support-status-badge .dot {
            width: 8px;
            height: 8px;
            background: #27ae60;
            border-radius: 50%;
            display: inline-block;
        }
        .support-content {
            max-width: 1180px;
            margin: 45px auto 80px;
            padding: 0 24px;
        }
        .support-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-bottom: 50px;
        }
        @media (max-width: 900px) {
            .support-grid { grid-template-columns: 1fr; }
        }
        .support-card {
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 28px 24px;
            text-decoration: none;
            color: var(--ink);
            display: flex;
            flex-direction: column;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }
        .support-card:hover {
            transform: translateY(-3px);
            border-color: #2f9d76;
            box-shadow: 0 12px 28px rgba(18, 60, 53, 0.08);
        }
        .support-card-icon {
            font-size: 32px;
            margin-bottom: 14px;
            display: inline-block;
        }
        .support-card h3 {
            font-size: 20px;
            margin: 0 0 10px;
            font-weight: 700;
            letter-spacing: -0.4px;
        }
        .support-card p {
            font-size: 14px;
            color: var(--muted);
            line-height: 1.6;
            margin: 0 0 18px;
            flex: 1;
        }
        .support-card-link {
            font-size: 13px;
            font-weight: 700;
            color: #207e5d;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .faq-section {
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 34px 32px;
            margin-bottom: 50px;
        }
        .faq-section h2 {
            font-size: 26px;
            letter-spacing: -0.8px;
            margin: 0 0 8px;
        }
        .faq-section-subtitle {
            font-size: 14px;
            color: var(--muted);
            margin: 0 0 20px;
        }
        .faq-item {
            border-top: 1px solid var(--line);
            padding: 18px 0;
        }
        .faq-question {
            font-size: 16px;
            font-weight: 700;
            color: var(--ink);
        }
        .faq-answer {
            margin-top: 10px;
            font-size: 14px;
            color: #4e5e59;
            line-height: 1.65;
        }
        .contact-box {
            background: linear-gradient(135deg, #123c35 0%, #174e44 100%);
            border-radius: 12px;
            padding: 38px 34px;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 30px;
        }
        @media (max-width: 780px) {
            .contact-box { flex-direction: column; text-align: center; }
        }
        .contact-box h3 {
            font-size: 23px;
            margin: 0 0 8px;
            letter-spacing: -0.5px;
        }
        .contact-box p {
            margin: 0;
            color: #c1ded5;
            font-size: 14px;
        }
        .contact-box .btn {
            background: var(--mint);
            color: #0b2520;
            padding: 13px 24px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 14px;
            text-decoration: none;
            white-space: nowrap;
        }
    </style>
</head>
<body class="docs-body">
<header class="docs-topbar">
    <div class="docs-topbar-inner">
        <a class="docs-brand" href="{{ route('hub') }}">
            <span class="docs-brand-mark"><img src="{{ asset('images/rozehub-icon.png') }}" alt="RozeHub"></span>
            <span><strong>RozeHub</strong><small>Support</small></span>
        </a>
        <nav>
            <a href="{{ route('hub') }}">Home</a>
            <a href="{{ route('docs.index') }}">Docs</a>
            <a href="{{ route('docs.project', $project) }}">{{ $project->name }} Docs</a>
            <a class="active" href="{{ route('support.project', $project) }}">Support</a>
        </nav>
        <form class="docs-search" action="{{ route('docs.search') }}">
            <span>⌕</span>
            <input name="q" placeholder="Search documentation…">
        </form>
    </div>
</header>

<div class="project-switch-bar">
    <a href="{{ route('support.index') }}" class="back-link">&larr; All Projects Support</a>
    <div class="project-selector">
        <label for="project-select">Project:</label>
        <select id="project-select" onchange="window.location.href=this.value">
            @foreach($allProjects as $p)
                <option value="{{ route('support.project', $p) }}" {{ $p->id === $project->id ? 'selected' : '' }}>
                    {{ $p->name }} ({{ $p->category }})
                </option>
            @endforeach
        </select>
    </div>
</div>

<section class="support-hero">
    <div class="support-hero-inner">
        <h1>
            <span class="hero-icon">{{ $project->icon }}</span>
            <span>{{ $project->name }} Support Center</span>
        </h1>
        <p>Need help with {{ $project->name }}? Check out official documentation, browse troubleshooting solutions, or file an issue.</p>

        <form class="support-search-box" action="{{ route('docs.search') }}" method="GET">
            <span>⌕</span>
            <input type="text" name="q" placeholder="Search {{ $project->name }} documentation and solutions...">
        </form>

        <div class="support-status-badge">
            <span class="dot"></span>
            <span>All systems operational &bull; {{ $project->name }}</span>
        </div>
    </div>
</section>

<main class="support-content">
    <div class="support-grid">
        <a href="{{ route('docs.project', $project) }}" class="support-card">
            <span class="support-card-icon">📖</span>
            <h3>Getting started</h3>
            <p>Explore official tutorials, setup instructions, and practical guides for {{ $project->name }}.</p>
            <span class="support-card-link">Browse {{ $project->name }} docs &rarr;</span>
        </a>

        <a href="#faq" class="support-card">
            <span class="support-card-icon">🧩</span>
            <h3>Resolve issues & FAQs</h3>
            <p>Find immediate solutions to common questions, configuration pitfalls, and unexpected behavior.</p>
            <span class="support-card-link">View {{ $project->name }} solutions &rarr;</span>
        </a>

        <a href="{{ $project->github_url ? $project->github_url . '/issues' : 'https://github.com/firoze-hossain' }}" target="_blank" rel="noopener" class="support-card">
            <span class="support-card-icon">📝</span>
            <h3>GitHub issue tracker</h3>
            <p>View open issues, report bugs, track feature requests, or participate in {{ $project->name }} discussions.</p>
            <span class="support-card-link">Open GitHub issues &rarr;</span>
        </a>
    </div>

    <section id="faq" class="faq-section">
        <h2>{{ $project->name }} Frequently Asked Questions</h2>
        <p class="faq-section-subtitle">Common solutions and guidance for developers working with {{ $project->name }}.</p>

        @foreach($faqs as $index => $faq)
            <div class="faq-item">
                <div class="faq-question">{{ $index + 1 }}. {{ $faq['q'] }}</div>
                <div class="faq-answer">{!! nl2br(e($faq['a'])) !!}</div>
            </div>
        @endforeach
    </section>

    <div class="contact-box">
        <div>
            <h3>Need direct assistance with {{ $project->name }}?</h3>
            <p>Connect with the core developers, report reproducible issues, or contribute on GitHub.</p>
        </div>
        @if($project->github_url)
            <a href="{{ $project->github_url }}" target="_blank" rel="noopener" class="btn">View {{ $project->name }} on GitHub ↗</a>
        @else
            <a href="https://github.com/firoze-hossain" target="_blank" rel="noopener" class="btn">Visit GitHub Profile ↗</a>
        @endif
    </div>
</main>

<footer class="docs-footer">
    <span>{{ $project->name }} Support &bull; RozeHub Ecosystem</span>
    <span><a href="{{ route('hub') }}">Home</a> &bull; <a href="{{ route('docs.index') }}">Docs</a> &bull; <a href="{{ route('support.index') }}">All Support</a></span>
</footer>
</body>
</html>
