<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceDependency;
use App\Models\MarketplaceItem;
use App\Models\MarketplaceRelease;
use App\Http\Requests\MarketplaceItemRequest;
use App\Http\Requests\MarketplaceReleaseRequest;
use App\Services\MarketplaceService;
use App\Models\SoftwareProject;
use App\Services\ReleaseStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class AdminMarketplaceController extends Controller
{
    public function __construct(private readonly ReleaseStorageService $storage, private readonly MarketplaceService $marketplace)
    {
    }

    public function index(Request $request)
    {
        $items = MarketplaceItem::with('project')
            ->when($request->filled('project'), fn ($q) => $q->where('software_project_id', $request->integer('project')))
            ->when($request->filled('type'), fn ($q) => $q->where('item_type', $request->string('type')))
            ->when($request->filled('q'), fn ($q) => $q->where(function ($inner) use ($request) {
                $term = '%'.$request->string('q').'%';
                $inner->where('name', 'like', $term)->orWhere('item_id', 'like', $term)->orWhere('vendor', 'like', $term);
            }))
            ->latest('id')->paginate(15)->withQueryString();

        return view('admin.marketplace.index', [
            'items' => $items,
            'projects' => SoftwareProject::with('ecosystemProfile')->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        $projects = SoftwareProject::with('ecosystemProfile')->orderBy('name')->get();
        return view('admin.marketplace.item-form', [
            'item' => new MarketplaceItem([
                'item_type' => $projects->first()?->ecosystemProfile?->item_types[0] ?? 'plugin',
                'is_official' => true, 'is_verified' => true,
            ]), 'projects' => $projects, 'mode' => 'create',
        ]);
    }

    public function store(MarketplaceItemRequest $request)
    {
        $data = $request->validated();
        $project = $this->marketplace->projectForMarketplace((int)$data['software_project_id']);
        $this->marketplace->assertItemAllowed($project, $data['item_type']);
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $meta=$this->marketplace->itemPayload($request); $data=array_merge($data,$meta);
        $data['is_published'] = $request->boolean('is_published');
        $data['is_official'] = $request->boolean('is_official');
        $data['is_verified'] = $request->boolean('is_verified');

        MarketplaceItem::create($data);

        return redirect()->route('admin.marketplace.index')->with('success', 'Marketplace item created.');
    }

    public function edit(MarketplaceItem $item)
    {
        return view('admin.marketplace.item-form', [
            'item' => $item,
            'projects' => SoftwareProject::with('ecosystemProfile')->orderBy('name')->get(),
            'mode' => 'edit',
        ]);
    }

    public function update(MarketplaceItemRequest $request, MarketplaceItem $item)
    {
        $data = $request->validated();
        $project = $this->marketplace->projectForMarketplace((int)$data['software_project_id']);
        $this->marketplace->assertItemAllowed($project, $data['item_type']);
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $meta=$this->marketplace->itemPayload($request); $data=array_merge($data,$meta);
        $data['is_published'] = $request->boolean('is_published');
        $data['is_official'] = $request->boolean('is_official');
        $data['is_verified'] = $request->boolean('is_verified');

        $item->update($data);

        return redirect()->route('admin.marketplace.index')->with('success', 'Marketplace item updated.');
    }

    public function destroy(MarketplaceItem $item)
    {
        $item->load('releases');
        foreach ($item->releases as $release) {
            $this->storage->delete($release->file_path);
        }
        $item->delete();

        return back()->with('success', 'Marketplace item and all stored packages were deleted.');
    }

    public function releases(MarketplaceItem $item)
    {
        $releases = $item->releases()->latest('id')->paginate(15);

        return view('admin.marketplace.releases.index', compact('item', 'releases'));
    }

    public function createRelease(MarketplaceItem $item)
    {
        $item->load('project.ecosystemProfile'); $p=$item->project->ecosystemProfile;
        return view('admin.marketplace.releases.form', ['item'=>$item,'release'=>new MarketplaceRelease([
            'platform'=>$p?->platforms[0] ?? 'All','architecture'=>$p?->architectures[0] ?? 'All','channel'=>$p?->channels[0] ?? 'Stable','package_type'=>$p?->package_types[0] ?? 'zip',
        ]),'mode'=>'create']);
    }

    public function storeRelease(MarketplaceReleaseRequest $request, MarketplaceItem $item)
    {
        $data = $request->validated();
        $this->marketplace->assertReleaseAllowed($item, $data);
        $package = $this->packageFromRequest($request, $item, $data, true);

        $data = array_merge($data, $package);
        $data['marketplace_item_id'] = $item->id;
        $data['is_published'] = $request->boolean('is_published');
        $data['is_mandatory'] = $request->boolean('is_mandatory');
        $data['published_at'] = $data['is_published'] ? now() : null;
        $data['dependencies'] = $this->dependencyPayload($request);

        unset($data['package'], $data['upload_token']);

        MarketplaceRelease::create($data);

        return redirect()->route('admin.marketplace.releases.index', $item)->with('success', 'Marketplace release published to external storage.');
    }

    public function editRelease(MarketplaceRelease $release)
    {
        $release->load('item.project.ecosystemProfile');

        return view('admin.marketplace.releases.form', [
            'item' => $release->item,
            'profile' => $release->item->project->ecosystemProfile,
            'release' => $release,
            'mode' => 'edit',
        ]);
    }

    public function updateRelease(MarketplaceReleaseRequest $request, MarketplaceRelease $release)
    {
        $release->load('item');
        $data = $request->validated();
        $this->marketplace->assertReleaseAllowed($release->item, $data);
        $data['is_published'] = $request->boolean('is_published');
        $data['is_mandatory'] = $request->boolean('is_mandatory');
        $data['published_at'] = $data['is_published'] ? ($release->published_at ?: now()) : null;
        $data['dependencies'] = $this->dependencyPayload($request);

        $oldPath = $release->file_path;

        if ($request->hasFile('package') || $request->filled('upload_token')) {
            $data = array_merge($data, $this->packageFromRequest($request, $release->item, $data, true));
        }

        unset($data['package'], $data['upload_token']);
        $release->update($data);

        if ($oldPath && $oldPath !== $release->file_path) {
            $this->storage->delete($oldPath);
        }

        return redirect()->route('admin.marketplace.releases.index', $release->item)->with('success', 'Marketplace release updated.');
    }

    public function toggleRelease(MarketplaceRelease $release)
    {
        $publish = !$release->is_published;
        $release->update([
            'is_published' => $publish,
            'published_at' => $publish ? now() : null,
        ]);

        return back()->with('success', $publish ? 'Marketplace release published.' : 'Marketplace release unpublished.');
    }

    public function destroyRelease(MarketplaceRelease $release)
    {
        $item = $release->item;
        $this->storage->delete($release->file_path);
        $release->delete();

        return redirect()->route('admin.marketplace.releases.index', $item)
            ->with('success', 'Marketplace release and package deleted.');
    }

    private function dependencyPayload(Request $request): array
    {
        $raw=trim((string)$request->input('dependencies_text','')); $out=[];
        foreach(array_values(array_filter(array_map('trim',preg_split('/\r\n|\r|\n/',$raw)))) as $line){[$id,$min]=array_pad(explode('@',$line,2),2,null);$out[]=['itemId'=>trim($id),'minimumVersion'=>$min?trim($min):null];}
        return $out;
    }

    private function packageFromRequest(Request $request, MarketplaceItem $item, array $metadata, bool $required): array
    {
        try {
            if ($request->filled('upload_token')) {
                return $this->storage->consumeUploadTokenToMarketplace((string) $request->input('upload_token'), $item, $metadata);
            }

            if ($request->hasFile('package')) {
                return $this->storage->storeMarketplaceUploadedFile($request->file('package'), $item, $metadata);
            }
        } catch (Throwable $e) {
            throw ValidationException::withMessages(['package' => $e->getMessage()]);
        }

        if ($required) {
            throw ValidationException::withMessages(['package' => 'Please select a package.']);
        }

        return [];
    }
}
