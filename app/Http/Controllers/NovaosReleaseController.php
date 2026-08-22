<?php

namespace App\Http\Controllers;

use App\Models\Release;
use App\Models\SoftwareProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class NovaosReleaseController extends Controller
{
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
        $release = new Release([
            'platform' => 'NOVAOS',
            'architecture' => 'x64',
            'channel' => 'Stable',
        ]);

        return view('admin/novaos/releases/form', compact('project', 'release'));
    }

    public function store(Request $request)
    {
        $project = $this->project();
        $data = $this->validated($request);
        $file = $request->file('package');

        $data['software_project_id'] = $project->id;
        $data['platform'] = 'NOVAOS';
        $data['file_path'] = $file->store('releases/novaos', 'local');
        $data['file_name'] = $file->getClientOriginalName();
        $data['file_size'] = $file->getSize();
        $data['sha256'] = hash_file('sha256', $file->getRealPath());
        $data['is_published'] = $request->boolean('is_published');
        $data['published_at'] = $data['is_published'] ? now() : null;

        Release::create($data);

        return redirect()->route('admin.novaos.releases.index')->with('success', 'NOVAOS release uploaded and SHA-256 calculated successfully.');
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
        $data['platform'] = 'NOVAOS';
        $data['is_published'] = $request->boolean('is_published');
        $data['published_at'] = $data['is_published'] ? ($release->published_at ?: now()) : null;

        if ($request->hasFile('package')) {
            $file = $request->file('package');
            if ($release->file_path && Storage::disk('local')->exists($release->file_path)) {
                Storage::disk('local')->delete($release->file_path);
            }
            $data['file_path'] = $file->store('releases/novaos', 'local');
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_size'] = $file->getSize();
            $data['sha256'] = hash_file('sha256', $file->getRealPath());
        }

        $release->update($data);

        return redirect()->route('admin.novaos.releases.index')->with('success', 'NOVAOS release updated.');
    }

    public function toggle(Release $release)
    {
        abort_unless($release->project?->slug === 'novaos', 404);
        $release->update(['is_published' => !$release->is_published, 'published_at' => !$release->is_published ? now() : null]);
        return back()->with('success', $release->is_published ? 'NOVAOS release published.' : 'NOVAOS release unpublished.');
    }

    public function destroy(Release $release)
    {
        abort_unless($release->project?->slug === 'novaos', 404);
        if ($release->file_path && Storage::disk('local')->exists($release->file_path)) {
            Storage::disk('local')->delete($release->file_path);
        }
        $release->delete();
        return back()->with('success', 'NOVAOS release deleted.');
    }

    private function validated(Request $request, ?Release $release = null, bool $requireFile = true): array
    {
        $rules = [
            'version' => ['required', 'string', 'max:32'],
            'major_version' => ['required', 'string', 'max:32'],
            'codename' => ['nullable', 'string', 'max:80'],
            'build_number' => ['required', 'string', 'max:80'],
            'architecture' => ['required', 'in:x64,ARM64'],
            'channel' => ['required', 'in:Stable,Beta,Nightly'],
            'notes' => ['nullable', 'string', 'max:10000'],
            'package' => [($requireFile ? 'required' : 'nullable'), 'file', 'extensions:iso,img,raw,zip,xz,gz', 'max:4194304'],
        ];

        $rules['version'][] = Rule::unique('releases', 'version')
            ->where(fn ($q) => $q->where('software_project_id', $this->project()->id)->where('platform', 'NOVAOS')->where('architecture', $request->string('architecture')))
            ->ignore($release?->id);

        return $request->validate($rules);
    }
}
