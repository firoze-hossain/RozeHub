@extends('admin.layout')

@php
    $heading = 'Marketplace Categories';
    $title = 'Marketplace Categories · RozeHub Admin';
    $totalCategories = $projects->sum(fn($p) => $p->marketplaceCategories->count());
    $activeCategories = $projects->sum(fn($p) => $p->marketplaceCategories->where('is_active', true)->count());
    $configuredProjects = $projects->filter(fn($p) => $p->marketplaceCategories->count() > 0)->count();
@endphp

@section('content')
<style>
    /* Scoped Marketplace Categories Admin Styles */
    .cat-shell {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    /* Header & Breadcrumbs */
    .cat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }
    .cat-breadcrumbs {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: var(--admin-muted);
        margin-bottom: 6px;
    }
    .cat-breadcrumbs a {
        color: var(--admin-muted);
        text-decoration: none;
        transition: color 0.15s;
    }
    .cat-breadcrumbs a:hover {
        color: var(--admin-ink);
        text-decoration: underline;
    }
    .cat-breadcrumbs .sep {
        color: #b8c4be;
    }
    .cat-title-row h2 {
        margin: 0;
        font-size: 26px;
        font-weight: 800;
        letter-spacing: -0.6px;
        color: var(--admin-ink);
        line-height: 1.2;
    }
    .cat-subtitle {
        margin: 4px 0 0;
        font-size: 13px;
        color: var(--admin-muted);
    }
    .cat-header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .cat-btn-primary {
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
    .cat-btn-primary:hover {
        background: #183831;
        transform: translateY(-1px);
        color: #ffffff;
    }
    .cat-btn-primary svg {
        color: #66ddae;
    }
    .cat-btn-secondary {
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
    .cat-btn-secondary:hover {
        background: #f7faf7;
        border-color: #bccbc1;
    }

    /* KPI Metrics Grid */
    .cat-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
    }
    .cat-stat-card {
        background: var(--admin-panel);
        border: 1px solid var(--admin-line);
        padding: 18px 20px;
        box-shadow: var(--admin-shadow);
        border-radius: 6px;
        display: flex;
        flex-direction: column;
        gap: 4px;
        position: relative;
        overflow: hidden;
    }
    .cat-stat-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: transparent;
    }
    .cat-stat-card.accent-green::before { background: #1d7758; }
    .cat-stat-card.accent-mint::before { background: #66ddae; }
    .cat-stat-card.accent-dark::before { background: #10221e; }
    .cat-stat-card.accent-blue::before { background: #3b82f6; }

    .cat-stat-kicker {
        font-size: 9px;
        letter-spacing: 1.2px;
        font-weight: 800;
        text-transform: uppercase;
        color: var(--admin-muted);
    }
    .cat-stat-val {
        font-size: 28px;
        font-weight: 800;
        letter-spacing: -1px;
        color: var(--admin-ink);
        line-height: 1.2;
    }
    .cat-stat-sub {
        font-size: 11px;
        color: var(--admin-muted);
    }

    /* Add Category Card (Form Builder) */
    .cat-create-card {
        background: #ffffff;
        border: 1px solid #d4dfd8;
        border-radius: 8px;
        box-shadow: 0 10px 30px rgba(16,34,30,0.06);
        padding: 24px;
        margin-bottom: 4px;
    }
    .cat-create-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 14px;
        border-bottom: 1px solid #eef2ef;
    }
    .cat-create-title {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .cat-create-icon {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        background: #e8f5ee;
        color: #176b4e;
        display: grid;
        place-items: center;
        font-size: 18px;
        font-weight: bold;
        flex-shrink: 0;
    }
    .cat-create-title h3 {
        margin: 0;
        font-size: 17px;
        font-weight: 800;
        color: var(--admin-ink);
        letter-spacing: -0.3px;
    }
    .cat-create-title p {
        margin: 2px 0 0;
        font-size: 12px;
        color: var(--admin-muted);
    }

    .cat-form-grid {
        display: grid;
        grid-template-columns: 220px 1.4fr 2fr 130px 100px auto;
        gap: 12px;
        align-items: flex-end;
    }
    .cat-field-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    .cat-field-group label {
        font-size: 11px;
        font-weight: 700;
        color: #2c423b;
        letter-spacing: 0.2px;
    }
    .cat-field-group label small {
        font-weight: 400;
        color: var(--admin-muted);
    }
    .cat-input-styled, .cat-select-styled {
        height: 40px;
        border: 1px solid #ccd8d1;
        border-radius: 6px;
        padding: 0 12px;
        font-size: 13px;
        color: #12221e;
        background: #ffffff;
        box-sizing: border-box;
        transition: border-color 0.15s, box-shadow 0.15s;
        width: 100%;
    }
    .cat-input-styled:focus, .cat-select-styled:focus {
        border-color: #1d7758;
        outline: 0;
        box-shadow: 0 0 0 3px rgba(29, 119, 88, 0.12);
    }
    .cat-btn-submit {
        height: 40px;
        padding: 0 20px;
        background: #1d7758;
        color: #ffffff;
        border: 0;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all 0.15s;
        white-space: nowrap;
    }
    .cat-btn-submit:hover {
        background: #145e45;
        transform: translateY(-1px);
    }

    /* Filter Pills & Live Search Toolbar */
    .cat-filter-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        background: #ffffff;
        border: 1px solid var(--admin-line);
        border-radius: 8px;
        padding: 10px 14px;
        box-shadow: 0 2px 8px rgba(16,34,30,0.03);
    }
    .cat-pills-list {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }
    .cat-pill-btn {
        background: transparent;
        border: 1px solid transparent;
        color: #556761;
        font-size: 12px;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .cat-pill-btn:hover {
        background: #eef4f0;
        color: #10221e;
    }
    .cat-pill-btn.active {
        background: #10221e;
        color: #ffffff;
        border-color: #10221e;
    }
    .cat-pill-badge {
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 10px;
        background: #e2ede6;
        color: #145e45;
        font-weight: 700;
    }
    .cat-pill-btn.active .cat-pill-badge {
        background: #24463c;
        color: #66ddae;
    }
    .cat-search-box {
        position: relative;
        min-width: 250px;
    }
    .cat-search-box input {
        width: 100%;
        height: 36px;
        border: 1px solid #ccd8d1;
        border-radius: 20px;
        padding: 0 14px 0 34px;
        font-size: 12px;
        background: #fbfdfb;
        color: #12221e;
        box-sizing: border-box;
        transition: all 0.15s;
    }
    .cat-search-box input:focus {
        background: #ffffff;
        border-color: #1d7758;
        outline: 0;
        box-shadow: 0 0 0 3px rgba(29, 119, 88, 0.12);
    }
    .cat-search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #8da098;
        pointer-events: none;
        font-size: 13px;
    }

    /* Ecosystem Group Cards & Category Tables */
    .eco-group-card {
        background: var(--admin-panel);
        border: 1px solid var(--admin-line);
        box-shadow: var(--admin-shadow);
        border-radius: 8px;
        overflow: hidden;
        transition: box-shadow 0.2s;
    }
    .eco-group-card:hover {
        box-shadow: 0 12px 35px rgba(16,34,30,0.08);
    }
    .eco-group-header {
        padding: 16px 20px;
        background: linear-gradient(to bottom, #ffffff, #f9fbf9);
        border-bottom: 1px solid var(--admin-line);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
    }
    .eco-group-info {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .eco-group-badge {
        width: 36px;
        height: 36px;
        border-radius: 6px;
        background: #e3f2ea;
        color: #176b4e;
        font-weight: 800;
        font-size: 13px;
        display: grid;
        place-items: center;
        border: 1px solid #c9e2d3;
        flex-shrink: 0;
    }
    .eco-group-name {
        margin: 0;
        font-size: 17px;
        font-weight: 800;
        color: var(--admin-ink);
        letter-spacing: -0.3px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .eco-tag {
        font-size: 10px;
        font-weight: 600;
        color: #60756d;
        background: #ecf3ee;
        padding: 2px 7px;
        border-radius: 4px;
    }
    .eco-group-meta {
        font-size: 11px;
        color: var(--admin-muted);
        margin-top: 2px;
    }
    .eco-group-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .eco-add-btn {
        background: #ffffff;
        border: 1px solid #ccd8d1;
        color: #176b4e;
        padding: 6px 12px;
        border-radius: 5px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.15s;
    }
    .eco-add-btn:hover {
        background: #f1f8f4;
        border-color: #1d7758;
    }

    /* Table Styles */
    .cat-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }
    .cat-table th {
        background: #f8faf8;
        padding: 10px 16px;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        color: #6a7c74;
        border-bottom: 1px solid var(--admin-line);
        text-align: left;
    }
    .cat-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #eef2ef;
        vertical-align: middle;
        background: #ffffff;
    }
    .cat-table tr:last-child td {
        border-bottom: 0;
    }
    .cat-table tr:hover td {
        background: #fcfefc;
    }

    /* Row Form Inputs */
    .row-input-name {
        width: 100%;
        max-width: 200px;
        height: 34px;
        padding: 0 10px;
        border: 1px solid #ccd8d1;
        border-radius: 5px;
        font-size: 13px;
        font-weight: 700;
        color: #10221e;
        transition: all 0.15s;
    }
    .row-input-name:focus {
        border-color: #1d7758;
        outline: 0;
        box-shadow: 0 0 0 3px rgba(29, 119, 88, 0.12);
    }
    .row-slug-hint {
        display: block;
        font-size: 10px;
        color: #7b8e86;
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        margin-top: 3px;
    }

    .row-input-desc {
        width: 100%;
        min-width: 200px;
        height: 34px;
        padding: 0 10px;
        border: 1px solid #ccd8d1;
        border-radius: 5px;
        font-size: 12px;
        color: #3b5048;
        transition: all 0.15s;
    }
    .row-input-desc:focus {
        border-color: #1d7758;
        outline: 0;
        box-shadow: 0 0 0 3px rgba(29, 119, 88, 0.12);
    }

    .row-input-icon {
        width: 90px;
        height: 34px;
        padding: 0 8px;
        border: 1px solid #ccd8d1;
        border-radius: 5px;
        font-size: 12px;
        color: #3b5048;
        text-align: center;
        transition: all 0.15s;
    }
    .row-input-icon:focus {
        border-color: #1d7758;
        outline: 0;
        box-shadow: 0 0 0 3px rgba(29, 119, 88, 0.12);
    }

    .row-input-sort {
        width: 60px;
        height: 34px;
        padding: 0 6px;
        border: 1px solid #ccd8d1;
        border-radius: 5px;
        font-size: 12px;
        font-weight: 700;
        color: #10221e;
        text-align: center;
        transition: all 0.15s;
    }
    .row-input-sort:focus {
        border-color: #1d7758;
        outline: 0;
        box-shadow: 0 0 0 3px rgba(29, 119, 88, 0.12);
    }

    .cat-toggle-wrap {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 600;
        color: #455a52;
        cursor: pointer;
        user-select: none;
    }
    .cat-toggle-wrap input[type=checkbox] {
        cursor: pointer;
        accent-color: #1d7758;
        width: 15px;
        height: 15px;
    }

    .row-actions-cell {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 6px;
        white-space: nowrap;
    }
    .btn-row-save {
        height: 32px;
        padding: 0 12px;
        background: #10221e;
        color: #ffffff;
        border: 0;
        border-radius: 5px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.15s;
    }
    .btn-row-save:hover {
        background: #183831;
    }
    .btn-row-delete {
        height: 32px;
        width: 32px;
        background: transparent;
        color: #8c9c94;
        border: 1px solid #d8e2dc;
        border-radius: 5px;
        display: inline-grid;
        place-items: center;
        cursor: pointer;
        transition: all 0.15s;
    }
    .btn-row-delete:hover {
        background: #fdefee;
        border-color: #f4c7c3;
        color: #b64b3c;
    }

    /* Icon Preview Badge */
    .icon-preview-box {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .icon-badge {
        width: 26px;
        height: 26px;
        border-radius: 4px;
        background: #f0f5f2;
        border: 1px solid #d5e2da;
        color: #176b4e;
        display: grid;
        place-items: center;
        font-size: 11px;
        font-weight: bold;
        flex-shrink: 0;
    }

    /* Empty state */
    .cat-empty-state {
        padding: 36px 20px;
        text-align: center;
        color: var(--admin-muted);
        font-size: 13px;
    }

    @media (max-width: 1200px) {
        .cat-form-grid {
            grid-template-columns: 1fr 1fr 1fr;
        }
        .cat-stats-grid {
            grid-template-columns: 1fr 1fr;
        }
    }
    @media (max-width: 768px) {
        .cat-form-grid {
            grid-template-columns: 1fr;
        }
        .cat-stats-grid {
            grid-template-columns: 1fr;
        }
        .cat-filter-bar {
            flex-direction: column;
            align-items: stretch;
        }
        .cat-search-box {
            width: 100%;
        }
    }
</style>

<div class="cat-shell">

    {{-- Top Header --}}
    <div class="cat-header">
        <div>
            <div class="cat-breadcrumbs">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <span class="sep">/</span>
                <a href="{{ route('admin.marketplace.index') }}">Marketplace</a>
                <span class="sep">/</span>
                <span>Categories</span>
            </div>
            <div class="cat-title-row">
                <h2>Marketplace Categories</h2>
            </div>
            <p class="cat-subtitle">Define and curate extension, tool, and asset taxonomies independently for each project ecosystem.</p>
        </div>
        <div class="cat-header-actions">
            <a href="{{ route('admin.marketplace.index') }}" class="cat-btn-secondary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
                <span>Marketplace Studio</span>
            </a>
            <button type="button" onclick="focusCreateForm()" class="cat-btn-primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <span>New category</span>
            </button>
        </div>
    </div>

    {{-- KPI Summary Stats --}}
    <div class="cat-stats-grid">
        <div class="cat-stat-card accent-green">
            <span class="cat-stat-kicker">Total Taxonomies</span>
            <div class="cat-stat-val">{{ $totalCategories }}</div>
            <span class="cat-stat-sub">Across {{ $projects->count() }} software ecosystems</span>
        </div>
        <div class="cat-stat-card accent-mint">
            <span class="cat-stat-kicker">Active Categories</span>
            <div class="cat-stat-val">{{ $activeCategories }}</div>
            <span class="cat-stat-sub">{{ $totalCategories > 0 ? round(($activeCategories / $totalCategories) * 100) : 0 }}% enabled for public discovery</span>
        </div>
        <div class="cat-stat-card accent-dark">
            <span class="cat-stat-kicker">Configured Ecosystems</span>
            <div class="cat-stat-val">{{ $configuredProjects }} / {{ $projects->count() }}</div>
            <span class="cat-stat-sub">Ecosystems with defined catalog tags</span>
        </div>
        <div class="cat-stat-card accent-blue">
            <span class="cat-stat-kicker">Avg. per Project</span>
            <div class="cat-stat-val">{{ $projects->count() > 0 ? round($totalCategories / $projects->count(), 1) : 0 }}</div>
            <span class="cat-stat-sub">Optimal catalog segmentation depth</span>
        </div>
    </div>

    {{-- Add New Category Form Card --}}
    <div class="cat-create-card" id="createCategoryCard">
        <div class="cat-create-head">
            <div class="cat-create-title">
                <div class="cat-create-icon">+</div>
                <div>
                    <h3>Create Marketplace Category</h3>
                    <p>Add a new category classification to an ecosystem catalog.</p>
                </div>
            </div>
            <span class="status published" style="font-size: 10px;">Ecosystem Taxonomy Builder</span>
        </div>

        <form method="POST" action="{{ route('admin.marketplace.categories.store') }}">
            @csrf
            <div class="cat-form-grid">
                <div class="cat-field-group">
                    <label for="create_project_id">Ecosystem</label>
                    <select name="software_project_id" id="create_project_id" class="cat-select-styled" required>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" {{ old('software_project_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->name }} ({{ $p->marketplaceCategories->count() }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="cat-field-group">
                    <label for="create_name">Category Name <small>(e.g. Drivers, Themes)</small></label>
                    <input type="text" name="name" id="create_name" class="cat-input-styled" placeholder="Name" value="{{ old('name') }}" required>
                </div>

                <div class="cat-field-group">
                    <label for="create_description">Description <small>(Optional)</small></label>
                    <input type="text" name="description" id="create_description" class="cat-input-styled" placeholder="Short description for browse experience" value="{{ old('description') }}">
                </div>

                <div class="cat-field-group">
                    <label for="create_icon">Icon <small>(Key / Symbol)</small></label>
                    <input type="text" name="icon" id="create_icon" class="cat-input-styled" placeholder="e.g. terminal" value="{{ old('icon') }}">
                </div>

                <div class="cat-field-group">
                    <label for="create_sort">Sort <small>(Order)</small></label>
                    <input type="number" name="sort_order" id="create_sort" class="cat-input-styled" value="{{ old('sort_order', 0) }}" min="0">
                </div>

                <div class="cat-field-group" style="padding-bottom: 2px;">
                    <button type="submit" class="cat-btn-submit">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Add category</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Interactive Ecosystem Filter Toolbar & Live Search --}}
    <div class="cat-filter-bar">
        <div class="cat-pills-list">
            <button type="button" class="cat-pill-btn active" onclick="filterEcosystem('all', this)">
                <span>All Ecosystems</span>
                <span class="cat-pill-badge">{{ $totalCategories }}</span>
            </button>
            @foreach($projects as $p)
                <button type="button" class="cat-pill-btn" onclick="filterEcosystem('project-{{ $p->id }}', this)">
                    <span>{{ $p->name }}</span>
                    <span class="cat-pill-badge">{{ $p->marketplaceCategories->count() }}</span>
                </button>
            @endforeach
        </div>

        <div class="cat-search-box">
            <span class="cat-search-icon">🔍</span>
            <input type="text" id="categorySearchInput" placeholder="Filter categories by name..." oninput="handleCategorySearch(this.value)">
        </div>
    </div>

    {{-- Ecosystem Groups with Category Tables --}}
    @foreach($projects as $p)
        <div class="eco-group-card" id="project-{{ $p->id }}" data-project-name="{{ strtolower($p->name) }}">
            {{-- Group Card Header --}}
            <div class="eco-group-header">
                <div class="eco-group-info">
                    <div class="eco-group-badge">
                        {{ strtoupper(substr($p->name, 0, 2)) }}
                    </div>
                    <div>
                        <h3 class="eco-group-name">
                            {{ $p->name }}
                            <span class="eco-tag">{{ $p->category ?? 'Software' }}</span>
                        </h3>
                        <div class="eco-group-meta">
                            <span>{{ $p->marketplaceCategories->count() }} categories defined</span>
                            @if($p->marketplaceCategories->where('is_active', true)->count() > 0)
                                · <span style="color: #176b4e; font-weight: 700;">{{ $p->marketplaceCategories->where('is_active', true)->count() }} active</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="eco-group-actions">
                    <button type="button" class="eco-add-btn" onclick="addCategoryToProject({{ $p->id }}, '{{ addslashes($p->name) }}')">
                        <span>+ Add to {{ $p->name }}</span>
                    </button>
                </div>
            </div>

            {{-- Categories Table --}}
            @if($p->marketplaceCategories->isEmpty())
                <div class="cat-empty-state">
                    No categories have been defined for {{ $p->name }} yet. Click <strong>+ Add to {{ $p->name }}</strong> to get started.
                </div>
            @else
                <table class="cat-table">
                    <thead>
                        <tr>
                            <th style="width: 75px; text-align: center;">Order</th>
                            <th style="width: 140px;">Icon</th>
                            <th style="width: 260px;">Category Name & Slug</th>
                            <th>Description</th>
                            <th style="width: 110px; text-align: center;">Status</th>
                            <th style="width: 120px; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($p->marketplaceCategories as $c)
                            {{-- Dedicated hidden forms for HTML5 table isolation --}}
                            <form id="edit-cat-{{ $c->id }}" method="POST" action="{{ route('admin.marketplace.categories.update', $c) }}">
                                @csrf
                                @method('PUT')
                            </form>
                            <form id="del-cat-{{ $c->id }}" method="POST" action="{{ route('admin.marketplace.categories.destroy', $c) }}" onsubmit="return confirm('Are you sure you want to delete category \'{{ addslashes($c->name) }}\'?')">
                                @csrf
                                @method('DELETE')
                            </form>

                            <tr class="category-row" data-cat-name="{{ strtolower($c->name) }}" data-cat-desc="{{ strtolower($c->description ?? '') }}">
                                {{-- Sort Order --}}
                                <td style="text-align: center;">
                                    <input type="number" name="sort_order" value="{{ $c->sort_order }}" form="edit-cat-{{ $c->id }}" class="row-input-sort" title="Display Order" min="0">
                                </td>

                                {{-- Icon Identifier & Preview Badge --}}
                                <td>
                                    <div class="icon-preview-box">
                                        <div class="icon-badge" title="Visual Icon Identifier">
                                            @if($c->icon)
                                                <span>{{ substr($c->icon, 0, 3) }}</span>
                                            @else
                                                <span style="color:#a0afa7;">◇</span>
                                            @endif
                                        </div>
                                        <input type="text" name="icon" value="{{ $c->icon }}" form="edit-cat-{{ $c->id }}" class="row-input-icon" placeholder="icon key">
                                    </div>
                                </td>

                                {{-- Name & Slug --}}
                                <td>
                                    <input type="text" name="name" value="{{ $c->name }}" form="edit-cat-{{ $c->id }}" class="row-input-name" required placeholder="Category name">
                                    <span class="row-slug-hint">/{{ $c->slug }}</span>
                                </td>

                                {{-- Description --}}
                                <td>
                                    <input type="text" name="description" value="{{ $c->description }}" form="edit-cat-{{ $c->id }}" class="row-input-desc" placeholder="Describe the extensions in this category...">
                                </td>

                                {{-- Status (Active / Hidden) --}}
                                <td style="text-align: center;">
                                    <label class="cat-toggle-wrap">
                                        <input type="checkbox" name="is_active" value="1" {{ $c->is_active ? 'checked' : '' }} form="edit-cat-{{ $c->id }}">
                                        <span style="{{ $c->is_active ? 'color: #176b4e; font-weight:700;' : 'color: #8c9c94;' }}">
                                            {{ $c->is_active ? 'Active' : 'Draft' }}
                                        </span>
                                    </label>
                                </td>

                                {{-- Actions: Save & Delete --}}
                                <td>
                                    <div class="row-actions-cell">
                                        <button type="submit" form="edit-cat-{{ $c->id }}" class="btn-row-save" title="Save changes">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                            <span>Save</span>
                                        </button>
                                        <button type="submit" form="del-cat-{{ $c->id }}" class="btn-row-delete" title="Delete category">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endforeach

</div>

<script>
    function focusCreateForm() {
        const card = document.getElementById('createCategoryCard');
        if (card) {
            card.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(() => {
                const nameInput = document.getElementById('create_name');
                if (nameInput) nameInput.focus();
            }, 300);
        }
    }

    function addCategoryToProject(projectId, projectName) {
        const select = document.getElementById('create_project_id');
        if (select) {
            select.value = projectId;
        }
        focusCreateForm();
    }

    function filterEcosystem(targetId, btn) {
        // Update active pill button
        document.querySelectorAll('.cat-pill-btn').forEach(b => b.classList.remove('active'));
        if (btn) btn.classList.add('active');

        const cards = document.querySelectorAll('.eco-group-card');
        if (targetId === 'all') {
            cards.forEach(card => card.style.display = '');
        } else {
            cards.forEach(card => {
                card.style.display = (card.id === targetId) ? '' : 'none';
            });
        }
    }

    function handleCategorySearch(query) {
        const term = (query || '').toLowerCase().trim();
        const rows = document.querySelectorAll('.category-row');
        const cards = document.querySelectorAll('.eco-group-card');

        if (!term) {
            rows.forEach(r => r.style.display = '');
            cards.forEach(c => c.style.display = '');
            return;
        }

        // Show matching rows, hide non-matching
        cards.forEach(card => {
            const cardRows = card.querySelectorAll('.category-row');
            let hasMatch = false;

            cardRows.forEach(row => {
                const name = row.getAttribute('data-cat-name') || '';
                const desc = row.getAttribute('data-cat-desc') || '';
                if (name.includes(term) || desc.includes(term)) {
                    row.style.display = '';
                    hasMatch = true;
                } else {
                    row.style.display = 'none';
                }
            });

            card.style.display = hasMatch ? '' : 'none';
        });
    }
</script>
@endsection
