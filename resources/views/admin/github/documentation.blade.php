@extends('admin.layout')

@php
    $heading = 'Edit Documentation · ' . $project->name;
    $title = 'Edit ' . $path . ' · ' . $project->name . ' · RozeHub Admin';
    $fileContent = $file ? base64_decode($file['content'] ?? '') : '';
    $defaultBranch = $project->githubRepository?->default_branch ?: 'master';
    $currentBranch = request('branch', $file['branch'] ?? $defaultBranch);
@endphp

@section('content')
<style>
    /* Scoped GitHub Documentation Editor Styles */
    .gdoc-shell {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /* Page Header */
    .gdoc-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }
    .gdoc-breadcrumbs {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: var(--admin-muted);
        margin-bottom: 6px;
    }
    .gdoc-breadcrumbs a {
        color: var(--admin-muted);
        text-decoration: none;
    }
    .gdoc-breadcrumbs a:hover {
        color: var(--admin-ink);
        text-decoration: underline;
    }
    .gdoc-breadcrumbs .sep {
        color: #b8c4be;
    }
    .gdoc-title-row h2 {
        margin: 0;
        font-size: 24px;
        font-weight: 800;
        letter-spacing: -0.5px;
        color: var(--admin-ink);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .gdoc-subtitle {
        margin: 4px 0 0;
        font-size: 13px;
        color: var(--admin-muted);
    }
    .gdoc-header-actions {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    .gdoc-btn-secondary {
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
    .gdoc-btn-secondary:hover {
        background: #f8faf8;
        border-color: #cbd7d0;
    }

    /* Path & Branch Switcher Strip */
    .gdoc-nav-card {
        background: #ffffff;
        border: 1px solid var(--admin-line);
        border-radius: 12px;
        padding: 16px 20px;
        box-shadow: 0 2px 10px rgba(16,34,30,0.03);
    }
    .gdoc-nav-form {
        display: flex;
        align-items: flex-end;
        gap: 14px;
        flex-wrap: wrap;
    }
    .gdoc-nav-field {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    .gdoc-nav-field label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--admin-muted);
    }
    .gdoc-input-box {
        display: flex;
        align-items: center;
        background: #f9fbf9;
        border: 1px solid #d2ded7;
        border-radius: 6px;
        padding: 0 10px;
        height: 38px;
        transition: all 0.15s;
    }
    .gdoc-input-box:focus-within {
        background: #ffffff;
        border-color: #2b8a73;
        box-shadow: 0 0 0 3px rgba(43, 138, 115, 0.12);
    }
    .gdoc-input-box svg {
        color: #7b8e85;
        margin-right: 8px;
        flex-shrink: 0;
    }
    .gdoc-input-box input {
        border: 0;
        background: transparent;
        outline: 0;
        font-size: 13px;
        font-family: inherit;
        color: var(--admin-ink);
        width: 100%;
    }
    .gdoc-btn-load {
        background: #10221e;
        color: #ffffff;
        border: 0;
        border-radius: 6px;
        padding: 0 18px;
        height: 38px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background 0.15s;
    }
    .gdoc-btn-load:hover {
        background: #183831;
    }

    /* Quick Suggestion Chips */
    .gdoc-quick-chips {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 12px;
        padding-top: 10px;
        border-top: 1px solid #f0f4f2;
        flex-wrap: wrap;
    }
    .gdoc-chip-label {
        font-size: 11px;
        color: var(--admin-muted);
        font-weight: 600;
    }
    .gdoc-chip-btn {
        background: #f1f6f3;
        border: 1px solid #dbe6df;
        border-radius: 4px;
        padding: 3px 8px;
        font-size: 11px;
        font-family: ui-monospace, monospace;
        color: #1f5644;
        cursor: pointer;
        transition: all 0.12s;
    }
    .gdoc-chip-btn:hover {
        background: #e2ede7;
        border-color: #cbd8cf;
    }

    /* Main Workspace Grid (70% Editor + 30% Commit Sidebar) */
    .gdoc-grid {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 20px;
        align-items: start;
    }

    /* Editor Frame */
    .gdoc-editor-frame {
        background: #ffffff;
        border: 1px solid var(--admin-line);
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(16,34,30,0.04);
        display: flex;
        flex-direction: column;
    }

    /* Editor Window Titlebar */
    .gdoc-editor-titlebar {
        background: #f6f8f7;
        border-bottom: 1px solid #e1e7e4;
        padding: 10px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }
    .gdoc-win-dots {
        display: flex;
        gap: 6px;
        align-items: center;
    }
    .gdoc-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
    }
    .gdoc-dot.red { background: #ff5f56; }
    .gdoc-dot.yellow { background: #ffbd2e; }
    .gdoc-dot.green { background: #27c93f; }
    .gdoc-filename-display {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 13px;
        font-weight: 700;
        color: var(--admin-ink);
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .gdoc-mode-tabs {
        display: flex;
        background: #e9eee9;
        border-radius: 6px;
        padding: 2px;
        gap: 2px;
    }
    .gdoc-tab-btn {
        background: transparent;
        border: 0;
        padding: 5px 12px;
        font-size: 12px;
        font-weight: 700;
        color: #556760;
        border-radius: 5px;
        cursor: pointer;
        transition: all 0.12s;
    }
    .gdoc-tab-btn.active {
        background: #ffffff;
        color: var(--admin-ink);
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }

    /* Markdown Formatting Toolbar */
    .gdoc-toolbar {
        background: #fafbfa;
        border-bottom: 1px solid #e6ece9;
        padding: 6px 14px;
        display: flex;
        align-items: center;
        gap: 4px;
        flex-wrap: wrap;
    }
    .gdoc-tool-btn {
        background: transparent;
        border: 1px solid transparent;
        border-radius: 4px;
        padding: 4px 8px;
        font-size: 12px;
        font-weight: 600;
        color: #40524b;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.12s;
    }
    .gdoc-tool-btn:hover {
        background: #e8f0ec;
        border-color: #cbd8d1;
        color: var(--admin-ink);
    }
    .gdoc-tool-sep {
        width: 1px;
        height: 18px;
        background: #d8e2dc;
        margin: 0 4px;
    }

    /* Code Textarea */
    .gdoc-textarea-wrapper {
        position: relative;
    }
    .gdoc-editor-textarea {
        width: 100%;
        box-sizing: border-box;
        border: 0;
        outline: 0;
        background: #ffffff;
        color: #172622;
        font-family: ui-monospace, SFMono-Regular, "JetBrains Mono", Menlo, Consolas, monospace;
        font-size: 13px;
        line-height: 1.65;
        padding: 20px 24px;
        min-height: 520px;
        resize: vertical;
        tab-size: 4;
    }
    .gdoc-editor-textarea:focus {
        box-shadow: inset 0 0 0 1px rgba(43, 138, 115, 0.2);
    }

    /* Markdown Live Preview */
    .gdoc-preview-pane {
        display: none;
        padding: 24px 28px;
        min-height: 520px;
        background: #ffffff;
        font-size: 14px;
        line-height: 1.7;
        color: #243530;
    }
    .gdoc-preview-pane.active {
        display: block;
    }
    .gdoc-preview-pane h1 { font-size: 26px; border-bottom: 1px solid #e1e7e4; padding-bottom: 8px; margin-top: 0; }
    .gdoc-preview-pane h2 { font-size: 20px; border-bottom: 1px solid #e1e7e4; padding-bottom: 6px; margin-top: 24px; }
    .gdoc-preview-pane h3 { font-size: 16px; margin-top: 18px; }
    .gdoc-preview-pane p { margin: 0 0 12px; }
    .gdoc-preview-pane code { font-family: ui-monospace, monospace; background: #f0f4f2; padding: 2px 5px; border-radius: 4px; font-size: 12px; color: #a13f35; }
    .gdoc-preview-pane pre { background: #11221e; color: #eaf7f1; padding: 14px 18px; border-radius: 8px; overflow-x: auto; font-family: ui-monospace, monospace; font-size: 12px; line-height: 1.6; }
    .gdoc-preview-pane pre code { background: transparent; padding: 0; color: inherit; }
    .gdoc-preview-pane blockquote { border-left: 4px solid #2b8a73; margin: 14px 0; padding: 8px 16px; background: #f2f8f5; border-radius: 0 6px 6px 0; color: #435952; }
    .gdoc-preview-pane ul, .gdoc-preview-pane ol { padding-left: 24px; margin: 10px 0; }
    .gdoc-preview-pane li { margin-bottom: 4px; }

    /* Editor Statusbar */
    .gdoc-statusbar {
        background: #f6f8f7;
        border-top: 1px solid #e2e8e5;
        padding: 8px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 11px;
        color: var(--admin-muted);
    }
    .gdoc-status-stats {
        display: flex;
        gap: 16px;
    }

    /* Right Sidebar (Commit & Metadata) */
    .gdoc-sidebar {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }
    .gdoc-side-card {
        background: #ffffff;
        border: 1px solid var(--admin-line);
        border-radius: 14px;
        padding: 22px;
        box-shadow: 0 4px 18px rgba(16,34,30,0.03);
    }
    .gdoc-side-title {
        font-size: 15px;
        font-weight: 800;
        color: var(--admin-ink);
        margin: 0 0 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .gdoc-form-group {
        margin-bottom: 16px;
    }
    .gdoc-form-group label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--admin-muted);
        margin-bottom: 6px;
    }
    .gdoc-commit-input {
        width: 100%;
        box-sizing: border-box;
        border: 1px solid #ccd8d1;
        border-radius: 6px;
        padding: 10px 12px;
        font-size: 13px;
        font-family: inherit;
        color: var(--admin-ink);
        background: #ffffff;
        transition: all 0.15s;
    }
    .gdoc-commit-input:focus {
        border-color: #2b8a73;
        outline: 0;
        box-shadow: 0 0 0 3px rgba(43, 138, 115, 0.12);
    }
    .gdoc-author-badge {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #f7faf8;
        border: 1px solid #dce5df;
        border-radius: 8px;
        padding: 10px 12px;
        margin-bottom: 18px;
    }
    .gdoc-author-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #175e45;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 13px;
        flex-shrink: 0;
    }
    .gdoc-author-info strong {
        display: block;
        font-size: 12px;
        color: var(--admin-ink);
    }
    .gdoc-author-info small {
        display: block;
        font-size: 11px;
        color: var(--admin-muted);
    }
    .gdoc-btn-commit {
        width: 100%;
        background: #10221e;
        color: #ffffff;
        border: 0;
        border-radius: 8px;
        padding: 12px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 4px 14px rgba(16,34,30,0.14);
        transition: all 0.15s ease;
    }
    .gdoc-btn-commit:hover {
        background: #183831;
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(16,34,30,0.2);
    }
    .gdoc-btn-commit svg {
        color: #66ddae;
    }

    /* File Meta Table */
    .gdoc-meta-table {
        display: flex;
        flex-direction: column;
        gap: 10px;
        font-size: 12px;
    }
    .gdoc-meta-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 8px;
        border-bottom: 1px solid #edf1ee;
    }
    .gdoc-meta-row:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }
    .gdoc-meta-label {
        color: var(--admin-muted);
    }
    .gdoc-meta-val {
        font-weight: 600;
        color: var(--admin-ink);
        font-family: ui-monospace, monospace;
    }

    /* GITHUB_TOKEN Notice */
    .gdoc-token-notice {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 8px;
        padding: 12px 14px;
        font-size: 12px;
        line-height: 1.5;
        color: #1e40af;
        margin-top: 14px;
    }

    @media (max-width: 960px) {
        .gdoc-grid {
            grid-template-columns: 1fr;
        }
        .gdoc-editor-textarea, .gdoc-preview-pane {
            min-height: 400px;
        }
    }
</style>

<div class="gdoc-shell">

    <!-- Top Header & Breadcrumbs -->
    <div class="gdoc-header">
        <div>
            <div class="gdoc-breadcrumbs">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <span class="sep">/</span>
                <a href="{{ route('admin.projects.index') }}">Software</a>
                <span class="sep">/</span>
                <a href="{{ route('admin.github.show', $project) }}">{{ $project->name }}</a>
                <span class="sep">/</span>
                <strong style="color:var(--admin-ink);">Edit Documentation</strong>
            </div>
            <div class="gdoc-title-row">
                <h2>
                    <span>Edit Documentation</span>
                    <span style="font-size:14px; font-weight:normal; color:var(--admin-muted);">({{ $project->name }})</span>
                </h2>
                <p class="gdoc-subtitle">Directly read, edit, and commit repository files via GitHub Contents API.</p>
            </div>
        </div>

        <div class="gdoc-header-actions">
            <a class="gdoc-btn-secondary" href="{{ route('admin.github.show', $project) }}">
                ← Back to GitHub overview
            </a>
            @if($file && isset($file['html_url']))
                <a class="gdoc-btn-secondary" href="{{ $file['html_url'] }}" target="_blank" rel="noopener">
                    View on GitHub ↗
                </a>
            @endif
        </div>
    </div>

    @if($error)
        <div class="admin-alert error" style="border-radius:10px;">
            <strong>GitHub API Error:</strong> {{ $error }}
        </div>
    @endif

    <!-- Repository File & Branch Switcher Strip -->
    <div class="gdoc-nav-card">
        <form method="GET" action="{{ route('admin.github.documentation', $project) }}" class="gdoc-nav-form">
            <div class="gdoc-nav-field" style="flex: 2; min-width: 220px;">
                <label for="filePathInput">Repository File Path</label>
                <div class="gdoc-input-box">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    <input type="text" id="filePathInput" name="path" value="{{ $path }}" required placeholder="e.g. README.md, docs/index.md">
                </div>
            </div>

            <div class="gdoc-nav-field" style="flex: 1; min-width: 140px;">
                <label for="branchInput">Target Branch</label>
                <div class="gdoc-input-box">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="6" y1="3" x2="6" y2="15"></line><circle cx="18" cy="6" r="3"></circle><circle cx="6" cy="18" r="3"></circle><path d="M18 9a9 9 0 0 1-9 9"></path></svg>
                    <input type="text" id="branchInput" name="branch" value="{{ $currentBranch }}" placeholder="master">
                </div>
            </div>

            <button type="submit" class="gdoc-btn-load">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
                Load File
            </button>
        </form>

        <!-- Quick Switch Suggestion Chips -->
        <div class="gdoc-quick-chips">
            <span class="gdoc-chip-label">Quick select:</span>
            <button type="button" class="gdoc-chip-btn" onclick="selectQuickDoc('README.md')">README.md</button>
            <button type="button" class="gdoc-chip-btn" onclick="selectQuickDoc('CONTRIBUTING.md')">CONTRIBUTING.md</button>
            <button type="button" class="gdoc-chip-btn" onclick="selectQuickDoc('LICENSE')">LICENSE</button>
            <button type="button" class="gdoc-chip-btn" onclick="selectQuickDoc('docs/index.md')">docs/index.md</button>
        </div>
    </div>

    @if($file)
        <!-- Editor & Commit Form -->
        <form method="POST" action="{{ route('admin.github.documentation.update', $project) }}" id="updateDocForm">
            @csrf
            @method('PUT')

            <!-- Hidden Meta Fields required by API -->
            <input type="hidden" name="path" value="{{ $path }}">
            <input type="hidden" name="branch" value="{{ $currentBranch }}">
            <input type="hidden" name="sha" value="{{ $file['sha'] }}">

            <div class="gdoc-grid">
                <!-- Left: Full Editor Pane -->
                <div class="gdoc-editor-frame">
                    <!-- Titlebar -->
                    <div class="gdoc-editor-titlebar">
                        <div class="gdoc-win-dots">
                            <span class="gdoc-dot red"></span>
                            <span class="gdoc-dot yellow"></span>
                            <span class="gdoc-dot green"></span>
                            <span class="gdoc-filename-display">
                                📄 {{ $file['name'] ?: $path }}
                            </span>
                        </div>

                        <!-- Mode Switch Tabs -->
                        <div class="gdoc-mode-tabs">
                            <button type="button" class="gdoc-tab-btn active" id="tabEdit" onclick="switchEditorMode('edit')">
                                Edit Markdown
                            </button>
                            <button type="button" class="gdoc-tab-btn" id="tabPreview" onclick="switchEditorMode('preview')">
                                Live Preview
                            </button>
                        </div>
                    </div>

                    <!-- Markdown Formatting Toolbar -->
                    <div class="gdoc-toolbar" id="gdocToolbar">
                        <button type="button" class="gdoc-tool-btn" onclick="insertSyntax('**', '**')" title="Bold (Ctrl+B)"><b>B</b></button>
                        <button type="button" class="gdoc-tool-btn" onclick="insertSyntax('*', '*')" title="Italic (Ctrl+I)"><i>I</i></button>
                        <button type="button" class="gdoc-tool-btn" onclick="insertSyntax('`', '`')" title="Inline Code"><code>&lt;&gt;</code></button>
                        <span class="gdoc-tool-sep"></span>
                        <button type="button" class="gdoc-tool-btn" onclick="insertLinePrefix('## ')" title="Heading 2">H2</button>
                        <button type="button" class="gdoc-tool-btn" onclick="insertLinePrefix('### ')" title="Heading 3">H3</button>
                        <span class="gdoc-tool-sep"></span>
                        <button type="button" class="gdoc-tool-btn" onclick="insertLinePrefix('> ')" title="Blockquote">” Quote</button>
                        <button type="button" class="gdoc-tool-btn" onclick="insertLinePrefix('- ')" title="Bullet List">• List</button>
                        <button type="button" class="gdoc-tool-btn" onclick="insertSyntax('[', '](url)')" title="Link">🔗 Link</button>
                        <button type="button" class="gdoc-tool-btn" onclick="insertSyntax('```\n', '\n```')" title="Code Block">``` Block</button>
                    </div>

                    <!-- Editor Textarea -->
                    <div class="gdoc-textarea-wrapper">
                        <textarea
                            id="editorContent"
                            name="content"
                            class="gdoc-editor-textarea"
                            placeholder="Write your markdown content here..."
                            oninput="updateStats()"
                            spellcheck="false"
                        >{{ old('content', $fileContent) }}</textarea>

                        <!-- Live Preview Pane -->
                        <div id="previewPane" class="gdoc-preview-pane"></div>
                    </div>

                    <!-- Editor Status Bar -->
                    <div class="gdoc-statusbar">
                        <div class="gdoc-status-stats">
                            <span id="statLines">0 lines</span>
                            <span id="statWords">0 words</span>
                            <span id="statChars">0 characters</span>
                        </div>
                        <div>
                            <span>UTF-8 · Markdown · GitHub Flavored</span>
                        </div>
                    </div>
                </div>

                <!-- Right: Commit & Metadata Sidebar -->
                <aside class="gdoc-sidebar">
                    <!-- Commit Settings Card -->
                    <div class="gdoc-side-card">
                        <div class="gdoc-side-title">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="4"></circle><line x1="1.05" y1="12" x2="7" y2="12"></line><line x1="17.01" y1="12" x2="22.96" y2="12"></line></svg>
                            Commit Changes
                        </div>

                        <div class="gdoc-form-group">
                            <label for="commitMessageInput">Commit Message</label>
                            <input
                                type="text"
                                id="commitMessageInput"
                                name="message"
                                class="gdoc-commit-input"
                                value="{{ old('message', 'docs: update ' . $path) }}"
                                required
                                placeholder="Describe your changes..."
                            >
                        </div>

                        <div class="gdoc-author-badge">
                            <div class="gdoc-author-avatar">
                                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="gdoc-author-info">
                                <strong>{{ auth()->user()->name }}</strong>
                                <small>{{ auth()->user()->email }}</small>
                            </div>
                        </div>

                        <button type="submit" class="gdoc-btn-commit">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            Commit to GitHub
                        </button>
                    </div>

                    <!-- File Details Card -->
                    <div class="gdoc-side-card">
                        <div class="gdoc-side-title">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                            File Information
                        </div>
                        <div class="gdoc-meta-table">
                            <div class="gdoc-meta-row">
                                <span class="gdoc-meta-label">Path</span>
                                <span class="gdoc-meta-val">{{ $path }}</span>
                            </div>
                            <div class="gdoc-meta-row">
                                <span class="gdoc-meta-label">Branch</span>
                                <span class="gdoc-meta-val">{{ $currentBranch }}</span>
                            </div>
                            <div class="gdoc-meta-row">
                                <span class="gdoc-meta-label">Blob SHA</span>
                                <span class="gdoc-meta-val" title="{{ $file['sha'] }}">{{ substr($file['sha'], 0, 7) }}...</span>
                            </div>
                            <div class="gdoc-meta-row">
                                <span class="gdoc-meta-label">File Size</span>
                                <span class="gdoc-meta-val">{{ number_format($file['size'] ?? strlen($fileContent)) }} B</span>
                            </div>
                        </div>

                        @if(!config('github.token'))
                            <div class="gdoc-token-notice">
                                <strong>Note:</strong> Direct commits require a <code>GITHUB_TOKEN</code> set in your RozeHub <code>.env</code> with repository write access.
                            </div>
                        @endif
                    </div>
                </aside>
            </div>
        </form>
    @else
        <!-- File Not Found State -->
        <div class="gdoc-nav-card" style="text-align:center; padding:50px 20px;">
            <div style="width:48px; height:48px; border-radius:50%; background:#f2f7f4; color:#175e45; display:inline-flex; align-items:center; justify-content:center; font-size:22px; margin-bottom:14px;">
                📄
            </div>
            <h3 style="margin:0 0 8px; font-size:18px; color:var(--admin-ink);">No file loaded at &ldquo;{{ $path }}&rdquo;</h3>
            <p style="margin:0 0 20px; font-size:13px; color:var(--admin-muted); max-width:480px; margin-left:auto; margin-right:auto;">
                Please verify the repository path and branch above, then click <strong>Load File</strong>. If you want to edit standard documentation, try one of the quick suggestions below.
            </p>
            <div style="display:flex; justify-content:center; gap:10px; flex-wrap:wrap;">
                <button type="button" class="gdoc-btn-secondary" onclick="selectQuickDoc('README.md')">Load README.md</button>
                <button type="button" class="gdoc-btn-secondary" onclick="selectQuickDoc('CONTRIBUTING.md')">Load CONTRIBUTING.md</button>
            </div>
        </div>
    @endif

</div>

<!-- Interactive Editor Scripts -->
<script>
    function selectQuickDoc(path) {
        document.getElementById('filePathInput').value = path;
        document.querySelector('.gdoc-nav-form').submit();
    }

    function switchEditorMode(mode) {
        const tabEdit = document.getElementById('tabEdit');
        const tabPrev = document.getElementById('tabPreview');
        const textarea = document.getElementById('editorContent');
        const preview = document.getElementById('previewPane');
        const toolbar = document.getElementById('gdocToolbar');

        if (mode === 'edit') {
            tabEdit.classList.add('active');
            tabPrev.classList.remove('active');
            textarea.style.display = 'block';
            preview.classList.remove('active');
            toolbar.style.display = 'flex';
        } else {
            tabPrev.classList.add('active');
            tabEdit.classList.remove('active');
            textarea.style.display = 'none';
            toolbar.style.display = 'none';
            renderMarkdown(textarea.value);
            preview.classList.add('active');
        }
    }

    function renderMarkdown(raw) {
        const pane = document.getElementById('previewPane');
        if (!raw || !raw.trim()) {
            pane.innerHTML = '<p style="color:#7b8e85; font-style:italic;">Nothing to preview yet.</p>';
            return;
        }

        // Lightweight safe client-side markdown renderer
        let html = raw
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');

        // Code blocks
        html = html.replace(/```([a-zA-Z0-9_-]*)\n([\s\S]*?)```/g, function(match, lang, code) {
            return '<pre><code>' + code + '</code></pre>';
        });

        // Inline code
        html = html.replace(/`([^`]+)`/g, '<code>$1</code>');

        // Headers
        html = html.replace(/^### (.*$)/gim, '<h3>$1</h3>');
        html = html.replace(/^## (.*$)/gim, '<h2>$1</h2>');
        html = html.replace(/^# (.*$)/gim, '<h1>$1</h1>');

        // Blockquotes
        html = html.replace(/^\> (.*$)/gim, '<blockquote>$1</blockquote>');

        // Bold & Italic
        html = html.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
        html = html.replace(/\*([^*]+)\*/g, '<em>$1</em>');

        // Links
        html = html.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_blank" rel="noopener" style="color:#1d7758; text-decoration:underline;">$1</a>');

        // Bullet lists
        html = html.replace(/^\s*-\s+(.*$)/gim, '<li>$1</li>');
        html = html.replace(/(<li>.*<\/li>)/gim, '<ul>$1</ul>');

        // Paragraphs
        html = html.split('\n\n').map(function(block) {
            block = block.trim();
            if (!block) return '';
            if (block.startsWith('<h') || block.startsWith('<pre') || block.startsWith('<blockquote') || block.startsWith('<ul')) {
                return block;
            }
            return '<p>' + block.replace(/\n/g, '<br>') + '</p>';
        }).join('');

        pane.innerHTML = html;
    }

    function insertSyntax(before, after) {
        const ta = document.getElementById('editorContent');
        if (!ta) return;
        const start = ta.selectionStart;
        const end = ta.selectionEnd;
        const selected = ta.value.substring(start, end);
        const replacement = before + (selected || 'text') + after;
        ta.value = ta.value.substring(0, start) + replacement + ta.value.substring(end);
        ta.focus();
        ta.setSelectionRange(start + before.length, start + before.length + (selected ? selected.length : 4));
        updateStats();
    }

    function insertLinePrefix(prefix) {
        const ta = document.getElementById('editorContent');
        if (!ta) return;
        const start = ta.selectionStart;
        const lineStart = ta.value.lastIndexOf('\n', start - 1) + 1;
        ta.value = ta.value.substring(0, lineStart) + prefix + ta.value.substring(lineStart);
        ta.focus();
        updateStats();
    }

    function updateStats() {
        const ta = document.getElementById('editorContent');
        if (!ta) return;
        const val = ta.value;
        const lines = val ? val.split('\n').length : 0;
        const words = val.trim() ? val.trim().split(/\s+/).length : 0;
        const chars = val.length;

        const statLines = document.getElementById('statLines');
        const statWords = document.getElementById('statWords');
        const statChars = document.getElementById('statChars');

        if (statLines) statLines.textContent = lines + ' lines';
        if (statWords) statWords.textContent = words + ' words';
        if (statChars) statChars.textContent = chars + ' characters';
    }

    // Initialize stats on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateStats();
    });
</script>
@endsection
