@extends('developer.layout', ['title' => 'Developer Center · RozeHub'])

@section('content')
@php
    $projectLogos = [
        'dbnavigator' => 'dbnavigator.png',
        'lumina' => 'lumina.png',
        'novaos' => 'novaos.png',
        'roze' => 'roze.png',
        'stratosdb' => 'stratosdb.png',
        'thundercall' => 'thundercall.png',
        'trackeye' => 'trackeye.png',
    ];
@endphp

<!-- Hero Banner -->
<section class="dev-hero">
    <div class="dev-hero-content">
        <div class="dev-hero-badge">
            <span class="pulse-dot"></span>
            DEVELOPER MARKETPLACE PLATFORM
        </div>
        <h1>Build once. <span class="highlight-text">Publish across the ecosystem.</span></h1>
        <p>Create plugins, drivers, modules, and extensions for all 7 RozeHub platforms — Lumina, DBNavigator, NOVAOS, StratosDB, Roze, ThunderCall, and TrackEye. Every release undergoes automated vulnerability checks and risk assessment before publication.</p>
        <div class="dev-hero-features">
            <span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                Multi-Runtime Sandboxing
            </span>
            <span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                Automated Risk Scoring
            </span>
            <span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                Fast-Track Moderation
            </span>
            <span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                Chunked Release Uploads
            </span>
        </div>
    </div>
    <div class="dev-hero-actions">
        <a class="dev-primary-btn" href="{{ route('developer.marketplace.create') }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Create Plugin / Extension
        </a>
        <a class="dev-secondary-btn" href="{{ route('developer.marketplace.submissions') }}">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
            Review Submissions
        </a>
    </div>
</section>

<!-- Stat Metric Counters -->
<div class="dev-stat-grid">
    <div class="dev-stat-card">
        <div class="dev-stat-card-top">
            <div class="dev-stat-icon emerald">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
            </div>
            <span class="dev-stat-badge">EXTENSIONS</span>
        </div>
        <div class="dev-stat-val">{{ $items->count() }}</div>
        <div class="dev-stat-label">Marketplace Items</div>
        <p class="dev-stat-sub">Registered by your account</p>
    </div>

    <div class="dev-stat-card">
        <div class="dev-stat-card-top">
            <div class="dev-stat-icon blue">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
            </div>
            <span class="dev-stat-badge">RELEASES</span>
        </div>
        <div class="dev-stat-val">{{ $submissions->total() }}</div>
        <div class="dev-stat-label">Total Submissions</div>
        <p class="dev-stat-sub">Version builds processed</p>
    </div>

    <div class="dev-stat-card">
        <div class="dev-stat-card-top">
            <div class="dev-stat-icon amber">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </div>
            <span class="dev-stat-badge">QUEUE</span>
        </div>
        <div class="dev-stat-val">{{ $submissions->whereIn('status',['SUBMITTED','UNDER_REVIEW'])->count() }}</div>
        <div class="dev-stat-label">In Review</div>
        <p class="dev-stat-sub">Pending moderation sign-off</p>
    </div>

    <div class="dev-stat-card">
        <div class="dev-stat-card-top">
            <div class="dev-stat-icon violet">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
            </div>
            <span class="dev-stat-badge">AUDIT</span>
        </div>
        <div class="dev-stat-val">{{ $unread }}</div>
        <div class="dev-stat-label">Unread Alerts</div>
        <p class="dev-stat-sub">Review feedback & updates</p>
    </div>
</div>

<!-- Ecosystem Section -->
<div class="dev-section-head">
    <div class="dev-section-head-info">
        <span class="eyebrow">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
            ROZEHUB ECOSYSTEM
        </span>
        <h2>Build for the right product</h2>
        <p>Each of the seven RozeHub projects has its own extension runtime, manifest schema, and capability profile. Select a product to create a targeted plugin or review allowed capabilities.</p>
    </div>
</div>

<div class="ecosystem-dashboard-grid">
    @foreach($ecosystems as $project)
        @php
            $key = strtolower(preg_replace('/[^a-z0-9]/', '', $project->name));
            $logo = $projectLogos[$key] ?? null;
        @endphp
        <article class="eco-card">
            <div>
                <div class="eco-card-top">
                    <div class="eco-logo-wrap" title="{{ $project->name }}">
                        @if($logo)
                            <img src="{{ asset('images/projects/' . $logo) }}" alt="{{ $project->name }}">
                        @else
                            <div class="eco-monogram">{{ substr($project->name, 0, 2) }}</div>
                        @endif
                    </div>
                    <span class="eco-type-tag">{{ strtoupper(str_replace('_',' ',$project->ecosystemProfile->ecosystem_type)) }}</span>
                </div>
                <div class="eco-card-body">
                    <h3>{{ $project->name }}</h3>
                    <div class="eco-card-subtitle">{{ $project->ecosystemProfile->title }}</div>
                    <p class="eco-card-desc">{{ $project->ecosystemProfile->description }}</p>
                    <div class="chip-list">
                        @foreach(array_slice($project->ecosystemProfile->item_types ?? [], 0, 4) as $type)
                            <span class="chip">{{ $type }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="eco-card-footer">
                <a href="{{ route('developer.marketplace.create', ['project_id' => $project->id]) }}" class="eco-btn-action">
                    <span>Build for {{ $project->name }}</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </a>
            </div>
        </article>
    @endforeach

    <!-- 8th Companion Card: Completes the 4x2 grid perfectly without orphans -->
    <article class="eco-card eco-card-sdk">
        <div>
            <div class="eco-card-top">
                <div class="eco-logo-wrap" style="background:#0c221a; border-color: rgba(56, 220, 156, 0.3);">
                    <img src="{{ asset('images/rozehub-icon.png') }}" alt="RozeHub SDK" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <span class="eco-monogram" style="display:none; color:var(--dev-mint);">SDK</span>
                </div>
                <span class="eco-type-tag" style="background:var(--dev-emerald-light); color:var(--dev-emerald-dark); border-color:var(--dev-emerald-border);">DOCUMENTATION</span>
            </div>
            <div class="eco-card-body">
                <h3>Extension Architecture SDK</h3>
                <div class="eco-card-subtitle">Universal Manifest & Sandboxing</div>
                <p class="eco-card-desc">Review developer specifications for IPC communication bridges, secure permissions, manifest.json schemas, and automated security risk scanners.</p>
                <div class="chip-list">
                    <span class="chip">manifest.json</span>
                    <span class="chip">ipc-bridge</span>
                    <span class="chip">sandbox</span>
                    <span class="chip">audit-v1</span>
                </div>
            </div>
        </div>
        <div class="eco-card-footer">
            <a href="{{ route('docs.index') }}" class="eco-btn-action">
                <span>Browse Developer Docs</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </a>
        </div>
    </article>
</div>

<!-- My Marketplace Items Section -->
<div class="dev-section-head">
    <div class="dev-section-head-info">
        <span class="eyebrow">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
            MY MARKETPLACE ITEMS
        </span>
        <h2>Your plugins & extensions</h2>
        <p>Manage version releases, edit metadata, and track public availability for your extensions.</p>
    </div>
    <a href="{{ route('developer.marketplace.create') }}" class="dev-head-action">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        + New Item
    </a>
</div>

<div class="dev-items-grid">
    @forelse($items as $item)
        <article class="item-card">
            <div>
                <div class="item-card-top">
                    <div class="item-card-target">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>{{ $item->project->name }}</span>
                    </div>
                    <div class="item-badges-group">
                        <span class="dev-badge {{ $item->item_type }}">{{ ucfirst($item->item_type) }}</span>
                        @if($item->is_published)
                            <span class="badge-status is-published" title="Visible on public marketplace"><span class="dot"></span> Published</span>
                        @else
                            <span class="badge-status is-draft" title="Draft status - submit a release to publish"><span class="dot"></span> Draft</span>
                        @endif
                    </div>
                </div>

                <h3 class="item-card-title">{{ $item->name }}</h3>
                <div class="item-card-id">ID: <code>{{ $item->item_id ?: $item->slug }}</code></div>
                <p class="item-card-desc">{{ $item->summary ?: 'No summary description provided yet. Click edit details to provide documentation and features.' }}</p>

                <div class="item-card-meta">
                    <span>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg>
                        {{ $item->releases->count() }} {{ \Illuminate\Support\Str::plural('release', $item->releases->count()) }}
                    </span>
                    <span>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>
                        Updated {{ $item->updated_at->diffForHumans() }}
                    </span>
                </div>
            </div>

            <div class="item-card-actions">
                <a href="{{ route('developer.marketplace.releases.create', $item) }}" class="btn-card-release">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    New release
                </a>
                <a href="{{ route('developer.marketplace.edit', $item) }}" class="btn-card-edit">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    Edit details
                </a>
                @if($item->is_published)
                    <a href="{{ route('marketplace.item', $item->slug) }}" class="btn-card-public" target="_blank">
                        View item ↗
                    </a>
                @endif
            </div>
        </article>
    @empty
        <div class="dev-empty-box" style="grid-column: 1 / -1;">
            <div class="dev-empty-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
            </div>
            <h3>No marketplace items registered yet</h3>
            <p>You haven't created any plugins or extensions yet. Pick a target platform from the ecosystem above or start by creating a new extension.</p>
            <a href="{{ route('developer.marketplace.create') }}" class="dev-primary-btn" style="color:#07261d;">
                + Create Your First Extension
            </a>
        </div>
    @endforelse
</div>

<!-- Review Status / Submissions Section -->
<div class="dev-section-head">
    <div class="dev-section-head-info">
        <span class="eyebrow">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
            MODERATION & REVIEW PIPELINE
        </span>
        <h2>Review status</h2>
        <p>Real-time automated risk evaluation, sandbox checks, and review queue progress.</p>
    </div>
    <a href="{{ route('developer.marketplace.submissions') }}" class="dev-head-action">
        View all submissions →
    </a>
</div>

<div class="dev-table-card">
    <table>
        <thead>
            <tr>
                <th>Item / Extension</th>
                <th>Release</th>
                <th>Status</th>
                <th>Security Risk</th>
                <th>Submitted</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($submissions as $s)
                <tr>
                    <td>
                        <a href="{{ route('developer.marketplace.submission', $s) }}" class="table-item-name">
                            {{ $s->item->name }}
                        </a>
                        <div class="table-item-meta">
                            <span class="dev-badge {{ $s->item->item_type }}">{{ ucfirst($s->item->item_type) }}</span>
                            <span style="font-size:11.5px; color:var(--dev-muted);">{{ $s->item->project->name }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="table-version-badge">
                            {{ $s->release?->version ? 'v' . $s->release->version : '—' }}
                        </span>
                    </td>
                    <td>
                        <span class="status-pill status-{{ strtolower($s->status) }}">
                            ● {{ str_replace('_', ' ', $s->status) }}
                        </span>
                    </td>
                    <td>
                        <span class="risk-pill risk-{{ strtolower($s->risk_level) }}">
                            {{ $s->risk_level }} · {{ $s->risk_score }}/100
                        </span>
                    </td>
                    <td class="table-time">
                        {{ optional($s->submitted_at ?? $s->created_at)->diffForHumans() ?? 'Draft' }}
                    </td>
                    <td>
                        <a href="{{ route('developer.marketplace.submission', $s) }}" class="table-action-link">
                            Details →
                        </a>
                    </td>
                </tr>
            @empty
                <tr class="table-empty-row">
                    <td colspan="6">
                        <div class="table-empty-content">
                            <div class="table-empty-icon">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                            </div>
                            <h4 class="table-empty-title">No submissions currently in review</h4>
                            <p class="table-empty-sub">When you package a release for one of your plugins or extensions and submit it, automated vulnerability analysis, risk scoring, and reviewer notes will appear here.</p>
                            @if($items->isNotEmpty())
                                <a href="{{ route('developer.marketplace.releases.create', $items->first()) }}" class="dev-head-action" style="margin-top: 14px;">
                                    + Submit release for {{ $items->first()->name }}
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
