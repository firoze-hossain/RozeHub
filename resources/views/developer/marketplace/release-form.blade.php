@extends('developer.layout',['title'=>'Marketplace Release · RozeHub'])
@section('content')
<div class="dev-page-head"><div><span class="eyebrow">{{ $item->project->name }} · {{ strtoupper($item->item_type) }}</span><h1>{{ $item->name }} · {{ $release->version ?: 'New release' }}</h1><p>Release policy is inherited from the selected ecosystem. Unsupported targets are rejected before the package reaches moderation.</p></div></div>

@if($profile)
<div class="ecosystem-policy"><strong>{{ $profile->title }}</strong><span>{{ $profile->description }}</span><div class="chip-list">@foreach($profile->platforms ?? [] as $x)<span class="chip">{{ $x }}</span>@endforeach @foreach($profile->architectures ?? [] as $x)<span class="chip">{{ $x }}</span>@endforeach @foreach($profile->package_types ?? [] as $x)<span class="chip">{{ strtoupper($x) }}</span>@endforeach</div></div>
@endif

<form class="dev-form" method="POST" enctype="multipart/form-data" action="{{ $release->exists?route('developer.marketplace.release.update',$release):route('developer.marketplace.releases.store',$item) }}">
@csrf @if($release->exists)@method('PUT')@endif
<div class="form-grid">
<label>Version<input name="version" required value="{{ old('version',$release->version) }}" placeholder="1.0.0"></label>
<label>Package type<select name="package_type" id="package-type">@foreach($profile?->package_types ?? ['zip','jar','vsix','native','tar.gz'] as $v)<option @selected(old('package_type',$release->package_type)===$v)>{{ $v }}</option>@endforeach</select></label>
<label>Platform<select name="platform" id="release-platform">@foreach($profile?->platforms ?? ['All','Windows','macOS','Linux'] as $v)<option @selected(old('platform',$release->platform)===$v)>{{ $v }}</option>@endforeach</select></label>
<label>Architecture<select name="architecture">@foreach($profile?->architectures ?? ['All','x64','ARM64'] as $v)<option @selected(old('architecture',$release->architecture)===$v)>{{ $v }}</option>@endforeach</select></label>
<label>Channel<select name="channel">@foreach(['Stable','Beta','Nightly'] as $v)<option @selected(old('channel',$release->channel)===$v)>{{ $v }}</option>@endforeach</select></label>
<label>Minimum project version<input name="minimum_app_version" value="{{ old('minimum_app_version',$release->minimum_app_version) }}" placeholder="1.0.0"></label>
<label>Maximum project version<input name="maximum_app_version" value="{{ old('maximum_app_version',$release->maximum_app_version) }}" placeholder="2.x"></label>
</div>
<label>Release notes<textarea name="release_notes" rows="7" placeholder="Describe changes, compatibility and migration notes.">{{ old('release_notes',$release->release_notes) }}</textarea></label>
<label>Dependencies<textarea name="dependencies_text" rows="4" placeholder="com.roze.example.core@1.2.0">{{ old('dependencies_text',collect($release->dependencies??[])->map(fn($d)=>($d['itemId']??'').(($d['minimumVersion']??null)?'@'.$d['minimumVersion']:''))->implode("\n")) }}</textarea></label>
<label>Package file<input type="file" name="package" accept=".zip,.jar,.vsix,.tar,.gz,.tgz"> <small>Packages are stored outside the Laravel web root. SHA-256 is recorded with the release identity.</small>@if($release->file_name)<small>Current: {{ $release->file_name }} · {{ number_format(($release->file_size??0)/1048576,1) }} MB</small>@endif</label>
<input type="hidden" name="upload_token"><div data-upload-status class="chunk-upload-status" hidden><strong data-upload-message>Preparing…</strong><div class="chunk-progress"><span data-upload-progress style="width:0"></span></div></div>
<div class="check-row"><label><input type="checkbox" name="is_mandatory" value="1" @checked(old('is_mandatory',$release->is_mandatory))> Mandatory update</label></div>
<div class="dev-form-actions"><a href="{{ route('developer.marketplace.edit',$item) }}">Cancel</a><button type="submit" class="dev-primary">Save draft</button></div>
@include('developer.marketplace.chunk-uploader')
</form>
@if($release->exists)<div class="submit-panel"><h2>Ready for review?</h2><p>RozeHub will record the release identity, run automated package and capability checks, and place it in the moderation queue. Approval is required before publication.</p><form method="POST" action="{{ route('developer.marketplace.submit',$release) }}">@csrf<label>Message to reviewer<textarea name="developer_message" rows="3" placeholder="What changed and why should this release be approved?"></textarea></label><button type="submit" class="dev-primary">Submit for review</button></form></div>@endif
@endsection
