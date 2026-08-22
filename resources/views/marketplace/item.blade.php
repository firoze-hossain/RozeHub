<!doctype html>
<html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ $item->name }} · RozeHub Marketplace</title>
<link rel="icon" href="{{ asset('images/rozehub-icon.png') }}"><link rel="stylesheet" href="{{ asset('css/rozehub.css') }}">
<style>
.item-page{max-width:1000px;margin:auto;padding:50px 24px 80px}.item-hero{padding:36px;border:1px solid #e6e1f0;border-radius:26px;background:linear-gradient(135deg,#fff,#f8f6ff)}.item-title{display:flex;gap:20px;align-items:center}.item-icon{width:76px;height:76px;border-radius:20px;background:#eee8ff;display:grid;place-items:center;font-size:30px;font-weight:800}.item-title h1{margin:0;font-size:38px}.muted{color:#777}.badge{display:inline-block;padding:6px 10px;border-radius:999px;background:#f1eef7;font-size:12px;margin:4px}.release{border:1px solid #e6e2eb;border-radius:18px;padding:20px;margin-top:14px}.release-head{display:flex;justify-content:space-between;gap:20px}.download{display:inline-block;padding:10px 16px;background:#5b3fc1;color:#fff;border-radius:10px;text-decoration:none;font-weight:700}.item-body{margin-top:28px;line-height:1.7}.permission{font-family:monospace;background:#f7f5fa;padding:5px 8px;border-radius:7px;display:inline-block;margin:3px}
</style></head>
<body><div class="item-page">
<a href="{{ route('marketplace.index') }}">← Marketplace</a>
<section class="item-hero">
<div class="item-title"><div class="item-icon">{{ $item->item_type==='extension'?'◈':'◆' }}</div><div>
<span class="badge">{{ ucfirst($item->item_type) }}</span>
@if($item->is_official)<span class="badge">Official</span>@endif
@if($item->is_verified)<span class="badge">Verified</span>@endif
<h1>{{ $item->name }}</h1><div class="muted">{{ $item->project->name }} · {{ $item->item_id }}</div>
</div></div>
<p>{{ $item->summary }}</p>
<div class="muted">{{ number_format($item->downloads_count) }} installs · {{ $item->vendor ?: 'Community publisher' }}</div>
</section>

<div class="item-body">
<h2>About</h2><div>{!! nl2br(e($item->description)) !!}</div>
@if($item->permissions)
<h2>Capabilities</h2>@foreach($item->permissions as $permission)<span class="permission">{{ $permission }}</span>@endforeach
@endif
<h2>Versions</h2>
@forelse($item->releases as $release)
<div class="release"><div class="release-head"><div><strong>v{{ $release->version }}</strong> · {{ $release->channel }}<br><span class="muted">{{ $release->platform }} · {{ $release->architecture }} · {{ $release->package_type }}</span></div>
<a class="download" href="{{ route('api.marketplace.download',$release) }}">Download</a></div>
<p>{{ $release->release_notes }}</p>
@if($release->minimum_app_version || $release->maximum_app_version)<small>Compatible with {{ $release->minimum_app_version ?: 'any' }} → {{ $release->maximum_app_version ?: 'latest' }}</small>@endif
</div>
@empty <p class="muted">No published versions yet.</p>@endforelse
</div>
</div></body></html>
