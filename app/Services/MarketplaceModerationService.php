<?php

namespace App\Services;

use App\Models\MarketplaceNotification;
use App\Models\MarketplaceSubmission;
use App\Models\MarketplaceSubmissionLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class MarketplaceModerationService
{
    public function transition(MarketplaceSubmission $submission, string $to, ?User $actor, ?string $comment = null, array $metadata = []): void
    {
        $allowed = [
            'DRAFT' => ['SUBMITTED'],
            'SUBMITTED' => ['UNDER_REVIEW','DRAFT'],
            'UNDER_REVIEW' => ['NEEDS_CHANGES','REJECTED','APPROVED'],
            'NEEDS_CHANGES' => ['SUBMITTED','DRAFT'],
            'APPROVED' => ['PUBLISHED'],
            'REJECTED' => ['DRAFT'],
            'PUBLISHED' => ['APPROVED'],
        ];
        $from = $submission->status;
        if (!in_array($to, $allowed[$from] ?? [], true)) throw new RuntimeException("Invalid marketplace submission transition: {$from} → {$to}");
        $submission->update([
            'status'=>$to,
            'submitted_at'=>$to==='SUBMITTED' ? now() : $submission->submitted_at,
            'review_started_at'=>$to==='UNDER_REVIEW' ? now() : $submission->review_started_at,
            'reviewed_at'=>in_array($to,['NEEDS_CHANGES','REJECTED','APPROVED'],true) ? now() : $submission->reviewed_at,
            'published_at'=>$to==='PUBLISHED' ? now() : $submission->published_at,
            'reviewed_by'=>in_array($to,['NEEDS_CHANGES','REJECTED','APPROVED','PUBLISHED'],true) ? $actor?->id : $submission->reviewed_by,
        ]);
        MarketplaceSubmissionLog::create([
            'submission_id'=>$submission->id,'actor_id'=>$actor?->id,'action'=>strtolower($to),
            'from_status'=>$from,'to_status'=>$to,'comment'=>$comment,'metadata'=>$metadata ?: null,
        ]);
    }

    public function notify(User $user, MarketplaceSubmission $submission, string $type, string $title, string $message, ?string $url=null): void
    {
        MarketplaceNotification::create([
            'user_id'=>$user->id,'submission_id'=>$submission->id,'type'=>$type,
            'title'=>$title,'message'=>$message,'action_url'=>$url,
        ]);
    }
}
