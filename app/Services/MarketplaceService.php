<?php
namespace App\Services;

use App\Models\MarketplaceItem;
use App\Models\MarketplaceRelease;
use App\Models\SoftwareProject;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Throwable;

class MarketplaceService
{
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

    private function lines(?string $value): array { return array_values(array_filter(array_map('trim',preg_split('/\r\n|\r|\n/',(string)$value)))); }
}
