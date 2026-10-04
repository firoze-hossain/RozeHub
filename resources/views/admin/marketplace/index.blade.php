@extends('admin.layout')

@php
    $heading = 'Desktop Marketplace';
    $title = 'Desktop Marketplace Studio · RozeHub Admin';
    $pendingReviews = \App\Models\MarketplaceSubmission::whereIn('status', ['SUBMITTED', 'UNDER_REVIEW'])->count();
@endphp

@section('content')
<style>
    /* Scoped Marketplace Admin Studio Styles */
    .market-shell {
        display: flex;
        flex-direction: column;
        gap: 22px;
    }

    /* Page Header */
    .market-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }
    .market-breadcrumbs {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: var(--admin-muted);
        margin-bottom: 6px;
    }
    .market-breadcrumbs a {
        color: var(--admin-muted);
        text-decoration: none;
    }
    .market-breadcrumbs a:hover {
        color: var(--admin-ink);
        text-decoration: underline;
    }
    .market-breadcrumbs .sep {
        color: #b8c4be;
    }
    .market-title-row h2 {
        margin: 0;
        font-size: 26px;
        font-weight: 800;
        letter-spacing: -0.6px;
        color: var(--admin-ink);
        line-height: 1.2;
    }
    .market-subtitle {
        margin: 4px 0 0;
        font-size: 13px;
        color: var(--admin-muted);
    }
    .market-header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .market-btn-primary {
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
    .market-btn-primary:hover {
        background: #183831;
        transform: translateY(-1px);
        color: #ffffff;
    }
    .market-btn-primary svg {
        color: #66ddae;
    }
    .market-btn-ghost {
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
    .market-btn-ghost:hover {
        background: #f7faf8;
        border-color: #adbdb4;
        color: #0b1a16;
    }
    .market-pill-count {
        background: #eef5f1;
        color: #17654b;
        font-size: 10px;
        font-weight: 800;
        padding: 2px 6px;
        border-radius: 10px;
    }

    /* Executive Stats Strip */
    .market-stats-strip {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
    }
    .market-stat-box {
        background: #ffffff;
        border: 1px solid var(--admin-line);
        border-radius: 8px;
        padding: 16px 18px;
        display: flex;
        flex-direction: column;
        gap: 3px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.02);
    }
    .market-stat-box small {
        font-size: 9px;
        letter-spacing: 1px;
        font-weight: 800;
        text-transform: uppercase;
        color: #70827b;
    }
    .market-stat-box strong {
        font-size: 24px;
        font-weight: 900;
        color: var(--admin-ink);
        letter-spacing: -0.5px;
    }
    .market-stat-box span {
        font-size: 11px;
        color: #667972;
    }

    /* Modern Filter Strip */
    .market-filter-bar {
        background: #ffffff;
        border: 1px solid var(--admin-line);
        border-radius: 10px;
        padding: 14px 18px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.02);
    }
    .market-filter-form {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .market-search-wrap {
        flex: 2;
        min-width: 240px;
        position: relative;
    }
    .market-search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #8c9d95;
        pointer-events: none;
    }
    .market-input-search {
        width: 100%;
        padding: 9px 12px 9px 36px;
        border: 1px solid #c9ded2;
        border-radius: 6px;
        font-size: 13px;
        color: var(--admin-ink);
        background: #fafcfa;
        box-sizing: border-box;
        transition: all 0.15s;
    }
    .market-input-search:focus {
        background: #ffffff;
        border-color: #17654b;
        outline: none;
        box-shadow: 0 0 0 2px rgba(23,101,75,0.12);
    }
    .market-select {
        flex: 1;
        min-width: 170px;
        padding: 9px 12px;
        border: 1px solid #c9ded2;
        border-radius: 6px;
        font-size: 13px;
        color: var(--admin-ink);
        background: #fafcfa;
        cursor: pointer;
        box-sizing: border-box;
        transition: all 0.15s;
    }
    .market-select:focus {
        background: #ffffff;
        border-color: #17654b;
        outline: none;
    }
    .market-filter-btn {
        background: #10221e;
        color: #ffffff;
        border: 0;
        padding: 9px 18px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.15s;
        white-space: nowrap;
    }
    .market-filter-btn:hover {
        background: #183831;
    }
    .market-reset-btn {
        color: #61776e;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        padding: 8px 10px;
    }
    .market-reset-btn:hover {
        color: var(--admin-ink);
        text-decoration: underline;
    }

    /* Product Cards Grid */
    .market-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 18px;
    }
    .market-item-card {
        background: #ffffff;
        border: 1px solid var(--admin-line);
        border-radius: 10px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        transition: all 0.15s ease;
    }
    .market-item-card:hover {
        border-color: #b3c7bd;
        box-shadow: 0 6px 16px rgba(0,0,0,0.05);
        transform: translateY(-2px);
    }

    /* Top Row of Card */
    .market-card-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 14px;
    }
    .market-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        background: #eef6f2;
        border: 1px solid #d1e5db;
        display: grid;
        place-items: center;
        flex-shrink: 0;
        overflow: hidden;
    }
    .market-icon-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .market-icon-box svg {
        color: #17654b;
    }
    .market-tag-list {
        display: flex;
        align-items: center;
        gap: 5px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }
    .market-badge-pill {
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.4px;
        text-transform: uppercase;
        padding: 3px 8px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .market-badge-pill.plugin { background: #e8f5ee; color: #166534; border: 1px solid #c7e8d6; }
    .market-badge-pill.theme { background: #fdf4ff; color: #86198f; border: 1px solid #f5d0fe; }
    .market-badge-pill.extension { background: #eff6ff; color: #1e40af; border: 1px solid #dbeafe; }
    .market-badge-pill.driver { background: #fefce8; color: #854d0e; border: 1px solid #fef08a; }
    .market-badge-pill.official { background: #10221e; color: #ffffff; }
    .market-badge-pill.verified { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }

    /* Card Main Content */
    .market-card-body {
        margin-bottom: 16px;
    }
    .market-target-bar {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 6px;
    }
    .market-target-badge {
        font-size: 11px;
        font-weight: 700;
        color: #17654b;
        background: #eef6f2;
        padding: 2px 7px;
        border-radius: 4px;
    }
    .market-bundle-code {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 11px;
        color: #7b8e86;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 190px;
    }
    .market-item-title {
        margin: 0 0 6px;
        font-size: 17px;
        font-weight: 800;
        letter-spacing: -0.3px;
        color: var(--admin-ink);
        line-height: 1.3;
    }
    .market-item-desc {
        font-size: 12px;
        color: #5c6f67;
        margin: 0;
        line-height: 1.5;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Card Metrics Strip */
    .market-card-metrics {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 12px;
        background: #f7faf8;
        border-radius: 6px;
        border: 1px solid #e5ede8;
        margin-bottom: 16px;
        font-size: 11px;
    }
    .market-metric-item {
        display: flex;
        align-items: center;
        gap: 5px;
        font-weight: 700;
        color: #3b5047;
    }
    .market-metric-item svg {
        color: #17654b;
    }
    .market-status-indicator {
        font-weight: 700;
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 4px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }
    .market-status-indicator.published {
        background: #dcfce7;
        color: #166534;
    }
    .market-status-indicator.draft {
        background: #fef3c7;
        color: #92400e;
    }

    /* Card Actions Footer */
    .market-card-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 14px;
        border-top: 1px solid #edf2ef;
        gap: 8px;
    }
    .market-btn-group-left {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .market-action-btn {
        font-size: 11px;
        font-weight: 700;
        padding: 6px 11px;
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
    .market-action-btn.releases {
        background: #ffffff;
        color: var(--admin-ink);
        border: 1px solid #c9ded2;
    }
    .market-action-btn.releases:hover {
        background: #f1f7f4;
        border-color: #17654b;
        color: #17654b;
    }
    .market-action-btn.edit {
        background: #ffffff;
        color: var(--admin-ink);
        border: 1px solid #d6ded9;
    }
    .market-action-btn.edit:hover {
        background: #f5f8f6;
        border-color: #adbdb4;
    }
    .market-action-btn.preview {
        color: #17654b;
        background: #eef6f2;
        border: 1px solid #d0e4d9;
    }
    .market-action-btn.preview:hover {
        background: #dff0e6;
    }
    .market-action-btn.delete {
        color: #dc2626;
        padding: 6px 8px;
    }
    .market-action-btn.delete:hover {
        background: #fee2e2;
        border-color: #fca5a5;
    }

    /* Empty state */
    .market-empty-card {
        background: #ffffff;
        border: 1px solid var(--admin-line);
        border-radius: 10px;
        padding: 60px 20px;
        text-align: center;
        grid-column: 1 / -1;
    }
    .market-empty-icon {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        background: #eef6f2;
        color: #17654b;
        display: grid;
        place-items: center;
        font-size: 24px;
        margin: 0 auto 14px;
    }
    .market-empty-card h3 {
        margin: 0 0 6px;
        font-size: 18px;
        font-weight: 800;
        color: var(--admin-ink);
    }
    .market-empty-card p {
        margin: 0 auto 16px;
        font-size: 13px;
        color: var(--admin-muted);
        max-width: 440px;
        line-height: 1.5;
    }

    @media (max-width: 960px) {
        .market-stats-strip {
            grid-template-columns: repeat(2, 1fr);
        }
        .market-cards-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="market-shell">

    <!-- Page Header & Action Bar -->
    <div class="market-header">
        <div>
            <div class="market-breadcrumbs">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <span class="sep">/</span>
                <strong style="color:var(--admin-ink);">Desktop Marketplace</strong>
            </div>

            <div class="market-title-row">
                <h2>Desktop Marketplace Studio</h2>
                <p class="market-subtitle">Manage installable plugins, themes, database drivers, and extensions for Lumina and DBNavigator.</p>
            </div>
        </div>

        <div class="market-header-actions">
            <a class="market-btn-ghost" href="{{ route('marketplace.index') }}" target="_blank" rel="noopener">
                View Public Store ↗
            </a>

            <a class="market-btn-ghost" href="{{ route('admin.marketplace.categories') }}">
                Categories ▤
            </a>

            <a class="market-btn-ghost" href="{{ route('admin.marketplace.review.index') }}">
                Review Queue ✓
                @if($pendingReviews > 0)
                    <span class="market-pill-count">{{ $pendingReviews }}</span>
                @endif
            </a>

            <a class="market-btn-primary" href="{{ route('admin.marketplace.create') }}">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                + New Marketplace Item
            </a>
        </div>
    </div>

    <!-- Executive Stats Strip (4 Metric Cards) -->
    <section class="market-stats-strip">
        <div class="market-stat-box">
            <small>Total Marketplace Items</small>
            <strong>{{ $stats['total_items'] ?? $items->total() }}</strong>
            <span>Active registered capabilities</span>
        </div>

        <div class="market-stat-box">
            <small>Total Downloads</small>
            <strong style="color: #17654b;">{{ number_format($stats['total_downloads'] ?? 0) }}</strong>
            <span>Cross-IDE client installations</span>
        </div>

        <div class="market-stat-box">
            <small>Official Capabilities</small>
            <strong>{{ $stats['official_count'] ?? 0 }}</strong>
            <span>First-party RozeHub packages</span>
        </div>

        <div class="market-stat-box">
            <small>Verified Signatures</small>
            <strong style="color: #15803d;">{{ $stats['verified_count'] ?? 0 }}</strong>
            <span>Security scanned &amp; verified</span>
        </div>
    </section>

    <!-- Search & Filter Bar -->
    <div class="market-filter-bar">
        <form method="GET" action="{{ route('admin.marketplace.index') }}" class="market-filter-form">
            <div class="market-search-wrap">
                <svg class="market-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <input class="market-input-search" name="q" value="{{ request('q') }}" placeholder="Search name, bundle ID, or vendor…">
            </div>

            <select class="market-select" name="project">
                <option value="">All Applications</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" @selected(request('project') == $project->id)>{{ $project->name }}</option>
                @endforeach
            </select>

            <select class="market-select" name="type">
                <option value="">All Types (Plugins, Themes, etc.)</option>
                <option value="plugin" @selected(request('type') === 'plugin')>Plugins</option>
                <option value="extension" @selected(request('type') === 'extension')>Extensions</option>
                <option value="theme" @selected(request('type') === 'theme')>Themes</option>
                <option value="driver" @selected(request('type') === 'driver')>Database Drivers</option>
            </select>

            <button class="market-filter-btn" type="submit">Filter Items</button>

            @if(request()->hasAny(['q', 'project', 'type']))
                <a href="{{ route('admin.marketplace.index') }}" class="market-reset-btn">Reset</a>
            @endif
        </form>
    </div>

    <!-- Marketplace Cards Grid -->
    <div class="market-cards-grid">
        @forelse($items as $item)
            @php
                $itemType = strtolower($item->item_type);
            @endphp
            <article class="market-item-card">
                <div>
                    <!-- Top Row: Icon & Status Badges -->
                    <div class="market-card-head">
                        <div class="market-icon-box">
                            @if($item->icon_path)
                                <img src="{{ asset($item->icon_path) }}" alt="{{ $item->name }}">
                            @elseif($itemType === 'theme')
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="13.5" cy="6.5" r=".5" fill="currentColor"></circle><circle cx="17.5" cy="10.5" r=".5" fill="currentColor"></circle><circle cx="8.5" cy="7.5" r=".5" fill="currentColor"></circle><circle cx="6.5" cy="12.5" r=".5" fill="currentColor"></circle><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.9 0 1.7-.2 2.4-.6.6-.3 1.1-.9 1.1-1.6 0-.8-.6-1.5-1.4-1.8-.8-.3-1.3-1-1.3-1.9 0-1.1.9-2 2-2h1.6c3.1 0 5.6-2.5 5.6-5.6 0-4.6-4.5-8.5-10-8.5z"></path></svg>
                            @elseif($itemType === 'driver')
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>
                            @elseif($itemType === 'extension')
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                            @else
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
                            @endif
                        </div>

                        <div class="market-tag-list">
                            <span class="market-badge-pill {{ $itemType }}">
                                {{ ucfirst($itemType) }}
                            </span>
                            @if($item->is_official)
                                <span class="market-badge-pill official" title="RozeHub Official First-Party Capability">
                                    ★ Official
                                </span>
                            @endif
                            @if($item->is_verified)
                                <span class="market-badge-pill verified" title="Security Verified">
                                    ✓ Verified
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Main Info -->
                    <div class="market-card-body">
                        <div class="market-target-bar">
                            <span class="market-target-badge">{{ $item->project->name }}</span>
                            <span style="color:#b8c4be;">·</span>
                            <code class="market-bundle-code" title="{{ $item->item_id }}">{{ $item->item_id }}</code>
                        </div>

                        <h3 class="market-item-title">{{ $item->name }}</h3>

                        <p class="market-item-desc">
                            {{ $item->summary ?: 'No summary description provided.' }}
                        </p>
                    </div>
                </div>

                <div>
                    <!-- Metrics Bar (Downloads & Release Count) -->
                    <div class="market-card-metrics">
                        <div class="market-metric-item" title="Total Downloads">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                            <span>{{ number_format($item->downloads_count) }} downloads</span>
                        </div>

                        <div class="market-metric-item" title="Releases Available">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                            <span>{{ $item->releases_count ?? 0 }} {{ Str::plural('release', $item->releases_count ?? 0) }}</span>
                        </div>

                        <span class="market-status-indicator {{ $item->is_published ? 'published' : 'draft' }}">
                            {{ $item->is_published ? '● Live' : '○ Draft' }}
                        </span>
                    </div>

                    <!-- Footer Action Buttons -->
                    <div class="market-card-actions">
                        <div class="market-btn-group-left">
                            <a class="market-action-btn releases" href="{{ route('admin.marketplace.releases.index', $item) }}">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                                Releases
                            </a>

                            <a class="market-action-btn edit" href="{{ route('admin.marketplace.edit', $item) }}">
                                Edit
                            </a>

                            <a class="market-action-btn preview" href="{{ route('marketplace.item', $item->slug) }}" target="_blank" rel="noopener" title="View Public Page ↗">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                            </a>
                        </div>

                        <form method="POST" action="{{ route('admin.marketplace.destroy', $item) }}" onsubmit="return confirm('Delete \'{{ addslashes($item->name) }}\' and all associated release packages? This action is permanent.');" style="margin:0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="market-action-btn delete" title="Delete Capability">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <div class="market-empty-card">
                <div class="market-empty-icon">◇</div>
                <h3>No Marketplace Items Found</h3>
                <p>No capabilities match your search query. Try resetting filters or create a new extension package for your desktop applications.</p>
                <a href="{{ route('admin.marketplace.create') }}" class="market-btn-primary" style="display:inline-flex;">
                    + Create First Marketplace Item
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div style="margin-top: 10px;">
        {{ $items->links() }}
    </div>

</div>
@endsection
