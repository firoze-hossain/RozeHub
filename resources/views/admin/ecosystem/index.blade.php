@extends('admin.layout')
@php
    $heading = 'Ecosystem Policies';
    $title = 'Ecosystem Policies · RozeHub Admin';
@endphp
@section('content')
<div class="admin-page-head"><div><p class="admin-kicker">PROJECT ECOSYSTEM</p><h2>Marketplace & Extension Policies</h2><p class="muted">Configure what each RozeHub project can publish. Project-specific rules, item types, and community contribution rights.</p></div></div>
<div class="admin-card table-card"><table class="admin-table"><thead><tr><th>Project</th><th>Ecosystem Title</th><th>Marketplace</th><th>Community</th><th>Supported Types</th><th></th></tr></thead><tbody>
@foreach($projects as $project) @php($p=$project->ecosystemProfile)
<tr><td><strong>{{ $project->name }}</strong><div class="muted" style="font-size:10px;">{{ $project->category }}</div></td><td><strong>{{ $p?->title ?? 'Not configured' }}</strong></td><td><span class="status {{ $p?->marketplace_enabled ? 'published' : 'draft' }}">{{ $p?->marketplace_enabled ? 'Enabled' : 'Disabled' }}</span></td><td><span class="status {{ $p?->community_contributions ? 'published' : 'draft' }}">{{ $p?->community_contributions ? 'Enabled' : 'Disabled' }}</span></td><td style="max-width:320px; line-height:1.5;">{{ implode(', ', $p?->item_types ?? []) ?: '—' }}</td><td style="text-align:right;"><a class="admin-secondary" style="font-size:11px; padding:6px 12px;" href="{{ route('admin.ecosystem.edit',$project) }}">Configure →</a></td></tr>
@endforeach
</tbody></table></div>
@endsection
