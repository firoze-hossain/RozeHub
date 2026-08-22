@extends('admin.layout', ['heading' => $item->name.' releases'])

@section('content')
<div class="admin-page-head">
    <div>
        <p class="admin-kicker">{{ ucfirst($item->item_type) }} · {{ $item->project->name }}</p>
        <h2>{{ $item->name }}</h2>
        <p class="muted">{{ $item->item_id }} · Manage compatibility, packages and channels.</p>
    </div>
    <div class="market-actions">
        <a class="admin-button" href="{{ route('admin.marketplace.edit', $item) }}">Edit item</a>
        <a class="admin-button primary" href="{{ route('admin.marketplace.releases.create', $item) }}">＋ New release</a>
    </div>
</div>

<div class="admin-table-wrap">
<table class="admin-table">
<thead><tr><th>Version</th><th>Compatibility</th><th>Target</th><th>Channel</th><th>Package</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>
@forelse($releases as $release)
<tr>
    <td><strong>{{ $release->version }}</strong><br><small>{{ $release->package_type }}</small></td>
    <td>{{ $release->minimum_app_version ?: 'Any' }} → {{ $release->maximum_app_version ?: 'Any' }}</td>
    <td>{{ $release->platform }} · {{ $release->architecture }}</td>
    <td>{{ $release->channel }}</td>
    <td>{{ $release->file_name ?: '—' }}<br><small>{{ $release->file_size ? number_format($release->file_size / 1048576, 1).' MB' : '—' }}</small></td>
    <td>
        <span class="market-badge {{ $release->is_published ? 'verified' : 'muted-badge' }}">{{ $release->is_published ? 'Published' : 'Draft' }}</span>
        @if($release->is_mandatory)<span class="market-badge danger-badge">Mandatory</span>@endif
    </td>
    <td>
        <div class="market-actions">
            <a class="admin-button small" href="{{ route('admin.marketplace.releases.edit', $release) }}">Edit</a>
            <form method="POST" action="{{ route('admin.marketplace.releases.toggle', $release) }}">@csrf<button class="admin-button small">{{ $release->is_published ? 'Unpublish' : 'Publish' }}</button></form>
            <form method="POST" action="{{ route('admin.marketplace.releases.destroy', $release) }}" onsubmit="return confirm('Delete this release and package?')">@csrf @method('DELETE')<button class="admin-button small danger">Delete</button></form>
        </div>
    </td>
</tr>
@empty
<tr><td colspan="7" class="admin-empty">No releases for this marketplace item.</td></tr>
@endforelse
</tbody>
</table>
</div>

{{ $releases->links() }}
@endsection
