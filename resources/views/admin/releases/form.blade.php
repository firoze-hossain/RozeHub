@php
    $projectName = $release->project?->name ?? 'Application';
    $heading = $mode === 'create' ? 'Create Application Release' : 'Edit Application Release';
    $pageTitle = ($mode === 'create' ? 'Create Release' : 'Edit ' . $projectName . ' v' . $release->version) . ' · RozeHub Admin';

    $projectLogos = [
        'dbnavigator' => 'dbnavigator.png',
        'lumina' => 'lumina.png',
        'novaos' => 'novaos.png',
        'roze' => 'roze.png',
        'stratosdb' => 'stratosdb.png',
        'thundercall' => 'thundercall.png',
        'trackeye' => 'trackeye.png',
    ];
    $logoKey = strtolower(preg_replace('/[^a-z0-9]/', '', $projectName));
    $logoFile = $projectLogos[$logoKey] ?? null;
@endphp

@extends('admin.layout', [
    'heading' => $heading,
    'title' => $pageTitle
])

@section('content')

<!-- Hero Banner -->
<div class="release-admin-hero">
    <div class="release-hero-main">
        <div class="release-hero-logo">
            @if($logoFile)
                <img src="{{ asset('images/projects/' . $logoFile) }}" alt="{{ $projectName }}">
            @else
                <span class="release-hero-monogram">{{ substr($projectName, 0, 2) }}</span>
            @endif
        </div>
        <div class="release-hero-meta">
            <span class="release-hero-kicker">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                RELEASE DISTRIBUTION
            </span>
            <h2 class="release-hero-title">
                {{ $mode === 'create' ? 'Create Application Release' : 'Edit ' . $projectName . ' Release' }}
                @if($mode === 'edit' && $release->version)
                    <span class="release-version-pill">v{{ $release->version }}</span>
                @endif
            </h2>
            <p class="release-hero-desc">Release packages are securely stored in external storage. RozeHub manages checksums, sizes, and desktop update manifests in MySQL.</p>
        </div>
    </div>
    <div class="release-hero-side">
        @if($mode === 'edit')
            <div style="display: flex; gap: 8px; align-items: center;">
                <span class="badge-channel {{ strtolower($release->channel) }}">{{ $release->channel }}</span>
                @if($release->is_published)
                    <span class="eco-badge-pill active">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10"></circle></svg>
                        Published
                    </span>
                @else
                    <span class="eco-badge-pill inactive">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle></svg>
                        Draft
                    </span>
                @endif
            </div>
        @endif
        <a href="{{ route('admin.releases.index') }}" class="eco-hero-link">
            ← Back to all releases
        </a>
    </div>
</div>

<form class="release-form-workspace" method="POST" enctype="multipart/form-data" action="{{ $mode === 'create' ? route('admin.releases.store') : route('admin.releases.update', $release) }}" id="release-form">
    @csrf
    @if($mode === 'edit')
        @method('PUT')
    @endif

    <div class="eco-policy-grid">

        <!-- 01 / Identity & Target -->
        <section class="eco-section-card">
            <div class="eco-section-head">
                <div>
                    <div class="eco-section-eyebrow">01 · Target & Identity</div>
                    <h3 class="eco-section-title">Software, Version & Distribution Ring</h3>
                </div>
                <p class="eco-section-desc">Identifies the target software binary, semantic version tag, and platform channel.</p>
            </div>

            <div class="eco-2col">
                <div class="eco-field">
                    <label class="eco-field-label" for="software_project_id">
                        Target Software
                        <small>Select application product</small>
                    </label>
                    <select name="software_project_id" id="software_project_id" class="eco-select" required>
                        <option value="">Choose software project</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" @selected(old('software_project_id', $release->software_project_id) == $p->id)>
                                {{ $p->name }} — {{ $p->category }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="eco-field">
                    <label class="eco-field-label" for="version">
                        Release Version
                        <small>Semantic format (e.g. 1.2.0 or 2026.1)</small>
                    </label>
                    <input type="text" name="version" id="version" class="eco-input" value="{{ old('version', $release->version) }}" placeholder="1.2.0" required style="font-weight: 750; letter-spacing: 0.5px;">
                </div>
            </div>

            <div class="form-grid three" style="margin-top: 16px;">
                <div class="eco-field">
                    <label class="eco-field-label" for="platform">
                        Platform (OS)
                        <small>Target operating system</small>
                    </label>
                    <select name="platform" id="platform" class="eco-select">
                        @foreach(['Windows', 'macOS', 'Linux'] as $v)
                            <option value="{{ $v }}" @selected(old('platform', $release->platform) === $v)>
                                {{ $v }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="eco-field">
                    <label class="eco-field-label" for="architecture">
                        Architecture
                        <small>CPU instruction set</small>
                    </label>
                    <select name="architecture" id="architecture" class="eco-select">
                        @foreach(['x64', 'ARM64'] as $v)
                            <option value="{{ $v }}" @selected(old('architecture', $release->architecture) === $v)>
                                {{ $v }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="eco-field">
                    <label class="eco-field-label" for="channel">
                        Release Channel
                        <small>Distribution release ring</small>
                    </label>
                    <select name="channel" id="channel" class="eco-select">
                        @foreach(['Stable', 'Beta', 'Nightly'] as $v)
                            <option value="{{ $v }}" @selected(old('channel', $release->channel) === $v)>
                                {{ $v }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </section>

        <!-- 02 / Update Policy -->
        <section class="eco-section-card">
            <div class="eco-section-head">
                <div>
                    <div class="eco-section-eyebrow">02 · Update Governance</div>
                    <h3 class="eco-section-title">Client Update Enforcement & Rollout</h3>
                </div>
                <p class="eco-section-desc">Dictates how desktop client auto-update services discover and enforce this release.</p>
            </div>

            <div class="eco-2col" style="align-items: start;">
                <div>
                    <div class="eco-field">
                        <label class="eco-field-label" for="minimum_version">
                            Minimum Supported Version
                            <small>Enforce mandatory upgrade baseline</small>
                        </label>
                        <input type="text" name="minimum_version" id="minimum_version" class="eco-input" value="{{ old('minimum_version', $release->minimum_version) }}" placeholder="e.g. 1.0.0">
                        <small style="display:block; color:var(--admin-muted); font-size:11px; margin-top:5px; line-height:1.45;">Clients older than this version are forced to update when this build is detected.</small>
                    </div>

                    <div class="eco-field" style="margin-top: 14px;">
                        <label class="eco-field-label" for="rollout_percentage">
                            Staged Rollout Percentage
                            <small>Gradual deployment (1% - 100%)</small>
                        </label>
                        <input type="number" min="1" max="100" name="rollout_percentage" id="rollout_percentage" class="eco-input" value="{{ old('rollout_percentage', $release->rollout_percentage ?? 100) }}" placeholder="100">
                    </div>
                </div>

                <div>
                    <label class="eco-toggle-box {{ old('is_mandatory', $release->is_mandatory) ? 'is-selected' : '' }}" style="height: 100%; box-sizing: border-box;">
                        <input type="checkbox" name="is_mandatory" value="1" class="eco-toggle-checkbox" @checked(old('is_mandatory', $release->is_mandatory))>
                        <div class="eco-toggle-content">
                            <div class="eco-toggle-title">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color:#d97706;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                                Enforce Mandatory Update
                            </div>
                            <p class="eco-toggle-text">Signal all compatible desktop clients that this release contains critical patches and must be installed before users can continue.</p>
                        </div>
                    </label>
                </div>
            </div>
        </section>

        <!-- 03 / Distribution Artifacts -->
        <section class="eco-section-card">
            <div class="eco-section-head">
                <div>
                    <div class="eco-section-eyebrow">03 · Package Binaries</div>
                    <h3 class="eco-section-title">Distribution Artifacts & Package Binaries</h3>
                </div>
                <p class="eco-section-desc">Binaries are stored in external release storage. MySQL retains cryptographic hashes, file sizes, and paths.</p>
            </div>

            <!-- Artifact 1: Public Installer -->
            <div class="release-artifact-card" data-chunk-upload>
                <div class="release-artifact-top">
                    <div class="release-artifact-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#2563eb;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        <span>INSTALLER PACKAGE</span>
                        <span style="font-size: 11.5px; font-weight: 500; color: var(--admin-muted);">— Used by new users downloading from RozeHub</span>
                    </div>
                    <span class="release-artifact-badge installer">PUBLIC DOWNLOAD</span>
                </div>

                @if($mode === 'edit' && $release->file_name)
                    <div class="artifact-inspector-box">
                        <div class="artifact-inspector-top">
                            <div class="artifact-inspector-file">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>
                                <strong>{{ $release->file_name }}</strong>
                            </div>
                            <span class="artifact-inspector-size">
                                {{ $release->file_size ? number_format($release->file_size / 1048576, 1) . ' MB' : 'Size unavailable' }}
                            </span>
                        </div>
                        @if($release->sha256)
                            <div class="artifact-inspector-hash">
                                <span>SHA-256:</span>
                                <code>{{ $release->sha256 }}</code>
                            </div>
                        @endif
                        <div class="artifact-inspector-path">
                            Stored at <code>{{ $release->file_path }}</code>
                        </div>
                    </div>
                @endif

                <div class="modern-dropzone" style="margin-top: 14px;">
                    <div class="dropzone-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                    </div>
                    <div class="dropzone-title">{{ $mode === 'create' ? 'Select installer package binary' : 'Upload replacement installer binary' }}</div>
                    <div class="dropzone-sub">Click to browse or drag and drop (.exe, .dmg, .pkg, .deb, .AppImage, .zip). Large files use chunked upload.</div>
                    <input type="file" name="package" class="file-drop" {{ $mode === 'create' ? 'required' : '' }} onchange="previewFile(this, 'preview-installer')">
                    <div id="preview-installer" class="dropzone-selected-file"></div>
                </div>

                <div class="release-upload-status" data-upload-status hidden>
                    <div><strong data-upload-message>Preparing upload…</strong><span>External release storage</span></div>
                    <div class="release-upload-track"><i data-upload-progress></i></div>
                </div>
            </div>

            <!-- Artifact 2: Auto-Updater (Optional) -->
            <div class="release-artifact-card" data-chunk-upload style="margin-bottom: 0;">
                <div class="release-artifact-top">
                    <div class="release-artifact-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#7c3aed;"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
                        <span>UPDATER PACKAGE (OPTIONAL)</span>
                        <span style="font-size: 11.5px; font-weight: 500; color: var(--admin-muted);">— Used by installed desktop clients for in-app auto updates</span>
                    </div>
                    <span class="release-artifact-badge updater">AUTO UPDATE</span>
                </div>

                @if($mode === 'edit' && $release->artifacts()->where('purpose','UPDATER')->first())
                    @php($updater = $release->artifacts()->where('purpose','UPDATER')->first())
                    <div class="artifact-inspector-box">
                        <div class="artifact-inspector-top">
                            <div class="artifact-inspector-file">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>
                                <strong>{{ $updater->file_name }}</strong>
                            </div>
                            <span class="artifact-inspector-size">
                                {{ number_format($updater->file_size / 1048576, 1) }} MB
                            </span>
                        </div>
                        @if($updater->sha256)
                            <div class="artifact-inspector-hash">
                                <span>SHA-256:</span>
                                <code>{{ $updater->sha256 }}</code>
                            </div>
                        @endif
                        <div class="artifact-inspector-path">
                            Stored at <code>{{ $updater->file_path }}</code>
                        </div>
                    </div>
                @endif

                <div class="modern-dropzone" style="margin-top: 14px;">
                    <div class="dropzone-icon" style="background: #f5f3ff; color: #7c3aed;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                    </div>
                    <div class="dropzone-title">Upload updater package (Optional)</div>
                    <div class="dropzone-sub">macOS: .pkg · Windows: .msi/.exe · Linux: .deb/.AppImage. Large files use chunked upload.</div>
                    <input type="file" name="update_package" class="file-drop" onchange="previewFile(this, 'preview-updater')">
                    <div id="preview-updater" class="dropzone-selected-file"></div>
                </div>

                <div class="release-upload-status" data-upload-status hidden>
                    <div><strong data-upload-message>Preparing upload…</strong><span>External release storage</span></div>
                    <div class="release-upload-track"><i data-upload-progress></i></div>
                </div>
            </div>
        </section>

        <!-- 04 / Release Notes -->
        <section class="eco-section-card">
            <div class="eco-section-head">
                <div>
                    <div class="eco-section-eyebrow">04 · Changelog</div>
                    <h3 class="eco-section-title">Release Notes & Public Announcements</h3>
                </div>
                <p class="eco-section-desc">Shown on public release download pages and in desktop client update prompt dialogs.</p>
            </div>

            <div class="eco-field">
                <label class="eco-field-label" for="notes">
                    Release Notes Content
                    <small>Markdown bullet points or formatted highlights</small>
                </label>
                <textarea name="notes" id="notes" class="eco-textarea" rows="4" placeholder="Highlight key features, bug fixes, and performance improvements in this release...">{{ old('notes', $release->notes) }}</textarea>
            </div>

            <!-- Publish Switch Card -->
            <label class="release-publish-box {{ old('is_published', $release->is_published) ? 'is-published' : '' }}">
                <div class="release-publish-left">
                    <input type="checkbox" name="is_published" value="1" class="eco-toggle-checkbox" @checked(old('is_published', $release->is_published))>
                    <div>
                        <div class="release-publish-title">Publish this release immediately</div>
                        <p class="release-publish-sub">When checked, this build is immediately live on the RozeHub public site and served to desktop client update checks.</p>
                    </div>
                </div>
            </label>
        </section>

    </div>

    <!-- Sticky Bottom Actions -->
    <div class="eco-actions-bar">
        <div class="eco-actions-left">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--admin-green);"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            <span>Release binaries are automatically hashed with SHA-256 and stored outside the web root.</span>
        </div>
        <div class="eco-actions-right">
            <a class="eco-btn-cancel" href="{{ route('admin.releases.index') }}">Cancel</a>
            <button class="eco-btn-save" type="submit">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                {{ $mode === 'create' ? 'Upload & Save Release' : 'Save Release Changes' }}
            </button>
        </div>
    </div>
</form>

@include('admin.releases._chunk-uploader')

<script>
    // Live update toggle active classes
    document.querySelectorAll('.eco-toggle-box, .release-publish-box').forEach(function(box) {
        var input = box.querySelector('input[type="checkbox"]');
        if (input) {
            input.addEventListener('change', function() {
                if (box.classList.contains('release-publish-box')) {
                    box.classList.toggle('is-published', input.checked);
                } else {
                    box.classList.toggle('is-selected', input.checked);
                }
            });
        }
    });

    // File preview badge helper
    function previewFile(input, targetId) {
        var el = document.getElementById(targetId);
        if (!el) return;
        if (input.files && input.files[0]) {
            var file = input.files[0];
            var mb = (file.size / (1024 * 1024)).toFixed(1);
            el.textContent = 'Selected: ' + file.name + ' (' + mb + ' MB)';
            el.style.display = 'inline-block';
        } else {
            el.style.display = 'none';
        }
    }
</script>
@endsection
