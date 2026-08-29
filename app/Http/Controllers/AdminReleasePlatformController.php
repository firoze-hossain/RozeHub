<?php
namespace App\Http\Controllers;
use App\Jobs\ProcessReleaseArtifact;
use App\Models\{Release, ReleaseChannel, SoftwareProject};
use App\Services\ReleasePlatformService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminReleasePlatformController extends Controller {
 public function index(Request $request){
  $releases=Release::with('project')->latest('id')->paginate(25)->withQueryString();
  return view('admin.release-platform.index',compact('releases'));
 }
 public function syncGithub(SoftwareProject $project, ReleasePlatformService $service){try{$count=$service->syncGithubReleases($project,true); return back()->with('success',"GitHub release synchronization completed: {$count} release(s) processed.");}catch(\Throwable $e){return back()->with('error',$e->getMessage());}}
 public function process(Release $release){ProcessReleaseArtifact::dispatch($release->id); return back()->with('success','Release processing queued.');}
 public function health(Release $release, ReleasePlatformService $service){try{$result=$service->healthCheck($release);return back()->with($result['status']==='HEALTHY'?'success':'warning','Release health: '.$result['status'].'.');}catch(\Throwable $e){return back()->with('error',$e->getMessage());}}
 public function rollback(Release $release, ReleasePlatformService $service){if(!$release->is_published) return back()->with('error','Only a published release can be rolled back.'); try{$restored=$service->rollback($release);return back()->with('success',$restored->id!==$release->id?'Rollback completed. Previous release '.$restored->version.' is active.':'Release rolled back; no previous release was available.');}catch(\Throwable $e){return back()->with('error',$e->getMessage());}}
 public function channels(SoftwareProject $project){$channels=$project->releaseChannels()->orderBy('sort_order')->get();return view('admin.release-platform.channels',compact('project','channels'));}
 public function storeChannel(Request $request, SoftwareProject $project){$data=$request->validate(['key'=>['required','alpha_dash','max:30',Rule::unique('release_channels','key')->where('software_project_id',$project->id)],'name'=>'required|string|max:80','description'=>'nullable|string|max:500','is_enabled'=>'nullable|boolean','is_default'=>'nullable|boolean','sort_order'=>'nullable|integer|min:0']); if(!empty($data['is_default'])) $project->releaseChannels()->update(['is_default'=>false]); $project->releaseChannels()->create($data+['is_enabled'=>$request->boolean('is_enabled'),'is_default'=>$request->boolean('is_default')]); return back()->with('success','Release channel created.');}
 public function updateChannel(Request $request, SoftwareProject $project, ReleaseChannel $channel){abort_unless($channel->software_project_id===$project->id,404);$data=$request->validate(['name'=>'required|string|max:80','description'=>'nullable|string|max:500','is_enabled'=>'nullable|boolean','is_default'=>'nullable|boolean','sort_order'=>'nullable|integer|min:0']);if($request->boolean('is_default'))$project->releaseChannels()->update(['is_default'=>false]);$channel->update($data+['is_enabled'=>$request->boolean('is_enabled'),'is_default'=>$request->boolean('is_default')]);return back()->with('success','Release channel updated.');}
 public function destroyChannel(SoftwareProject $project, ReleaseChannel $channel){abort_unless($channel->software_project_id===$project->id,404);abort_if($channel->is_default,422,'The default channel cannot be deleted.');$channel->delete();return back()->with('success','Release channel deleted.');}
}
