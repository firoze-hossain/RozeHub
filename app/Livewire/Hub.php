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
        $this->resetValidation();
    }


    public function projectImageFor(string $slug): ?string
    {
        return [
            'dbnavigator' => 'dbnavigator.png',
            'thundercall' => 'thundercall.png',
            'stratosdb' => 'stratosdb.png',
            'lumina' => 'lumina.png',
            'roze-language' => 'roze.png',
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
        $projects = SoftwareProject::query()
            ->with(['releases' => fn ($query) => $query->where('is_published', true)->latest('published_at')])
            ->when($this->search, fn (Builder $query) => $query->where(fn (Builder $nested) => $nested
                ->where('name', 'like', "%{$this->search}%")
                ->orWhere('tagline', 'like', "%{$this->search}%")
                ->orWhere('category', 'like', "%{$this->search}%")))
            ->when($this->platform !== 'All platforms', fn (Builder $query) => $query->whereHas('releases', fn (Builder $release) => $release
                ->where('platform', $this->platform)->where('is_published', true)))
            ->orderBy('name')
            ->get();

        $selected = SoftwareProject::with(['releases' => fn ($query) => $query->where('is_published', true)->latest('published_at'), 'reviews' => fn ($query) => $query->where('is_approved', true)->latest()->take(4)])
            ->find($this->selectedProjectId) ?? $projects->first();

        return view('livewire.hub', [
            'projects' => $projects,
            'selected' => $selected,
            'totalDownloads' => SoftwareProject::query()->withSum('releases', 'downloads_count')->get()->sum('releases_sum_downloads_count'),
            'releaseCount' => SoftwareProject::query()->withCount('releases')->get()->sum('releases_count'),
        ])->layout('components.layouts.app');
    }
}
