@extends('admin.layout')

@php
    $heading = 'Ecosystem Experience';
    $title = 'Ecosystem Experience & Health Intelligence · RozeHub Admin';
    $avgHealth = round($projects->avg(fn($p) => $healthData[$p->id]['score'] ?? 0));
    $topProject = $projects->sortByDesc(fn($p) => $healthData[$p->id]['score'] ?? 0)->first();
    $totalRoadmaps = $projects->sum(fn($p) => $p->roadmaps->count());
@endphp

@section('content')
<style>
    /* Scoped Ecosystem Experience Dashboard Styles */
    .eco-shell {
        display: flex;
        flex-direction: column;
        gap: 22px;
    }

    /* Page Header */
    .eco-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }
    .eco-breadcrumbs {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: var(--admin-muted);
        margin-bottom: 6px;
    }
    .eco-breadcrumbs a {
        color: var(--admin-muted);
        text-decoration: none;
    }
    .eco-breadcrumbs a:hover {
        color: var(--admin-ink);
        text-decoration: underline;
    }
    .eco-breadcrumbs .sep {
        color: #b8c4be;
    }
    .eco-title-row h2 {
        margin: 0;
        font-size: 26px;
        font-weight: 800;
        letter-spacing: -0.6px;
        color: var(--admin-ink);
    }
    .eco-subtitle {
        margin: 4px 0 0;
        font-size: 13px;
        color: var(--admin-muted);
    }
    .eco-header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .eco-btn-primary {
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
        text-decoration: none;
    }
    .eco-btn-primary:hover {
        background: #183831;
        transform: translateY(-1px);
        color: #fff;
    }
    .eco-btn-primary svg {
        color: #66ddae;
    }
    .eco-btn-secondary {
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
    .eco-btn-secondary:hover {
        background: #f8faf8;
        border-color: #cbd7d0;
    }

    /* Top Overview Metric Strip */
    .eco-stats-strip {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
    }
    .eco-stat-box {
        background: #ffffff;
        border: 1px solid var(--admin-line);
        border-radius: 12px;
        padding: 18px 20px;
        box-shadow: 0 2px 10px rgba(16,34,30,0.03);
    }
    .eco-stat-box small {
        display: block;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--admin-muted);
        margin-bottom: 4px;
    }
    .eco-stat-box strong {
        display: block;
        font-size: 26px;
        font-weight: 800;
        color: var(--admin-ink);
        letter-spacing: -0.5px;
        line-height: 1.1;
    }
    .eco-stat-box span {
        display: block;
        font-size: 11px;
        color: #6a7c75;
        margin-top: 4px;
    }

    /* Projects Experience Grid */
    .eco-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
        gap: 20px;
    }

    /* Project Card */
    .eco-card {
        background: #ffffff;
        border: 1px solid var(--admin-line);
        border-radius: 14px;
        box-shadow: 0 4px 18px rgba(16,34,30,0.04);
        padding: 24px;
        display: flex;
        flex-direction: column;
        transition: transform 0.15s, border-color 0.15s;
    }
    .eco-card:hover {
        border-color: #c4d4cc;
    }

    /* Card Top Header */
    .eco-card-top {
        display: flex;
        gap: 14px;
        align-items: flex-start;
        margin-bottom: 16px;
    }
    .eco-project-logo {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        background: #f7faf8;
        border: 1px solid #dce5df;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
        padding: 4px;
        box-sizing: border-box;
    }
    .eco-project-logo img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    .eco-card-title-group {
        flex: 1;
        min-width: 0;
    }
    .eco-card-title-group h3 {
        margin: 0 0 2px;
        font-size: 18px;
        font-weight: 800;
        color: var(--admin-ink);
        letter-spacing: -0.3px;
    }
    .eco-card-category {
        font-size: 11px;
        color: var(--admin-muted);
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.4px;
    }
    .eco-card-tagline {
        font-size: 12px;
        color: #4b5d56;
        line-height: 1.45;
        margin: 8px 0 16px;
        min-height: 36px;
    }

    /* Health Gauge & Score Section */
    .eco-health-section {
        background: #f8faf8;
        border: 1px solid #e2e8e4;
        border-radius: 10px;
        padding: 14px 16px;
        margin-bottom: 18px;
    }
    .eco-health-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }
    .eco-health-score-val {
        font-size: 24px;
        font-weight: 800;
        letter-spacing: -0.5px;
    }
    .eco-health-score-val.high { color: #15803d; }
    .eco-health-score-val.med { color: #b45309; }
    .eco-health-score-val.low { color: #b91c1c; }

    .eco-health-badge {
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        padding: 2px 7px;
        border-radius: 10px;
        letter-spacing: 0.4px;
    }
    .eco-health-badge.high { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .eco-health-badge.med { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .eco-health-badge.low { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

    /* Health Progress Bar */
    .eco-health-bar-track {
        height: 6px;
        background: #e5ece7;
        border-radius: 3px;
        overflow: hidden;
        margin-bottom: 12px;
    }
    .eco-health-bar-fill {
        height: 100%;
        border-radius: 3px;
        transition: width 0.3s ease;
    }
    .eco-health-bar-fill.high { background: linear-gradient(90deg, #22c55e, #16a34a); }
    .eco-health-bar-fill.med { background: linear-gradient(90deg, #f59e0b, #d97706); }
    .eco-health-bar-fill.low { background: linear-gradient(90deg, #ef4444, #dc2626); }

    /* Breakdown Mini Stats */
    .eco-breakdown-row {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 6px;
        text-align: center;
        font-size: 10px;
        border-top: 1px solid #e7eee9;
        padding-top: 10px;
    }
    .eco-breakdown-col small {
        display: block;
        color: var(--admin-muted);
        margin-bottom: 2px;
        font-size: 9px;
    }
    .eco-breakdown-col strong {
        font-weight: 700;
        color: var(--admin-ink);
    }

    /* Calibrate Metric Form Section */
    .eco-section-box {
        border: 1px solid #edf1ee;
        background: #ffffff;
        border-radius: 10px;
        padding: 14px 16px;
        margin-bottom: 16px;
    }
    .eco-section-box-title {
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--admin-muted);
        margin: 0 0 10px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .eco-form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-bottom: 10px;
    }
    .eco-field {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .eco-field label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        color: #43544e;
    }
    .eco-input {
        width: 100%;
        box-sizing: border-box;
        border: 1px solid #cfdad3;
        border-radius: 6px;
        padding: 7px 10px;
        font-size: 12px;
        font-family: inherit;
        color: var(--admin-ink);
        background: #ffffff;
        transition: all 0.15s;
    }
    .eco-input:focus {
        border-color: #2b8a73;
        outline: 0;
        box-shadow: 0 0 0 3px rgba(43, 138, 115, 0.12);
    }
    .eco-btn-save {
        width: 100%;
        background: #ffffff;
        border: 1px solid #c9d7d0;
        color: var(--admin-ink);
        font-weight: 700;
        font-size: 12px;
        border-radius: 6px;
        padding: 7px 12px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all 0.12s;
    }
    .eco-btn-save:hover {
        background: #f2f7f4;
        border-color: #b3c5bc;
    }

    /* Roadmaps Section */
    .eco-roadmaps-box {
        margin-top: auto;
    }
    .eco-roadmap-list {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 12px;
    }
    .eco-roadmap-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 10px;
        background: #f7faf8;
        border: 1px solid #e1e9e4;
        border-radius: 6px;
        font-size: 11px;
    }
    .eco-roadmap-item strong {
        color: var(--admin-ink);
    }
    .eco-rm-status {
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
        padding: 1px 5px;
        border-radius: 3px;
    }
    .eco-rm-status.active { background: #dcfce7; color: #166534; }
    .eco-rm-status.planned { background: #eff6ff; color: #1e40af; }
    .eco-rm-status.archived { background: #f3f4f6; color: #4b5563; }

    /* Accordion New Roadmap Form */
    .eco-new-rm-details {
        margin-top: 8px;
    }
    .eco-new-rm-details summary {
        font-size: 11px;
        font-weight: 700;
        color: #17654b;
        cursor: pointer;
        user-select: none;
        padding: 4px 0;
    }
    .eco-new-rm-form {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-top: 8px;
        padding: 12px;
        background: #fafcfa;
        border: 1px solid #e1e9e4;
        border-radius: 8px;
    }

    /* Card Footer Quick Links */
    .eco-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid #edf1ee;
        padding-top: 14px;
        margin-top: 14px;
        font-size: 11px;
    }
    .eco-footer-link {
        color: #17654b;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 3px;
    }
    .eco-footer-link:hover {
        text-decoration: underline;
    }

    @media (max-width: 960px) {
        .eco-stats-strip {
            grid-template-columns: repeat(2, 1fr);
        }
        .eco-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="eco-shell">

    <!-- Page Header & Action Bar -->
    <div class="eco-header">
        <div>
            <div class="eco-breadcrumbs">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <span class="sep">/</span>
                <a href="{{ route('admin.ecosystem.index') }}">Ecosystem</a>
                <span class="sep">/</span>
                <strong style="color:var(--admin-ink);">Ecosystem Experience</strong>
            </div>
            <div class="eco-title-row">
                <h2>Ecosystem Experience &amp; Health Intelligence</h2>
                <p class="eco-subtitle">Monitor algorithmic project health scores, fine-tune community weights, and manage release roadmaps.</p>
            </div>
        </div>

        <div class="eco-header-actions">
            <a class="eco-btn-secondary" href="{{ route('ecosystem.graph') }}" target="_blank" rel="noopener">
                View Public Graph ↗
            </a>

            <form method="POST" action="{{ route('admin.ecosystem-experience.graph.sync') }}" style="margin:0;">
                @csrf
                <button type="submit" class="eco-btn-primary">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
                    Sync Graph Nodes
                </button>
            </form>
        </div>
    </div>

    <!-- Overview Stats Strip (4 Metric Cards) -->
    <section class="eco-stats-strip">
        <div class="eco-stat-box">
            <small>Average Ecosystem Health</small>
            <strong style="color: {{ $avgHealth >= 50 ? '#15803d' : '#b45309' }};">{{ $avgHealth }}%</strong>
            <span>Aggregated cross-project health index</span>
        </div>

        <div class="eco-stat-box">
            <small>Top Performing Project</small>
            <strong>{{ $topProject?->name ?? 'None' }}</strong>
            <span>Health score: {{ $healthData[$topProject?->id]['score'] ?? 0 }}%</span>
        </div>

        <div class="eco-stat-box">
            <small>Connected Projects</small>
            <strong>{{ $projects->count() }}</strong>
            <span>Active in software registry</span>
        </div>

        <div class="eco-stat-box">
            <small>Total Roadmaps</small>
            <strong>{{ $totalRoadmaps }}</strong>
            <span>Public roadmap milestones</span>
        </div>
    </section>

    <!-- Projects Experience Grid -->
    <div class="eco-grid">
        @foreach($projects as $project)
            @php
                $h = $healthData[$project->id] ?? ['score' => 0, 'breakdown' => []];
                $score = $h['score'];
                $bd = $h['breakdown'];
                $badgeClass = $score >= 60 ? 'high' : ($score >= 40 ? 'med' : 'low');
                $badgeText = $score >= 60 ? 'Optimal Health' : ($score >= 40 ? 'Moderate Activity' : 'Needs Attention');
                $logoPath = file_exists(public_path('images/projects/'.$project->slug.'.png')) ? asset('images/projects/'.$project->slug.'.png') : null;
            @endphp

            <article class="eco-card">
                <!-- Card Header -->
                <div class="eco-card-top">
                    <div class="eco-project-logo">
                        @if($logoPath)
                            <img src="{{ $logoPath }}" alt="{{ $project->name }}">
                        @else
                            <strong style="font-size:16px; color:#175e45;">{{ strtoupper(substr($project->name, 0, 1)) }}</strong>
                        @endif
                    </div>
                    <div class="eco-card-title-group">
                        <h3>{{ $project->name }}</h3>
                        <span class="eco-card-category">{{ $project->category }}</span>
                    </div>
                </div>

                <p class="eco-card-tagline">{{ $project->tagline ?: $project->description }}</p>

                <!-- Health Score Gauge -->
                <div class="eco-health-section">
                    <div class="eco-health-head">
                        <span style="font-size:11px; font-weight:700; color:var(--admin-muted); text-transform:uppercase;">Health Score</span>
                        <span class="eco-health-badge {{ $badgeClass }}">{{ $badgeText }}</span>
                    </div>

                    <div class="eco-health-head">
                        <div class="eco-health-score-val {{ $badgeClass }}">{{ $score }}%</div>
                        <span style="font-size:11px; color:#6b7e77;">Calculated composite</span>
                    </div>

                    <div class="eco-health-bar-track">
                        <div class="eco-health-bar-fill {{ $badgeClass }}" style="width: {{ $score }}%;"></div>
                    </div>

                    <!-- Breakdown Mini Stats -->
                    <div class="eco-breakdown-row">
                        <div class="eco-breakdown-col" title="GitHub Activity">
                            <small>GitHub</small>
                            <strong>{{ $bd['github_activity'] ?? 0 }}%</strong>
                        </div>
                        <div class="eco-breakdown-col" title="Release Freshness">
                            <small>Releases</small>
                            <strong>{{ $bd['release_freshness'] ?? 0 }}%</strong>
                        </div>
                        <div class="eco-breakdown-col" title="Documentation Coverage">
                            <small>Docs</small>
                            <strong>{{ $bd['documentation'] ?? 0 }}%</strong>
                        </div>
                        <div class="eco-breakdown-col" title="Community Contributions">
                            <small>Comm.</small>
                            <strong>{{ $bd['community_activity'] ?? 0 }}%</strong>
                        </div>
                        <div class="eco-breakdown-col" title="Marketplace Extensions">
                            <small>Market</small>
                            <strong>{{ $bd['marketplace_activity'] ?? 0 }}%</strong>
                        </div>
                    </div>
                </div>

                <!-- Calibrate Metric Weight Form -->
                <div class="eco-section-box">
                    <div class="eco-section-box-title">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                        Calibrate Community Activity
                    </div>

                    <form method="POST" action="{{ route('admin.ecosystem-experience.metrics.save', $project) }}">
                        @csrf
                        <input type="hidden" name="metric_key" value="community_activity">

                        <div class="eco-form-row">
                            <div class="eco-field">
                                <label for="weight_{{ $project->id }}">Weight (Multiplier)</label>
                                <input id="weight_{{ $project->id }}" class="eco-input" name="weight" type="number" step="0.1" value="1.0" required>
                            </div>
                            <div class="eco-field">
                                <label for="score_{{ $project->id }}">Score Override (0-100)</label>
                                <input id="score_{{ $project->id }}" class="eco-input" name="score" type="number" min="0" max="100" value="{{ $bd['community_activity'] ?? 0 }}" required>
                            </div>
                        </div>

                        <button class="eco-btn-save" type="submit">
                            Save Metric Adjustment
                        </button>
                    </form>
                </div>

                <!-- Roadmaps Section -->
                <div class="eco-roadmaps-box">
                    <div class="eco-section-box-title">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                        Roadmaps &amp; Milestones ({{ $project->roadmaps->count() }})
                    </div>

                    @if($project->roadmaps->count() > 0)
                        <div class="eco-roadmap-list">
                            @foreach($project->roadmaps as $rm)
                                <div class="eco-roadmap-item">
                                    <div>
                                        <strong>{{ $rm->title }}</strong>
                                        <span style="font-size:10px; color:var(--admin-muted); display:block;">{{ $rm->items->count() }} task(s)</span>
                                    </div>
                                    <span class="eco-rm-status {{ $rm->status }}">{{ $rm->status }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Add Roadmap Drawer -->
                    <details class="eco-new-rm-details">
                        <summary>+ Add New Roadmap</summary>
                        <form method="POST" action="{{ route('admin.ecosystem-experience.roadmaps.save', $project) }}" class="eco-new-rm-form">
                            @csrf
                            <input class="eco-input" name="title" placeholder="Roadmap Title (e.g. v2.0 Architecture)" required>
                            <input class="eco-input" name="description" placeholder="Short description or goal">
                            <select class="eco-input" name="status">
                                <option value="active">Active Roadmap</option>
                                <option value="planned">Planned Milestone</option>
                                <option value="archived">Archived</option>
                            </select>
                            <button class="eco-btn-save" type="submit" style="background:#10221e; color:#fff; border-color:#10221e;">
                                Create Roadmap
                            </button>
                        </form>
                    </details>
                </div>

                <!-- Card Footer Navigation -->
                <div class="eco-card-footer">
                    <a href="{{ route('admin.github.show', $project) }}" class="eco-footer-link">
                        GitHub Sync →
                    </a>
                    <a href="{{ route('admin.ecosystem.edit', $project) }}" class="eco-footer-link">
                        Policy →
                    </a>
                    <a href="{{ route('ecosystem.roadmap', $project) }}" target="_blank" rel="noopener" class="eco-footer-link">
                        Public View ↗
                    </a>
                </div>
            </article>
        @endforeach
    </div>

</div>
@endsection
