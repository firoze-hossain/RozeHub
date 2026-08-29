<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectEcosystemProfile extends Model
{
    protected $fillable = [
        'software_project_id', 'ecosystem_type', 'title', 'description',
        'item_types', 'capabilities', 'package_types', 'platforms',
        'architectures', 'integration_targets', 'marketplace_enabled',
        'community_contributions', 'moderation_required',
    ];

    protected function casts(): array
    {
        return [
            'item_types' => 'array',
            'capabilities' => 'array',
            'package_types' => 'array',
            'platforms' => 'array',
            'architectures' => 'array',
            'integration_targets' => 'array',
            'marketplace_enabled' => 'boolean',
            'community_contributions' => 'boolean',
            'moderation_required' => 'boolean',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(SoftwareProject::class, 'software_project_id');
    }
}
