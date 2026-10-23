<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $project ? $project->name : 'ThunderCall' }} Support Center · RozeHub</title>
    <link rel="icon" href="{{ asset('images/rozehub-icon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/rozehub.css') }}">
    <style>
        .support-hero {
            background: linear-gradient(180deg, #f8fbf9 0%, #edf5f0 100%);
            border-bottom: 1px solid var(--line);
            padding: 70px 20px 60px;
            text-align: center;
        }
        .support-hero-inner {
            max-width: 820px;
            margin: 0 auto;
        }
        .support-hero h1 {
            font-size: 46px;
            letter-spacing: -1.5px;
            margin: 0 0 14px;
            color: var(--ink);
            font-weight: 800;
        }
        .support-hero p {
            font-size: 18px;
            color: var(--muted);
            margin: 0 0 28px;
            line-height: 1.5;
        }
        .support-search-box {
            position: relative;
            max-width: 580px;
            margin: 0 auto 22px;
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
            height: 52px;
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
            padding: 7px 16px;
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
            margin: 50px auto 80px;
            padding: 0 24px;
        }
        .support-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-bottom: 60px;
        }
        @media (max-width: 900px) {
            .support-grid { grid-template-columns: 1fr; }
        }
        .support-card {
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 30px 24px;
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
            font-size: 34px;
            margin-bottom: 16px;
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
            margin: 0 0 20px;
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
            padding: 36px 32px;
            margin-bottom: 50px;
        }
        .faq-section h2 {
            font-size: 26px;
            letter-spacing: -0.8px;
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
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
            padding: 40px 36px;
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
            font-size: 24px;
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
            @if($project)
                <a href="{{ route('docs.project', $project) }}">{{ $project->name }} Docs</a>
            @endif
            <a class="active" href="{{ route('support.index') }}">Support</a>
        </nav>
        <form class="docs-search" action="{{ route('docs.search') }}">
            <span>⌕</span>
            <input name="q" placeholder="Search documentation…">
        </form>
    </div>
</header>

<section class="support-hero">
    <div class="support-hero-inner">
        <h1>{{ $project ? $project->name : 'ThunderCall' }} Support Center</h1>
        <p>Need help? Check out our documentation, browse troubleshooting FAQs, or report an issue.</p>

        <form class="support-search-box" action="{{ route('docs.search') }}" method="GET">
            <span>⌕</span>
            <input type="text" name="q" placeholder="How can we help? (e.g. variables, ssl, clear cache)...">
        </form>

        <div class="support-status-badge">
            <span class="dot"></span>
            <span>All systems operational &bull; Spring Boot Engine v1.0.0</span>
        </div>
    </div>
</section>

<main class="support-content">
    <div class="support-grid">
        <a href="{{ $project ? route('docs.project', $project) : route('docs.index') }}" class="support-card">
            <span class="support-card-icon">📖</span>
            <h3>Getting started</h3>
            <p>Explore official tutorials, request workflow guides, and learn how to construct requests and inspect responses.</p>
            <span class="support-card-link">Browse documentation &rarr;</span>
        </a>

        <a href="#faq" class="support-card">
            <span class="support-card-icon">🧩</span>
            <h3>Resolve issues & FAQs</h3>
            <p>Get instant solutions for common networking errors, self-signed SSL certs, environment variables, and cache issues.</p>
            <span class="support-card-link">View common solutions &rarr;</span>
        </a>

        <a href="{{ $project && $project->github_url ? $project->github_url . '/issues' : 'https://github.com/firoze-hossain/thundercall/issues' }}" target="_blank" rel="noopener" class="support-card">
            <span class="support-card-icon">📝</span>
            <h3>GitHub issue tracker</h3>
            <p>Browse open discussions, submit bug reports, track development progress, or request new capabilities.</p>
            <span class="support-card-link">Open GitHub issues &rarr;</span>
        </a>
    </div>

    <section id="faq" class="faq-section">
        <h2>Frequently Asked Questions</h2>

        <div class="faq-item">
            <div class="faq-question">1. How do I fix "Connection Refused" when testing localhost APIs?</div>
            <div class="faq-answer">
                Verify that your backend service (e.g. Spring Boot, Node.js, FastAPI) is running and bound to <code>0.0.0.0</code> or <code>127.0.0.1</code>. If using Docker, ensure the port mapping (e.g. <code>-p 8080:8080</code>) is properly exposed to the host machine.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">2. How does "Clear Cache and Reload" work?</div>
            <div class="faq-answer">
                Under the <strong>Help &gt; Clear Cache and Reload</strong> menu, ThunderCall safely flushes transient in-memory caches (collection variables, comment threads, schema introspections, and temporary response bodies) and refetches all collections and environments freshly from the backend server. Unsaved edits in open tabs will prompt before being discarded.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">3. How do I use dynamic environment variables?</div>
            <div class="faq-answer">
                Wrap variable names with double curly braces like <code>&#123;&#123;baseUrl&#125;&#125;</code> or <code>&#123;&#123;token&#125;&#125;</code> in URLs, headers, or request bodies. ThunderCall will resolve them hierarchically: Local &rarr; Environment &rarr; Collection &rarr; Global.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">4. Can I import my existing Postman collections?</div>
            <div class="faq-answer">
                Yes! ThunderCall natively supports the Postman Collection v2.1.0 schema. Click <strong>File &gt; Import…</strong> or the top bar <strong>Import</strong> button and select your exported JSON file.
            </div>
        </div>
    </section>

    <div class="contact-box">
        <div>
            <h3>Need direct support from our developers?</h3>
            <p>Our engineering team is actively building and improving ThunderCall. Drop us an issue or contribute on GitHub.</p>
        </div>
        <a href="https://github.com/firoze-hossain/thundercall" target="_blank" rel="noopener" class="btn">Visit GitHub Repository ↗</a>
    </div>
</main>

<footer class="docs-footer">
    <span>ThunderCall Support Center &bull; Powered by RozeHub</span>
    <span><a href="{{ route('hub') }}">RozeHub Home</a> &bull; <a href="{{ route('docs.index') }}">Documentation</a></span>
</footer>
</body>
</html>
