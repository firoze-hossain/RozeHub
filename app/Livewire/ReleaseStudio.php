<?php

namespace App\Livewire;

use App\Models\Release;
use App\Models\SoftwareProject;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class ReleaseStudio extends Component
{
    use WithFileUploads;

    public string $projectId = '';
    public string $version = '';
    public string $platform = 'Windows';
    public string $architecture = 'x64';
    public string $channel = 'Stable';
    public string $notes = '';
    public $package;

    public function save(): void
    {
        $this->validate([
            'projectId' => ['required', 'exists:software_projects,id'],
            'version' => ['required', 'string', 'max:32'],
            'platform' => ['required', 'in:Windows,macOS,Linux'],
            'architecture' => ['required', 'in:x64,ARM64'],
            'channel' => ['required', 'in:Stable,Beta,Nightly'],
            'notes' => ['nullable', 'string', 'max:3000'],
            'package' => ['required', 'file', 'max:1048576'],
        ]);

        $path = $this->package->store('releases/'.Str::slug(SoftwareProject::findOrFail($this->projectId)->name), 'public');

        Release::updateOrCreate([
            'software_project_id' => $this->projectId,
            'version' => $this->version,
            'platform' => $this->platform,
            'architecture' => $this->architecture,
        ], [
            'channel' => $this->channel,
            'file_path' => $path,
            'file_name' => $this->package->getClientOriginalName(),
            'file_size' => $this->package->getSize(),
            'notes' => $this->notes,
            'is_published' => true,
            'published_at' => now(),
        ]);

        $this->reset('version', 'notes', 'package');
        session()->flash('release-saved', 'Release published and ready to download.');
    }

    public function render()
    {
        return view('livewire.release-studio', [
            'projects' => SoftwareProject::orderBy('name')->get(),
            'releases' => Release::with('project')->latest('published_at')->take(12)->get(),
        ])->layout('components.layouts.app');
    }
}
