<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;



class Release extends Model
{
    protected $fillable = ['software_project_id', 'version', 'major_version', 'codename', 'build_number', 'platform', 'architecture', 'channel', 'minimum_version', 'is_mandatory', 'file_path', 'file_name', 'file_size', 'sha256', 'notes', 'is_published', 'published_at', 'downloads_count'];

    protected static function booted(): void
    {
        static::saving(function (self $release): void {
            $release->release_identity_hash = hash('sha256', implode('|', [
                (string) $release->software_project_id,
                (string) $release->version,
                (string) $release->platform,
                (string) $release->architecture,
                (string) $release->channel,
            ]));
        });
    }

    protected function casts(): array
    {
        return ['is_published' => 'boolean', 'is_mandatory' => 'boolean', 'published_at' => 'datetime'];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(SoftwareProject::class, 'software_project_id');
    }

    public function artifacts(): HasMany
    {
        return $this->hasMany(ReleaseArtifact::class);
    }

    public function documentationPages(): HasMany
    {
        return $this->hasMany(DocumentationPage::class, 'release_id');
    }
}
