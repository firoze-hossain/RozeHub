<?php

namespace App\Http\Controllers;

use App\Models\Release;
use App\Models\SoftwareProject;
use App\Services\ReleaseStorageService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class AdminReleaseController extends Controller
{
    public function __construct(private readonly ReleaseStorageService $storage)
    {
    }

    private function applicationProjects()
    {
        return SoftwareProject::query()->where('slug', '!=', 'novaos')->orderBy('name')->get();
    }

    public function index(Request $request)
    {
        $releases = Release::with('project')
            ->whereHas('project', fn ($q) => $q->where('slug', '!=', 'novaos'))
            ->when($request->filled('project'), fn ($q) => $q->where('software_project_id', $request->integer('project')))
            ->when($request->filled('platform'), fn ($q) => $q->where('platform', $request->string('platform')))
            ->when($request->filled('channel'), fn ($q) => $q->where('channel', $request->string('channel')))
            ->latest('created_at')->paginate(15)->withQueryString();

        return view('admin.releases.index', [
            'releases' => $releases,
            'projects' => $this->applicationProjects(),
        ]);
    }

    public function create()
    {
        return view('admin.releases.form', [
            'release' => new Release(['platform' => 'Windows', 'architecture' => 'x64', 'channel' => 'Stable']),
            'projects' => $this->applicationProjects(),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $project = SoftwareProject::findOrFail($data['software_project_id']);
        $package = $this->packageFromRequest($request, $project, $data, true);

        $data = array_merge($data, $package);
        $data['is_published'] = $request->boolean('is_published');
        $data['published_at'] = $data['is_published'] ? now() : null;
        unset($data['package'], $data['upload_token']);

        Release::create($data);

        return redirect()->route('admin.releases.index')->with('success', 'Application release uploaded and stored outside the Laravel project.');
    }

    public function edit(Release $release)
    {
        abort_if($release->project?->slug === 'novaos', 404);

        return view('admin.releases.form', [
            'release' => $release,
            'projects' => $this->applicationProjects(),
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, Release $release)
    {
        abort_if($release->project?->slug === 'novaos', 404);

        $data = $this->validated($request, $release, false);
        $data['is_published'] = $request->boolean('is_published');
        $data['published_at'] = $data['is_published'] ? ($release->published_at ?: now()) : null;

        $oldPath = $release->file_path;
        $project = SoftwareProject::findOrFail($data['software_project_id']);

        if ($request->hasFile('package') || $request->filled('upload_token')) {
            $data = array_merge($data, $this->packageFromRequest($request, $project, $data, true));
        }

        unset($data['package'], $data['upload_token']);
        $release->update($data);

        if ($oldPath && $oldPath !== $release->file_path) {
            $this->storage->delete($oldPath);
        }

        return redirect()->route('admin.releases.index')->with('success', 'Application release updated.');
    }

    public function toggle(Release $release)
    {
        abort_if($release->project?->slug === 'novaos', 404);
        $publish = !$release->is_published;
        $release->update(['is_published' => $publish, 'published_at' => $publish ? now() : null]);

        return back()->with('success', $publish ? 'Release published.' : 'Release unpublished.');
    }

    public function destroy(Release $release)
    {
        abort_if($release->project?->slug === 'novaos', 404);

        $this->storage->delete($release->file_path);
        $release->delete();

        return back()->with('success', 'Release and its external package were deleted.');
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
            throw ValidationException::withMessages(['package' => 'Please select a release package.']);
        }

        return [];
    }

    private function validated(Request $request, ?Release $release = null, bool $requireFile = true): array
    {
        $rules = [
            'software_project_id' => ['required', 'exists:software_projects,id'],
            'version' => ['required', 'string', 'max:80'],
            'platform' => ['required', Rule::in(['Windows', 'macOS', 'Linux'])],
            'architecture' => ['required', 'in:x64,ARM64'],
            'channel' => ['required', 'in:Stable,Beta,Nightly'],
            'minimum_version' => ['nullable', 'string', 'max:80'],
            'is_mandatory' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:10000'],
            'package' => ['nullable', 'file', 'max:8388608'],
            'upload_token' => ['nullable', 'string', 'regex:/^[A-Za-z0-9_-]{20,100}$/'],
        ];

        if ($requireFile) {
            $rules['package'][] = 'required_without:upload_token';
            $rules['upload_token'][] = 'required_without:package';
        }

        $rules['version'][] = Rule::unique('releases', 'version')
            ->where(fn ($q) => $q
                ->where('software_project_id', $request->integer('software_project_id'))
                ->where('platform', $request->string('platform'))
                ->where('architecture', $request->string('architecture'))
                ->where('channel', $request->string('channel')))
            ->ignore($release?->id);

        $data = $request->validate($rules);

        abort_if(
            SoftwareProject::find($data['software_project_id'])?->slug === 'novaos',
            422,
            'NOVAOS releases must be managed from the dedicated NOVAOS release center.'
        );

        return $data;
    }
}
