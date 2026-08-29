<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceItem;
use App\Models\MarketplaceRelease;
use App\Models\SoftwareProject;
use App\Services\MarketplaceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class MarketplaceController extends Controller
{
    public function __construct(private readonly MarketplaceService $marketplace) {}

    public function index(Request $request, string $project)
    {
        $projectModel=SoftwareProject::with('ecosystemProfile')->where('slug',$project)->firstOrFail();
        $profile=$projectModel->ecosystemProfile;
        abort_unless($profile?->marketplace_enabled,404);
        $data=$request->validate(['type'=>['nullable','string','max:60'],'platform'=>['nullable','string','max:30'],'architecture'=>['nullable','string','max:20'],'channel'=>['nullable','string','max:30'],'appVersion'=>['nullable','string','max:80']]);
        foreach ([['type','item_types'],['platform','platforms'],['architecture','architectures'],['channel','channels']] as [$field,$source]) if(isset($data[$field]) && ($profile->{$source} ?? []) && !in_array($data[$field],$profile->{$source},true)) throw ValidationException::withMessages([$field=>"Unsupported {$field} for {$projectModel->name}."]);
        $cacheKey='rozehub:marketplace:v2:'.sha1(json_encode([$project,$data]));
        $items=Cache::remember($cacheKey,now()->addSeconds(30),fn()=>MarketplaceItem::query()->where('is_published',true)->where('software_project_id',$projectModel->id)->when(isset($data['type']),fn($q)=>$q->where('item_type',$data['type']))->with(['releases'=>function($q)use($data){$q->where('is_published',true)->when(isset($data['platform']),fn($r)=>$r->whereIn('platform',['All',$data['platform']]))->when(isset($data['architecture']),fn($r)=>$r->whereIn('architecture',['All',$data['architecture']]))->when(isset($data['channel']),fn($r)=>$r->where('channel',$data['channel']))->latest('published_at')->latest('id');}])->orderByDesc('is_official')->orderBy('name')->get());
        return response()->json(['project'=>$project,'ecosystem'=>$this->ecosystem($profile),'filters'=>['types'=>$profile->item_types??[],'platforms'=>$profile->platforms??[],'architectures'=>$profile->architectures??[],'channels'=>$profile->channels??[],'packageTypes'=>$profile->package_types??[]],'count'=>$items->count(),'items'=>$items->map(fn(MarketplaceItem $item)=>$this->itemPayload($item))->values(),'checkedAt'=>now()->toIso8601String()]);
    }

    public function item(string $project, MarketplaceItem $item)
    {
        abort_unless($item->is_published && $item->project?->slug===$project,404);
        $item->load(['project.ecosystemProfile','releases'=>fn($q)=>$q->where('is_published',true)->latest('published_at')]);
        return response()->json(['project'=>$project,'ecosystem'=>$this->ecosystem($item->project->ecosystemProfile),'item'=>$this->itemPayload($item,true)]);
    }

    public function download(MarketplaceRelease $release)
    {
        abort_unless($release->is_published && $release->item?->is_published && $release->file_path,404);
        abort_unless(Storage::disk('releases')->exists($release->file_path),404);
        $release->increment('downloads_count'); $release->item()->increment('downloads_count');
        return Storage::disk('releases')->download($release->file_path,$release->file_name,['Content-Type'=>'application/octet-stream','X-RozeHub-Plugin'=>(string)$release->item?->item_id,'X-RozeHub-Version'=>$release->version,'X-RozeHub-SHA256'=>(string)$release->sha256,'Cache-Control'=>'public, max-age=3600']);
    }

    private function ecosystem($profile): ?array {
        if (!$profile) return null;
        return ['type'=>$profile->ecosystem_type,'title'=>$profile->title,'description'=>$profile->description,'itemTypes'=>$profile->item_types??[],'capabilities'=>$profile->capabilities??[],'packageTypes'=>$profile->package_types??[],'platforms'=>$profile->platforms??[],'architectures'=>$profile->architectures??[],'channels'=>$profile->channels??[],'integrations'=>$profile->integration_targets??[]];
    }
    private function itemPayload(MarketplaceItem $item,bool $withReleases=false): array { $latest=$item->releases->first(); $base=['id'=>$item->item_id,'name'=>$item->name,'slug'=>$item->slug,'type'=>$item->item_type,'vendor'=>$item->vendor,'category'=>$item->category,'summary'=>$item->summary,'description'=>$item->description,'license'=>$item->license,'website'=>$item->website,'supportUrl'=>$item->support_url,'repositoryUrl'=>$item->repository_url,'permissions'=>$item->permissions??[],'capabilities'=>$item->capabilities??[],'compatibility'=>$item->compatibility??[],'iconUrl'=>$item->icon_path?asset($item->icon_path):null,'official'=>(bool)$item->is_official,'verified'=>(bool)$item->is_verified,'downloads'=>(int)$item->downloads_count,'latest'=>$latest?$this->release($latest):null]; if($withReleases)$base['releases']=$item->releases->map(fn($r)=>$this->release($r))->values(); return $base; }
    private function release(MarketplaceRelease $r):array{return ['id'=>$r->id,'version'=>$r->version,'platform'=>$r->platform,'architecture'=>$r->architecture,'channel'=>$r->channel,'minimumAppVersion'=>$r->minimum_app_version,'maximumAppVersion'=>$r->maximum_app_version,'packageType'=>$r->package_type,'fileName'=>$r->file_name,'fileSize'=>(int)$r->file_size,'sha256'=>$r->sha256,'mandatory'=>(bool)$r->is_mandatory,'releaseNotes'=>$r->release_notes,'dependencies'=>$r->dependencies??[],'publishedAt'=>optional($r->published_at)->toIso8601String(),'downloadUrl'=>route('api.marketplace.download',$r)];}
}
