<?php
namespace App\Services;

use App\Models\MarketplaceItem;
use App\Models\MarketplaceRelease;
use App\Models\SoftwareProject;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Throwable;
use App\Models\MarketplaceDependency;
use App\Models\MarketplaceReview;
use Illuminate\Support\Facades\DB;

class MarketplaceService
{
    public function __construct(private readonly SemverService $semver, private readonly MarketplacePackageInspector $inspector, private readonly MarketplaceManifestService $manifestService) {}

    public function projects(bool $communityOnly = false)
    {
        return SoftwareProject::query()->with('ecosystemProfile')->whereHas('ecosystemProfile', function($q) use ($communityOnly) {
            $q->where('marketplace_enabled', true);
            if ($communityOnly) $q->where('community_contributions', true);
        })->orderBy('name')->get();
    }

    public function projectForMarketplace(int $id, bool $community = false): SoftwareProject
    {
        $query = SoftwareProject::with('ecosystemProfile')->whereKey($id)->whereHas('ecosystemProfile', function($q) use ($community) {
            $q->where('marketplace_enabled', true);
            if ($community) $q->where('community_contributions', true);
        });
        $project = $query->first();
        if (!$project) abort(404, 'The selected project is not available in the marketplace.');
        return $project;
    }

    public function assertItemAllowed(SoftwareProject $project, string $type): void
    {
        $profile = $project->ecosystemProfile;
        if (!$profile || !in_array($type, $profile->item_types ?? [], true)) {
            throw ValidationException::withMessages(['item_type' => "{$type} is not supported by {$project->name}."]);
        }
    }

    public function assertCategoryAllowed(SoftwareProject $project, ?string $category): void {
        if (!$category) return;
        $categories=$project->marketplaceCategories()->where('is_active',true)->pluck('name')->all();
        if ($categories && !in_array($category,$categories,true)) throw ValidationException::withMessages(['category'=>"{$category} is not a valid category for {$project->name}."]);
    }

    public function assertReleaseAllowed(MarketplaceItem $item, array $data): void
    {
        $profile = $item->project?->ecosystemProfile;
        if (!$profile) throw ValidationException::withMessages(['software_project_id'=>'The selected project has no marketplace policy.']);
        foreach ([['platform','platforms'],['architecture','architectures'],['package_type','package_types'],['channel','channels']] as [$field,$source]) {
            $allowed = $profile->{$source} ?? [];
            if ($allowed && !in_array($data[$field], $allowed, true)) {
                throw ValidationException::withMessages([$field => "{$data[$field]} is not supported by {$item->project->name}."]);
            }
        }
    }

    public function itemPayload(Request $request): array
    {
        return [
            'slug'=>Str::slug($request->input('slug') ?: $request->input('name')),
            'permissions'=>$this->lines($request->input('permissions_text')),
            'capabilities'=>$this->lines($request->input('capabilities_text')),
            'compatibility'=>['targets'=>$this->lines($request->input('compatibility_text')), 'minimumProjectVersion'=>trim((string)$request->input('minimum_project_version')) ?: null],
        ];
    }

    public function dependencyPayload(?string $raw): array
    {
        $out=[];
        foreach ($this->lines($raw) as $line) { [$id,$min]=array_pad(explode('@',$line,2),2,null); $out[]=['itemId'=>trim($id),'minimumVersion'=>$min?trim($min):null]; }
        return $out;
    }

    public function package(Request $request, MarketplaceItem $item, array $metadata, ReleaseStorageService $storage): array
    {
        try {
            if ($request->filled('upload_token')) return $storage->consumeUploadTokenToMarketplace((string)$request->input('upload_token'),$item,$metadata);
            if ($request->hasFile('package')) return $storage->storeMarketplaceUploadedFile($request->file('package'),$item,$metadata);
        } catch (Throwable $e) { throw ValidationException::withMessages(['package'=>$e->getMessage()]); }
        throw ValidationException::withMessages(['package'=>'Please select a package.']);
    }

    public function inspectAndValidatePackage(\Illuminate\Http\UploadedFile $file, MarketplaceItem $item, array $release): array {
        $inspection=$this->inspector->inspect($file);
        if(!$inspection['manifest']) throw ValidationException::withMessages(['package'=>'Package must contain a valid rozehub.json manifest.']);
        $manifest=$this->manifestService->validate($inspection['manifest'],$item,$release);
        return [$inspection,$manifest];
    }
    public function inspectStoredPackage(string $path, MarketplaceItem $item, array $release): array {
        $inspection=$this->inspector->inspect(\Illuminate\Support\Facades\Storage::disk('releases')->path($path));
        if(!$inspection['manifest']) throw ValidationException::withMessages(['package'=>'Package must contain a valid rozehub.json manifest.']);
        return [$inspection,$this->manifestService->validate($inspection['manifest'],$item,$release)];
    }

    public function syncDependencies(MarketplaceRelease $release, array $manifest): void {
        $release->dependencies()->delete();
        foreach($this->manifestService->dependencyRows($manifest) as $dep){
            $item=MarketplaceItem::where('item_id',$dep['itemId'])->where('is_published',true)->first();
            if(!$item) { if(!($dep['optional']??false)) throw ValidationException::withMessages(['package'=>'Required dependency not found: '.$dep['itemId']]); continue; }
            $release->dependencies()->create(['dependency_item_id'=>$item->id,'minimum_version'=>$dep['constraint'],'optional'=>(bool)$dep['optional']]);
        }
    }

    public function dependencyCheck(MarketplaceRelease $release): array {
        $problems=[]; foreach($release->dependencies()->with('dependency.publishedReleases')->get() as $dep){ $versions=$dep->dependency->publishedReleases->pluck('version')->all(); $ok=false; foreach($versions as $v){ try{ if($this->semver->satisfies($v,$dep->minimum_version)){ $ok=true; break; } }catch(\Throwable $e){} } if(!$ok && !$dep->optional) $problems[]='No published version of '.$dep->dependency->item_id.' satisfies '.$dep->minimum_version; } return $problems;
    }

    public function ratingSummary(MarketplaceItem $item): array {
        $q=$item->marketplaceReviews()->where('is_approved',true); $count=(clone $q)->count(); $avg=round((float)(clone $q)->avg('rating'),2); $dist=[]; for($i=5;$i>=1;$i--) $dist[$i]=(clone $q)->where('rating',$i)->count(); return ['average'=>$avg,'count'=>$count,'distribution'=>$dist];
    }

    private function lines(?string $value): array { return array_values(array_filter(array_map('trim',preg_split('/\r\n|\r|\n/',(string)$value)))); }
}
