<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Release extends Model
{
    protected $fillable = ['software_project_id', 'version', 'major_version', 'codename', 'build_number', 'platform', 'architecture', 'channel', 'file_path', 'file_name', 'file_size', 'sha256', 'notes', 'is_published', 'published_at', 'downloads_count'];

    protected function casts(): array
    {
        return ['is_published' => 'boolean', 'published_at' => 'datetime'];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(SoftwareProject::class, 'software_project_id');
    }

    public function documentationPages(): HasMany
    {
        return $this->hasMany(DocumentationPage::class, 'release_id');
    }
}
