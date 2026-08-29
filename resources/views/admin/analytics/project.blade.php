@extends('admin.layout')
@section('content')
<div class="admin-actions"><div><span class="eyebrow">PROJECT ANALYTICS</span><h1>{{ $project->name }}</h1><p class="admin-muted">Operational activity for the selected period.</p></div><a class="admin-secondary" href="{{ route('admin.analytics.index') }}">← All analytics</a></div>
<div class="metric-grid">@foreach([['Events','events'],['Downloads','downloads'],['Marketplace','marketplace'],['Documentation','documentation'],['GitHub','github']] as [$label,$key])<div class="metric"><span>{{ $label }}</span><strong>{{ number_format($stats[$key]) }}</strong><small>last {{ $days }} days</small></div>@endforeach</div>
<section class="admin-card"><div class="card-heading"><div><span>ACTIVITY</span><h2>Daily events</h2></div></div><table class="admin-table"><thead><tr><th>Date</th><th>Events</th></tr></thead><tbody>@forelse($daily as $d)<tr><td>{{ $d->day }}</td><td>{{ $d->total }}</td></tr>@empty<tr><td colspan="2">No activity yet.</td></tr>@endforelse</tbody></table></section>
@endsection
