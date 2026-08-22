<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketplaceItem extends Model
{
    protected $fillable = [
        'software_project_id',
        'item_type',
        'name',
        'slug',
        'item_id',
        'vendor',
        'category',
        'icon_path',
        'website',
        'repository_url',
        'summary',
        'description',
        'permissions',
        'is_official',
        'is_verified',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'permissions' => 'array',
            'is_official' => 'boolean',
            'is_verified' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(SoftwareProject::class, 'software_project_id');
    }

    public function releases(): HasMany
    {
        return $this->hasMany(MarketplaceRelease::class);
    }

    public function publishedReleases(): HasMany
    {
        return $this->releases()->where('is_published', true);
    }
}
