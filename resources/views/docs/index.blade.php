<!doctype html>
<html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Documentation · RozeHub</title><link rel="icon" href="{{ asset('images/rozehub-icon.png') }}"><link rel="stylesheet" href="{{ asset('css/rozehub.css') }}">
</head><body class="docs-body">
<header class="docs-topbar"><div class="docs-topbar-inner"><a class="docs-brand" href="{{ route('hub') }}"><span class="docs-brand-mark"><img src="{{ asset('images/rozehub-icon.png') }}" alt="RozeHub"></span><span><strong>RozeHub</strong><small>Documentation</small></span></a><nav><a href="{{ route('hub') }}">Home</a><a class="active" href="{{ route('docs.index') }}">Docs</a><a href="{{ route('hub') }}#releases">Releases</a></nav><form class="docs-search" action="{{ route('docs.search') }}"><span>⌕</span><input name="q" placeholder="Search documentation…" value=""></form></div></header>
<main class="docs-wrap">
<section class="docs-landing-hero"><div><span class="docs-kicker">ROZEHUB DOCUMENTATION</span><h1>Build. Learn. Understand.</h1><p>Versioned documentation for every project in the RozeHub ecosystem — from developer tools to NOVAOS, the Roze programming language, and StratosDB.</p><div class="docs-hero-actions"><a class="docs-primary" href="#projects">Browse documentation <span>↓</span></a><a class="docs-secondary" href="{{ route('docs.search') }}">Search docs →</a></div></div><div class="docs-hero-orbit"><div class="orbit-core">R</div><span class="orbit-pill one">NOVAOS</span><span class="orbit-pill two">Roze</span><span class="orbit-pill three">StratosDB</span><span class="orbit-pill four">Tools</span></div></section>
<section id="projects" class="docs-projects"><div class="docs-section-heading"><div><span class="docs-kicker">DOCUMENTATION LIBRARY</span><h2>Choose a project.</h2></div><p>Each project has its own navigation, guides, reference material, release notes, and printable documentation.</p></div>
<div class="docs-project-grid">
@foreach($projects as $project)
<article class="docs-project-card {{ in_array($project->slug,['novaos','roze-language','stratosdb']) ? 'docs-special' : '' }}">
<div class="docs-project-card-top"><span class="docs-project-icon accent-{{ $project->accent }}">{{ $project->icon }}</span><span>{{ $project->category }}</span></div>
<h3>{{ $project->name }}</h3><p>{{ $project->tagline }}</p>
<div class="docs-project-meta"><span>{{ $project->published_docs_count }} published pages</span><span>{{ $project->documentationSections->count() }} sections</span><span>{{ optional($project->releases->first())->version ? 'v'.optional($project->releases->first())->version : 'docs current' }}</span></div>
<div class="docs-card-actions"><a href="{{ route('docs.project',$project) }}">Open docs <span>→</span></a>@if($project->github_url)<a class="quiet" href="{{ $project->github_url }}" target="_blank" rel="noopener">GitHub ↗</a>@else<a class="quiet" href="{{ route('docs.print',$project) }}">Print / PDF</a>@endif</div>
</article>
@endforeach
</div></section>
</main>
<footer class="docs-footer"><span>RozeHub Documentation</span><span>Laravel · PHP · No Node.js required</span></footer>
</body></html>
