@extends('admin.layout')

@php
    $heading = 'GitHub · ' . $project->name;
    $title = $project->name . ' · GitHub Ecosystem · RozeHub Admin';
@endphp

@section('content')
<style>
    /* Scoped GitHub Ecosystem Dashboard Styles */
    .gh-shell {
        display: flex;
        flex-direction: column;
        gap: 22px;
    }

    /* Page Header */
    .gh-header-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
        padding-bottom: 4px;
    }
    .gh-breadcrumbs {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: var(--admin-muted);
        margin-bottom: 6px;
    }
    .gh-breadcrumbs a {
        color: var(--admin-muted);
        text-decoration: none;
        transition: color 0.15s;
    }
    .gh-breadcrumbs a:hover {
        color: var(--admin-ink);
        text-decoration: underline;
    }
    .gh-breadcrumbs .sep {
        color: #b8c4be;
    }
    .gh-title-row {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .gh-project-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: #eef4f0;
        color: #175e45;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 16px;
        border: 1px solid #d4e2d9;
    }
    .gh-title-row h2 {
        margin: 0;
        font-size: 26px;
        font-weight: 800;
        letter-spacing: -0.6px;
        color: var(--admin-ink);
    }
    .gh-subtitle {
        margin: 4px 0 0;
        font-size: 13px;
        color: var(--admin-muted);
    }
    .gh-header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .gh-btn-sync {
        background: #10221e;
        color: #fff;
        border: 0;
        padding: 10px 18px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.15s ease;
        box-shadow: 0 4px 12px rgba(16,34,30,0.12);
    }
    .gh-btn-sync:hover {
        background: #183831;
        transform: translateY(-1px);
    }
    .gh-btn-sync svg {
        color: #66ddae;
    }
    .gh-btn-secondary {
        background: #ffffff;
        color: var(--admin-ink);
        border: 1px solid var(--admin-line);
        padding: 9px 15px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.15s;
    }
    .gh-btn-secondary:hover {
        background: #f8faf8;
        border-color: #cbd7d0;
    }

    /* Repository Overview Hero Card */
    .gh-repo-hero {
        background: #ffffff;
        border: 1px solid var(--admin-line);
        border-radius: 14px;
        padding: 24px 28px;
        box-shadow: 0 4px 20px rgba(16,34,30,0.04);
        position: relative;
    }
    .gh-repo-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }
    .gh-repo-name-group {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .gh-octo-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        background: #11221e;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .gh-repo-title {
        font-size: 20px;
        font-weight: 800;
        color: var(--admin-ink);
        letter-spacing: -0.3px;
        margin: 0 0 4px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .gh-copy-chip {
        background: #f1f5f2;
        border: 1px solid #dce5df;
        border-radius: 4px;
        padding: 2px 7px;
        font-size: 11px;
        font-family: ui-monospace, monospace;
        color: #275244;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.12s;
    }
    .gh-copy-chip:hover {
        background: #e2ede6;
        border-color: #cbd9d0;
    }
    .gh-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 12px;
        background: #eaf7f0;
        color: #126345;
        border: 1px solid #bce4d0;
    }
    .gh-pulse-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #25a878;
        display: inline-block;
    }
    .gh-repo-desc {
        font-size: 14px;
        color: #4b5c56;
        line-height: 1.55;
        margin: 0 0 16px;
        max-width: 820px;
    }
    .gh-repo-tags {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }
    .gh-tag-pill {
        font-size: 11px;
        background: #f2f7f4;
        color: #1a5845;
        border: 1px solid #dbe6df;
        border-radius: 12px;
        padding: 3px 10px;
        font-weight: 600;
    }
    .gh-repo-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid #edf1ee;
        padding-top: 14px;
        font-size: 12px;
        color: var(--admin-muted);
        flex-wrap: wrap;
        gap: 12px;
    }
    .gh-footer-meta {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }
    .gh-meta-segment {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* Metrics Grid (4 Stat Cards) */
    .gh-metrics-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
    }
    .gh-stat-card {
        background: #ffffff;
        border: 1px solid var(--admin-line);
        border-radius: 12px;
        padding: 18px 20px;
        box-shadow: 0 2px 10px rgba(16,34,30,0.03);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform 0.15s, border-color 0.15s;
    }
    .gh-stat-card:hover {
        transform: translateY(-2px);
        border-color: #cbd7cf;
    }
    .gh-stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 20px;
    }
    .gh-stat-icon.star { background: #fef7ec; color: #d97706; border: 1px solid #fde4ba; }
    .gh-stat-icon.fork { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
    .gh-stat-icon.issue { background: #fdf2f8; color: #db2777; border: 1px solid #fbcfe8; }
    .gh-stat-icon.lang { background: #f5f3ff; color: #7c3aed; border: 1px solid #ddd6fe; }
    .gh-stat-body small {
        display: block;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--admin-muted);
        margin-bottom: 2px;
    }
    .gh-stat-body strong {
        display: block;
        font-size: 22px;
        font-weight: 800;
        color: var(--admin-ink);
        letter-spacing: -0.5px;
        line-height: 1.1;
    }
    .gh-stat-body span {
        display: block;
        font-size: 11px;
        color: #7d8e87;
        margin-top: 3px;
    }

    /* Two-Column Grid */
    .gh-dash-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    .gh-card {
        background: #ffffff;
        border: 1px solid var(--admin-line);
        border-radius: 14px;
        box-shadow: 0 4px 18px rgba(16,34,30,0.04);
        padding: 22px 24px;
        display: flex;
        flex-direction: column;
    }
    .gh-card-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid #edf1ee;
    }
    .gh-card-head h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 800;
        color: var(--admin-ink);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .gh-count-pill {
        font-size: 11px;
        font-weight: 700;
        background: #f0f4f1;
        color: #274b3e;
        padding: 2px 8px;
        border-radius: 10px;
        border: 1px solid #d9e3dd;
    }

    /* Contributor Row */
    .gh-contrib-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .gh-contrib-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 12px;
        background: #fafbfa;
        border: 1px solid #eef2ef;
        border-radius: 8px;
        transition: background 0.12s;
    }
    .gh-contrib-row:hover {
        background: #f2f7f4;
        border-color: #dbe7df;
    }
    .gh-contrib-user {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .gh-contrib-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        object-fit: cover;
        background: #e6eee9;
        border: 1px solid #d2ded7;
    }
    .gh-contrib-avatar-fallback {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #175e45;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 800;
    }
    .gh-contrib-name {
        font-size: 13px;
        font-weight: 700;
        color: var(--admin-ink);
        text-decoration: none;
        display: block;
    }
    .gh-contrib-name:hover {
        color: #175e45;
        text-decoration: underline;
    }
    .gh-contrib-commits {
        text-align: right;
    }
    .gh-commits-count {
        font-size: 13px;
        font-weight: 800;
        color: #175e45;
    }
    .gh-commits-label {
        font-size: 10px;
        color: var(--admin-muted);
        display: block;
    }

    /* Release Row */
    .gh-release-item {
        padding: 12px 14px;
        border: 1px solid #eef2ef;
        background: #fafbfa;
        border-radius: 8px;
        margin-bottom: 10px;
    }
    .gh-release-item:last-child {
        margin-bottom: 0;
    }
    .gh-release-item-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 4px;
    }
    .gh-tag-badge {
        font-size: 12px;
        font-weight: 800;
        color: var(--admin-ink);
        background: #eef5f1;
        border: 1px solid #d4e4db;
        padding: 2px 7px;
        border-radius: 5px;
        font-family: ui-monospace, monospace;
        text-decoration: none;
    }
    .gh-release-title {
        font-size: 13px;
        font-weight: 600;
        color: #3b4e47;
        margin: 6px 0 0;
    }

    /* Issue & PR Rows */
    .gh-item-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 10px 12px;
        border-bottom: 1px solid #f0f4f1;
        gap: 12px;
    }
    .gh-item-row:last-child {
        border-bottom: 0;
    }
    .gh-item-main {
        flex: 1;
        min-width: 0;
    }
    .gh-item-link {
        font-size: 13px;
        font-weight: 600;
        color: var(--admin-ink);
        text-decoration: none;
        display: block;
        line-height: 1.4;
    }
    .gh-item-link:hover {
        color: #175e45;
        text-decoration: underline;
    }
    .gh-item-sub {
        font-size: 11px;
        color: var(--admin-muted);
        margin-top: 4px;
    }

    /* Documentation Callout Card */
    .gh-doc-card {
        background: linear-gradient(135deg, #f7faf8 0%, #edf5f0 100%);
        border: 1px solid #cbe1d4;
        border-radius: 14px;
        padding: 22px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }
    .gh-doc-info {
        flex: 1;
        min-width: 280px;
    }
    .gh-doc-info h3 {
        margin: 0 0 6px;
        font-size: 16px;
        font-weight: 800;
        color: #11382c;
    }
    .gh-doc-info p {
        margin: 0;
        font-size: 13px;
        line-height: 1.55;
        color: #445952;
    }

    /* Webhook Integration Card */
    .gh-webhook-card {
        background: #ffffff;
        border: 1px solid var(--admin-line);
        border-radius: 14px;
        padding: 20px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }
    .gh-webhook-info h4 {
        margin: 0 0 4px;
        font-size: 14px;
        font-weight: 800;
        color: var(--admin-ink);
    }
    .gh-webhook-info p {
        margin: 0;
        font-size: 12px;
        color: var(--admin-muted);
    }
    .gh-webhook-url-box {
        display: flex;
        align-items: center;
        background: #f3f6f4;
        border: 1px solid #dce5df;
        border-radius: 6px;
        padding: 6px 10px;
        gap: 8px;
        font-family: ui-monospace, monospace;
        font-size: 11px;
        color: #274b3e;
    }

    /* Empty States */
    .gh-empty-box {
        text-align: center;
        padding: 32px 20px;
        color: var(--admin-muted);
    }
    .gh-empty-icon {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #f0f4f1;
        color: #65776f;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        margin-bottom: 10px;
    }
    .gh-empty-box p {
        margin: 0;
        font-size: 13px;
        color: var(--admin-muted);
    }

    /* Toast */
    .gh-toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: #10221e;
        color: #fff;
        padding: 10px 18px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        box-shadow: 0 8px 24px rgba(0,0,0,0.18);
        z-index: 1000;
        opacity: 0;
        transform: translateY(10px);
        transition: all 0.2s ease;
        pointer-events: none;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .gh-toast.show {
        opacity: 1;
        transform: translateY(0);
    }

    @media (max-width: 900px) {
        .gh-metrics-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .gh-dash-grid {
            grid-template-columns: 1fr;
        }
    }
    @media (max-width: 550px) {
        .gh-metrics-grid {
            grid-template-columns: 1fr;
        }
        .gh-header-bar {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<div class="gh-shell">
    <!-- Header Bar with Breadcrumb and Actions -->
    <div class="gh-header-bar">
        <div>
            <div class="gh-breadcrumbs">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <span class="sep">/</span>
                <a href="{{ route('admin.projects.index') }}">Software</a>
                <span class="sep">/</span>
                <a href="{{ route('admin.projects.edit', $project) }}">{{ $project->name }}</a>
                <span class="sep">/</span>
                <strong style="color:var(--admin-ink);">GitHub Intelligence</strong>
            </div>
            <div class="gh-title-row">
                <div class="gh-project-icon">
                    {{ strtoupper(substr($project->name, 0, 1)) }}
                </div>
                <div>
                    <h2>{{ $project->name }}</h2>
                    <p class="gh-subtitle">Repository intelligence, live activity, release tracking, and documentation sync.</p>
                </div>
            </div>
        </div>

        <div class="gh-header-actions">
            <a class="gh-btn-secondary" href="{{ route('admin.projects.edit', $project) }}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                Project settings
            </a>

            @if($project->github_url)
                <form method="POST" action="{{ route('admin.github.sync', $project) }}" style="margin:0;">
                    @csrf
                    <button type="submit" class="gh-btn-sync">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
                        Sync GitHub
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if(!$project->github_url)
        <div class="admin-alert error" style="border-radius:10px;">
            <strong>No GitHub repository configured.</strong> Please add a GitHub repository URL in <a href="{{ route('admin.projects.edit', $project) }}" style="text-decoration:underline;">Project Settings</a> to enable sync.
        </div>
    @else

        <!-- Repository Hero Card -->
        <section class="gh-repo-hero">
            <div class="gh-repo-top">
                <div class="gh-repo-name-group">
                    <div class="gh-octo-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                    </div>
                    <div>
                        <h3 class="gh-repo-title">
                            <span>{{ $repo?->full_name ?: str_replace('https://github.com/', '', rtrim($project->github_url, '/')) }}</span>
                            <button type="button" class="gh-copy-chip" onclick="copyRepo('{{ $repo?->full_name ?: $project->github_url }}')">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                Copy
                            </button>
                        </h3>
                        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                            <span class="gh-status-badge">
                                <span class="gh-pulse-dot"></span>
                                Connected &amp; Synced
                            </span>
                            @if($repo?->default_branch)
                                <span style="font-size:11px; color:var(--admin-muted); font-family:ui-monospace, monospace; background:#f4f7f5; padding:2px 6px; border-radius:4px; border:1px solid #dce5df;">
                                    branch: <strong>{{ $repo->default_branch }}</strong>
                                </span>
                            @endif
                            @if($repo?->is_archived)
                                <span style="font-size:11px; background:#fff1f0; color:#b03426; padding:2px 7px; border-radius:4px; font-weight:700;">Archived</span>
                            @endif
                            @if($repo?->is_fork)
                                <span style="font-size:11px; background:#f0f4ff; color:#2b5ca8; padding:2px 7px; border-radius:4px; font-weight:700;">Fork</span>
                            @endif
                        </div>
                    </div>
                </div>

                <a class="gh-btn-secondary" href="{{ $project->github_url }}" target="_blank" rel="noopener">
                    Open on GitHub ↗
                </a>
            </div>

            <p class="gh-repo-desc">
                {{ $repo?->description ?: ($project->description ?: 'No repository description found on GitHub.') }}
            </p>

            @if($repo && !empty($repo->topics))
                <div class="gh-repo-tags">
                    @foreach($repo->topics as $topic)
                        <span class="gh-tag-pill">#{{ $topic }}</span>
                    @endforeach
                </div>
            @endif

            <div class="gh-repo-footer">
                <div class="gh-footer-meta">
                    <span class="gh-meta-segment">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        Last synchronized: <strong style="color:var(--admin-ink);">{{ $repo?->synced_at ? $repo->synced_at->diffForHumans() : 'Never' }}</strong>
                        @if($repo?->synced_at)
                            <span style="color:#8a9a93;">({{ $repo->synced_at->format('M j, Y H:i:s') }})</span>
                        @endif
                    </span>
                    @if($repo?->license_name)
                        <span class="gh-meta-segment">
                            ⚖ License: <strong>{{ $repo->license_name }}</strong>
                        </span>
                    @endif
                </div>

                <span style="font-size:11px; color:#6b7e76;">
                    API Target: <code>api.github.com/repos/{{ $repo?->full_name ?: '' }}</code>
                </span>
            </div>
        </section>

        @if($repo)
            <!-- Key Repository Metrics Grid (4 Stat Cards) -->
            <section class="gh-metrics-grid">
                <div class="gh-stat-card">
                    <div class="gh-stat-icon star">★</div>
                    <div class="gh-stat-body">
                        <small>Stars</small>
                        <strong>{{ number_format($repo->stars) }}</strong>
                        <span>Stargazers</span>
                    </div>
                </div>

                <div class="gh-stat-card">
                    <div class="gh-stat-icon fork">⑂</div>
                    <div class="gh-stat-body">
                        <small>Forks</small>
                        <strong>{{ number_format($repo->forks) }}</strong>
                        <span>Community forks</span>
                    </div>
                </div>

                <div class="gh-stat-card">
                    <div class="gh-stat-icon issue">⊙</div>
                    <div class="gh-stat-body">
                        <small>Open Issues</small>
                        <strong>{{ number_format($repo->open_issues) }}</strong>
                        <span>Tracked discussions</span>
                    </div>
                </div>

                <div class="gh-stat-card">
                    <div class="gh-stat-icon lang">&lt;/&gt;</div>
                    <div class="gh-stat-body">
                        <small>Language</small>
                        <strong>{{ $repo->language ?: 'Multi-language' }}</strong>
                        <span>Primary language</span>
                    </div>
                </div>
            </section>

            <!-- Two-Column Activity & Intelligence Grid -->
            <div class="gh-dash-grid">
                <!-- Left Column -->
                <div style="display:flex; flex-direction:column; gap:20px;">

                    <!-- Contributors Card -->
                    <div class="gh-card">
                        <div class="gh-card-head">
                            <h3>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                Contributors
                            </h3>
                            <span class="gh-count-pill">{{ $repo->contributors->count() }} members</span>
                        </div>

                        <div class="gh-contrib-list">
                            @forelse($repo->contributors as $c)
                                <div class="gh-contrib-row">
                                    <div class="gh-contrib-user">
                                        @if($c->avatar_url)
                                            <img src="{{ $c->avatar_url }}" alt="{{ $c->login }}" class="gh-contrib-avatar">
                                        @else
                                            <div class="gh-contrib-avatar-fallback">{{ strtoupper(substr($c->login, 0, 1)) }}</div>
                                        @endif
                                        <div>
                                            <a href="{{ $c->html_url ?: 'https://github.com/'.$c->login }}" target="_blank" rel="noopener" class="gh-contrib-name">
                                                {{ $c->login }}
                                            </a>
                                            <span style="font-size:11px; color:var(--admin-muted);">GitHub Contributor</span>
                                        </div>
                                    </div>
                                    <div class="gh-contrib-commits">
                                        <div class="gh-commits-count">{{ number_format($c->contributions) }}</div>
                                        <span class="gh-commits-label">commits</span>
                                    </div>
                                </div>
                            @empty
                                <div class="gh-empty-box">
                                    <div class="gh-empty-icon">👥</div>
                                    <p>No contributors found yet. Sync to refresh data.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Open Issues Card -->
                    <div class="gh-card">
                        <div class="gh-card-head">
                            <h3>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                Open Issues
                            </h3>
                            @php($openIssues = $repo->issues->where('state', 'open'))
                            <span class="gh-count-pill">{{ $openIssues->count() }} open</span>
                        </div>

                        @forelse($openIssues as $i)
                            <div class="gh-item-row">
                                <div class="gh-item-main">
                                    <a href="{{ $i->html_url }}" target="_blank" rel="noopener" class="gh-item-link">
                                        #{{ $i->number }} · {{ $i->title }}
                                    </a>
                                    <div class="gh-item-sub">
                                        Opened by <strong>{{ $i->author_login ?: 'community member' }}</strong> · {{ $i->opened_at?->diffForHumans() }}
                                    </div>
                                </div>
                                <span style="font-size:10px; font-weight:700; background:#fef2f2; color:#b91c1c; padding:2px 7px; border-radius:4px;">Open</span>
                            </div>
                        @empty
                            <div class="gh-empty-box">
                                <div class="gh-empty-icon" style="background:#eaf7f0; color:#176a4b;">✓</div>
                                <p>No open issues. All issues in this repository are closed or resolved!</p>
                            </div>
                        @endforelse
                    </div>

                </div>

                <!-- Right Column -->
                <div style="display:flex; flex-direction:column; gap:20px;">

                    <!-- Latest Releases Card -->
                    <div class="gh-card">
                        <div class="gh-card-head">
                            <h3>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
                                Latest Releases
                            </h3>
                            <span class="gh-count-pill">{{ $repo->releases->count() }} releases</span>
                        </div>

                        @forelse($repo->releases as $r)
                            <div class="gh-release-item">
                                <div class="gh-release-item-top">
                                    <a href="{{ $r->html_url }}" target="_blank" rel="noopener" class="gh-tag-badge">
                                        {{ $r->tag_name }}
                                    </a>
                                    <span style="font-size:11px; color:var(--admin-muted);">
                                        {{ $r->published_at_github?->format('M j, Y') ?: 'Draft' }}
                                    </span>
                                </div>
                                <div class="gh-release-title">{{ $r->name ?: $r->tag_name }}</div>
                                @if($r->prerelease)
                                    <span style="font-size:10px; background:#fff7ed; color:#c2410c; padding:1px 6px; border-radius:3px; font-weight:700; display:inline-block; margin-top:4px;">Pre-release</span>
                                @endif
                            </div>
                        @empty
                            <div class="gh-empty-box">
                                <div class="gh-empty-icon">🏷</div>
                                <p>No GitHub releases published yet.</p>
                                <span style="font-size:11px; color:#889992; display:block; margin-top:4px;">Releases tagged on GitHub will show here automatically.</span>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pull Requests Card -->
                    <div class="gh-card">
                        <div class="gh-card-head">
                            <h3>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="18" r="3"></circle><circle cx="6" cy="6" r="3"></circle><path d="M13 6h3a2 2 0 0 1 2 2v7"></path><line x1="6" y1="9" x2="6" y2="21"></line></svg>
                                Pull Requests
                            </h3>
                            @php($openPRs = $repo->pullRequests->where('state', 'open'))
                            <span class="gh-count-pill">{{ $openPRs->count() }} open</span>
                        </div>

                        @forelse($openPRs as $pr)
                            <div class="gh-item-row">
                                <div class="gh-item-main">
                                    <a href="{{ $pr->html_url }}" target="_blank" rel="noopener" class="gh-item-link">
                                        #{{ $pr->number }} · {{ $pr->title }}
                                    </a>
                                    <div class="gh-item-sub">
                                        By <strong>{{ $pr->author_login ?: 'contributor' }}</strong> · {{ $pr->opened_at?->diffForHumans() }}
                                    </div>
                                </div>
                                <span style="font-size:10px; font-weight:700; background:#f0fdf4; color:#15803d; padding:2px 7px; border-radius:4px;">PR</span>
                            </div>
                        @empty
                            <div class="gh-empty-box">
                                <div class="gh-empty-icon" style="background:#eaf7f0; color:#176a4b;">✓</div>
                                <p>No open pull requests.</p>
                                <span style="font-size:11px; color:#889992; display:block; margin-top:4px;">Code contributions will appear here when opened on GitHub.</span>
                            </div>
                        @endforelse
                    </div>

                </div>
            </div>

            <!-- Documentation API Banner -->
            <section class="gh-doc-card">
                <div class="gh-doc-info">
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
                        <span style="font-size:18px;">📖</span>
                        <h3 style="margin:0;">Interactive Documentation Sync</h3>
                    </div>
                    <p>Read, preview, and update repository documentation directly through GitHub's Contents API. Every change creates an authenticated Git commit right from the RozeHub studio.</p>
                </div>
                <a class="gh-btn-sync" href="{{ route('admin.github.documentation', $project) }}" style="text-decoration:none; padding:11px 20px;">
                    Edit Documentation on GitHub →
                </a>
            </section>

            <!-- Real-time Webhook Configuration -->
            <section class="gh-webhook-card">
                <div class="gh-webhook-info">
                    <h4>Real-time Webhook Synchronization</h4>
                    <p>RozeHub can automatically sync releases, commits, and issues in real time whenever someone pushes to GitHub.</p>
                </div>
                <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                    <div class="gh-webhook-url-box">
                        <span>{{ route('github.webhook') }}</span>
                        <button type="button" style="background:transparent; border:0; cursor:pointer; color:#175e45; padding:0; display:flex;" onclick="copyRepo('{{ route('github.webhook') }}')" title="Copy Webhook URL">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                        </button>
                    </div>
                    <span style="font-size:11px; font-weight:700; padding:4px 8px; border-radius:4px; {{ config('github.webhook_secret') ? 'background:#eaf7f0; color:#166542;' : 'background:#fef3c7; color:#92400e;' }}">
                        {{ config('github.webhook_secret') ? 'Secret Configured' : 'Secret Recommended' }}
                    </span>
                </div>
            </section>
        @endif
    @endif
</div>

<!-- Simple Toast Notification -->
<div class="gh-toast" id="ghToast">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
    <span id="ghToastText">Copied to clipboard!</span>
</div>

<script>
    function copyRepo(text) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(() => showGhToast());
        } else {
            const el = document.createElement('textarea');
            el.value = text;
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);
            showGhToast();
        }
    }

    function showGhToast(msg) {
        const t = document.getElementById('ghToast');
        if (msg) document.getElementById('ghToastText').textContent = msg;
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 2200);
    }
</script>
@endsection
