<?php

namespace App\Http\Controllers;

use App\Models\Release;
use App\Models\SoftwareProject;
use App\Services\ReleaseStorageService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class NovaosReleaseController extends Controller
{
    public function __construct(private readonly ReleaseStorageService $storage)
    {
    }

    private function project(): SoftwareProject
    {
        return SoftwareProject::where('slug', 'novaos')->firstOrFail();
    }

    public function index(Request $request)
    {
        $project = $this->project();
        $releases = $project->releases()
            ->when($request->filled('channel'), fn ($q) => $q->where('channel', $request->string('channel')))
            ->when($request->filled('architecture'), fn ($q) => $q->where('architecture', $request->string('architecture')))
            ->latest('created_at')->paginate(15)->withQueryString();

        return view('admin/novaos/releases/index', compact('project', 'releases'));
    }

    public function create()
    {
        $project = $this->project();
        $release = new Release(['platform' => 'NOVAOS', 'architecture' => 'x64', 'channel' => 'Stable']);
        return view('admin/novaos/releases/form', compact('project', 'release'));
    }

    public function store(Request $request)
    {
        $project = $this->project();
        $data = $this->validated($request);
        $data['software_project_id'] = $project->id;
        $data['platform'] = 'NOVAOS';
        $package = $this->packageFromRequest($request, $project, $data, true);
        $data = array_merge($data, $package);
        $data['is_published'] = $request->boolean('is_published');
        $data['published_at'] = $data['is_published'] ? now() : null;
        unset($data['package'], $data['upload_token']);

        Release::create($data);
        return redirect()->route('admin.novaos.releases.index')->with('success', 'NOVAOS release uploaded to external release storage and SHA-256 calculated.');
    }

    public function edit(Release $release)
    {
        abort_unless($release->project?->slug === 'novaos', 404);
        $project = $this->project();
        return view('admin/novaos/releases/form', compact('project', 'release'));
    }

    public function update(Request $request, Release $release)
    {
        abort_unless($release->project?->slug === 'novaos', 404);
        $data = $this->validated($request, $release, false);
        $data['software_project_id'] = $release->software_project_id;
        $data['platform'] = 'NOVAOS';
        $data['is_published'] = $request->boolean('is_published');
        $data['published_at'] = $data['is_published'] ? ($release->published_at ?: now()) : null;

        $oldPath = $release->file_path;
        $project = $this->project();
        if ($request->hasFile('package') || $request->filled('upload_token')) {
            $data = array_merge($data, $this->packageFromRequest($request, $project, $data, true));
        }
        unset($data['package'], $data['upload_token']);
        $release->update($data);

        if ($oldPath && $oldPath !== $release->file_path) {
            $this->storage->delete($oldPath);
        }

        return redirect()->route('admin.novaos.releases.index')->with('success', 'NOVAOS release updated.');
    }

    public function toggle(Release $release)
    {
        abort_unless($release->project?->slug === 'novaos', 404);
        $publish = !$release->is_published;
        $release->update(['is_published' => $publish, 'published_at' => $publish ? now() : null]);
        return back()->with('success', $publish ? 'NOVAOS release published.' : 'NOVAOS release unpublished.');
    }

    public function destroy(Release $release)
    {
        abort_unless($release->project?->slug === 'novaos', 404);
        $this->storage->delete($release->file_path);
        $release->delete();
        return back()->with('success', 'NOVAOS release and its external system image were deleted.');
    }

    private function packageFromRequest(Request $request, SoftwareProject $project, array $metadata, bool $required): array
    {
        try {
            if ($request->filled('upload_token')) {
                return $this->storage->consumeUploadToken((string) $request->input('upload_token'), $project, $metadata);
            }
            if ($request->hasFile('package')) {
                return $this->storage->storeUploadedFile($request->file('package'), $project, $metadata);
            }
        } catch (Throwable $e) {
            throw ValidationException::withMessages(['package' => $e->getMessage()]);
        }
        if ($required) {
            throw ValidationException::withMessages(['package' => 'Please select a NOVAOS system image.']);
        }
        return [];
    }

    private function validated(Request $request, ?Release $release = null, bool $requireFile = true): array
    {
        $rules = [
            'version' => ['required', 'string', 'max:80'],
            'major_version' => ['required', 'string', 'max:80'],
            'codename' => ['nullable', 'string', 'max:80'],
            'build_number' => ['required', 'string', 'max:80'],
            'architecture' => ['required', 'in:x64,ARM64'],
            'channel' => ['required', 'in:Stable,Beta,Nightly'],
            'minimum_version' => ['nullable', 'string', 'max:80'],
            'is_mandatory' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:20000'],
            'package' => ['nullable', 'file', 'extensions:iso,img,raw,zip,xz,gz', 'max:8388608'],
            'upload_token' => ['nullable', 'string', 'regex:/^[A-Za-z0-9_-]{20,100}$/'],
        ];
        if ($requireFile) {
            $rules['package'][] = 'required_without:upload_token';
            $rules['upload_token'][] = 'required_without:package';
        }

        $rules['version'][] = Rule::unique('releases', 'version')
            ->where(fn ($q) => $q
                ->where('software_project_id', $this->project()->id)
                ->where('platform', 'NOVAOS')
                ->where('architecture', $request->string('architecture'))
                ->where('channel', $request->string('channel')))
            ->ignore($release?->id);

        return $request->validate($rules);
    }
}
