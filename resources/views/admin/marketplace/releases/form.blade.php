@extends('admin.layout', ['heading' => $mode === 'create' ? 'New marketplace release' : 'Edit marketplace release'])

@section('content')
<div class="admin-page-head">
    <div>
        <p class="admin-kicker">{{ ucfirst($item->item_type) }} · {{ $item->project->name }}</p>
        <h2>{{ $item->name }} · {{ $mode === 'create' ? 'New version' : $release->version }}</h2>
        <p class="muted">Compatibility is evaluated by the desktop application before installation.</p>
    </div>
</div>

<form class="admin-form" method="POST" enctype="multipart/form-data" action="{{ $mode === 'create' ? route('admin.marketplace.releases.store', $item) : route('admin.marketplace.releases.update', $release) }}">
@csrf
@if($mode === 'edit') @method('PUT') @endif

<div class="form-grid-2">
    <label>Version<input name="version" required value="{{ old('version', $release->version) }}" placeholder="1.2.0"></label>
    <label>Package type
        <select name="package_type">
            @foreach(['zip','jar','vsix','native','tar.gz'] as $type)
                <option @selected(old('package_type', $release->package_type) === $type)>{{ $type }}</option>
            @endforeach
        </select>
    </label>
    <label>Platform
        <select name="platform">
            @foreach(['All','Windows','macOS','Linux'] as $v)<option @selected(old('platform',$release->platform)===$v)>{{ $v }}</option>@endforeach
        </select>
    </label>
    <label>Architecture
        <select name="architecture">
            @foreach(['All','x64','ARM64'] as $v)<option @selected(old('architecture',$release->architecture)===$v)>{{ $v }}</option>@endforeach
        </select>
    </label>
    <label>Channel
        <select name="channel">
            @foreach(['Stable','Beta','Nightly'] as $v)<option @selected(old('channel',$release->channel)===$v)>{{ $v }}</option>@endforeach
        </select>
    </label>
    <label>Minimum application version<input name="minimum_app_version" value="{{ old('minimum_app_version',$release->minimum_app_version) }}" placeholder="2.0.0"></label>
    <label>Maximum application version<input name="maximum_app_version" value="{{ old('maximum_app_version',$release->maximum_app_version) }}" placeholder="3.x"></label>
</div>

<label>Release notes<textarea name="release_notes" rows="7">{{ old('release_notes',$release->release_notes) }}</textarea></label>

<label>Dependencies <small>(one item ID per line; optional minimum version after @)</small>
<textarea name="dependencies_text" rows="4" placeholder="com.roze.dbnavigator.sqlformatter@2.0.0&#10;com.roze.dbnavigator.core">{{ old('dependencies_text', collect($release->dependencies ?? [])->map(fn($d)=>($d['itemId'] ?? '').(($d['minimumVersion'] ?? null)?'@'.$d['minimumVersion']:''))->implode("\n")) }}</textarea>

<label>Package
    <input type="file" name="package" accept=".zip,.jar,.vsix,.tar,.gz,.tgz,.jar">
    @if($release->file_name)<small>Current: {{ $release->file_name }} · {{ number_format(($release->file_size ?? 0)/1048576,1) }} MB</small>@endif
</label>
<input type="hidden" name="upload_token">

<div data-upload-status class="chunk-upload-status" hidden>
    <strong data-upload-message>Preparing…</strong>
    <div class="chunk-progress"><span data-upload-progress style="width:0"></span></div>
</div>

<div class="check-row">
    <label><input type="checkbox" name="is_mandatory" value="1" @checked(old('is_mandatory',$release->is_mandatory))> Mandatory update</label>
    <label><input type="checkbox" name="is_published" value="1" @checked(old('is_published',$release->is_published))> Publish release</label>
</div>

<div class="form-actions">
    <a class="admin-button" href="{{ route('admin.marketplace.releases.index',$item) }}">Cancel</a>
    <button type="submit" class="admin-button primary">Save marketplace release</button>
</div>

@include('admin.releases._chunk-uploader')
</form>
@endsection
