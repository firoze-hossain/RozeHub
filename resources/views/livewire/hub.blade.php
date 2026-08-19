<main class="shell">
        <header class="topbar">
            <a class="brand" href="{{ route('hub') }}"><span class="brand-mark">R</span><span>RozeHub</span></a>
            <nav><a class="active" href="#catalog">Explore</a><a href="#releases">Releases</a><a href="#community">Community</a></nav>
            <a class="studio-link" href="{{ route('admin.login') }}">Admin login <span>↗</span></a>
        </header>

        <section class="hero">
            <div class="hero-copy">
                <p class="eyebrow"><i></i> Independent developer ecosystem</p>
                <h1>Tools for the way<br>you <em>build.</em></h1>
                <p class="hero-text">A focused collection of desktop software, developer tools, and infrastructure made by Firoze. One place to discover, download, and keep everything current.</p>
                <div class="hero-actions"><a class="button primary" href="#catalog">Browse software <span>↓</span></a><a class="text-link" href="#releases">Latest releases <span>→</span></a></div>
            </div>
            <div class="hero-art" aria-label="RozeHub product ecosystem">
                <div class="art-glow"></div><div class="art-window back"><span></span><span></span><span></span></div>
                <div class="art-window main"><div class="art-bar"><b>DBNavigator</b><span>...</span></div><div class="art-body"><aside><i></i><i></i><i></i><i></i></aside><section><div class="code-line wide"></div><div class="code-line"></div><div class="code-line soft"></div><div class="table-grid"><b></b><b></b><b></b><b></b><b></b><b></b></div></section></div></div>
                <div class="terminal"><span>›</span> roze build <b>ready</b></div>
            </div>
        </section>

        <section class="pulse" aria-label="RozeHub statistics"><div><strong>{{ number_format($totalDownloads) }}</strong><span>downloads delivered</span></div><div><strong>{{ $projects->count() }}</strong><span>projects in the ecosystem</span></div><div><strong>{{ $releaseCount }}</strong><span>release packages available</span></div><div><strong>3</strong><span>platforms supported</span></div></section>

        <section id="catalog" class="catalog-section">
            <div class="section-heading"><div><p class="eyebrow">Software catalog</p><h2>Find your next workflow.</h2></div><p>Every download is a direct package from the latest published release.</p></div>
            <div class="filterbar"><label class="search"><span>⌕</span><input wire:model.live.debounce.250ms="search" placeholder="Search software, category, capability"></label><div class="platforms">@foreach(['All platforms', 'Windows', 'macOS', 'Linux'] as $item)<button wire:click="$set('platform', '{{ $item }}')" class="{{ $platform === $item ? 'selected' : '' }}">{{ $item }}</button>@endforeach</div></div>
            <div class="project-grid">
                @forelse($projects as $project)
                    @php($latest = $project->releases->first())
                    <article class="project-card {{ $selected?->id === $project->id ? 'is-selected' : '' }}" wire:click="selectProject({{ $project->id }})">
                        <div class="project-top"><span class="project-icon accent-{{ $project->accent }}">{{ $project->icon }}</span><span class="category">{{ $project->category }}</span></div>
                        <h3>{{ $project->name }}</h3><p>{{ $project->tagline }}</p>
                        <footer><span>{{ $latest ? 'v'.$latest->version : 'Coming soon' }}</span><span>{{ $project->releases->sum('downloads_count') > 0 ? number_format($project->releases->sum('downloads_count')).' downloads' : 'New project' }}</span></footer>
                    </article>
                @empty
                    <div class="empty">No software matches that search.</div>
                @endforelse
            </div>
        </section>

        @if($selected)
        <section id="releases" class="detail-section">
            <div class="detail-intro"><span class="project-icon large accent-{{ $selected->accent }}">{{ $selected->icon }}</span><div><p class="eyebrow">Selected project</p><h2>{{ $selected->name }}</h2><p>{{ $selected->description }}</p></div></div>
            <div class="detail-layout"><div class="release-panel"><div class="panel-title"><h3>Available downloads</h3><span>{{ $selected->releases->count() }} builds</span></div>
                @forelse($selected->releases as $release)
                    <div class="release-row"><div class="os-icon">{{ $release->platform === 'Windows' ? '⊞' : ($release->platform === 'macOS' ? '◉' : '◈') }}</div><div><strong>{{ $release->platform }} · {{ $release->architecture }}</strong><p>v{{ $release->version }} · {{ $release->channel }} · {{ $release->published_at?->format('M j, Y') }}</p></div><div class="release-count">{{ number_format($release->downloads_count) }}<small>downloads</small></div><a class="download-button" href="{{ route('releases.download', $release) }}" title="Download {{ $release->file_name }}">↓</a></div>
                @empty <p class="empty">No published packages yet.</p> @endforelse
            </div>
            <aside class="release-notes"><p class="eyebrow">Release notes</p><h3>{{ $selected->releases->first()?->version ? 'What\'s new in v'.$selected->releases->first()->version : 'Next release in progress' }}</h3><p>{{ $selected->releases->first()?->notes ?: 'The next version is being prepared. Check the publisher studio when it is ready.' }}</p><div class="compat"><span>◎</span><div><strong>Verified releases</strong><small>Packages are listed only after publishing.</small></div></div></aside></div>
        </section>
        @endif

        <section id="community" class="community-section"><div class="section-heading"><div><p class="eyebrow">Community notes</p><h2>Built in public.</h2></div><p>Share feedback about {{ $selected?->name ?? 'the ecosystem' }} to help shape the next release.</p></div>
            <div class="community-layout"><div class="review-list">@forelse($selected?->reviews ?? [] as $review)<article class="review"><div class="review-avatar">{{ strtoupper(substr($review->author_name, 0, 1)) }}</div><div><div class="review-meta"><strong>{{ $review->author_name }}</strong><span>{{ str_repeat('★', $review->rating) }}<i>{{ str_repeat('★', 5 - $review->rating) }}</i></span></div><p>{{ $review->body }}</p></div></article>@empty <p class="empty">Be the first to leave a review for this project.</p>@endforelse</div>
                <form class="review-form" wire:submit="saveReview"><p class="eyebrow">Leave a review</p><h3>Your release notes, in context.</h3>@if(session('review-sent'))<p class="success">{{ session('review-sent') }}</p>@endif<label>Your name<input wire:model="reviewName" placeholder="Name or handle"></label>@error('reviewName')<small class="error">{{ $message }}</small>@enderror<label>Rating<select wire:model="reviewRating">@for($rating = 5; $rating >= 1; $rating--)<option value="{{ $rating }}">{{ $rating }} stars</option>@endfor</select></label><label>What worked for you?<textarea wire:model="reviewBody" placeholder="Share a useful, specific note..."></textarea></label>@error('reviewBody')<small class="error">{{ $message }}</small>@enderror<button class="button primary" type="submit">Publish review <span>→</span></button></form>
            </div>
        </section>
        <footer class="site-footer"><a class="brand" href="#"><span class="brand-mark">R</span><span>RozeHub</span></a><p>Software made with intent. Built by Firoze.</p><a href="{{ route('admin.login') }}">Admin login →</a></footer>
</main>
