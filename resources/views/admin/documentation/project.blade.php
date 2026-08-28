@extends('admin.layout')
@section('content')
<div class="admin-page-head admin-doc-project-head">
    <div class="admin-doc-title-wrap">
        <div class="admin-doc-project-mark">{{ $project->icon }}</div>
        <div>
            <span>{{ strtoupper($project->name) }} DOCUMENTATION</span>
            <h2>Documentation workspace</h2>
            <p>Build a clean, version-aware knowledge base for {{ $project->name }}.</p>
        </div>
    </div>
    <div class="admin-head-actions">
        @if($project->github_url)<a class="admin-secondary" href="{{ $project->github_url }}" target="_blank" rel="noopener">GitHub ↗</a>@endif
        <a class="admin-secondary" href="{{ route('docs.project',$project) }}">Public docs ↗</a>
        <a class="admin-primary" href="{{ route('admin.documentation.pages.create',$project) }}">+ New page</a>
    </div>
</div>

<div class="admin-doc-source-strip">
    <div><span>OPEN SOURCE PROJECT</span><strong>{{ $project->name }} documentation is connected to its source repository.</strong><p>Keep implementation changes, issues, pull requests, and contributor discussion in GitHub while RozeHub remains the public documentation and release hub.</p></div>
    @if($project->github_url)<a href="{{ $project->github_url }}" target="_blank" rel="noopener">Open repository ↗</a>@endif
</div>

<div class="admin-doc-release-strip">
    <div>
        <span>DOCUMENTATION VERSIONING</span>
        <strong>Assign each article to a release when the content is version-specific.</strong>
        <p>Use <b>All versions</b> for stable concepts. Select a release for installation steps, APIs, commands, architecture changes, or release notes that belong to one software version.</p>
    </div>
    <div class="admin-doc-release-list">
        @forelse($project->releases->take(5) as $release)
            <span class="admin-doc-release-pill"><b>v{{ $release->version }}</b><small>{{ strtoupper($release->channel ?: 'release') }}</small></span>
        @empty
            <span class="admin-doc-release-pill muted">No releases yet</span>
        @endforelse
    </div>
</div>

<div class="admin-doc-stats">
    <div><span>SECTIONS</span><strong>{{ $project->documentationSections->count() }}</strong><small>navigation groups</small></div>
    <div><span>PAGES</span><strong>{{ $project->documentationSections->sum('pages_count') }}</strong><small>documentation articles</small></div>
    <div><span>RELEASES</span><strong>{{ $project->releases->count() }}</strong><small>available versions</small></div>
    <div><span>PUBLIC SITE</span><strong>LIVE</strong><small>version-aware docs</small></div>
</div>

<div class="admin-doc-workspace">
    <section class="admin-card admin-doc-sections">
        <div class="admin-card-heading">
            <div><span>DOCUMENTATION NAVIGATION</span><h3>Sections & pages</h3></div>
            <small>{{ $project->documentationSections->count() }} sections</small>
        </div>

        @forelse($project->documentationSections as $section)
            <details class="admin-doc-section" open>
                <summary>
                    <span class="section-grip">{{ $section->icon }}</span>
                    <div><strong>{{ $section->title }}</strong><small>{{ $section->description ?: 'Documentation section' }}</small></div>
                    <em>{{ $section->pages_count }} pages</em>
                    <span class="admin-doc-chevron">⌄</span>
                </summary>
                <div class="admin-doc-section-body">
                    <div class="admin-doc-section-toolbar">
                        <form method="POST" action="{{ route('admin.documentation.sections.update',$section) }}" class="admin-doc-inline-form">
                            @csrf @method('PUT')
                            <label>Section title<input name="title" value="{{ $section->title }}" required></label>
                            <label>Icon<input name="icon" value="{{ $section->icon }}" maxlength="20"></label>
                            <label>Order<input type="number" name="sort_order" value="{{ $section->sort_order }}" min="0"></label>
                            <label class="wide">Description<input name="description" value="{{ $section->description }}"></label>
                            <button class="admin-secondary" type="submit">Save section</button>
                        </form>
                        <form method="POST" action="{{ route('admin.documentation.sections.destroy',$section) }}" onsubmit="return confirm('Delete this section? Pages will become unassigned.')">
                            @csrf @method('DELETE')
                            <button class="admin-danger" type="submit">Delete section</button>
                        </form>
                    </div>

                    <div class="admin-doc-page-list">
                        @forelse($section->pages as $page)
                            <div class="admin-doc-page-row">
                                <div class="admin-doc-page-main">
                                    <div class="admin-doc-page-tags">
                                        <span class="docs-kind">{{ strtoupper(str_replace('-',' ',$page->kind)) }}</span>
                                        @if($page->release)
                                            <span class="doc-version-chip">v{{ $page->release->version }}</span>
                                        @else
                                            <span class="doc-version-chip general">ALL VERSIONS</span>
                                        @endif
                                        <span class="admin-doc-status {{ $page->is_published ? 'published' : 'draft' }}">{{ $page->is_published ? 'Published' : 'Draft' }}</span>
                                    </div>
                                    <strong>{{ $page->title }}</strong>
                                    <small>{{ $page->summary ?: 'No summary added.' }}</small>
                                </div>
                                <div class="row-actions">
                                    <a href="{{ route('admin.documentation.pages.edit',$page) }}">Edit</a>
                                    <form method="POST" action="{{ route('admin.documentation.pages.toggle',$page) }}">@csrf<button>{{ $page->is_published ? 'Unpublish' : 'Publish' }}</button></form>
                                    <form method="POST" action="{{ route('admin.documentation.pages.destroy',$page) }}" onsubmit="return confirm('Delete this page?')">@csrf @method('DELETE')<button>Delete</button></form>
                                </div>
                            </div>
                        @empty
                            <div class="admin-empty">No pages in this section yet. Create the first article for this part of the documentation.</div>
                        @endforelse
                    </div>
                </div>
            </details>
        @empty
            <div class="admin-doc-empty-state">
                <div>⌘</div><h3>No documentation sections yet</h3><p>Create a navigation section such as Overview, Installation, Reference, Architecture, or Troubleshooting.</p>
            </div>
        @endforelse
    </section>

    <aside class="admin-doc-create-section">
        <div class="admin-card admin-doc-section-builder">
            <div class="admin-doc-builder-head">
                <div class="admin-doc-builder-icon">＋</div>
                <div>
                    <span class="admin-card-kicker">STRUCTURE</span>
                    <h3>Add documentation section</h3>
                    <p>Sections become the navigation groups visitors use to explore this documentation.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.documentation.sections.store',$project) }}" class="admin-doc-section-form">
                @csrf

                <label class="admin-doc-field">
                    <span>Section title <i>Required</i></span>
                    <div class="admin-doc-input-wrap">
                        <span class="admin-doc-input-icon">▤</span>
                        <input name="title" maxlength="160" placeholder="e.g. API Reference" required>
                        <small data-count-for="title">0 / 160</small>
                    </div>
                </label>

                <div class="admin-doc-form-two">
                    <label class="admin-doc-field">
                        <span>Icon <i>Optional</i></span>
                        <div class="admin-doc-input-wrap compact">
                            <span class="admin-doc-input-icon">◈</span>
                            <input name="icon" maxlength="20" placeholder="⌘">
                        </div>
                    </label>

                    <label class="admin-doc-field">
                        <span>Order</span>
                        <div class="admin-doc-input-wrap compact">
                            <input type="number" name="sort_order" value="99" min="0">
                            <span class="admin-doc-number-arrows">↕</span>
                        </div>
                    </label>
                </div>

                <label class="admin-doc-field">
                    <span>Description <i>Optional</i></span>
                    <div class="admin-doc-input-wrap textarea-wrap">
                        <textarea name="description" maxlength="500" rows="3" placeholder="What belongs in this section?"></textarea>
                        <small data-count-for="description">0 / 500</small>
                    </div>
                </label>

                <button class="admin-primary admin-doc-add-button" type="submit">
                    <span>＋</span> Add section
                </button>
            </form>
        </div>

        <div class="admin-doc-tip admin-doc-version-tip">
            <div class="admin-doc-tip-icon">✦</div>
            <div>
                <strong>Versioning tip</strong>
                <p>For NOVAOS, Roze, and StratosDB, use release-specific pages for compatibility-sensitive documentation. Keep conceptual pages unassigned when they apply across releases.</p>
            </div>
        </div>
    </aside>

    <script>
        (() => {
            const bindCounter = (field, counter) => {
                if (!field || !counter) return;
                const update = () => counter.textContent = `${field.value.length} / ${field.maxLength}`;
                field.addEventListener('input', update);
                update();
            };
            bindCounter(document.querySelector('[name="title"]'), document.querySelector('[data-count-for="title"]'));
            bindCounter(document.querySelector('[name="description"]'), document.querySelector('[data-count-for="description"]'));
        })();
    </script>
</div>
@endsection
