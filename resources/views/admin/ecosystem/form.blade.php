@extends('admin.layout')
@section('content')
<div class="admin-page-head"><div><p class="admin-kicker">{{ $project->name }}</p><h2>Ecosystem policy</h2><p class="muted">These values are the source of truth for developer forms, marketplace validation and API metadata.</p></div></div>
@if(session('success'))<div class="admin-alert success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="admin-alert error"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('admin.ecosystem.update',$project) }}" class="admin-form">@csrf @method('PUT')
<label>Ecosystem type<input name="ecosystem_type" value="{{ old('ecosystem_type',$profile->ecosystem_type) }}" required></label>
<label>Title<input name="title" value="{{ old('title',$profile->title) }}" required></label>
<label>Description<textarea name="description" rows="4">{{ old('description',$profile->description) }}</textarea></label>
@php($lists=['item_types'=>'Item types','capabilities'=>'Capabilities','package_types'=>'Package types','platforms'=>'Platforms','architectures'=>'Architectures','channels'=>'Channels','integration_targets'=>'Integration targets'])
@foreach($lists as $key=>$label)<label>{{ $label }}<textarea name="{{ $key }}[]" rows="4" placeholder="One value per line">{{ implode("\n", old($key,$profile->{$key} ?? [])) }}</textarea></label>@endforeach
<div class="checkbox-row"><label><input type="checkbox" name="marketplace_enabled" value="1" @checked(old('marketplace_enabled',$profile->marketplace_enabled))> Marketplace enabled</label><label><input type="checkbox" name="community_contributions" value="1" @checked(old('community_contributions',$profile->community_contributions))> Community contributions enabled</label><label><input type="checkbox" name="moderation_required" value="1" @checked(old('moderation_required',$profile->moderation_required))> Moderation required</label></div>
<button class="admin-primary">Save ecosystem policy</button> <a class="admin-secondary" href="{{ route('admin.ecosystem.index') }}">Back</a>
</form>
@endsection
