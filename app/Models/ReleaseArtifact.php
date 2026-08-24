<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReleaseArtifact extends Model
{
    protected $fillable = [
        'release_id', 'purpose', 'package_type', 'file_path', 'file_name',
        'file_size', 'sha256', 'is_primary', 'downloads_count',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'file_size' => 'integer',
            'downloads_count' => 'integer',
        ];
    }

    public function release(): BelongsTo
    {
        return $this->belongsTo(Release::class);
    }

    public function isInstaller(): bool
    {
        return strtoupper((string) $this->purpose) === 'INSTALLER';
    }

    public function isUpdater(): bool
    {
        return strtoupper((string) $this->purpose) === 'UPDATER';
    }
}
