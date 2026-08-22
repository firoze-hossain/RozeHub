<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketplaceRelease extends Model
{
    protected $fillable = [
        'marketplace_item_id',
        'version',
        'platform',
        'architecture',
        'channel',
        'minimum_app_version',
        'maximum_app_version',
        'package_type',
        'file_path',
        'file_name',
        'file_size',
        'sha256',
        'release_identity_hash',
        'release_notes',
        'dependencies',
        'is_mandatory',
        'is_published',
        'published_at',
        'downloads_count',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $release): void {
            $release->release_identity_hash = hash('sha256', implode('|', [
                (string) $release->marketplace_item_id,
                (string) $release->version,
                (string) $release->platform,
                (string) $release->architecture,
                (string) $release->channel,
            ]));
        });
    }

    protected function casts(): array
    {
        return [
            'dependencies' => 'array',
            'is_mandatory' => 'boolean',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(MarketplaceItem::class, 'marketplace_item_id');
    }

    public function dependencies(): HasMany
    {
        return $this->hasMany(MarketplaceDependency::class);
    }
}
