<?php

namespace App\Http\Controllers;

use App\Models\DocumentationPage;
use App\Models\SoftwareProject;
use App\Support\DocumentationRenderer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DocumentationController extends Controller
{
    public function index(Request $request)
    {
        $projects = SoftwareProject::query()
            ->withCount(['documentationPages as published_docs_count' => fn ($q) => $q->where('is_published', true)])
            ->with(['releases' => fn ($q) => $q->where('is_published', true)->latest('published_at')])
            ->with(['documentationSections' => fn ($q) => $q->where('is_published', true)->orderBy('sort_order')->withCount(['pages as published_pages_count' => fn ($p) => $p->where('is_published', true)])])
            ->orderBy('name')
            ->get();

        return view('docs.index', compact('projects'));
    }

    public function project(Request $request, SoftwareProject $project)
    {
        $project->load([
            'releases' => fn ($q) => $q->where('is_published', true)->latest('published_at')->latest('id'),
        ]);

        $selectedRelease = $this->selectedRelease($request, $project);
        $releaseId = $selectedRelease?->id;

        $project->load([
            'documentationSections' => fn ($q) => $q
                ->where('is_published', true)
                ->orderBy('sort_order')
                ->with(['pages' => fn ($p) => $this->visiblePageQuery($p, $releaseId)]),
        ]);

        $firstPage = $project->documentationSections->flatMap->pages->first();

        return view('docs.project', compact('project', 'firstPage', 'selectedRelease'));
    }

    public function page(Request $request, SoftwareProject $project, string $pageSlug)
    {
        $project->load(['releases' => fn ($q) => $q->where('is_published', true)->latest('published_at')->latest('id')]);
        $selectedRelease = $this->selectedRelease($request, $project);
        $releaseId = $selectedRelease?->id;

        $project->load([
            'documentationSections' => fn ($q) => $q
                ->where('is_published', true)
                ->orderBy('sort_order')
                ->with(['pages' => fn ($p) => $this->visiblePageQuery($p, $releaseId)]),
        ]);

        $page = DocumentationPage::query()
            ->with('release')
            ->where('software_project_id', $project->id)
            ->where('slug', $pageSlug)
            ->where('is_published', true)
            ->where(function ($q) use ($releaseId) {
                if ($releaseId) {
                    $q->where('release_id', $releaseId)->orWhereNull('release_id');
                } else {
                    $q->whereNull('release_id');
                }
            })
            ->orderByRaw('CASE WHEN release_id IS NULL THEN 1 ELSE 0 END')
            ->orderByDesc('updated_at')
            ->firstOrFail();

        $html = DocumentationRenderer::html($page->content);

        return view('docs.page', compact('project', 'page', 'html', 'selectedRelease'));
    }

    public function search(Request $request)
    {
        $term = trim((string) $request->string('q'));
        $results = collect();
        if ($term !== '') {
            $results = DocumentationPage::query()
                ->with(['project', 'release'])
                ->where('is_published', true)
                ->where(function ($q) use ($term) {
                    $q->where('title', 'like', "%{$term}%")
                        ->orWhere('summary', 'like', "%{$term}%")
                        ->orWhere('content', 'like', "%{$term}%");
                })
                ->orderBy('title')->paginate(20)->withQueryString();
        }
        return view('docs.search', compact('term', 'results'));
    }

    public function print(Request $request, SoftwareProject $project)
    {
        $project->load(['releases' => fn ($q) => $q->where('is_published', true)->latest('published_at')->latest('id')]);
        $selectedRelease = $this->selectedRelease($request, $project);
        $releaseId = $selectedRelease?->id;
        $project->load([
            'documentationSections' => fn ($q) => $q->where('is_published', true)->orderBy('sort_order')->with(['pages' => fn ($p) => $this->visiblePageQuery($p, $releaseId)]),
        ]);
        foreach ($project->documentationSections as $section) {
            foreach ($section->pages as $page) {
                $page->rendered_html = DocumentationRenderer::html($page->content);
            }
        }
        return view('docs.print', compact('project', 'selectedRelease'));
    }

    public function markdown(Request $request, SoftwareProject $project)
    {
        $project->load(['releases' => fn ($q) => $q->where('is_published', true)->latest('published_at')->latest('id')]);
        $selectedRelease = $this->selectedRelease($request, $project);
        $releaseId = $selectedRelease?->id;
        $project->load(['documentationSections' => fn ($q) => $q->where('is_published', true)->orderBy('sort_order')->with(['pages' => fn ($p) => $this->visiblePageQuery($p, $releaseId)])]);
        $titleVersion = $selectedRelease ? ' · '.$selectedRelease->version : '';
        $lines = ['# '.$project->name.' Documentation'.$titleVersion, '', $project->description, ''];
        foreach ($project->documentationSections as $section) {
            $lines[] = '## '.$section->title; $lines[] = ''; if ($section->description) { $lines[] = $section->description; $lines[] = ''; }
            foreach ($section->pages as $page) {
                $lines[] = '### '.$page->title; $lines[] = ''; if ($page->summary) { $lines[] = $page->summary; $lines[] = ''; } $lines[] = $page->content; $lines[] = ''; }
        }
        $suffix = $selectedRelease ? '-'.Str::slug($selectedRelease->version) : '';
        return response(implode("\n", $lines), 200, [
            'Content-Type' => 'text/markdown; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.Str::slug($project->name).'-documentation'.$suffix.'.md"',
        ]);
    }

    private function selectedRelease(Request $request, SoftwareProject $project)
    {
        $requested = $request->integer('release');
        if ($requested) {
            $release = $project->releases->firstWhere('id', $requested);
            if ($release) {
                return $release;
            }
        }

        return $project->releases->first();
    }

    private function visiblePageQuery($query, ?int $releaseId)
    {
        return $query
            ->where('is_published', true)
            ->where(function ($q) use ($releaseId) {
                if ($releaseId) {
                    $q->where('release_id', $releaseId)->orWhereNull('release_id');
                } else {
                    $q->whereNull('release_id');
                }
            })
            ->orderBy('sort_order')
            ->orderBy('title');
    }
}
