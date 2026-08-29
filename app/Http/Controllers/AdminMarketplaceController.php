<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceDependency;
use App\Models\MarketplaceItem;
use App\Models\MarketplaceRelease;
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
    public function __construct(private readonly ReleaseStorageService $storage)
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
        return view('admin.marketplace.item-form', [
            'item' => new MarketplaceItem([
                'item_type' => 'plugin',
                'is_official' => true,
                'is_verified' => true,
            ]),
            'projects' => SoftwareProject::with('ecosystemProfile')->orderBy('name')->get(),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedItem($request);
        $project = SoftwareProject::with('ecosystemProfile')->findOrFail($data['software_project_id']);
        abort_unless(in_array($data['item_type'], $project->ecosystemProfile?->item_types ?? [], true), 422, 'Unsupported extension type for the selected project.');
        $data['slug'] = Str::slug($data['slug'] ?: $data['name']);
        $data['permissions'] = $this->permissions($request);
        $data['capabilities'] = $this->permissions($request->input('capabilities_text'));
        $data['compatibility'] = ['targets' => $this->permissions($request->input('compatibility_text')), 'minimumProjectVersion' => trim((string)$request->input('minimum_project_version')) ?: null];
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

    public function update(Request $request, MarketplaceItem $item)
    {
        $data = $this->validatedItem($request, $item);
        $project = SoftwareProject::with('ecosystemProfile')->findOrFail($data['software_project_id']);
        abort_unless(in_array($data['item_type'], $project->ecosystemProfile?->item_types ?? [], true), 422, 'Unsupported extension type for the selected project.');
        $data['slug'] = Str::slug($data['slug'] ?: $data['name']);
        $data['permissions'] = $this->permissions($request);
        $data['capabilities'] = $this->permissions($request->input('capabilities_text'));
        $data['compatibility'] = ['targets' => $this->permissions($request->input('compatibility_text')), 'minimumProjectVersion' => trim((string)$request->input('minimum_project_version')) ?: null];
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
        return view('admin.marketplace.releases.form', [
            'item' => $item,
            'release' => new MarketplaceRelease([
                'platform' => 'All',
                'architecture' => 'All',
                'channel' => 'Stable',
                'package_type' => 'zip',
            ]),
            'mode' => 'create',
        ]);
    }

    public function storeRelease(Request $request, MarketplaceItem $item)
    {
        $data = $this->validatedRelease($request);
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
        $release->load('item');

        return view('admin.marketplace.releases.form', [
            'item' => $release->item,
            'release' => $release,
            'mode' => 'edit',
        ]);
    }

    public function updateRelease(Request $request, MarketplaceRelease $release)
    {
        $release->load('item');
        $data = $this->validatedRelease($request, $release);
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

    private function validatedItem(Request $request, ?MarketplaceItem $item = null): array
    {
        return $request->validate([
            'software_project_id' => ['required', 'exists:software_projects,id'],
            'item_type' => ['required', 'string', 'max:30'],
            'name' => ['required', 'string', 'max:160'],
            'slug' => ['nullable', 'string', 'max:120'],
            'item_id' => ['required', 'string', 'max:160'],
            'vendor' => ['nullable', 'string', 'max:160'],
            'category' => ['nullable', 'string', 'max:100'],
            'icon_path' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'repository_url' => ['nullable', 'url', 'max:255'],
            'support_url' => ['nullable', 'url', 'max:255'],
            'license' => ['nullable', 'string', 'max:80'],
            'summary' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:30000'],
            'is_official' => ['nullable', 'boolean'],
            'is_verified' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
        ]);
    }

    private function permissions(Request $request): array
    {
        return array_values(array_filter(array_map(
            'trim',
            preg_split('/\r\n|\r|\n/', (string) $request->input('permissions_text', ''))
        )));
    }

    private function dependencyPayload(Request $request): array
    {
        $raw = trim((string) $request->input('dependencies_text', ''));
        if ($raw === '') {
            return [];
        }

        $items = [];
        foreach (preg_split('/\r\n|\r|\n/', $raw) as $line) {
            $line = trim($line);
            if ($line === '') continue;

            [$id, $min] = array_pad(explode('@', $line, 2), 2, null);
            $items[] = [
                'itemId' => trim($id),
                'minimumVersion' => $min ? trim($min) : null,
            ];
        }

        return $items;
    }

    private function validatedRelease(Request $request, ?MarketplaceRelease $release = null): array
    {
        $rules = [
            'version' => ['required', 'string', 'max:80'],
            'platform' => ['required', 'string', 'max:30'],
            'architecture' => ['required', 'string', 'max:20'],
            'channel' => ['required', 'in:Stable,Beta,Nightly'],
            'minimum_app_version' => ['nullable', 'string', 'max:80'],
            'maximum_app_version' => ['nullable', 'string', 'max:80'],
            'package_type' => ['required', 'string', 'max:30'],
            'release_notes' => ['nullable', 'string', 'max:30000'],
            'is_mandatory' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
            'package' => ['nullable', 'file', 'max:8388608'],
            'upload_token' => ['nullable', 'string', 'regex:/^[A-Za-z0-9_-]{20,100}$/'],
        ];

        if (!$release) {
            $rules['package'][] = 'required_without:upload_token';
            $rules['upload_token'][] = 'required_without:package';
        }

        return $request->validate($rules);
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
