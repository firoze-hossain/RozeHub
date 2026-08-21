@extends('admin.layout')
@section('content')
<div class="novaos-admin-hero">
    <div class="novaos-admin-hero-copy">
        <span class="novaos-admin-kicker">INDEPENDENT OPERATING SYSTEM</span>
        <h2>NOVAOS Release Center</h2>
        <p>Manage operating-system images independently from application releases. Publish Stable, Beta and Nightly builds with architecture, build metadata and automatically generated SHA-256 checksums.</p>
        <div class="novaos-admin-actions"><a class="admin-primary" href="{{ route('admin.novaos.releases.create') }}">+ Create NOVAOS release</a><a class="admin-secondary" href="{{ route('admin.novaos.releases.index') }}">Manage releases →</a></div>
    </div>
    <img src="{{ asset('images/projects/novaos.png') }}" alt="NOVAOS">
</div>
<div class="metric-grid novaos-metrics">
    <div class="metric"><span>Latest Stable</span><strong>{{ $latestStable?->version ?? '—' }}</strong><small>{{ $latestStable?->codename ?: 'No stable build published' }}</small></div>
    <div class="metric"><span>Published Builds</span><strong>{{ $publishedCount }}</strong><small>{{ $stableCount }} stable builds</small></div>
    <div class="metric"><span>Downloads</span><strong>{{ number_format($downloadCount) }}</strong><small>all NOVAOS builds</small></div>
    <div class="metric"><span>Release Channels</span><strong>3</strong><small>Stable · Beta · Nightly</small></div>
</div>
<div class="admin-card novaos-admin-recent">
    <div class="card-heading"><div><span>RELEASE ARCHIVE</span><h2>Recent NOVAOS builds</h2></div><a href="{{ route('admin.novaos.releases.index') }}">View all →</a></div>
    @forelse($releases->take(8) as $release)
        <div class="novaos-admin-release-row">
            <div class="novaos-admin-version"><strong>{{ $release->version }}</strong><small>{{ $release->major_version }} @if($release->codename) · {{ $release->codename }} @endif</small></div>
            <div><span class="novaos-channel channel-{{ strtolower($release->channel) }}">{{ $release->channel }}</span><small>{{ $release->architecture }} · build {{ $release->build_number }}</small></div>
            <div><small>{{ $release->file_name ?: 'No package' }}</small><small>{{ $release->sha256 ? 'SHA-256 ready' : 'Checksum unavailable' }}</small></div>
            <span class="status {{ $release->is_published?'published':'draft' }}">{{ $release->is_published?'Published':'Draft' }}</span>
            <a href="{{ route('admin.novaos.releases.edit',$release) }}">Edit</a>
        </div>
    @empty
        <p class="admin-empty">No NOVAOS releases yet. Create the first operating-system build.</p>
    @endforelse
</div>
@endsection
