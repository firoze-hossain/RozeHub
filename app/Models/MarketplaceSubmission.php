<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketplaceSubmission extends Model
{
    public const DRAFT = 'DRAFT';
    public const SUBMITTED = 'SUBMITTED';
    public const UNDER_REVIEW = 'UNDER_REVIEW';
    public const NEEDS_CHANGES = 'NEEDS_CHANGES';
    public const REJECTED = 'REJECTED';
    public const APPROVED = 'APPROVED';
    public const PUBLISHED = 'PUBLISHED';

    protected $fillable = [
        'marketplace_item_id','marketplace_release_id','submitted_by','reviewed_by','status',
        'risk_level','risk_score','risk_summary','developer_message','reviewer_notes',
        'decision_reason','resubmission_count','submitted_at','review_started_at','reviewed_at','published_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime', 'review_started_at' => 'datetime',
            'reviewed_at' => 'datetime', 'published_at' => 'datetime',
        ];
    }

    public function item(): BelongsTo { return $this->belongsTo(MarketplaceItem::class, 'marketplace_item_id'); }
    public function release(): BelongsTo { return $this->belongsTo(MarketplaceRelease::class, 'marketplace_release_id'); }
    public function submitter(): BelongsTo { return $this->belongsTo(User::class, 'submitted_by'); }
    public function reviewer(): BelongsTo { return $this->belongsTo(User::class, 'reviewed_by'); }
    public function risks(): HasMany { return $this->hasMany(MarketplaceSubmissionRisk::class, 'submission_id'); }
    public function logs(): HasMany { return $this->hasMany(MarketplaceSubmissionLog::class, 'submission_id'); }
}
