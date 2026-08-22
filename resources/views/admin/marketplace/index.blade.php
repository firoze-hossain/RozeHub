@extends('admin.layout', ['heading' => 'Marketplace'])

@section('content')
<div class="admin-page-head">
    <div>
        <p class="admin-kicker">Plugins & extensions</p>
        <h2>Desktop Marketplace</h2>
        <p class="muted">Manage installable capabilities for Lumina, DBNavigator, and future RozeHub desktop applications.</p>
    </div>
    <a class="admin-button primary" href="{{ route('admin.marketplace.create') }}">＋ New marketplace item</a>
</div>

<div class="market-filter">
    <form method="GET">
        <input name="q" value="{{ request('q') }}" placeholder="Search name, ID, vendor…">
        <select name="project">
            <option value="">All applications</option>
            @foreach($projects as $project)
                <option value="{{ $project->id }}" @selected(request('project') == $project->id)>{{ $project->name }}</option>
            @endforeach
        </select>
        <select name="type">
            <option value="">Plugins + extensions</option>
            <option value="plugin" @selected(request('type') === 'plugin')>Plugins</option>
            <option value="extension" @selected(request('type') === 'extension')>Extensions</option>
        </select>
        <button class="admin-button" type="submit">Filter</button>
    </form>
</div>

<div class="market-grid">
@forelse($items as $item)
    <article class="market-card">
        <div class="market-card-top">
            <div class="market-icon">{{ $item->icon_path ? '' : ($item->item_type === 'extension' ? '◈' : '◆') }}
                @if($item->icon_path)<img src="{{ asset($item->icon_path) }}" alt="">@endif
            </div>
            <div class="market-badges">
                <span class="market-badge {{ $item->item_type }}">{{ ucfirst($item->item_type) }}</span>
                @if($item->is_official)<span class="market-badge official">Official</span>@endif
                @if($item->is_verified)<span class="market-badge verified">Verified</span>@endif
            </div>
        </div>
        <h3>{{ $item->name }}</h3>
        <p class="market-meta">{{ $item->project->name }} · {{ $item->item_id }}</p>
        <p>{{ $item->summary ?: 'No summary yet.' }}</p>
        <div class="market-card-footer">
            <span>{{ number_format($item->downloads_count) }} downloads</span>
            <div class="market-actions">
                <a class="admin-button small" href="{{ route('admin.marketplace.releases.index', $item) }}">Releases</a>
                <a class="admin-button small" href="{{ route('admin.marketplace.edit', $item) }}">Edit</a>
                <form method="POST" action="{{ route('admin.marketplace.destroy', $item) }}" onsubmit="return confirm('Delete this marketplace item and all packages?')">
                    @csrf @method('DELETE')
                    <button class="admin-button small danger">Delete</button>
                </form>
            </div>
        </div>
    </article>
@empty
    <div class="admin-empty">No marketplace items yet. Create the first plugin or extension.</div>
@endforelse
</div>

{{ $items->links() }}
@endsection
