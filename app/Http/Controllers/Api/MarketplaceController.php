<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceItem;
use App\Models\MarketplaceRelease;
use App\Models\SoftwareProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class MarketplaceController extends Controller
{
    public function index(Request $request, string $project)
    {
        $data = $request->validate([
            'type' => ['nullable', 'in:plugin,extension'],
            'platform' => ['nullable', 'string', 'max:30'],
            'architecture' => ['nullable', 'string', 'max:20'],
            'channel' => ['nullable', 'in:Stable,Beta,Nightly'],
            'appVersion' => ['nullable', 'string', 'max:80'],
        ]);

        $cacheKey = 'rozehub:marketplace:'.sha1(json_encode([$project, $data]));

        $items = Cache::remember($cacheKey, now()->addSeconds(30), function () use ($project, $data) {
            return MarketplaceItem::query()
                ->where('is_published', true)
                ->whereHas('project', fn ($q) => $q->where('slug', $project))
                ->when(isset($data['type']), fn ($q) => $q->where('item_type', $data['type']))
                ->with(['releases' => function ($q) use ($data) {
                    $q->where('is_published', true)
                        ->when(isset($data['platform']), fn ($r) => $r->whereIn('platform', ['All', $data['platform']]))
                        ->when(isset($data['architecture']), fn ($r) => $r->whereIn('architecture', ['All', $data['architecture']]))
                        ->when(isset($data['channel']), fn ($r) => $r->where('channel', $data['channel']))
                        ->latest('published_at')->latest('id');
                }])
                ->orderByDesc('is_official')
                ->orderBy('name')
                ->get();
        });

        $projectModel = SoftwareProject::with('ecosystemProfile')->where('slug', $project)->first();

        return response()->json([
            'project' => $project,
            'ecosystem' => $projectModel?->ecosystemProfile ? [
                'type' => $projectModel->ecosystemProfile->ecosystem_type,
                'title' => $projectModel->ecosystemProfile->title,
                'description' => $projectModel->ecosystemProfile->description,
                'itemTypes' => $projectModel->ecosystemProfile->item_types ?? [],
                'capabilities' => $projectModel->ecosystemProfile->capabilities ?? [],
                'packageTypes' => $projectModel->ecosystemProfile->package_types ?? [],
                'platforms' => $projectModel->ecosystemProfile->platforms ?? [],
                'architectures' => $projectModel->ecosystemProfile->architectures ?? [],
                'integrations' => $projectModel->ecosystemProfile->integration_targets ?? [],
            ] : null,
            'count' => $items->count(),
            'items' => $items->map(function (MarketplaceItem $item) {
                $latest = $item->releases->first();

                return [
                    'id' => $item->item_id,
                    'name' => $item->name,
                    'slug' => $item->slug,
                    'type' => $item->item_type,
                    'vendor' => $item->vendor,
                    'category' => $item->category,
                    'summary' => $item->summary,
                    'description' => $item->description,
                    'license' => $item->license,
                    'website' => $item->website,
                    'supportUrl' => $item->support_url,
                    'repositoryUrl' => $item->repository_url,
                    'permissions' => $item->permissions ?? [],
                    'capabilities' => $item->capabilities ?? [],
                    'compatibility' => $item->compatibility ?? [],
                    'iconUrl' => $item->icon_path ? asset($item->icon_path) : null,
                    'official' => (bool) $item->is_official,
                    'verified' => (bool) $item->is_verified,
                    'downloads' => (int) $item->downloads_count,
                    'latest' => $latest ? $this->release($latest) : null,
                ];
            })->values(),
            'checkedAt' => now()->toIso8601String(),
        ]);
    }

    public function item(string $project, MarketplaceItem $item)
    {
        abort_unless($item->is_published && $item->project?->slug === $project, 404);

        $item->load(['project', 'releases' => fn ($q) => $q->where('is_published', true)->latest('published_at')]);

        return response()->json([
            'project' => $project,
            'item' => [
                'id' => $item->item_id,
                'name' => $item->name,
                'slug' => $item->slug,
                'type' => $item->item_type,
                'vendor' => $item->vendor,
                'category' => $item->category,
                'summary' => $item->summary,
                'description' => $item->description,
                'license' => $item->license,
                'website' => $item->website,
                'supportUrl' => $item->support_url,
                'repositoryUrl' => $item->repository_url,
                'permissions' => $item->permissions ?? [],
                'capabilities' => $item->capabilities ?? [],
                'compatibility' => $item->compatibility ?? [],
                'official' => (bool) $item->is_official,
                'verified' => (bool) $item->is_verified,
                'downloads' => (int) $item->downloads_count,
                'releases' => $item->releases->map(fn (MarketplaceRelease $release) => $this->release($release))->values(),
            ],
        ]);
    }

    public function download(MarketplaceRelease $release)
    {
        abort_unless($release->is_published && $release->item?->is_published && $release->file_path, 404);
        abort_unless(Storage::disk('releases')->exists($release->file_path), 404);

        $release->increment('downloads_count');
        $release->item()->increment('downloads_count');

        return Storage::disk('releases')->download($release->file_path, $release->file_name, [
            'Content-Type' => 'application/octet-stream',
            'X-RozeHub-Plugin' => (string) $release->item?->item_id,
            'X-RozeHub-Version' => $release->version,
            'X-RozeHub-SHA256' => (string) $release->sha256,
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    private function release(MarketplaceRelease $release): array
    {
        return [
            'id' => $release->id,
            'version' => $release->version,
            'platform' => $release->platform,
            'architecture' => $release->architecture,
            'channel' => $release->channel,
            'minimumAppVersion' => $release->minimum_app_version,
            'maximumAppVersion' => $release->maximum_app_version,
            'packageType' => $release->package_type,
            'fileName' => $release->file_name,
            'fileSize' => (int) $release->file_size,
            'sha256' => $release->sha256,
            'mandatory' => (bool) $release->is_mandatory,
            'releaseNotes' => $release->release_notes,
            'dependencies' => $release->dependencies ?? [],
            'publishedAt' => optional($release->published_at)->toIso8601String(),
            'downloadUrl' => route('api.marketplace.download', $release),
        ];
    }
}
