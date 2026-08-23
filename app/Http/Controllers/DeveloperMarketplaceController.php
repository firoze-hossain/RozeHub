<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceItem;
use App\Models\MarketplaceRelease;
use App\Models\MarketplaceSubmission;
use App\Models\MarketplaceNotification;
use App\Models\SoftwareProject;
use App\Services\MarketplaceModerationService;
use App\Services\MarketplaceRiskService;
use App\Services\ReleaseStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class DeveloperMarketplaceController extends Controller
{
    public function __construct(private readonly ReleaseStorageService $storage, private readonly MarketplaceRiskService $risk, private readonly MarketplaceModerationService $moderation) {}

    private function projects(){ return SoftwareProject::whereIn('slug',['lumina','dbnavigator'])->orderBy('name')->get(); }

    public function dashboard(){
        $items=MarketplaceItem::where('owner_user_id',auth()->id())->with(['project','releases'])->latest()->get();
        $submissions=MarketplaceSubmission::where('submitted_by',auth()->id())->with(['item.project','release'])->latest()->paginate(12);
        $unread=MarketplaceNotification::where('user_id',auth()->id())->whereNull('read_at')->count();
        return view('developer.dashboard',compact('items','submissions','unread'));
    }

    public function notifications(){
        $notifications=MarketplaceNotification::where('user_id',auth()->id())->latest()->paginate(20);
        return view('developer.notifications',compact('notifications'));
    }
    public function readNotification(MarketplaceNotification $notification){ abort_unless($notification->user_id===auth()->id(),403); $notification->update(['read_at'=>now()]); return back(); }

    public function create(){ return view('developer.marketplace.item-form',['projects'=>$this->projects(),'item'=>new MarketplaceItem(['item_type'=>'plugin']),'mode'=>'create']); }
    public function store(Request $request){
        $data=$this->validateItem($request);
        $project=SoftwareProject::whereKey($data['software_project_id'])->whereIn('slug',['lumina','dbnavigator'])->firstOrFail();
        $item=MarketplaceItem::create(array_merge($data,[
            'owner_user_id'=>auth()->id(),'slug'=>Str::slug($data['slug'] ?: $data['name']),
            'permissions'=>$this->permissions($request),'is_official'=>false,'is_verified'=>false,'is_published'=>false,
        ]));
        return redirect()->route('developer.marketplace.releases.create',$item)->with('success','Marketplace item created as a draft. Add your first release and submit it for review.');
    }
    public function edit(MarketplaceItem $item){
        $this->owned($item);
        $item->load(['project','releases'=>fn($q)=>$q->with(['submissions'=>fn($sq)=>$sq->latest()])->latest('id')]);
        return view('developer.marketplace.item-form',['projects'=>$this->projects(),'item'=>$item,'mode'=>'edit']);
    }
    public function update(Request $request,MarketplaceItem $item){
        $this->owned($item); $data=$this->validateItem($request);
        $project=SoftwareProject::whereKey($data['software_project_id'])->whereIn('slug',['lumina','dbnavigator'])->firstOrFail();
        $item->update(array_merge($data,['slug'=>Str::slug($data['slug'] ?: $data['name']),'permissions'=>$this->permissions($request),'is_published'=>$item->is_published]));
        return back()->with('success','Draft information updated.');
    }

    public function createRelease(MarketplaceItem $item){$this->owned($item); return view('developer.marketplace.release-form',['item'=>$item,'release'=>new MarketplaceRelease(['platform'=>'All','architecture'=>'All','channel'=>'Stable','package_type'=>'zip'])]);}
    public function storeRelease(Request $request,MarketplaceItem $item){
        $this->owned($item);
        $data=$this->validateRelease($request);

        // A release is uniquely identified by item + version + platform + architecture + channel.
        // If the developer retries the form (for example after a large upload or browser refresh),
        // update the existing unpublished draft instead of creating a duplicate row.
        $existing=MarketplaceRelease::where('marketplace_item_id',$item->id)
            ->where('version',$data['version'])
            ->where('platform',$data['platform'])
            ->where('architecture',$data['architecture'])
            ->where('channel',$data['channel'])
            ->first();

        if($existing){
            $latestSubmission=$existing->submissions()->latest('id')->first();

            // Published or currently moderated releases are immutable from the developer side.
            if($existing->is_published || ($latestSubmission && in_array($latestSubmission->status,[MarketplaceSubmission::SUBMITTED,MarketplaceSubmission::UNDER_REVIEW,MarketplaceSubmission::APPROVED,MarketplaceSubmission::PUBLISHED],true))){
                return redirect()->route('developer.marketplace.release.edit',$existing)
                    ->withErrors(['release'=>'This release version already exists and is currently published or under review. Create a new version instead.']);
            }

            $oldPath=$existing->file_path;
            try {
                $package=$this->package($request,$item,$data);
            } catch(Throwable $e) {
                throw ValidationException::withMessages(['package'=>$e->getMessage()]);
            }

            $existing->update(array_merge($data,$package,[
                'is_published'=>false,
                'is_mandatory'=>$request->boolean('is_mandatory'),
                'published_at'=>null,
                'dependencies'=>$this->dependencies($request),
            ]));

            // Remove the previous external package only after the database update succeeds.
            if($oldPath && $oldPath!==$existing->file_path){
                $this->storage->delete($oldPath);
            }

            return redirect()->route('developer.marketplace.release.edit',$existing)
                ->with('success','Existing draft updated with the new package. Review it and submit it for approval.');
        }

        try {
            $package=$this->package($request,$item,$data);
        } catch(Throwable $e) {
            throw ValidationException::withMessages(['package'=>$e->getMessage()]);
        }

        try {
            $release=MarketplaceRelease::create(array_merge($data,$package,[
                'marketplace_item_id'=>$item->id,
                'is_published'=>false,
                'is_mandatory'=>$request->boolean('is_mandatory'),
                'published_at'=>null,
                'dependencies'=>$this->dependencies($request),
            ]));
        } catch(\Illuminate\Database\QueryException $e) {
            // Protect against a simultaneous submission creating the same release identity.
            if((string)$e->getCode()==='23000'){
                if(!empty($package['file_path'])) $this->storage->delete($package['file_path']);
                $duplicate=MarketplaceRelease::where('marketplace_item_id',$item->id)
                    ->where('version',$data['version'])
                    ->where('platform',$data['platform'])
                    ->where('architecture',$data['architecture'])
                    ->where('channel',$data['channel'])
                    ->first();
                if($duplicate){
                    return redirect()->route('developer.marketplace.release.edit',$duplicate)
                        ->with('success','This release already existed, so the existing draft was opened.');
                }
            }
            throw $e;
        }

        return redirect()->route('developer.marketplace.release.edit',$release)
            ->with('success','Release saved as DRAFT. Review the metadata and submit it when ready.');
    }
    public function editRelease(MarketplaceRelease $release){$release->load('item');$this->owned($release->item);return view('developer.marketplace.release-form',['item'=>$release->item,'release'=>$release]);}
    public function updateRelease(Request $request,MarketplaceRelease $release){
        $release->load('item');$this->owned($release->item);$data=$this->validateRelease($request,false);
        $old=$release->file_path;
        if($request->hasFile('package')||$request->filled('upload_token')){try{$data=array_merge($data,$this->package($request,$release->item,$data));}catch(Throwable $e){throw ValidationException::withMessages(['package'=>$e->getMessage()]);}}
        unset($data['package'],$data['upload_token']); $release->update(array_merge($data,['is_published'=>false,'published_at'=>null,'dependencies'=>$this->dependencies($request)]));
        if($old && $old!==$release->file_path)$this->storage->delete($old);
        $release->submissions()->whereIn('status',[MarketplaceSubmission::SUBMITTED,MarketplaceSubmission::UNDER_REVIEW])->update(['status'=>MarketplaceSubmission::DRAFT]);
        return back()->with('success','Release updated and kept unpublished. Submit it again for review.');
    }

    public function submit(Request $request,MarketplaceRelease $release){
        $release->load('item.project');$this->owned($release->item);
        if(!$release->file_path) return back()->withErrors(['release'=>'Upload a package before submitting.']);
        $existing=$release->submissions()->latest()->first();
        if($existing && in_array($existing->status,[MarketplaceSubmission::SUBMITTED,MarketplaceSubmission::UNDER_REVIEW],true)) return back()->withErrors(['release'=>'This release is already in the review queue.']);
        if($existing && $existing->status===MarketplaceSubmission::NEEDS_CHANGES){
            $submission=$existing;
            $submission->update(['developer_message'=>$request->input('developer_message'),'decision_reason'=>null,'reviewer_notes'=>null,'reviewed_by'=>null,'reviewed_at'=>null,'resubmission_count'=>$submission->resubmission_count+1]);
        } else {
            $submission=MarketplaceSubmission::create(['marketplace_item_id'=>$release->marketplace_item_id,'marketplace_release_id'=>$release->id,'submitted_by'=>auth()->id(),'status'=>MarketplaceSubmission::DRAFT,'risk_level'=>'LOW','risk_score'=>0,'developer_message'=>$request->input('developer_message')]);
        }
        $assessment=$this->risk->assess($submission->load(['item','release']));
        $submission->update(['risk_level'=>$assessment['level'],'risk_score'=>$assessment['score'],'risk_summary'=>$assessment['summary']]);
        $this->moderation->transition($submission,MarketplaceSubmission::SUBMITTED,auth()->user(),$request->input('developer_message'));
        $this->notifyAdmins($submission,'Marketplace review requested','New marketplace submission',"{$release->item->name} v{$release->version} is ready for review.");
        return redirect()->route('developer.dashboard')->with('success','Submitted for admin review.');
    }

    public function submissions(){
        $submissions=MarketplaceSubmission::where('submitted_by',auth()->id())->with(['item.project','release','risks','logs.actor'])->latest()->paginate(15);
        return view('developer.submissions',compact('submissions'));
    }
    public function submission(MarketplaceSubmission $submission){abort_unless($submission->submitted_by===auth()->id(),403);$submission->load(['item.project','release','risks','logs.actor']);return view('developer.submission',compact('submission'));}
    public function resubmit(Request $request,MarketplaceSubmission $submission){
        abort_unless($submission->submitted_by===auth()->id(),403);
        if($submission->status!==MarketplaceSubmission::NEEDS_CHANGES)return back()->withErrors(['submission'=>'Only submissions requiring changes can be resubmitted.']);
        $submission->update(['developer_message'=>$request->input('developer_message'),'reviewer_notes'=>null,'decision_reason'=>null]);
        $this->risk->assess($submission->load(['item','release']));
        $this->moderation->transition($submission,MarketplaceSubmission::SUBMITTED,auth()->user(),$request->input('developer_message'));
        $this->notifyAdmins($submission,'Marketplace resubmitted','Developer resubmitted a marketplace release',"{$submission->item->name} v{$submission->release?->version} was resubmitted.");
        return back()->with('success','Resubmitted for review.');
    }

    private function owned(MarketplaceItem $item):void{abort_unless($item->owner_user_id===auth()->id(),403);}
    private function validateItem(Request $r):array{return $r->validate(['software_project_id'=>['required','exists:software_projects,id'],'item_type'=>['required',Rule::in(['plugin','extension'])],'name'=>['required','string','max:160'],'slug'=>['nullable','string','max:120'],'item_id'=>['required','string','max:160'],'vendor'=>['nullable','string','max:160'],'category'=>['nullable','string','max:100'],'icon_path'=>['nullable','string','max:255'],'website'=>['nullable','url','max:255'],'repository_url'=>['nullable','url','max:255'],'summary'=>['nullable','string','max:500'],'description'=>['nullable','string','max:30000']]);}
    private function permissions(Request $r):array{return array_values(array_filter(array_map('trim',preg_split('/\r\n|\r|\n/',(string)$r->input('permissions_text','')))));}
    private function dependencies(Request $r):array{$out=[];foreach(preg_split('/\r\n|\r|\n/',trim((string)$r->input('dependencies_text',''))) as $line){$line=trim($line);if(!$line)continue;[$id,$min]=array_pad(explode('@',$line,2),2,null);$out[]=['itemId'=>trim($id),'minimumVersion'=>$min?trim($min):null];}return $out;}
    private function validateRelease(Request $r,bool $required=true):array{$rules=['version'=>['required','string','max:80'],'platform'=>['required','string','max:30'],'architecture'=>['required','string','max:20'],'channel'=>['required','in:Stable,Beta,Nightly'],'minimum_app_version'=>['nullable','string','max:80'],'maximum_app_version'=>['nullable','string','max:80'],'package_type'=>['required','string','max:30'],'release_notes'=>['nullable','string','max:30000'],'is_mandatory'=>['nullable','boolean'],'package'=>['nullable','file','max:8388608'],'upload_token'=>['nullable','string','regex:/^[A-Za-z0-9_-]{20,100}$/']];if($required){$rules['package'][]='required_without:upload_token';$rules['upload_token'][]='required_without:package';}return $r->validate($rules);}
    private function package(Request $r,MarketplaceItem $item,array $metadata):array{if($r->filled('upload_token'))return $this->storage->consumeUploadTokenToMarketplace($r->input('upload_token'),$item,$metadata);if($r->hasFile('package'))return $this->storage->storeMarketplaceUploadedFile($r->file('package'),$item,$metadata);throw new \RuntimeException('Please select a package.');}
    private function notifyAdmins(MarketplaceSubmission $submission,string $type,string $title,string $message):void{foreach(\App\Models\User::where('is_admin',true)->get() as $admin)$this->moderation->notify($admin,$submission,$type,$title,$message,route('admin.marketplace.review.show',$submission));}
}
