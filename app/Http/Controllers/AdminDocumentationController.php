<?php

namespace App\Http\Controllers;

use App\Models\DocumentationPage;
use App\Models\DocumentationSection;
use App\Models\SoftwareProject;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminDocumentationController extends Controller
{
    public function index()
    {
        $projects = SoftwareProject::query()
            ->withCount('documentationSections')
            ->withCount(['documentationPages', 'documentationPages as published_documentation_pages_count' => fn ($q) => $q->where('is_published', true)])
            ->orderBy('name')->get();
        return view('admin.documentation.index', compact('projects'));
    }

    public function project(SoftwareProject $project)
    {
        $project->load([
            'releases' => fn ($q) => $q->orderByDesc('published_at')->orderByDesc('id'),
            'documentationSections' => fn ($q) => $q->orderBy('sort_order')->withCount('pages')->with(['pages' => fn ($p) => $p->with('release')->orderBy('sort_order')]),
        ]);
        return view('admin.documentation.project', compact('project'));
    }

    public function storeSection(Request $request, SoftwareProject $project)
    {
        $data = $request->validate([
            'title' => ['required','string','max:160'],
            'description' => ['nullable','string','max:500'],
            'icon' => ['nullable','string','max:20'],
            'sort_order' => ['nullable','integer','min:0','max:9999'],
        ]);
        $data['software_project_id'] = $project->id;
        $data['slug'] = Str::slug($data['title']);
        if (DocumentationSection::where('software_project_id', $project->id)->where('slug', $data['slug'])->exists()) {
            return back()->withErrors(['title' => 'A documentation section with this title already exists for this project.'])->withInput();
        }
        $data['icon'] = $data['icon'] ?: '◈';
        $data['sort_order'] = $data['sort_order'] ?? 0;
        DocumentationSection::create($data);
        return back()->with('success', 'Documentation section created.');
    }

    public function updateSection(Request $request, DocumentationSection $section)
    {
        $data = $request->validate([
            'title' => ['required','string','max:160'],
            'description' => ['nullable','string','max:500'],
            'icon' => ['nullable','string','max:20'],
            'sort_order' => ['nullable','integer','min:0','max:9999'],
        ]);
        $data['slug'] = Str::slug($data['title']);
        if (DocumentationSection::where('software_project_id', $section->software_project_id)->where('slug', $data['slug'])->where('id', '!=', $section->id)->exists()) {
            return back()->withErrors(['title' => 'A documentation section with this title already exists for this project.'])->withInput();
        }
        $data['icon'] = $data['icon'] ?: '◈';
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $section->update($data);
        return back()->with('success', 'Documentation section updated.');
    }

    public function destroySection(DocumentationSection $section)
    {
        $section->delete();
        return back()->with('success', 'Documentation section deleted.');
    }

    public function createPage(SoftwareProject $project)
    {
        $sections = $project->documentationSections()->orderBy('sort_order')->get();
        $releases = $project->releases()->orderByDesc('published_at')->orderByDesc('id')->get();
        $page = new DocumentationPage(['kind'=>'guide','is_published'=>true]);
        return view('admin.documentation.page-form', compact('project','sections','releases','page'))->with('mode','create');
    }

    public function storePage(Request $request, SoftwareProject $project)
    {
        $data = $this->validatedPage($request, $project);
        $data['software_project_id'] = $project->id;
        $data['slug'] = Str::slug($data['slug'] ?: $data['title']);
        $data['version'] = $this->versionLabel($data['release_id'] ?? null, $project);
        $data['is_published'] = $request->boolean('is_published');
        DocumentationPage::create($data);
        return redirect()->route('admin.documentation.project', $project)->with('success', 'Documentation page created.');
    }

    public function editPage(DocumentationPage $page)
    {
        $project = $page->project;
        $sections = $project->documentationSections()->orderBy('sort_order')->get();
        $releases = $project->releases()->orderByDesc('published_at')->orderByDesc('id')->get();
        return view('admin.documentation.page-form', compact('project','sections','releases','page'))->with('mode','edit');
    }

    public function updatePage(Request $request, DocumentationPage $page)
    {
        $project = $page->project;
        $data = $this->validatedPage($request, $project, $page);
        $data['slug'] = Str::slug($data['slug'] ?: $data['title']);
        $data['version'] = $this->versionLabel($data['release_id'] ?? null, $project);
        $data['is_published'] = $request->boolean('is_published');
        $page->update($data);
        return redirect()->route('admin.documentation.project', $project)->with('success', 'Documentation page updated.');
    }

    public function togglePage(DocumentationPage $page)
    {
        $page->update(['is_published' => !$page->is_published]);
        return back()->with('success', $page->is_published ? 'Documentation page published.' : 'Documentation page unpublished.');
    }

    public function destroyPage(DocumentationPage $page)
    {
        $project = $page->project;
        $page->delete();
        return redirect()->route('admin.documentation.project', $project)->with('success', 'Documentation page deleted.');
    }

    private function versionLabel($releaseId, SoftwareProject $project): string
    {
        if (!$releaseId) {
            return 'All versions';
        }

        return (string) optional($project->releases()->find($releaseId))->version ?: 'All versions';
    }

    private function validatedPage(Request $request, SoftwareProject $project, ?DocumentationPage $page = null): array
    {
        return $request->validate([
            'release_id' => ['nullable', Rule::exists('releases','id')->where(fn($q) => $q->where('software_project_id', $project->id))],
            'documentation_section_id' => ['nullable', Rule::exists('documentation_sections','id')->where(fn($q) => $q->where('software_project_id', $project->id))],
            'title' => ['required','string','max:180'],
            'slug' => ['nullable','string','max:200', Rule::unique('documentation_pages','slug')->where(function ($q) use ($project, $request) {
                $q->where('software_project_id', $project->id);
                $releaseId = $request->input('release_id');
                $releaseId === null || $releaseId === '' ? $q->whereNull('release_id') : $q->where('release_id', $releaseId);
            })->ignore($page?->id)],
            'kind' => ['required','in:overview,guide,installation,reference,architecture,api,tutorial,operations,development,troubleshooting,release,release-notes'],
            'version' => ['nullable','string','max:50'],
            'summary' => ['nullable','string','max:500'],
            'content' => ['required','string'],
            'sort_order' => ['nullable','integer','min:0','max:9999'],
        ]);
    }
}
