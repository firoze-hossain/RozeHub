<!doctype html>
<html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Marketplace · RozeHub</title>
<link rel="icon" href="{{ asset('images/rozehub-icon.png') }}">
<link rel="stylesheet" href="{{ asset('css/rozehub.css') }}">
<style>
.market-public{max-width:1200px;margin:0 auto;padding:48px 24px 80px}.market-hero{padding:48px;border:1px solid rgba(120,100,170,.18);border-radius:28px;background:linear-gradient(135deg,#fff,#f7f5ff);margin-bottom:28px}.market-hero h1{font-size:48px;margin:8px 0}.market-hero p{max-width:760px;color:#666}.market-search{display:flex;gap:10px;margin-top:24px}.market-search input,.market-search select{padding:13px 15px;border:1px solid #ddd;border-radius:12px;background:white}.market-search input{flex:1}.mp-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(290px,1fr));gap:18px}.mp-card{border:1px solid #e7e3ef;border-radius:20px;padding:22px;background:white;transition:.2s}.mp-card:hover{transform:translateY(-2px);box-shadow:0 12px 35px rgba(40,20,80,.08)}.mp-icon{width:56px;height:56px;border-radius:16px;background:#f0ebff;display:grid;place-items:center;font-weight:800;font-size:22px}.mp-card h2{font-size:20px;margin:16px 0 4px}.mp-meta{font-size:12px;color:#777}.mp-summary{min-height:48px;color:#555}.mp-badge{display:inline-block;padding:5px 9px;border-radius:999px;background:#f2f0f7;font-size:11px;margin:3px}.mp-badge.plugin{background:#eaf8ef;color:#16753b}.mp-badge.extension{background:#edf5ff;color:#2563a9}.mp-link{display:inline-block;margin-top:14px;color:#5b3fc1;font-weight:700;text-decoration:none}
</style></head>
<body>
<div class="market-public">
    <div class="market-hero">
        <span class="mp-badge">ROZEHUB MARKETPLACE</span>
        <h1>Extend your Roze applications.</h1>
        <p>Discover plugins and extensions for Lumina, DBNavigator, and the RozeHub desktop ecosystem. Every package is versioned, compatibility-aware and distributed through RozeHub.</p>
        <form class="market-search">
            <input name="q" value="{{ request('q') }}" placeholder="Search plugins, extensions, database tools…">
            <select name="type"><option value="">All types</option><option value="plugin" @selected(request('type')==='plugin')>Plugins</option><option value="extension" @selected(request('type')==='extension')>Extensions</option></select>
            <button class="admin-button primary">Search</button>
        </form>
    </div>

    <div class="mp-grid">
    @forelse($items as $item)
        <article class="mp-card">
            <div class="mp-icon">{{ $item->icon_path ? '' : ($item->item_type==='extension'?'◈':'◆') }}</div>
            <span class="mp-badge {{ $item->item_type }}">{{ ucfirst($item->item_type) }}</span>
            @if($item->is_official)<span class="mp-badge">Official</span>@endif
            <h2>{{ $item->name }}</h2>
            <div class="mp-meta">{{ $item->project->name }} · {{ $item->vendor ?: 'Community' }}</div>
            <p class="mp-summary">{{ $item->summary }}</p>
            <div class="mp-meta">{{ number_format($item->downloads_count) }} installs</div>
            <a class="mp-link" href="{{ route('marketplace.item',$item) }}">View details →</a>
        </article>
    @empty
        <p>No marketplace items match your search.</p>
    @endforelse
    </div>
    <div style="margin-top:28px">{{ $items->links() }}</div>
</div>
</body></html>
