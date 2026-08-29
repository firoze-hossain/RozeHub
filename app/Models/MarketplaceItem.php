<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketplaceItem extends Model
{
    protected $fillable = [
        'software_project_id',
        'owner_user_id',
        'item_type',
        'name',
        'slug',
        'item_id',
        'vendor',
        'category',
        'icon_path',
        'website',
        'repository_url', 'support_url', 'license',
        'summary',
        'description',
        'permissions', 'capabilities', 'compatibility',
        'is_official',
        'is_verified',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'permissions' => 'array',
            'capabilities' => 'array',
            'compatibility' => 'array',
            'is_official' => 'boolean',
            'is_verified' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(SoftwareProject::class, 'software_project_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(MarketplaceSubmission::class);
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
