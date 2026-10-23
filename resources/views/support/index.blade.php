<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Support Center · RozeHub</title>
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
        .section-header {
            margin-bottom: 28px;
        }
        .section-header h2 {
            font-size: 28px;
            letter-spacing: -0.8px;
            margin: 0 0 8px;
            color: var(--ink);
        }
        .section-header p {
            font-size: 15px;
            color: var(--muted);
            margin: 0;
        }
        .projects-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 22px;
            margin-bottom: 60px;
        }
        @media (max-width: 960px) {
            .projects-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 640px) {
            .projects-grid { grid-template-columns: 1fr; }
        }
        .project-support-card {
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 26px 22px;
            text-decoration: none;
            color: var(--ink);
            display: flex;
            flex-direction: column;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }
        .project-support-card:hover {
            transform: translateY(-4px);
            border-color: #2f9d76;
            box-shadow: 0 12px 28px rgba(18, 60, 53, 0.08);
        }
        .card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }
        .card-icon {
            width: 42px;
            height: 42px;
            background: var(--dark);
            color: var(--mint);
            font-weight: 800;
            font-size: 20px;
            display: grid;
            place-items: center;
            border-radius: 8px;
        }
        .card-category {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--muted);
            background: rgba(18, 60, 53, 0.06);
            padding: 4px 10px;
            border-radius: 12px;
        }
        .project-support-card h3 {
            font-size: 20px;
            margin: 0 0 8px;
            font-weight: 700;
            letter-spacing: -0.4px;
        }
        .project-support-card p {
            font-size: 13px;
            color: var(--muted);
            line-height: 1.55;
            margin: 0 0 18px;
            flex: 1;
        }
        .card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 1px solid #eef2ef;
            padding-top: 14px;
            font-size: 13px;
            font-weight: 700;
            color: #207e5d;
        }
        .card-footer .docs-count {
            font-size: 11px;
            color: var(--muted);
            font-weight: normal;
        }
        .support-global-resources {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 50px;
        }
        @media (max-width: 800px) {
            .support-global-resources { grid-template-columns: 1fr; }
        }
        .resource-box {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 24px 20px;
            display: flex;
            align-items: flex-start;
            gap: 16px;
        }
        .resource-box .res-icon {
            font-size: 28px;
        }
        .resource-box h4 {
            margin: 0 0 6px;
            font-size: 16px;
            font-weight: 700;
        }
        .resource-box p {
            margin: 0 0 10px;
            font-size: 13px;
            color: var(--muted);
            line-height: 1.5;
        }
        .resource-box a {
            color: #207e5d;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
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
            <a href="{{ route('docs.index') }}">Documentation</a>
            <a href="{{ route('hub') }}#releases">Releases</a>
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
        <h1>RozeHub Support Center</h1>
        <p>Choose a project below for dedicated documentation, troubleshooting solutions, FAQs, and issue trackers.</p>

        <form class="support-search-box" action="{{ route('docs.search') }}" method="GET">
            <span>⌕</span>
            <input type="text" name="q" placeholder="Search documentation and solutions across all projects...">
        </form>

        <div class="support-status-badge">
            <span class="dot"></span>
            <span>All systems operational &bull; RozeHub Developer Platform</span>
        </div>
    </div>
</section>

<main class="support-content">
    <div class="section-header">
        <h2>Select a Project for Dedicated Support</h2>
        <p>Each software project in the RozeHub ecosystem maintains its own guides, FAQs, and GitHub issue trackers.</p>
    </div>

    <div class="projects-grid">
        @foreach($projects as $project)
            <a href="{{ route('support.project', $project) }}" class="project-support-card">
                <div class="card-top">
                    <span class="card-icon">{{ $project->icon }}</span>
                    <span class="card-category">{{ $project->category }}</span>
                </div>
                <h3>{{ $project->name }}</h3>
                <p>{{ $project->tagline }}</p>
                <div class="card-footer">
                    <span>Open Support &rarr;</span>
                    @if($project->published_docs_count > 0)
                        <span class="docs-count">{{ $project->published_docs_count }} doc pages</span>
                    @endif
                </div>
            </a>
        @endforeach
    </div>

    <div class="section-header">
        <h2>Global Ecosystem Resources</h2>
        <p>Cross-project development tooling, open source channels, and downloads.</p>
    </div>

    <div class="support-global-resources">
        <div class="resource-box">
            <span class="res-icon">📚</span>
            <div>
                <h4>Documentation Library</h4>
                <p>Browse full versioned architecture manuals, quickstarts, and API guides.</p>
                <a href="{{ route('docs.index') }}">Visit Documentation &rarr;</a>
            </div>
        </div>

        <div class="resource-box">
            <span class="res-icon">📦</span>
            <div>
                <h4>Releases & Downloads</h4>
                <p>Download release binaries, checksums, and update notes for all apps.</p>
                <a href="{{ route('hub') }}#releases">View All Releases &rarr;</a>
            </div>
        </div>

        <div class="resource-box">
            <span class="res-icon">💻</span>
            <div>
                <h4>GitHub Organization</h4>
                <p>Explore source code repositories, open pull requests, and file issues.</p>
                <a href="https://github.com/firoze-hossain" target="_blank" rel="noopener">Visit GitHub ↗</a>
            </div>
        </div>
    </div>
</main>

<footer class="docs-footer">
    <span>RozeHub Support Center &bull; Developer Tooling &amp; Systems</span>
    <span><a href="{{ route('hub') }}">Home</a> &bull; <a href="{{ route('docs.index') }}">Docs</a> &bull; <a href="{{ route('support.index') }}">Support</a></span>
</footer>
</body>
</html>
