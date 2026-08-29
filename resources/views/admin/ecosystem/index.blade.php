@extends('admin.layout')
@section('content')
<div class="admin-page-head"><div><p class="admin-kicker">Project ecosystem</p><h2>Marketplace policies</h2><p class="muted">Configure what each RozeHub project can publish. No project-specific rules are stored in controller code.</p></div></div>
<div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Project</th><th>Ecosystem</th><th>Marketplace</th><th>Community</th><th>Item types</th><th></th></tr></thead><tbody>
@foreach($projects as $project) @php($p=$project->ecosystemProfile)
<tr><td><strong>{{ $project->name }}</strong><div class="muted">{{ $project->category }}</div></td><td>{{ $p?->title ?? 'Not configured' }}</td><td>{{ $p?->marketplace_enabled ? 'Enabled' : 'Disabled' }}</td><td>{{ $p?->community_contributions ? 'Enabled' : 'Disabled' }}</td><td>{{ implode(', ', $p?->item_types ?? []) ?: '—' }}</td><td><a class="admin-secondary" href="{{ route('admin.ecosystem.edit',$project) }}">Configure</a></td></tr>
@endforeach
</tbody></table></div>
@endsection
