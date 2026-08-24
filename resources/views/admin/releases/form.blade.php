@extends('admin.layout')
@section('content')
<div class="admin-page-head"><div><span>RELEASE DISTRIBUTION</span><h2>{{ $mode==='create'?'Create application release':'Edit application release' }}</h2><p>Release packages are stored outside the Laravel project. RozeHub keeps only the package path, checksum and update metadata in MySQL.</p></div><a class="admin-secondary" href="{{ route('admin.releases.index') }}">← Back</a></div>
<form class="admin-form-card release-form" method="POST" enctype="multipart/form-data" action="{{ $mode==='create'?route('admin.releases.store'):route('admin.releases.update',$release) }}">
@csrf @if($mode==='edit') @method('PUT') @endif
<div class="release-form-section"><div class="release-form-section-head"><span>RELEASE IDENTITY</span><strong>Version and distribution target</strong></div>
<div class="form-grid two"><label>Software<select name="software_project_id" required><option value="">Choose software</option>@foreach($projects as $p)<option value="{{ $p->id }}" @selected(old('software_project_id',$release->software_project_id)==$p->id)>{{ $p->name }}</option>@endforeach</select></label><label>Version<input name="version" value="{{ old('version',$release->version) }}" placeholder="2.1.0" required><small class="field-help">Use semantic or numeric versions such as 2.1.0 or 2026.2.1.</small></label></div>
<div class="form-grid three"><label>Platform<select name="platform">@foreach(['Windows','macOS','Linux'] as $v)<option @selected(old('platform',$release->platform)===$v)>{{ $v }}</option>@endforeach</select></label><label>Architecture<select name="architecture">@foreach(['x64','ARM64'] as $v)<option @selected(old('architecture',$release->architecture)===$v)>{{ $v }}</option>@endforeach</select></label><label>Channel<select name="channel">@foreach(['Stable','Beta','Nightly'] as $v)<option @selected(old('channel',$release->channel)===$v)>{{ $v }}</option>@endforeach</select></label></div></div>

<div class="release-form-section"><div class="release-form-section-head"><span>UPDATE POLICY</span><strong>Control how desktop clients receive this build</strong></div>
<div class="form-grid two"><label>Minimum supported version<input name="minimum_version" value="{{ old('minimum_version',$release->minimum_version) }}" placeholder="e.g. 1.8.0"><small class="field-help">Clients older than this version are forced to update when a newer build is available.</small></label><label class="check-line release-policy-check"><input type="checkbox" name="is_mandatory" value="1" @checked(old('is_mandatory',$release->is_mandatory))><span><strong>Mandatory update</strong><small>Tell compatible desktop clients that this release must be installed.</small></span></label></div></div>

<div class="release-form-section"><div class="release-form-section-head"><span>DISTRIBUTION ARTIFACTS</span><strong>Separate new-install and in-app update packages</strong></div>
<div class="artifact-card" data-chunk-upload>
<div class="artifact-card-head"><div><strong>INSTALLER</strong><span>Used by new users downloading this release from RozeHub.</span></div><span class="artifact-badge">PUBLIC DOWNLOAD</span></div>
<label>Installer package <span class="hint">Examples: .dmg, .msi, .deb, .exe. Large files use the fast chunk uploader.</span><input class="file-drop" type="file" name="package" {{ $mode==='create'?'required':'' }}></label>
@if($mode==='edit' && $release->file_name)<div class="current-file release-current-file"><strong>Current installer:</strong> {{ $release->file_name }} · {{ $release->file_size ? number_format($release->file_size/1048576,1).' MB' : 'size unavailable' }} @if($release->sha256)<br><strong>SHA-256:</strong> <code>{{ $release->sha256 }}</code>@endif<br><small>Stored at <code>{{ $release->file_path }}</code></small></div>@endif
<div class="release-upload-status" data-upload-status hidden><div><strong data-upload-message>Preparing upload…</strong><span>External release storage</span></div><div class="release-upload-track"><i data-upload-progress></i></div></div>
</div>
<div class="artifact-card" data-chunk-upload>
<div class="artifact-card-head"><div><strong>UPDATER</strong><span>Used by installed desktop applications for automatic updates.</span></div><span class="artifact-badge artifact-badge-update">AUTO UPDATE</span></div>
<label>Update package <span class="hint">macOS: .pkg · Windows: .msi/.exe · Linux: .deb/.AppImage. Optional.</span><input class="file-drop" type="file" name="update_package"></label>
@if($mode==='edit' && $release->artifacts()->where('purpose','UPDATER')->first()) @php($updater = $release->artifacts()->where('purpose','UPDATER')->first())<div class="current-file release-current-file"><strong>Current updater:</strong> {{ $updater->file_name }} · {{ number_format($updater->file_size/1048576,1) }} MB<br><strong>SHA-256:</strong> <code>{{ $updater->sha256 }}</code><br><small>Stored at <code>{{ $updater->file_path }}</code></small></div>@endif
<div class="release-upload-status" data-upload-status hidden><div><strong data-upload-message>Preparing upload…</strong><span>External release storage</span></div><div class="release-upload-track"><i data-upload-progress></i></div></div>
</div>
<p class="field-help">One release can have both artifacts. MySQL stores metadata and paths only; the actual packages stay in external release storage.</p>
</div>

<div class="release-form-section"><div class="release-form-section-head"><span>RELEASE NOTES</span><strong>Tell users what changed</strong></div><label>Release notes<textarea name="notes" placeholder="New features, fixes, compatibility changes…">{{ old('notes',$release->notes) }}</textarea></label></div>
<label class="check-line"><input type="checkbox" name="is_published" value="1" @checked(old('is_published',$release->is_published))><span>Publish this release immediately</span></label>
<div class="form-actions"><a class="admin-secondary" href="{{ route('admin.releases.index') }}">Cancel</a><button class="admin-primary" type="submit">{{ $mode==='create'?'Upload & save release':'Save release' }}</button></div>
</form>
@include('admin.releases._chunk-uploader')
@endsection
