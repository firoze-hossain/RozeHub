@extends('admin.layout')

@php
    $heading = $project->name . ' Documentation';
    $title = $project->name . ' Documentation Workspace · RozeHub Admin';
    $totalSections = $project->documentationSections->count();
    $totalArticles = $project->documentationSections->sum('pages_count');
    $publishedArticles = $project->documentationSections->sum(fn($s) => $s->pages->where('is_published', true)->count());
    $draftArticles = $totalArticles - $publishedArticles;
    $totalReleases = $project->releases->count();
    $logoPath = file_exists(public_path('images/projects/'.$project->slug.'.png')) ? asset('images/projects/'.$project->slug.'.png') : null;
@endphp

@section('content')
<style>
    /* Scoped Documentation Workspace Styles */
    .doc-shell {
        display: flex;
        flex-direction: column;
        gap: 22px;
    }

    /* Page Header */
    .doc-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }
    .doc-breadcrumbs {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: var(--admin-muted);
        margin-bottom: 6px;
    }
    .doc-breadcrumbs a {
        color: var(--admin-muted);
        text-decoration: none;
    }
    .doc-breadcrumbs a:hover {
        color: var(--admin-ink);
        text-decoration: underline;
    }
    .doc-breadcrumbs .sep {
        color: #b8c4be;
    }
    .doc-title-row {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .doc-project-avatar {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        background: #ffffff;
        border: 1px solid #d4ded8;
        padding: 5px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        object-fit: contain;
        display: block;
        flex-shrink: 0;
    }
    .doc-avatar-fallback {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        background: #e6f3ec;
        color: #17654b;
        font-size: 20px;
        font-weight: 800;
        display: grid;
        place-items: center;
        border: 1px solid #c8ded2;
        flex-shrink: 0;
    }
    .doc-title-row h2 {
        margin: 0;
        font-size: 26px;
        font-weight: 800;
        letter-spacing: -0.6px;
        color: var(--admin-ink);
        line-height: 1.2;
    }
    .doc-subtitle {
        margin: 4px 0 0;
        font-size: 13px;
        color: var(--admin-muted);
    }
    .doc-header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .doc-btn-primary {
        background: #10221e;
        color: #ffffff;
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
        text-decoration: none;
    }
    .doc-btn-primary:hover {
        background: #183831;
        transform: translateY(-1px);
        color: #ffffff;
    }
    .doc-btn-primary svg {
        color: #66ddae;
    }
    .doc-btn-ghost {
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
    .doc-btn-ghost:hover {
        background: #f7faf8;
        border-color: #adbdb4;
        color: #0b1a16;
    }

    /* Top Intelligence Strips */
    .doc-strips-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }
    .doc-banner-card {
        background: #ffffff;
        border: 1px solid var(--admin-line);
        border-radius: 10px;
        padding: 18px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 18px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    }
    .doc-banner-content {
        min-width: 0;
    }
    .doc-banner-kicker {
        font-size: 9px;
        letter-spacing: 1.2px;
        font-weight: 800;
        text-transform: uppercase;
        color: #17654b;
        margin-bottom: 4px;
        display: block;
    }
    .doc-banner-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--admin-ink);
        margin: 0 0 5px;
    }
    .doc-banner-desc {
        font-size: 12px;
        color: var(--admin-muted);
        margin: 0;
        line-height: 1.5;
    }
    .doc-banner-action {
        flex-shrink: 0;
    }
    .doc-banner-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #10221e;
        color: #fff;
        padding: 8px 14px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.15s;
        white-space: nowrap;
    }
    .doc-banner-btn:hover {
        background: #173b33;
        color: #fff;
    }
    .doc-release-pills {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
        margin-top: 8px;
    }
    .doc-release-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 8px;
        background: #f0f7f3;
        border: 1px solid #cbe0d4;
        border-radius: 5px;
        font-size: 11px;
        font-weight: 700;
        color: #166442;
    }
    .doc-release-chip small {
        font-size: 8px;
        letter-spacing: 0.8px;
        color: #6a8277;
        font-weight: 800;
        text-transform: uppercase;
    }

    /* Stats Strip (4 Metric Boxes) */
    .doc-stats-strip {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
    }
    .doc-stat-box {
        background: #ffffff;
        border: 1px solid var(--admin-line);
        border-radius: 8px;
        padding: 16px 18px;
        display: flex;
        flex-direction: column;
        gap: 3px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.02);
    }
    .doc-stat-box small {
        font-size: 9px;
        letter-spacing: 1px;
        font-weight: 800;
        text-transform: uppercase;
        color: #70827b;
    }
    .doc-stat-box strong {
        font-size: 24px;
        font-weight: 900;
        color: var(--admin-ink);
        letter-spacing: -0.5px;
    }
    .doc-stat-box span {
        font-size: 11px;
        color: #667972;
    }

    /* Main Workspace 2-Column Layout */
    .doc-workspace-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 340px;
        gap: 22px;
        align-items: start;
    }

    /* Left Column: Sections & Articles */
    .doc-sections-main {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }
    .doc-sections-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 4px;
    }
    .doc-sections-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 800;
        letter-spacing: -0.3px;
        color: var(--admin-ink);
    }
    .doc-sections-header p {
        margin: 2px 0 0;
        font-size: 12px;
        color: var(--admin-muted);
    }

    /* Section Block Container */
    .doc-section-card {
        background: #ffffff;
        border: 1px solid var(--admin-line);
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        overflow: hidden;
        transition: border-color 0.15s ease;
    }
    .doc-section-card:hover {
        border-color: #b5c7bd;
    }

    /* Section Top Bar */
    .doc-section-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
        background: #fbfdfc;
        border-bottom: 1px solid #e5ede8;
        gap: 16px;
        flex-wrap: wrap;
    }
    .doc-section-info {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }
    .doc-section-icon-badge {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: #e8f4ed;
        color: #17654b;
        font-size: 16px;
        font-weight: 800;
        display: grid;
        place-items: center;
        border: 1px solid #c9ded2;
        flex-shrink: 0;
    }
    .doc-section-text {
        min-width: 0;
    }
    .doc-section-text h4 {
        margin: 0;
        font-size: 16px;
        font-weight: 800;
        color: var(--admin-ink);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .doc-section-count-badge {
        font-size: 10px;
        font-weight: 700;
        background: #eef3f0;
        color: #4a6157;
        padding: 2px 7px;
        border-radius: 12px;
    }
    .doc-section-text p {
        margin: 2px 0 0;
        font-size: 12px;
        color: #637770;
    }
    .doc-section-controls {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .doc-control-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.15s;
        border: 1px solid transparent;
    }
    .doc-control-btn.add {
        background: #17654b;
        color: #ffffff;
    }
    .doc-control-btn.add:hover {
        background: #12503b;
        color: #ffffff;
    }
    .doc-control-btn.settings {
        background: #ffffff;
        color: var(--admin-ink);
        border: 1px solid #cfdad3;
    }
    .doc-control-btn.settings:hover {
        background: #f3f7f5;
        border-color: #adbdb4;
    }

    /* Section Edit Drawer (Hidden by default) */
    .doc-section-edit-drawer {
        background: #f6faf7;
        border-bottom: 1px solid #dde7e0;
        padding: 18px 20px;
        animation: fadeIn 0.15s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-4px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .doc-edit-drawer-title {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #17654b;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .doc-edit-form-grid {
        display: grid;
        grid-template-columns: 2fr 100px 90px;
        gap: 12px;
        align-items: end;
    }
    .doc-edit-form-grid .full-span {
        grid-column: 1 / -1;
    }
    .doc-field-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    .doc-field-group label {
        font-size: 11px;
        font-weight: 700;
        color: var(--admin-ink);
    }
    .doc-input-styled {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #c9ded2;
        border-radius: 6px;
        font-size: 12px;
        color: var(--admin-ink);
        background: #ffffff;
        box-sizing: border-box;
        transition: border-color 0.15s;
    }
    .doc-input-styled:focus {
        border-color: #17654b;
        outline: none;
        box-shadow: 0 0 0 2px rgba(23,101,75,0.12);
    }
    .doc-edit-drawer-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 14px;
        padding-top: 12px;
        border-top: 1px dashed #d5e3da;
    }
    .doc-btn-save-sm {
        background: #10221e;
        color: #ffffff;
        border: 0;
        padding: 7px 14px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background 0.15s;
    }
    .doc-btn-save-sm:hover {
        background: #174235;
    }
    .doc-btn-del-sm {
        background: transparent;
        color: #b91c1c;
        border: 1px solid #fecaca;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.15s;
    }
    .doc-btn-del-sm:hover {
        background: #fee2e2;
        border-color: #f87171;
    }

    /* Articles Rows */
    .doc-articles-list {
        display: flex;
        flex-direction: column;
    }
    .doc-article-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 20px;
        border-bottom: 1px solid #edf2ef;
        gap: 16px;
        transition: background-color 0.12s;
    }
    .doc-article-row:last-child {
        border-bottom: 0;
    }
    .doc-article-row:hover {
        background: #fbfdfc;
    }
    .doc-article-main {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        min-width: 0;
    }
    .doc-page-icon {
        width: 30px;
        height: 30px;
        border-radius: 6px;
        background: #f1f7f4;
        color: #19694e;
        display: grid;
        place-items: center;
        flex-shrink: 0;
        margin-top: 2px;
    }
    .doc-article-details {
        min-width: 0;
    }
    .doc-badges-row {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
        margin-bottom: 4px;
    }
    .doc-pill {
        display: inline-flex;
        align-items: center;
        padding: 2px 7px;
        border-radius: 4px;
        font-size: 9px;
        font-weight: 800;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }
    .doc-pill.kind {
        background: #edf3f0;
        color: #3b5349;
    }
    .doc-pill.version {
        background: #e4f4ec;
        color: #17654b;
        border: 1px solid #c7e5d5;
    }
    .doc-pill.version-all {
        background: #f2f4f3;
        color: #5b6e65;
    }
    .doc-pill.pub {
        background: #dcfce7;
        color: #166534;
    }
    .doc-pill.draft {
        background: #fef3c7;
        color: #92400e;
    }
    .doc-article-title-link {
        font-size: 14px;
        font-weight: 700;
        color: var(--admin-ink);
        text-decoration: none;
        display: block;
        line-height: 1.3;
    }
    .doc-article-title-link:hover {
        color: #17654b;
        text-decoration: underline;
    }
    .doc-article-summary {
        font-size: 11px;
        color: #71847c;
        margin: 3px 0 0;
        max-width: 650px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .doc-article-actions {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-shrink: 0;
    }
    .doc-item-action {
        font-size: 11px;
        font-weight: 700;
        padding: 5px 9px;
        border-radius: 5px;
        text-decoration: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.12s;
        border: 1px solid transparent;
        background: transparent;
    }
    .doc-item-action.edit {
        background: #ffffff;
        color: var(--admin-ink);
        border: 1px solid #d2ddd6;
    }
    .doc-item-action.edit:hover {
        background: #f3f7f5;
        border-color: #adbdb4;
    }
    .doc-item-action.toggle-pub {
        background: #ffffff;
        color: #92400e;
        border: 1px solid #fde68a;
    }
    .doc-item-action.toggle-pub:hover {
        background: #fef3c7;
    }
    .doc-item-action.toggle-unpub {
        background: #ffffff;
        color: #166534;
        border: 1px solid #bbf7d0;
    }
    .doc-item-action.toggle-unpub:hover {
        background: #dcfce7;
    }
    .doc-item-action.delete {
        color: #dc2626;
        padding: 5px 7px;
    }
    .doc-item-action.delete:hover {
        background: #fee2e2;
        border-color: #fca5a5;
    }
    .doc-item-action.preview {
        color: #17654b;
        background: #eef6f2;
        border: 1px solid #d0e4d9;
    }
    .doc-item-action.preview:hover {
        background: #dff0e6;
    }

    /* Empty states */
    .doc-section-empty {
        padding: 24px 20px;
        text-align: center;
        color: #798d84;
        font-size: 12px;
        background: #fafcfb;
    }
    .doc-section-empty a {
        color: #17654b;
        font-weight: 700;
        text-decoration: none;
        margin-left: 6px;
    }
    .doc-section-empty a:hover {
        text-decoration: underline;
    }

    /* Right Sidebar */
    .doc-sidebar {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }
    .doc-builder-card {
        background: #ffffff;
        border: 1px solid var(--admin-line);
        border-radius: 10px;
        padding: 22px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    }
    .doc-builder-head {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 18px;
        padding-bottom: 14px;
        border-bottom: 1px solid #eef3f0;
    }
    .doc-builder-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: #10221e;
        color: #66ddae;
        display: grid;
        place-items: center;
        font-size: 18px;
        font-weight: 900;
        flex-shrink: 0;
    }
    .doc-builder-head h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 800;
        color: var(--admin-ink);
    }
    .doc-builder-head p {
        margin: 2px 0 0;
        font-size: 11px;
        color: var(--admin-muted);
    }
    .doc-sidebar-form {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }
    .doc-sidebar-field {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    .doc-sidebar-field label {
        font-size: 11px;
        font-weight: 700;
        color: var(--admin-ink);
        display: flex;
        justify-content: space-between;
    }
    .doc-sidebar-field label i {
        font-style: normal;
        color: #7b8e86;
        font-size: 10px;
    }
    .doc-char-count {
        font-size: 10px;
        color: #8b9c94;
        text-align: right;
        margin-top: 2px;
    }
    .doc-btn-submit-section {
        background: #10221e;
        color: #ffffff;
        border: 0;
        padding: 11px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 6px;
        transition: all 0.15s;
        box-shadow: 0 3px 8px rgba(16,34,30,0.12);
        margin-top: 4px;
    }
    .doc-btn-submit-section:hover {
        background: #183831;
        transform: translateY(-1px);
    }

    /* Tip Card */
    .doc-tip-card {
        background: #10221e;
        color: #e5fff2;
        border: 1px solid #1f4236;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(16,34,30,0.15);
    }
    .doc-tip-head {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }
    .doc-tip-spark {
        color: #66ddae;
        font-size: 16px;
    }
    .doc-tip-head strong {
        font-size: 13px;
        font-weight: 800;
        color: #ffffff;
    }
    .doc-tip-card ul {
        margin: 0;
        padding-left: 18px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        font-size: 11px;
        color: #a7cbbe;
        line-height: 1.5;
    }
    .doc-tip-card li strong {
        color: #ffffff;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .doc-workspace-grid {
            grid-template-columns: 1fr;
        }
        .doc-strips-grid {
            grid-template-columns: 1fr;
        }
        .doc-stats-strip {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 640px) {
        .doc-edit-form-grid {
            grid-template-columns: 1fr;
        }
        .doc-article-row {
            flex-direction: column;
            align-items: flex-start;
        }
        .doc-article-actions {
            margin-top: 8px;
            width: 100%;
            justify-content: flex-start;
        }
    }
</style>

<div class="doc-shell">

    <!-- Top Page Header -->
    <div class="doc-header">
        <div>
            <div class="doc-breadcrumbs">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <span class="sep">/</span>
                <a href="{{ route('admin.documentation.index') }}">Documentation</a>
                <span class="sep">/</span>
                <strong style="color:var(--admin-ink);">{{ $project->name }}</strong>
            </div>

            <div class="doc-title-row">
                @if($logoPath)
                    <img src="{{ $logoPath }}" alt="{{ $project->name }}" class="doc-project-avatar">
                @else
                    <div class="doc-avatar-fallback">{{ strtoupper(substr($project->name, 0, 1)) }}</div>
                @endif
                <div>
                    <h2>{{ $project->name }} Documentation</h2>
                    <p class="doc-subtitle">Build, structure, and maintain the official developer knowledge base and API manuals for {{ $project->name }}.</p>
                </div>
            </div>
        </div>

        <div class="doc-header-actions">
            @if($project->github_url)
                <a class="doc-btn-ghost" href="{{ $project->github_url }}" target="_blank" rel="noopener">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0 0 24 12c0-6.63-5.37-12-12-12z"/></svg>
                    GitHub ↗
                </a>
            @endif

            <a class="doc-btn-ghost" href="{{ route('docs.project', $project) }}" target="_blank" rel="noopener">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                Public Docs ↗
            </a>

            <a class="doc-btn-primary" href="{{ route('admin.documentation.pages.create', $project) }}">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                + New Page
            </a>
        </div>
    </div>

    <!-- Intelligence Context Strips -->
    <div class="doc-strips-grid">
        <!-- Open Source Strip -->
        <div class="doc-banner-card">
            <div class="doc-banner-content">
                <span class="doc-banner-kicker">Open Source Synchronization</span>
                <h4 class="doc-banner-title">{{ $project->name }} repository is connected</h4>
                <p class="doc-banner-desc">Issues, PRs, and core code evolution stay in GitHub while RozeHub serves as the authoritative, versioned documentation portal.</p>
            </div>
            @if($project->github_url)
                <div class="doc-banner-action">
                    <a href="{{ $project->github_url }}" target="_blank" rel="noopener" class="doc-banner-btn">
                        Open repository ↗
                    </a>
                </div>
            @endif
        </div>

        <!-- Versioning Strip -->
        <div class="doc-banner-card">
            <div class="doc-banner-content">
                <span class="doc-banner-kicker">Documentation Versioning</span>
                <h4 class="doc-banner-title">Multi-Version Target Architecture</h4>
                <p class="doc-banner-desc">Assign articles to specific software releases, or keep them unassigned for evergreen conceptual guides.</p>
                <div class="doc-release-pills">
                    @forelse($project->releases->take(4) as $release)
                        <span class="doc-release-chip">
                            v{{ $release->version }}
                            <small>{{ $release->channel ?: 'stable' }}</small>
                        </span>
                    @empty
                        <span style="font-size:11px; color:#889a91;">No release tags published yet</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Metric Stats Strip (4 Metric Boxes) -->
    <section class="doc-stats-strip">
        <div class="doc-stat-box">
            <small>Navigation Sections</small>
            <strong>{{ $totalSections }}</strong>
            <span>Top-level category groups</span>
        </div>

        <div class="doc-stat-box">
            <small>Total Articles</small>
            <strong style="color: #17654b;">{{ $totalArticles }}</strong>
            <span>{{ $publishedArticles }} published · {{ $draftArticles }} drafts</span>
        </div>

        <div class="doc-stat-box">
            <small>Target Releases</small>
            <strong>{{ $totalReleases }}</strong>
            <span>Available version channels</span>
        </div>

        <div class="doc-stat-box">
            <small>Public Portal</small>
            <strong style="color: #15803d;">LIVE</strong>
            <span>Real-time version-aware docs</span>
        </div>
    </section>

    <!-- Main Workspace (Left: Sections & Articles, Right: Section Builder & Tips) -->
    <div class="doc-workspace-grid">

        <!-- Left Column: Sections & Pages -->
        <main class="doc-sections-main">
            <div class="doc-sections-header">
                <div>
                    <h3>Sections &amp; Articles</h3>
                    <p>Manage navigational categories and organize your knowledge base hierarchy.</p>
                </div>
                <div style="font-size:12px; font-weight:700; color:#52695e;">
                    {{ $totalSections }} {{ Str::plural('Section', $totalSections) }} · {{ $totalArticles }} {{ Str::plural('Article', $totalArticles) }}
                </div>
            </div>

            @forelse($project->documentationSections as $section)
                <div class="doc-section-card">

                    <!-- Section Header Bar -->
                    <div class="doc-section-bar">
                        <div class="doc-section-info">
                            <div class="doc-section-icon-badge">
                                {{ $section->icon ?: '◈' }}
                            </div>
                            <div class="doc-section-text">
                                <h4>
                                    {{ $section->title }}
                                    <span class="doc-section-count-badge">{{ $section->pages->count() }} {{ Str::plural('article', $section->pages->count()) }}</span>
                                </h4>
                                <p>{{ $section->description ?: 'Categorized documentation articles and technical guides.' }}</p>
                            </div>
                        </div>

                        <div class="doc-section-controls">
                            <!-- Direct Shortcut to create article under this section -->
                            <a href="{{ route('admin.documentation.pages.create', [$project, 'section_id' => $section->id]) }}" class="doc-control-btn add">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                + Add Article
                            </a>

                            <!-- Toggle Settings Panel -->
                            <button type="button" class="doc-control-btn settings" onclick="toggleSectionSettings('section-settings-{{ $section->id }}')">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                                Settings
                            </button>
                        </div>
                    </div>

                    <!-- Collapsible Section Edit Drawer (Cleanly styled, hidden by default) -->
                    <div id="section-settings-{{ $section->id }}" class="doc-section-edit-drawer" style="display: none;">
                        <div class="doc-edit-drawer-title">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                            Configure Section Details &amp; Ordering
                        </div>

                        <form method="POST" action="{{ route('admin.documentation.sections.update', $section) }}">
                            @csrf
                            @method('PUT')

                            <div class="doc-edit-form-grid">
                                <div class="doc-field-group">
                                    <label for="title_{{ $section->id }}">Section Title</label>
                                    <input id="title_{{ $section->id }}" name="title" class="doc-input-styled" value="{{ $section->title }}" required>
                                </div>

                                <div class="doc-field-group">
                                    <label for="icon_{{ $section->id }}">Icon</label>
                                    <input id="icon_{{ $section->id }}" name="icon" class="doc-input-styled" value="{{ $section->icon }}" maxlength="20">
                                </div>

                                <div class="doc-field-group">
                                    <label for="order_{{ $section->id }}">Sort Order</label>
                                    <input id="order_{{ $section->id }}" type="number" name="sort_order" class="doc-input-styled" value="{{ $section->sort_order }}" min="0">
                                </div>

                                <div class="doc-field-group full-span">
                                    <label for="desc_{{ $section->id }}">Description</label>
                                    <input id="desc_{{ $section->id }}" name="description" class="doc-input-styled" value="{{ $section->description }}" placeholder="Brief summary of articles in this section">
                                </div>
                            </div>

                            <div class="doc-edit-drawer-footer">
                                <button type="submit" class="doc-btn-save-sm">
                                    Save Section Changes
                                </button>
                        </form>

                        <form method="POST" action="{{ route('admin.documentation.sections.destroy', $section) }}" onsubmit="return confirm('Are you sure you want to delete \'{{ addslashes($section->title) }}\'? Any articles in this section will become unassigned.');" style="margin:0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="doc-btn-del-sm">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                Delete Section
                            </button>
                        </form>
                            </div>
                    </div>

                    <!-- Articles List Inside Section -->
                    <div class="doc-articles-list">
                        @forelse($section->pages as $page)
                            <div class="doc-article-row">
                                <div class="doc-article-main">
                                    <div class="doc-page-icon">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                    </div>
                                    <div class="doc-article-details">
                                        <div class="doc-badges-row">
                                            <span class="doc-pill kind">{{ strtoupper(str_replace('-', ' ', $page->kind)) }}</span>

                                            @if($page->release)
                                                <span class="doc-pill version">v{{ $page->release->version }}</span>
                                            @else
                                                <span class="doc-pill version-all">ALL VERSIONS</span>
                                            @endif

                                            <span class="doc-pill {{ $page->is_published ? 'pub' : 'draft' }}">
                                                {{ $page->is_published ? '● Published' : '○ Draft' }}
                                            </span>
                                        </div>

                                        <a href="{{ route('admin.documentation.pages.edit', $page) }}" class="doc-article-title-link">
                                            {{ $page->title }}
                                        </a>

                                        <p class="doc-article-summary">
                                            {{ $page->summary ?: 'No summary text provided for this article.' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="doc-article-actions">
                                    <!-- Edit Link -->
                                    <a href="{{ route('admin.documentation.pages.edit', $page) }}" class="doc-item-action edit">
                                        Edit
                                    </a>

                                    <!-- Toggle Publish Status Form -->
                                    <form method="POST" action="{{ route('admin.documentation.pages.toggle', $page) }}" style="margin:0;">
                                        @csrf
                                        <button type="submit" class="doc-item-action {{ $page->is_published ? 'toggle-pub' : 'toggle-unpub' }}">
                                            {{ $page->is_published ? 'Unpublish' : 'Publish' }}
                                        </button>
                                    </form>

                                    <!-- Public Preview Link -->
                                    <a href="{{ route('docs.page', [$project, $page->slug]) }}" target="_blank" rel="noopener" class="doc-item-action preview" title="View Public Page ↗">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                    </a>

                                    <!-- Delete Article Form -->
                                    <form method="POST" action="{{ route('admin.documentation.pages.destroy', $page) }}" onsubmit="return confirm('Delete \'{{ addslashes($page->title) }}\'? This action cannot be undone.');" style="margin:0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="doc-item-action delete" title="Delete Article">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="doc-section-empty">
                                No articles in this section yet.
                                <a href="{{ route('admin.documentation.pages.create', [$project, 'section_id' => $section->id]) }}">+ Add the first article</a>
                            </div>
                        @endforelse
                    </div>

                </div>
            @empty
                <div class="doc-section-card" style="padding: 40px 20px; text-align: center;">
                    <div style="width:48px; height:48px; border-radius:12px; background:#eef5f1; color:#17654b; display:grid; place-items:center; font-size:22px; margin: 0 auto 12px;">▤</div>
                    <h3 style="margin: 0 0 6px; font-size:17px; font-weight:800; color:var(--admin-ink);">No Documentation Sections Configured</h3>
                    <p style="margin: 0 auto 16px; font-size:12px; color:var(--admin-muted); max-width:440px; line-height:1.5;">Create your first navigation section (such as Overview, Getting Started, or API Reference) using the panel on the right.</p>
                </div>
            @endforelse
        </main>

        <!-- Right Column: Add Section Builder & Help Tip -->
        <aside class="doc-sidebar">

            <!-- Builder Card -->
            <div class="doc-builder-card">
                <div class="doc-builder-head">
                    <div class="doc-builder-icon">＋</div>
                    <div>
                        <h3>Add Navigation Section</h3>
                        <p>Sections become the high-level categories visitors use to browse the docs.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.documentation.sections.store', $project) }}" class="doc-sidebar-form">
                    @csrf

                    <div class="doc-sidebar-field">
                        <label for="new_section_title">
                            Section Title <i>Required</i>
                        </label>
                        <input id="new_section_title" name="title" class="doc-input-styled" maxlength="160" placeholder="e.g. Architecture &amp; Internals" required>
                        <div class="doc-char-count"><span id="new_title_count">0</span> / 160</div>
                    </div>

                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <div class="doc-sidebar-field">
                            <label for="new_section_icon">Icon <i>Optional</i></label>
                            <input id="new_section_icon" name="icon" class="doc-input-styled" maxlength="20" placeholder="⌘ or ◈">
                        </div>

                        <div class="doc-sidebar-field">
                            <label for="new_section_order">Sort Order</label>
                            <input id="new_section_order" type="number" name="sort_order" class="doc-input-styled" value="{{ (($project->documentationSections->max('sort_order') ?? 0) + 10) }}" min="0">
                        </div>
                    </div>

                    <div class="doc-sidebar-field">
                        <label for="new_section_desc">Description <i>Optional</i></label>
                        <textarea id="new_section_desc" name="description" class="doc-input-styled" rows="3" maxlength="500" placeholder="What technical topics belong in this section?"></textarea>
                        <div class="doc-char-count"><span id="new_desc_count">0</span> / 500</div>
                    </div>

                    <button type="submit" class="doc-btn-submit-section">
                        <span>＋</span> Add Documentation Section
                    </button>
                </form>
            </div>

            <!-- Versioning Strategy Tip Card -->
            <div class="doc-tip-card">
                <div class="doc-tip-head">
                    <span class="doc-tip-spark">✦</span>
                    <strong>Documentation Architecture Tips</strong>
                </div>
                <ul>
                    <li><strong>Universal Knowledge:</strong> Keep high-level concepts and introductory guides unassigned to show them across all versions.</li>
                    <li><strong>Release Bounding:</strong> Bind breaking APIs, new CLI parameters, and migration notes to explicit software releases.</li>
                    <li><strong>Immediate Publishing:</strong> Changes take effect in real-time across the RozeHub public developer portal.</li>
                </ul>
            </div>

        </aside>

    </div>

</div>

<script>
    function toggleSectionSettings(drawerId) {
        const el = document.getElementById(drawerId);
        if (!el) return;
        if (el.style.display === 'none' || el.style.display === '') {
            el.style.display = 'block';
            el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } else {
            el.style.display = 'none';
        }
    }

    (() => {
        const bindCharCounter = (inputEl, countEl) => {
            if (!inputEl || !countEl) return;
            const update = () => countEl.textContent = inputEl.value.length;
            inputEl.addEventListener('input', update);
            update();
        };

        bindCharCounter(document.getElementById('new_section_title'), document.getElementById('new_title_count'));
        bindCharCounter(document.getElementById('new_section_desc'), document.getElementById('new_desc_count'));
    })();
</script>
@endsection
