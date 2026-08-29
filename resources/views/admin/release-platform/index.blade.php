@extends('admin.layout')
@section('content')
<div class="admin-page">
 <div class="page-header"><div><h1>Release Platform</h1><p>Synchronize GitHub releases, process artifacts, verify integrity, monitor health and roll back safely.</p></div></div>
 @if(session('success'))<div class="alert alert-success">{{session('success')}}</div>@endif
 @if(session('error'))<div class="alert alert-danger">{{session('error')}}</div>@endif
 <div class="card" style="margin-bottom:20px"><h2>GitHub synchronization</h2><p>Import published GitHub releases and queue their installer artifacts for processing.</p>
  @foreach(\App\Models\SoftwareProject::whereNotNull('github_url')->orderBy('name')->get() as $project)
   <form method="POST" action="{{route('admin.release-platform.github-sync',$project)}}" style="display:inline-block;margin:6px 8px 6px 0">@csrf<button class="btn btn-primary">Sync {{$project->name}}</button></form>
  @endforeach
 </div>
 <div class="card"><div style="overflow:auto"><table class="admin-table"><thead><tr><th>Project</th><th>Version</th><th>Target</th><th>Channel</th><th>Source</th><th>Processing</th><th>Signature</th><th>Health</th><th>Actions</th></tr></thead><tbody>
 @forelse($releases as $release)<tr>
  <td>{{$release->project?->name}}</td><td><strong>{{$release->version}}</strong></td><td>{{$release->platform}} / {{$release->architecture}}</td><td>{{$release->channel}}</td><td>{{$release->source}}</td>
  <td>{{$release->processing_status}}</td><td>{{$release->signature_status}}</td><td>{{$release->health_status}}</td>
  <td style="white-space:nowrap">
   <form method="POST" action="{{route('admin.release-platform.process',$release)}}" style="display:inline">@csrf<button class="btn btn-sm">Process</button></form>
   <form method="POST" action="{{route('admin.release-platform.health',$release)}}" style="display:inline">@csrf<button class="btn btn-sm">Health</button></form>
   @if($release->is_published)<form method="POST" action="{{route('admin.release-platform.rollback',$release)}}" style="display:inline" onsubmit="return confirm('Rollback this release and restore the previous active release?')">@csrf<button class="btn btn-sm btn-danger">Rollback</button></form>@endif
  </td>
 </tr>@empty<tr><td colspan="9">No releases yet.</td></tr>@endforelse
 </tbody></table></div>{{$releases->links()}}</div>
</div>
@endsection
