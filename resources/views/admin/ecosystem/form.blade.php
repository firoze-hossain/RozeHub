@extends('admin.layout', [
    'heading' => 'Ecosystem Policy',
    'title' => 'Edit ' . $project->name . ' Policy · RozeHub Admin'
])

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
    $logoKey = strtolower(preg_replace('/[^a-z0-9]/', '', $project->name));
    $logoFile = $projectLogos[$logoKey] ?? null;

    $commonEcoTypes = [
        'desktop_application' => 'Desktop Application',
        'development_environment' => 'Development Environment / IDE',
        'operating_system' => 'Operating System',
        'programming_language' => 'Programming Language & Runtime',
        'database_engine' => 'Database Engine',
        'api_client' => 'API Client & Tooling',
        'monitoring_platform' => 'Observability & Monitoring Platform',
    ];
@endphp

<!-- Hero Banner -->
<div class="eco-admin-hero">
    <div class="eco-hero-main">
        <div class="eco-hero-logo">
            @if($logoFile)
                <img src="{{ asset('images/projects/' . $logoFile) }}" alt="{{ $project->name }}">
            @else
                <span class="eco-hero-monogram">{{ substr($project->name, 0, 2) }}</span>
            @endif
        </div>
        <div class="eco-hero-meta">
            <span class="eco-hero-kicker">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                ECOSYSTEM SPECIFICATION
            </span>
            <h2 class="eco-hero-title">
                {{ $project->name }}
                <span class="eco-hero-cat-tag">{{ $project->category }}</span>
            </h2>
            <p class="eco-hero-desc">{{ $project->tagline ?: 'Configure runtime extension capabilities, manifest schemas, and marketplace distribution policies.' }}</p>
        </div>
    </div>
    <div class="eco-hero-side">
        @if($profile->marketplace_enabled)
            <span class="eco-badge-pill active">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10"></circle></svg>
                Marketplace Enabled
            </span>
        @else
            <span class="eco-badge-pill inactive">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle></svg>
                Marketplace Disabled
            </span>
        @endif
        <a href="{{ route('admin.ecosystem.index') }}" class="eco-hero-link">
            ← Back to all ecosystems
        </a>
    </div>
</div>

<form method="POST" action="{{ route('admin.ecosystem.update', $project) }}" class="eco-policy-form" id="ecosystem-policy-form">
    @csrf
    @method('PUT')

    <div class="eco-policy-grid">

        <!-- 01 / Core Identification Card -->
        <section class="eco-section-card">
            <div class="eco-section-head">
                <div>
                    <div class="eco-section-eyebrow">01 · Identification</div>
                    <h3 class="eco-section-title">Core Ecosystem Profile</h3>
                </div>
                <p class="eco-section-desc">Identifies this product category across developer submission flows and marketplace directories.</p>
            </div>

            <div class="eco-2col">
                <div class="eco-field">
                    <label class="eco-field-label" for="ecosystem_type">
                        Ecosystem Type
                        <small>Defines classification schema</small>
                    </label>
                    <select name="ecosystem_type" id="ecosystem_type" class="eco-select" required>
                        @foreach($commonEcoTypes as $val => $label)
                            <option value="{{ $val }}" @selected(old('ecosystem_type', $profile->ecosystem_type) === $val)>
                                {{ $label }} ({{ $val }})
                            </option>
                        @endforeach
                        @if(!array_key_exists($profile->ecosystem_type, $commonEcoTypes) && !empty($profile->ecosystem_type))
                            <option value="{{ $profile->ecosystem_type }}" selected>
                                Custom ({{ $profile->ecosystem_type }})
                            </option>
                        @endif
                    </select>
                </div>

                <div class="eco-field">
                    <label class="eco-field-label" for="title">
                        Ecosystem Title
                        <small>Public header on Developer Hub</small>
                    </label>
                    <input type="text" name="title" id="title" class="eco-input" value="{{ old('title', $profile->title) }}" placeholder="e.g. Database client extensions" required>
                </div>
            </div>

            <div class="eco-field" style="margin-top: 16px;">
                <label class="eco-field-label" for="description">
                    Ecosystem Description
                    <small>Summary shown on the Developer Center and marketplace browse cards</small>
                </label>
                <textarea name="description" id="description" class="eco-textarea" rows="3" placeholder="Briefly explain what developers can build for this ecosystem...">{{ old('description', $profile->description) }}</textarea>
            </div>
        </section>

        <!-- 02 / Distribution Governance Card -->
        <section class="eco-section-card">
            <div class="eco-section-head">
                <div>
                    <div class="eco-section-eyebrow">02 · Governance</div>
                    <h3 class="eco-section-title">Marketplace Governance & Security</h3>
                </div>
                <p class="eco-section-desc">Control public discovery, community contribution permissions, and automated security moderation.</p>
            </div>

            <div class="eco-governance-grid">
                <!-- Toggle 1: Marketplace Enabled -->
                <label class="eco-toggle-box {{ old('marketplace_enabled', $profile->marketplace_enabled) ? 'is-selected' : '' }}">
                    <input type="checkbox" name="marketplace_enabled" value="1" class="eco-toggle-checkbox" @checked(old('marketplace_enabled', $profile->marketplace_enabled))>
                    <div class="eco-toggle-content">
                        <div class="eco-toggle-title">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                            Marketplace Enabled
                        </div>
                        <p class="eco-toggle-text">Enable public listing, search indexation, and release downloads in the RozeHub marketplace.</p>
                    </div>
                </label>

                <!-- Toggle 2: Community Contributions -->
                <label class="eco-toggle-box {{ old('community_contributions', $profile->community_contributions) ? 'is-selected' : '' }}">
                    <input type="checkbox" name="community_contributions" value="1" class="eco-toggle-checkbox" @checked(old('community_contributions', $profile->community_contributions))>
                    <div class="eco-toggle-content">
                        <div class="eco-toggle-title">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                            Community Submissions
                        </div>
                        <p class="eco-toggle-text">Allow external developers to register new extensions and upload version builds for {{ $project->name }}.</p>
                    </div>
                </label>

                <!-- Toggle 3: Moderation Required -->
                <label class="eco-toggle-box {{ old('moderation_required', $profile->moderation_required) ? 'is-selected' : '' }}">
                    <input type="checkbox" name="moderation_required" value="1" class="eco-toggle-checkbox" @checked(old('moderation_required', $profile->moderation_required))>
                    <div class="eco-toggle-content">
                        <div class="eco-toggle-title">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                            Moderation Required
                        </div>
                        <p class="eco-toggle-text">Require automated risk scoring and manual admin review before any release version goes live.</p>
                    </div>
                </label>
            </div>
        </section>

        <!-- 03 / Extension Model & Security Capabilities Card -->
        <section class="eco-section-card">
            <div class="eco-section-head">
                <div>
                    <div class="eco-section-eyebrow">03 · Specifications</div>
                    <h3 class="eco-section-title">Extension Types & Security Sandbox Scopes</h3>
                </div>
                <p class="eco-section-desc">Specify allowed plugin kinds and permission scopes checked by the automated risk scanner.</p>
            </div>

            <div class="eco-2col">
                <!-- Item Types -->
                <div class="eco-field">
                    <label class="eco-field-label" for="item_types_textarea">
                        Allowed Item Types
                        <small>One value per line or comma-separated</small>
                    </label>
                    <div class="eco-tag-chips" id="chips-item_types">
                        @foreach(old('item_types', $profile->item_types ?? []) as $t)
                            <span class="eco-chip">{{ $t }}</span>
                        @endforeach
                    </div>
                    <textarea name="item_types[]" id="item_types_textarea" class="eco-textarea" rows="5" placeholder="plugin&#10;driver&#10;formatter&#10;theme">{{ implode("\n", old('item_types', $profile->item_types ?? [])) }}</textarea>
                    <div class="eco-presets-strip">
                        <span class="eco-presets-label">Presets:</span>
                        <button type="button" class="eco-preset-pill" onclick="appendTag('item_types_textarea', 'plugin')">+ plugin</button>
                        <button type="button" class="eco-preset-pill" onclick="appendTag('item_types_textarea', 'driver')">+ driver</button>
                        <button type="button" class="eco-preset-pill" onclick="appendTag('item_types_textarea', 'extension')">+ extension</button>
                        <button type="button" class="eco-preset-pill" onclick="appendTag('item_types_textarea', 'theme')">+ theme</button>
                        <button type="button" class="eco-preset-pill" onclick="appendTag('item_types_textarea', 'formatter')">+ formatter</button>
                        <button type="button" class="eco-preset-pill" onclick="appendTag('item_types_textarea', 'exporter')">+ exporter</button>
                        <button type="button" class="eco-preset-pill" onclick="appendTag('item_types_textarea', 'language-support')">+ language-support</button>
                    </div>
                </div>

                <!-- Capabilities -->
                <div class="eco-field">
                    <label class="eco-field-label" for="capabilities_textarea">
                        Sandbox Capabilities (Permissions)
                        <small>Evaluated during automated risk scoring</small>
                    </label>
                    <div class="eco-tag-chips" id="chips-capabilities">
                        @foreach(old('capabilities', $profile->capabilities ?? []) as $c)
                            <span class="eco-chip mono">🔒 {{ $c }}</span>
                        @endforeach
                    </div>
                    <textarea name="capabilities[]" id="capabilities_textarea" class="eco-textarea mono" rows="5" placeholder="database.connect&#10;database.read&#10;filesystem.read">{{ implode("\n", old('capabilities', $profile->capabilities ?? [])) }}</textarea>
                    <div class="eco-presets-strip">
                        <span class="eco-presets-label">Presets:</span>
                        <button type="button" class="eco-preset-pill" onclick="appendTag('capabilities_textarea', 'database.connect')">+ database.connect</button>
                        <button type="button" class="eco-preset-pill" onclick="appendTag('capabilities_textarea', 'database.read')">+ database.read</button>
                        <button type="button" class="eco-preset-pill" onclick="appendTag('capabilities_textarea', 'database.write')">+ database.write</button>
                        <button type="button" class="eco-preset-pill" onclick="appendTag('capabilities_textarea', 'filesystem.read')">+ filesystem.read</button>
                        <button type="button" class="eco-preset-pill" onclick="appendTag('capabilities_textarea', 'filesystem.write')">+ filesystem.write</button>
                        <button type="button" class="eco-preset-pill" onclick="appendTag('capabilities_textarea', 'query.execute')">+ query.execute</button>
                        <button type="button" class="eco-preset-pill" onclick="appendTag('capabilities_textarea', 'network.http')">+ network.http</button>
                    </div>
                </div>
            </div>
        </section>

        <!-- 04 / Packaging, Platforms & Runtimes Card -->
        <section class="eco-section-card">
            <div class="eco-section-head">
                <div>
                    <div class="eco-section-eyebrow">04 · Target Environment</div>
                    <h3 class="eco-section-title">Packaging, Platforms & Release Channels</h3>
                </div>
                <p class="eco-section-desc">Defines binary formats, supported operating systems, architectures, and integration targets.</p>
            </div>

            <div class="eco-4col">
                <!-- Package types -->
                <div class="eco-field">
                    <label class="eco-field-label" for="package_types_textarea">
                        Package Types (Archives)
                        <small>Allowed upload extensions</small>
                    </label>
                    <div class="eco-tag-chips" id="chips-package_types">
                        @foreach(old('package_types', $profile->package_types ?? []) as $v)
                            <span class="eco-chip mono">{{ $v }}</span>
                        @endforeach
                    </div>
                    <textarea name="package_types[]" id="package_types_textarea" class="eco-textarea mono" rows="3" placeholder="zip&#10;jar&#10;native">{{ implode("\n", old('package_types', $profile->package_types ?? [])) }}</textarea>
                    <div class="eco-presets-strip">
                        <button type="button" class="eco-preset-pill" onclick="appendTag('package_types_textarea', 'zip')">+ zip</button>
                        <button type="button" class="eco-preset-pill" onclick="appendTag('package_types_textarea', 'jar')">+ jar</button>
                        <button type="button" class="eco-preset-pill" onclick="appendTag('package_types_textarea', 'native')">+ native</button>
                        <button type="button" class="eco-preset-pill" onclick="appendTag('package_types_textarea', 'tar.gz')">+ tar.gz</button>
                    </div>
                </div>

                <!-- Platforms -->
                <div class="eco-field">
                    <label class="eco-field-label" for="platforms_textarea">
                        Supported Platforms
                        <small>Operating systems</small>
                    </label>
                    <div class="eco-tag-chips" id="chips-platforms">
                        @foreach(old('platforms', $profile->platforms ?? []) as $v)
                            <span class="eco-chip">{{ $v }}</span>
                        @endforeach
                    </div>
                    <textarea name="platforms[]" id="platforms_textarea" class="eco-textarea" rows="3" placeholder="Windows&#10;macOS&#10;Linux">{{ implode("\n", old('platforms', $profile->platforms ?? [])) }}</textarea>
                    <div class="eco-presets-strip">
                        <button type="button" class="eco-preset-pill" onclick="appendTag('platforms_textarea', 'Windows')">+ Windows</button>
                        <button type="button" class="eco-preset-pill" onclick="appendTag('platforms_textarea', 'macOS')">+ macOS</button>
                        <button type="button" class="eco-preset-pill" onclick="appendTag('platforms_textarea', 'Linux')">+ Linux</button>
                        <button type="button" class="eco-preset-pill" onclick="appendTag('platforms_textarea', 'NOVAOS')">+ NOVAOS</button>
                    </div>
                </div>

                <!-- Architectures -->
                <div class="eco-field">
                    <label class="eco-field-label" for="architectures_textarea">
                        Architectures
                        <small>CPU architectures</small>
                    </label>
                    <div class="eco-tag-chips" id="chips-architectures">
                        @foreach(old('architectures', $profile->architectures ?? []) as $v)
                            <span class="eco-chip mono">{{ $v }}</span>
                        @endforeach
                    </div>
                    <textarea name="architectures[]" id="architectures_textarea" class="eco-textarea mono" rows="3" placeholder="x64&#10;ARM64">{{ implode("\n", old('architectures', $profile->architectures ?? [])) }}</textarea>
                    <div class="eco-presets-strip">
                        <button type="button" class="eco-preset-pill" onclick="appendTag('architectures_textarea', 'x64')">+ x64</button>
                        <button type="button" class="eco-preset-pill" onclick="appendTag('architectures_textarea', 'ARM64')">+ ARM64</button>
                        <button type="button" class="eco-preset-pill" onclick="appendTag('architectures_textarea', 'x86')">+ x86</button>
                        <button type="button" class="eco-preset-pill" onclick="appendTag('architectures_textarea', 'Universal')">+ Universal</button>
                    </div>
                </div>

                <!-- Channels -->
                <div class="eco-field">
                    <label class="eco-field-label" for="channels_textarea">
                        Release Channels
                        <small>Distribution release rings</small>
                    </label>
                    <div class="eco-tag-chips" id="chips-channels">
                        @foreach(old('channels', $profile->channels ?? []) as $v)
                            <span class="eco-chip">{{ $v }}</span>
                        @endforeach
                    </div>
                    <textarea name="channels[]" id="channels_textarea" class="eco-textarea" rows="3" placeholder="Stable&#10;Beta&#10;Nightly">{{ implode("\n", old('channels', $profile->channels ?? [])) }}</textarea>
                    <div class="eco-presets-strip">
                        <button type="button" class="eco-preset-pill" onclick="appendTag('channels_textarea', 'Stable')">+ Stable</button>
                        <button type="button" class="eco-preset-pill" onclick="appendTag('channels_textarea', 'Beta')">+ Beta</button>
                        <button type="button" class="eco-preset-pill" onclick="appendTag('channels_textarea', 'Nightly')">+ Nightly</button>
                        <button type="button" class="eco-preset-pill" onclick="appendTag('channels_textarea', 'Canary')">+ Canary</button>
                    </div>
                </div>
            </div>

            <!-- Integration Targets (Full Width) -->
            <div class="eco-field" style="margin-top: 18px;">
                <label class="eco-field-label" for="integration_targets_textarea">
                    Integration Targets & Connectors
                    <small>External systems, protocols or services supported by this ecosystem</small>
                </label>
                <div class="eco-tag-chips" id="chips-integration_targets">
                    @foreach(old('integration_targets', $profile->integration_targets ?? []) as $v)
                        <span class="eco-chip">{{ $v }}</span>
                    @endforeach
                </div>
                <textarea name="integration_targets[]" id="integration_targets_textarea" class="eco-textarea" rows="3" placeholder="PostgreSQL&#10;MySQL&#10;SQLite">{{ implode("\n", old('integration_targets', $profile->integration_targets ?? [])) }}</textarea>
                <div class="eco-presets-strip">
                    <span class="eco-presets-label">Popular Connectors:</span>
                    <button type="button" class="eco-preset-pill" onclick="appendTag('integration_targets_textarea', 'PostgreSQL')">+ PostgreSQL</button>
                    <button type="button" class="eco-preset-pill" onclick="appendTag('integration_targets_textarea', 'MySQL')">+ MySQL</button>
                    <button type="button" class="eco-preset-pill" onclick="appendTag('integration_targets_textarea', 'MariaDB')">+ MariaDB</button>
                    <button type="button" class="eco-preset-pill" onclick="appendTag('integration_targets_textarea', 'SQLite')">+ SQLite</button>
                    <button type="button" class="eco-preset-pill" onclick="appendTag('integration_targets_textarea', 'Oracle')">+ Oracle</button>
                    <button type="button" class="eco-preset-pill" onclick="appendTag('integration_targets_textarea', 'SQL Server')">+ SQL Server</button>
                    <button type="button" class="eco-preset-pill" onclick="appendTag('integration_targets_textarea', 'StratosDB')">+ StratosDB</button>
                </div>
            </div>
        </section>

    </div>

    <!-- Sticky Bottom Action Bar -->
    <div class="eco-actions-bar">
        <div class="eco-actions-left">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--admin-green);"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            <span>Policy values are validated dynamically in developer submission forms and marketplace APIs.</span>
        </div>
        <div class="eco-actions-right">
            <a class="eco-btn-cancel" href="{{ route('admin.ecosystem.index') }}">Cancel</a>
            <button type="submit" class="eco-btn-save">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                Save Ecosystem Policy
            </button>
        </div>
    </div>
</form>

<script>
    // Live update toggle card active state
    document.querySelectorAll('.eco-toggle-box').forEach(function(box) {
        var input = box.querySelector('input[type="checkbox"]');
        if (input) {
            input.addEventListener('change', function() {
                box.classList.toggle('is-selected', input.checked);
            });
        }
    });

    // Helper function to append tag preset into textarea without duplicates and update chip preview
    function appendTag(textareaId, tagValue) {
        var el = document.getElementById(textareaId);
        if (!el) return;
        var current = el.value.split(/[\r\n,]+/).map(function(s) { return s.trim(); }).filter(Boolean);
        if (!current.includes(tagValue)) {
            current.push(tagValue);
            el.value = current.join('\n');
            updateChips(textareaId);
        }
    }

    // Live sync textarea values to chips preview
    function updateChips(textareaId) {
        var el = document.getElementById(textareaId);
        if (!el) return;
        var fieldKey = textareaId.replace('_textarea', '');
        var container = document.getElementById('chips-' + fieldKey);
        if (!container) return;

        var items = el.value.split(/[\r\n,]+/).map(function(s) { return s.trim(); }).filter(Boolean);
        var isMono = el.classList.contains('mono');
        var isCap = fieldKey === 'capabilities';

        container.innerHTML = '';
        items.forEach(function(item) {
            var chip = document.createElement('span');
            chip.className = 'eco-chip' + (isMono ? ' mono' : '');
            chip.textContent = (isCap ? '🔒 ' : '') + item;
            container.appendChild(chip);
        });
    }

    // Attach input listeners to all list textareas
    ['item_types_textarea', 'capabilities_textarea', 'package_types_textarea', 'platforms_textarea', 'architectures_textarea', 'channels_textarea', 'integration_targets_textarea'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', function() {
                updateChips(id);
            });
        }
    });
</script>
@endsection
