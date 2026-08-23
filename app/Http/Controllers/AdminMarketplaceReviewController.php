<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceNotification;
use App\Models\MarketplaceSubmission;
use App\Models\MarketplaceSubmissionRisk;
use App\Services\MarketplaceModerationService;
use App\Services\MarketplaceRiskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminMarketplaceReviewController extends Controller
{
    public function __construct(private readonly MarketplaceModerationService $moderation, private readonly MarketplaceRiskService $risk) {}

    public function index(Request $request)
    {
        $submissions=MarketplaceSubmission::with(['item.project','release','submitter'])
            ->when($request->filled('status'),fn($q)=>$q->where('status',$request->string('status')))
            ->when($request->filled('risk'),fn($q)=>$q->where('risk_level',$request->string('risk')))
            ->when($request->filled('type'),fn($q)=>$q->whereHas('item',fn($i)=>$i->where('item_type',$request->string('type'))))
            ->when($request->filled('project'),fn($q)=>$q->whereHas('item.project',fn($p)=>$p->where('slug',$request->string('project'))))
            ->latest('id')->paginate(20)->withQueryString();
        $counts=MarketplaceSubmission::selectRaw('status,count(*) as total')->groupBy('status')->pluck('total','status');
        return view('admin.marketplace.review-index',compact('submissions','counts'));
    }

    public function show(MarketplaceSubmission $submission)
    {
        $submission->load(['item.project','release','submitter','reviewer','risks','logs.actor']);
        return view('admin.marketplace.review-show',compact('submission'));
    }

    public function start(MarketplaceSubmission $submission)
    {
        if($submission->status==='SUBMITTED')$this->moderation->transition($submission,MarketplaceSubmission::UNDER_REVIEW,auth()->user(),'Admin started security review.');
        $submission->load(['item','release']);
        $assessment=$this->risk->assess($submission);
        $submission->update(['risk_level'=>$assessment['level'],'risk_score'=>$assessment['score'],'risk_summary'=>$assessment['summary']]);
        return back()->with('success','Review started and automated risk assessment refreshed.');
    }

    public function updateRisk(Request $request,MarketplaceSubmission $submission)
    {
        $data=$request->validate(['risk_level'=>'required|in:LOW,MEDIUM,HIGH,CRITICAL','category'=>'required|string|max:50','status'=>'required|in:PASS,REVIEW,FAIL','score'=>'required|integer|min:0|max:100','notes'=>'nullable|string|max:5000']);
        MarketplaceSubmissionRisk::updateOrCreate(['submission_id'=>$submission->id,'category'=>$data['category']],['status'=>$data['status'],'score'=>$data['score'],'notes'=>$data['notes']??null,'summary'=>$data['notes']??null,'checked_by'=>auth()->id(),'checked_at'=>now()]);
        $score=min(100,(int)$submission->risks()->sum('score'));
        $level=$data['risk_level'];
        $submission->update(['risk_level'=>$level,'risk_score'=>$score,'risk_summary'=>$this->riskSummary($level)]);
        return back()->with('success','Risk assessment updated.');
    }

    public function decide(Request $request,MarketplaceSubmission $submission)
    {
        $data=$request->validate(['decision'=>'required|in:needs_changes,reject,approve','reason'=>'required|string|max:10000']);

        // Idempotent moderation: a second click / browser retry must never turn a
        // successful publication into a misleading validation error.
        if ($submission->status === MarketplaceSubmission::PUBLISHED) {
            return redirect()->route('admin.marketplace.review.show', $submission)
                ->with('success', 'This submission is already published. No additional moderation action was required.');
        }

        // If the admin chooses a decision directly from SUBMITTED, start the review
        // automatically and continue with the requested decision in the same request.
        if ($submission->status === MarketplaceSubmission::SUBMITTED) {
            $this->moderation->transition(
                $submission,
                MarketplaceSubmission::UNDER_REVIEW,
                auth()->user(),
                'Review started automatically before the administrator decision.'
            );
        }

        // APPROVED is normally followed immediately by PUBLISHED, but accepting it
        // here makes retries safe if a previous request stopped between those steps.
        if ($submission->status === MarketplaceSubmission::APPROVED && $data['decision'] === 'approve') {
            $submission->loadMissing(['item','release']);
            DB::transaction(function () use ($submission) {
                $submission->release?->update(['is_published'=>true,'published_at'=>now()]);
                $submission->item?->update(['is_published'=>true]);
                $this->moderation->transition($submission, MarketplaceSubmission::PUBLISHED, auth()->user(), 'Approved and published after moderation.');
            });
            return redirect()->route('admin.marketplace.review.show', $submission->refresh())
                ->with('success', 'Submission approved and published.');
        }

        if($submission->status!=='UNDER_REVIEW')return back()->withErrors(['decision'=>'This submission is not currently awaiting an administrator decision.']);

        $target=match($data['decision']){'needs_changes'=>MarketplaceSubmission::NEEDS_CHANGES,'reject'=>MarketplaceSubmission::REJECTED,'approve'=>MarketplaceSubmission::APPROVED};
        $labels = [
            MarketplaceSubmission::NEEDS_CHANGES => 'Changes requested',
            MarketplaceSubmission::REJECTED => 'Submission rejected',
            MarketplaceSubmission::APPROVED => 'Submission approved',
            MarketplaceSubmission::PUBLISHED => 'Submission approved and published',
        ];

        DB::transaction(function () use ($submission, $target, $data) {
            $submission->update([
                'decision_reason' => $data['reason'],
                'reviewer_notes' => $data['reason'],
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            $this->moderation->transition($submission, $target, auth()->user(), $data['reason']);

            if ($target === MarketplaceSubmission::APPROVED) {
                $submission->loadMissing(['item', 'release']);
                $submission->release?->update([
                    'is_published' => true,
                    'published_at' => now(),
                ]);
                $submission->item?->update(['is_published' => true]);
                $this->moderation->transition(
                    $submission,
                    MarketplaceSubmission::PUBLISHED,
                    auth()->user(),
                    'Approved and published after moderation.'
                );
            }
        });

        $submission->refresh()->load(['item','release','submitter']);
        $finalStatus = $submission->status;
        $label = $labels[$finalStatus] ?? 'Moderation decision recorded';
        $this->moderation->notify(
            $submission->submitter,
            $submission,
            'marketplace_' . strtolower($finalStatus),
            str_replace('_', ' ', ucfirst(strtolower($finalStatus))),
            $label . ": {$submission->item->name} v{$submission->release?->version}. " . $data['reason'],
            route('developer.marketplace.submission', $submission)
        );

        return redirect()->route('admin.marketplace.review.show', $submission)->with('success', $label . '.');
    }

    public function unpublish(MarketplaceSubmission $submission)
    {
        $submission->load(['item','release','submitter']);

        // Idempotent unpublish: if the release is already private, do not create
        // another moderation event or show a misleading failure.
        if ($submission->status !== MarketplaceSubmission::PUBLISHED || ! $submission->release?->is_published) {
            return redirect()->route('admin.marketplace.review.show', $submission)
                ->with('success', 'This release is already unpublished.');
        }

        DB::transaction(function () use ($submission) {
            $submission->release?->update([
                'is_published' => false,
                'published_at' => null,
            ]);

            // Keep the marketplace item public only when it still has another
            // published release. This supports multiple versions safely.
            $hasAnotherPublishedRelease = $submission->item
                ?->releases()
                ->where('is_published', true)
                ->exists();

            if (! $hasAnotherPublishedRelease) {
                $submission->item?->update(['is_published' => false]);
            }

            // PUBLISHED -> APPROVED means the package remains approved by the
            // moderator, but is no longer publicly downloadable/listed.
            $this->moderation->transition(
                $submission,
                MarketplaceSubmission::APPROVED,
                auth()->user(),
                'Release unpublished by administrator. The package remains approved but is no longer public.'
            );

            $submission->update(['published_at' => null]);
        });

        $submission->refresh()->load(['item','release','submitter']);

        if ($submission->submitter) {
            $this->moderation->notify(
                $submission->submitter,
                $submission,
                'marketplace_unpublished',
                'Marketplace release unpublished',
                "{$submission->item->name} v{$submission->release?->version} was unpublished by an administrator and is no longer publicly available.",
                route('developer.marketplace.submission', $submission)
            );
        }

        return redirect()->route('admin.marketplace.review.show', $submission)
            ->with('success', 'Release unpublished successfully. It is no longer public.');
    }

    public function markRead(MarketplaceNotification $notification){abort_unless($notification->user_id===auth()->id(),403);$notification->update(['read_at'=>now()]);return back();}
    private function riskSummary(string $level):string{return match($level){'LOW'=>'No significant elevated risk recorded.','MEDIUM'=>'Some capabilities require manual review.','HIGH'=>'Elevated-risk characteristics require careful inspection.','CRITICAL'=>'Critical safety concerns are present.'};}
}
