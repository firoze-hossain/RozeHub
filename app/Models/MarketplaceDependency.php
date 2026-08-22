<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketplaceDependency extends Model
{
    protected $fillable = [
        'marketplace_release_id',
        'dependency_item_id',
        'minimum_version',
        'optional',
    ];

    protected function casts(): array
    {
        return ['optional' => 'boolean'];
    }

    public function release(): BelongsTo
    {
        return $this->belongsTo(MarketplaceRelease::class, 'marketplace_release_id');
    }

    public function dependency(): BelongsTo
    {
        return $this->belongsTo(MarketplaceItem::class, 'dependency_item_id');
    }
}
