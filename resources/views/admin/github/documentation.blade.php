@extends('admin.layout')
@section('content')
<div class="admin-page-head"><div><span class="admin-kicker">GITHUB DOCUMENTATION</span><h2>Edit {{ $project->name }}</h2><p>Changes are committed directly to the configured GitHub repository.</p></div><a class="admin-secondary" href="{{ route('admin.github.show',$project) }}">← GitHub overview</a></div>
@if($error)<div class="admin-alert error">{{ $error }}</div>@endif
<form class="admin-card" method="POST" action="{{ route('admin.github.documentation.update',$project) }}">@csrf @method('PUT')
<div class="form-grid two"><label>Repository path<input name="path" value="{{ old('path',$path) }}" required placeholder="README.md"></label><label>Branch<input name="branch" value="{{ old('branch',$file['branch'] ?? ($project->githubRepository?->default_branch ?? '')) }}" placeholder="main"></label></div>
@if($file)<input type="hidden" name="sha" value="{{ $file['sha'] }}"><label>Commit message<input name="message" value="{{ old('message','docs: update '.$path) }}" required></label><label>Content<textarea name="content" rows="28" style="font-family:monospace">{{ old('content', base64_decode($file['content'] ?? '')) }}</textarea></label><div class="form-actions"><button class="admin-primary">Commit to GitHub</button></div>@else<p class="muted">Enter a valid repository file path and load it. If the path does not exist, RozeHub cannot edit it yet.</p><button class="admin-primary" formaction="{{ route('admin.github.documentation',$project) }}" formmethod="GET">Load file</button>@endif
</form>
@endsection
