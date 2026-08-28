<?php

namespace App\Livewire;

use App\Models\Review;
use App\Models\SoftwareProject;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class Hub extends Component
{
    public string $search = '';
    public string $platform = 'All platforms';
    public ?int $selectedProjectId = null;
    public string $reviewName = '';
    public int $reviewRating = 5;
    public string $reviewBody = '';

    public function mount(): void
    {
        $this->selectedProjectId = SoftwareProject::query()->orderBy('name')->value('id');
    }

    public function selectProject(int $projectId): void
    {
        $this->selectedProjectId = $projectId;

        $project = SoftwareProject::find($projectId);
        if ($this->isNovaosProject($project)) {
            // NOVAOS is a first-class operating-system project, not one of the
            // six application/developer-tool entries. Keep the catalog filter
            // and the selected detail view in sync when it is chosen.
            $this->platform = 'NOVAOS';
        }

        $this->resetValidation();
    }

    public function setPlatform(string $platform): void
    {
        $allowed = ['All platforms', 'Windows', 'macOS', 'Linux', 'NOVAOS'];
        if (!in_array($platform, $allowed, true)) {
            return;
        }

        $this->platform = $platform;
        $this->resetValidation();

        if ($platform === 'NOVAOS') {
            // Selecting NOVAOS must also select NOVAOS itself. This prevents
            // the previous project's release panel from remaining visible.
            $this->selectedProjectId = SoftwareProject::query()
                ->where(function (Builder $query) {
                    $query->whereIn('slug', ['novaos', 'roze-os'])
                        ->orWhereIn('name', ['NOVAOS', 'Roze OS']);
                })
                ->value('id');
            return;
        }

        // Moving back to the application catalog should never leave NOVAOS
        // selected. Pick a matching application so its detail panel follows
        // the active platform filter.
        $query = SoftwareProject::query()
            ->whereNotIn('slug', ['novaos', 'roze-os'])
            ->whereNotIn('name', ['NOVAOS', 'Roze OS']);

        if ($platform !== 'All platforms') {
            $query->whereHas('releases', fn (Builder $release) => $release
                ->where('platform', $platform)
                ->where('is_published', true));
        }

        $this->selectedProjectId = $query->orderBy('name')->value('id');
    }


    public function isNovaosProject(?SoftwareProject $project): bool
    {
        return $project !== null && (in_array($project->slug, ['novaos', 'roze-os'], true) || strcasecmp($project->name, 'NOVAOS') === 0 || strcasecmp($project->name, 'Roze OS') === 0);
    }

    public function projectImageFor(string $slug): ?string
    {
        return [
            'dbnavigator' => 'dbnavigator.png',
            'thundercall' => 'thundercall.png',
            'stratosdb' => 'stratosdb.png',
            'lumina' => 'lumina.png',
            'roze-language' => 'roze.png',
            'novaos' => 'novaos.png',
            'roze-os' => 'novaos.png',
            'trackline' => 'trackeye.png',
        ][$slug] ?? null;
    }

    public function saveReview(): void
    {
        $this->validate([
            'reviewName' => ['required', 'string', 'max:80'],
            'reviewRating' => ['required', 'integer', 'between:1,5'],
            'reviewBody' => ['required', 'string', 'min:12', 'max:1000'],
        ]);

        Review::create([
            'software_project_id' => $this->selectedProjectId,
            'author_name' => $this->reviewName,
            'rating' => $this->reviewRating,
            'body' => $this->reviewBody,
            'is_approved' => true,
        ]);

        $this->reset('reviewName', 'reviewBody');
        $this->reviewRating = 5;
        session()->flash('review-sent', 'Review published. Thanks for sharing your experience.');
    }

    public function render()
    {
        $baseSearch = function (Builder $query): void {
            $query->when($this->search, fn (Builder $nestedQuery) => $nestedQuery->where(function (Builder $nested) {
                $nested->where('name', 'like', "%{$this->search}%")
                    ->orWhere('tagline', 'like', "%{$this->search}%")
                    ->orWhere('category', 'like', "%{$this->search}%");
            }));
        };

        // Applications/tools use the normal platform filters. NOVAOS is intentionally
        // excluded here because it is an independent operating system, not an app.
        $projects = SoftwareProject::query()
            ->where(function (Builder $query) {
                $query->whereNotIn('slug', ['novaos', 'roze-os'])
                    ->whereNotIn('name', ['NOVAOS', 'Roze OS']);
            })
            ->when($this->platform === 'NOVAOS', fn (Builder $query) => $query->whereRaw('1 = 0'))
            ->with(['releases' => fn ($query) => $query->where('is_published', true)->latest('published_at')])
            ->when($this->search, $baseSearch)
            ->when($this->platform !== 'All platforms', fn (Builder $query) => $query->whereHas('releases', fn (Builder $release) => $release
                ->where('platform', $this->platform)->where('is_published', true)))
            ->orderBy('name')
            ->get();

        // NOVAOS always has its own catalog entry and is never filtered into
        // Windows/macOS/Linux application results.
        $novaos = SoftwareProject::query()
            ->where(function (Builder $query) {
                $query->whereIn('slug', ['novaos', 'roze-os'])
                    ->orWhereIn('name', ['NOVAOS', 'Roze OS']);
            })
            ->with(['releases' => fn ($query) => $query->where('is_published', true)->latest('published_at')])
            ->when($this->search, $baseSearch)
            ->first();

        $selected = SoftwareProject::with(['releases' => fn ($query) => $query->where('is_published', true)->latest('published_at'), 'reviews' => fn ($query) => $query->where('is_approved', true)->latest()->take(4)])
            ->find($this->selectedProjectId);

        if (!$selected) {
            $selected = $projects->first() ?? $novaos;
        }

        return view('livewire.hub', [
            'projects' => $projects,
            'novaos' => $novaos,
            'selected' => $selected,
            'totalDownloads' => SoftwareProject::query()->withSum('releases', 'downloads_count')->get()->sum('releases_sum_downloads_count'),
            'releaseCount' => SoftwareProject::query()->withCount('releases')->get()->sum('releases_count'),
        ])->layout('components.layouts.app');
    }
}
