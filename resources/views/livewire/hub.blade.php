<main class="shell">
        <header class="topbar">
            <a class="brand" href="{{ route('hub') }}"><span class="brand-logo"><img src="{{ asset('images/rozehub-ecosystem.png').'?v=20260828-professional-logo-2' }}" alt="RozeHub"></span><span>RozeHub</span></a>
            <nav><a class="active" href="#catalog">Explore</a><a href="#releases">Releases</a><a href="{{ route('docs.index') }}">Documentation</a><a href="#community">Community</a></nav>
            <a class="studio-link" href="{{ route('admin.login') }}">Admin login <span>↗</span></a>
        </header>

        <section class="hero">
            <div class="hero-copy">
                <p class="eyebrow"><i></i> Open Source · Built for Developers</p>
                <h1>Build. Code.<br><em>Innovate.</em></h1>
                <p class="hero-text">RozeHub is the home of independent software built with passion and purpose. Discover developer tools, desktop applications, programming technology, and infrastructure — all in one place.</p>
                <div class="hero-actions"><a class="button primary" href="#catalog">Browse software <span>↓</span></a><a class="text-link" href="#releases">Latest releases <span>→</span></a></div>
            </div>
            <div class="hero-art ecosystem-art" aria-label="RozeHub product ecosystem">
                <div class="ecosystem-glow"></div>
                <div class="ecosystem-visual">
                    <img class="ecosystem-image" src="{{ asset('images/rozehub-ecosystem.png').'?v=20260828-professional-logo-2' }}" alt="RozeHub ecosystem showing Lumina, DBNavigator, Thundercall, TrackEye, Roze, StratosDB and NOVAOS">

                    @php
                        $ecosystemProducts = [
                            ['slug' => 'lumina', 'name' => 'Lumina', 'image' => 'lumina.png', 'class' => 'lumina'],
                            ['slug' => 'dbnavigator', 'name' => 'DBNavigator', 'image' => 'dbnavigator.png', 'class' => 'dbnavigator'],
                            ['slug' => 'thundercall', 'name' => 'ThunderCall', 'image' => 'thundercall.png', 'class' => 'thundercall'],
                            ['slug' => 'novaos', 'name' => 'NOVAOS', 'image' => 'novaos.png', 'class' => 'novaos'],
                            ['slug' => 'trackline', 'name' => 'TrackEye', 'image' => 'trackeye.png', 'class' => 'trackeye'],
                            ['slug' => 'roze-language', 'name' => 'Roze', 'image' => 'roze.png', 'class' => 'roze'],
                            ['slug' => 'stratosdb', 'name' => 'StratosDB', 'image' => 'stratosdb.png', 'class' => 'stratosdb'],
                        ];
                    @endphp

                    <div class="ecosystem-hotspots" aria-label="Explore RozeHub projects">
                        @foreach($ecosystemProducts as $product)
                            <a class="ecosystem-hotspot hotspot-{{ $product['class'] }}" href="#catalog" aria-label="Preview {{ $product['name'] }}">
                                <span class="sr-only">{{ $product['name'] }}</span>
                                <span class="ecosystem-preview" role="img" aria-label="{{ $product['name'] }} preview">
                                    <img src="{{ asset('images/projects/'.$product['image']) }}" alt="{{ $product['name'] }}">
                                    <strong>{{ $product['name'] }}</strong>
                                    <small>View project</small>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="pulse" aria-label="RozeHub statistics"><div><strong>{{ number_format($totalDownloads) }}</strong><span>downloads delivered</span></div><div><strong>{{ $projects->count() + ($novaos ? 1 : 0) }}</strong><span>projects in the ecosystem</span></div><div><strong>{{ $releaseCount }}</strong><span>release packages available</span></div><div><strong>3</strong><span>platforms supported</span></div></section>

        <section id="catalog" class="catalog-section">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Software catalog</p>
                    <h2>Find your next workflow.</h2>
                </div>
                <p>Explore desktop tools, developer software and infrastructure projects. NOVAOS has its own dedicated operating-system area below.</p>
            </div>

            <div class="filterbar">
                <label class="search"><span>⌕</span><input wire:model.live.debounce.250ms="search" placeholder="Search software, category, capability"></label>
                <div class="platforms">
                    @foreach(['All platforms', 'Windows', 'macOS', 'Linux', 'NOVAOS'] as $item)
                        <button wire:click="$set('platform', '{{ $item }}')" class="{{ $platform === $item ? 'selected' : '' }}">{{ $item }}</button>
                    @endforeach
                </div>
            </div>

            @if($platform === 'NOVAOS')
                <div class="catalog-label novaos-filter-label">
                    <div><span class="catalog-dot novaos-dot"></span><strong>NOVAOS operating system</strong></div>
                    <span>Independent OS</span>
                </div>
            @else
                <div class="catalog-label">
                    <div><span class="catalog-dot"></span><strong>Applications & developer tools</strong></div>
                    <span>{{ $projects->count() }} projects</span>
                </div>
            @endif

            @if($platform !== 'NOVAOS')
            <div class="project-grid">
                @forelse($projects as $project)
                    @php($latest = $project->releases->first())
                    @php($projectImage = $this->projectImageFor($project->slug))
                    <article class="project-card {{ $selected?->id === $project->id ? 'is-selected' : '' }}" wire:click="selectProject({{ $project->id }})">
                        @if($projectImage)
                            <div class="project-hover-image" aria-hidden="true">
                                <img src="{{ asset('images/projects/'.$projectImage) }}" alt="">
                                <span class="project-hover-label">View {{ $project->name }}</span>
                            </div>
                        @endif
                        <div class="project-top"><span class="project-icon accent-{{ $project->accent }}">{{ $project->icon }}</span><span class="category">{{ $project->category }}</span></div>
                        <h3>{{ $project->name }}</h3>
                        <p>{{ $project->tagline }}</p>
                        <footer><span>{{ $latest ? 'v'.$latest->version : 'Coming soon' }}</span><span>{{ $project->releases->sum('downloads_count') > 0 ? number_format($project->releases->sum('downloads_count')).' downloads' : 'New project' }}</span></footer>
                    </article>
                @empty
                    <div class="empty">No software matches that search.</div>
                @endforelse
            </div>
            @endif

            @if($novaos && in_array($platform, ['All platforms', 'NOVAOS'], true))
                @php($novaLatest = $novaos->releases->first())
                <section class="novaos-catalog-card" aria-labelledby="novaos-catalog-title">
                    <div class="novaos-catalog-art">
                        <div class="novaos-catalog-glow"></div>
                        <img src="{{ asset('images/projects/novaos.png') }}" alt="NOVAOS independent operating system">
                        <span class="novaos-catalog-orbit orbit-one"></span>
                        <span class="novaos-catalog-orbit orbit-two"></span>
                    </div>

                    <div class="novaos-catalog-content">
                        <div class="novaos-catalog-topline">
                            <p class="eyebrow"><i></i> Independent operating system</p>
                            <span class="novaos-catalog-badge">Not an application</span>
                        </div>
                        <h2 id="novaos-catalog-title">NOVAOS</h2>
                        <p class="novaos-catalog-lead">An independent operating system project with its own builds, architecture targets and release lifecycle.</p>

                        <div class="novaos-facts">
                            <div><span>Latest build</span><strong>{{ $novaLatest ? 'v'.$novaLatest->version : 'In development' }}</strong></div>
                            <div><span>Published builds</span><strong>{{ $novaos->releases->count() }}</strong></div>
                            <div><span>Downloads</span><strong>{{ number_format($novaos->releases->sum('downloads_count')) }}</strong></div>
                        </div>

                        <div class="novaos-catalog-actions">
                            <a class="button primary" href="#releases" wire:click="selectProject({{ $novaos->id }})">Explore NOVAOS <span>→</span></a>
                            <span class="novaos-platform-note"><b>◈</b> NOVAOS builds are never grouped under Windows, macOS or Linux.</span>
                        </div>
                    </div>

                    <div class="novaos-catalog-builds">
                        <div class="novaos-mini-heading"><span>Latest NOVAOS builds</span><span>{{ $novaos->releases->count() }} total</span></div>
                        @forelse($novaos->releases->take(3) as $release)
                            <a href="#releases" wire:click="selectProject({{ $novaos->id }})" class="novaos-mini-build">
                                <span class="novaos-mini-version">{{ $release->version }}</span>
                                <span><b>{{ $release->channel }}</b><small>{{ $release->architecture }} · {{ $release->published_at?->format('M j, Y') }}</small></span>
                                <span class="novaos-mini-arrow">→</span>
                            </a>
                        @empty
                            <p class="empty">No published NOVAOS builds yet.</p>
                        @endforelse
                    </div>
                </section>
            @endif
        </section>

        @if($selected)
            @if($this->isNovaosProject($selected))
                <section id="releases" class="detail-section novaos-detail">
                    <div class="novaos-heading">
                        <div class="novaos-brand">
                            <span class="novaos-icon"><img src="{{ asset('images/projects/novaos.png') }}" alt="NOVAOS"></span>
                            <div>
                                <p class="eyebrow">Independent operating system</p>
                                <h2>NOVAOS</h2>
                                <p>NOVAOS is distributed as its own operating-system builds. It does not belong under Windows, macOS, or Linux.</p>
                            </div>
                        </div>
                        <span class="novaos-badge">Independent OS</span>
                    </div>

                    <div class="novaos-release-shell">
                        <div class="novaos-builds">
                            <div class="panel-title"><div><p class="eyebrow">NOVAOS releases</p><h3>Choose a build</h3></div><span>{{ $selected->releases->count() }} builds</span></div>
                            @forelse($selected->releases as $release)
                                <details class="novaos-build" @if($loop->first) open @endif>
                                    <summary>
                                        <span class="novaos-version">Version <strong>{{ $release->version }}</strong></span>
                                        <span class="novaos-build-meta"><b>{{ $release->channel }}</b> · {{ $release->published_at?->format('M j, Y') }} · {{ $release->architecture }}</span>
                                        <span class="novaos-chevron">⌄</span>
                                    </summary>
                                    <div class="novaos-build-body">
                                        <div class="novaos-build-info">
                                            <div><span>Major version</span><strong>{{ $release->major_version ?: $release->version }}</strong></div>
                                            <div><span>Build</span><strong>{{ $release->build_number ?: '—' }}</strong></div>
                                            <div><span>Architecture</span><strong>{{ $release->architecture }}</strong></div>
                                            <div><span>Channel</span><strong>{{ $release->channel }}</strong></div>
                                            <div><span>Published</span><strong>{{ $release->published_at?->format('F j, Y') }}</strong></div>
                                            <div><span>Codename</span><strong>{{ $release->codename ?: '—' }}</strong></div>
                                        </div>
                                        @if($release->notes)
                                            <p class="novaos-notes">{{ $release->notes }}</p>
                                        @endif
                                        <div class="novaos-download">
                                            <div><strong>{{ $release->file_name ?: 'NOVAOS build '.$release->version }}</strong><small>{{ $release->file_size ? number_format($release->file_size / 1048576, 1).' MB' : 'Official release package' }} · {{ number_format($release->downloads_count) }} downloads</small>@if($release->sha256)<small class="novaos-sha">SHA-256: {{ $release->sha256 }}</small>@endif</div>
                                            <a class="download-button" href="{{ route('releases.download', $release) }}" title="Download NOVAOS {{ $release->version }}">↓ Download</a>
                                        </div>
                                    </div>
                                </details>
                            @empty
                                <p class="empty">No published NOVAOS builds yet.</p>
                            @endforelse
                        </div>
                        <aside class="novaos-side">
                            <img src="{{ asset('images/projects/novaos.png') }}" alt="NOVAOS artwork">
                            <p class="eyebrow">About NOVAOS</p>
                            <h3>Independent by design.</h3>
                            <p>System releases are managed separately from application platforms, so NOVAOS users always see NOVAOS builds instead of Windows, macOS, or Linux labels.</p>
                            <div class="compat"><span>◈</span><div><strong>Official builds</strong><small>Published packages are delivered directly from RozeHub.</small></div></div>
                        </aside>
                    </div>
                </section>
            @else
                <section id="releases" class="detail-section">
                    <div class="detail-intro">
    <span class="project-icon large accent-{{ $selected->accent }}">{{ $selected->icon }}</span>
    <div class="detail-project-copy">
        <p class="eyebrow">Selected project</p>
        <h2>{{ $selected->name }}</h2>
        <p>{{ $selected->description }}</p>
        @if($selected->github_url)
            <div class="project-source-actions">
                <a class="github-button" href="{{ $selected->github_url }}" target="_blank" rel="noopener noreferrer">
                    <span aria-hidden="true">◈</span> View source &amp; contribute on GitHub <span aria-hidden="true">↗</span>
                </a>
                <span class="source-note">Open source · Issues &amp; pull requests welcome</span>
            </div>
        @endif
    </div>
</div>
                    <div class="detail-layout"><div class="release-panel"><div class="panel-title"><h3>Available downloads</h3><span>{{ $selected->releases->count() }} builds</span></div>
                        @forelse($selected->releases as $release)
                            <div class="release-row"><div class="os-icon">{{ $release->platform === 'Windows' ? '⊞' : ($release->platform === 'macOS' ? '◉' : '◈') }}</div><div><strong>{{ $release->platform }} · {{ $release->architecture }}</strong><p>v{{ $release->version }} · {{ $release->channel }} · {{ $release->published_at?->format('M j, Y') }}</p></div><div class="release-count">{{ number_format($release->downloads_count) }}<small>downloads</small></div><a class="download-button" href="{{ route('releases.download', $release) }}" title="Download {{ $release->file_name }}">↓</a></div>
                        @empty <p class="empty">No published packages yet.</p> @endforelse
                    </div>
                    <aside class="release-notes"><p class="eyebrow">Release notes</p><h3>{{ $selected->releases->first()?->version ? 'What\'s new in v'.$selected->releases->first()->version : 'Next release in progress' }}</h3><p>{{ $selected->releases->first()?->notes ?: 'The next version is being prepared. Check the publisher studio when it is ready.' }}</p><div class="compat"><span>◎</span><div><strong>Verified releases</strong><small>Packages are listed only after publishing.</small></div></aside></div>
                </section>
            @endif
        @endif

        <section id="community" class="community-section"><div class="section-heading"><div><p class="eyebrow">Community notes</p><h2>Built in public.</h2></div><p>Share feedback about {{ $selected?->name ?? 'the ecosystem' }} to help shape the next release.</p></div>
            <div class="community-layout"><div class="review-list">@forelse($selected?->reviews ?? [] as $review)<article class="review"><div class="review-avatar">{{ strtoupper(substr($review->author_name, 0, 1)) }}</div><div><div class="review-meta"><strong>{{ $review->author_name }}</strong><span>{{ str_repeat('★', $review->rating) }}<i>{{ str_repeat('★', 5 - $review->rating) }}</i></span></div><p>{{ $review->body }}</p></div></article>@empty <p class="empty">Be the first to leave a review for this project.</p>@endforelse</div>
                <form class="review-form" wire:submit="saveReview"><p class="eyebrow">Leave a review</p><h3>Your release notes, in context.</h3>@if(session('review-sent'))<p class="success">{{ session('review-sent') }}</p>@endif<label>Your name<input wire:model="reviewName" placeholder="Name or handle"></label>@error('reviewName')<small class="error">{{ $message }}</small>@enderror<label>Rating<select wire:model="reviewRating">@for($rating = 5; $rating >= 1; $rating--)<option value="{{ $rating }}">{{ $rating }} stars</option>@endfor</select></label><label>What worked for you?<textarea wire:model="reviewBody" placeholder="Share a useful, specific note..."></textarea></label>@error('reviewBody')<small class="error">{{ $message }}</small>@enderror<button class="button primary" type="submit">Publish review <span>→</span></button></form>
            </div>
        </section>
        <footer class="site-footer"><a class="brand" href="#"><span class="brand-logo"><img src="{{ asset('images/rozehub-ecosystem.png').'?v=20260828-professional-logo-2' }}" alt="RozeHub"></span><span>RozeHub</span></a><p>Software made with intent. Built by Firoze.</p><a href="{{ route('admin.login') }}">Admin login →</a></footer>
</main>
