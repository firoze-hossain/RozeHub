<?php

namespace App\Http\Controllers;

use App\Models\Release;
use App\Models\SoftwareProject;
use Illuminate\Http\Request;
use App\Services\ReleaseFileStorage;
use Illuminate\Validation\Rule;

class AdminReleaseController extends Controller
{
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

    public function store(Request $request, ReleaseFileStorage $releaseFiles)
    {
        $data = $this->validated($request);
        $file = $request->file('package');
        $project = SoftwareProject::findOrFail($data['software_project_id']);

        $stored = $releaseFiles->store($file, $project, $data['version']);
        $data = array_merge($data, $stored);
        $data['is_published'] = $request->boolean('is_published');
        $data['published_at'] = $data['is_published'] ? now() : null;

        Release::create($data);

        return redirect()->route('admin.releases.index')->with('success', 'Application release uploaded successfully.');
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

    public function update(Request $request, Release $release, ReleaseFileStorage $releaseFiles)
    {
        abort_if($release->project?->slug === 'novaos', 404);

        $data = $this->validated($request, $release, false);
        $data['is_published'] = $request->boolean('is_published');
        $data['published_at'] = $data['is_published'] ? ($release->published_at ?: now()) : null;

        $project = SoftwareProject::findOrFail($data['software_project_id']);

        if ($request->hasFile('package')) {
            $file = $request->file('package');
            $releaseFiles->delete($release->file_path);
            $stored = $releaseFiles->store($file, $project, $data['version']);
            $data = array_merge($data, $stored);
        } elseif ($release->file_path && (
            (int) $release->software_project_id !== (int) $project->id ||
            (string) $release->version !== (string) $data['version']
        )) {
            $relocated = $releaseFiles->relocate($release->file_path, $project, $data['version']);
            if ($relocated) {
                $data = array_merge($data, $relocated);
            }
        }

        $release->update($data);

        return redirect()->route('admin.releases.index')->with('success', 'Application release updated.');
    }

    public function toggle(Release $release)
    {
        abort_if($release->project?->slug === 'novaos', 404);
        $release->update(['is_published' => !$release->is_published, 'published_at' => !$release->is_published ? now() : null]);

        return back()->with('success', $release->is_published ? 'Release published.' : 'Release unpublished.');
    }

    public function destroy(Release $release, ReleaseFileStorage $releaseFiles)
    {
        abort_if($release->project?->slug === 'novaos', 404);

        $releaseFiles->delete($release->file_path);

        $release->delete();
        return back()->with('success', 'Release deleted.');
    }

    private function validated(Request $request, ?Release $release = null, bool $requireFile = true): array
    {
        $rules = [
            'software_project_id' => ['required', 'exists:software_projects,id'],
            'version' => ['required', 'string', 'max:32'],
            'platform' => ['required', Rule::in(['Windows', 'macOS', 'Linux'])],
            'architecture' => ['required', 'in:x64,ARM64'],
            'channel' => ['required', 'in:Stable,Beta,Nightly'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'package' => [($requireFile ? 'required' : 'nullable'), 'file', 'max:1048576'],
        ];

        $rules['version'][] = Rule::unique('releases', 'version')
            ->where(fn ($q) => $q->where('software_project_id', $request->integer('software_project_id'))->where('platform', $request->string('platform'))->where('architecture', $request->string('architecture')))
            ->ignore($release?->id);

        $data = $request->validate($rules);

        abort_if(SoftwareProject::find($data['software_project_id'])?->slug === 'novaos', 422, 'NOVAOS releases must be managed from the dedicated NOVAOS release center.');

        return $data;
    }
}
