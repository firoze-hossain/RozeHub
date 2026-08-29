<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceItem;
use App\Models\MarketplaceRelease;
use App\Models\MarketplaceSubmission;
use App\Models\MarketplaceNotification;
use App\Models\SoftwareProject;
use App\Http\Requests\MarketplaceItemRequest;
use App\Http\Requests\MarketplaceReleaseRequest;
use App\Services\MarketplaceService;
use App\Services\MarketplaceModerationService;
use App\Services\MarketplaceRiskService;
use App\Services\ReleaseStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class DeveloperMarketplaceController extends Controller
{
    public function __construct(
        private readonly ReleaseStorageService $storage,
        private readonly MarketplaceRiskService $risk,
        private readonly MarketplaceModerationService $moderation,
        private readonly MarketplaceService $marketplace
    ) {}

    private function projects()
    {
        return $this->marketplace->projects(true);
    }

    public function dashboard()
    {
        $items = MarketplaceItem::where('owner_user_id', auth()->id())->with(['project.ecosystemProfile', 'releases'])->latest()->get();
        $submissions = MarketplaceSubmission::where('submitted_by', auth()->id())->with(['item.project', 'release'])->latest()->paginate(12);
        $unread = MarketplaceNotification::where('user_id', auth()->id())->whereNull('read_at')->count();

        return view('developer.dashboard', compact('items', 'submissions', 'unread'));
    }

    public function notifications()
    {
        $notifications = MarketplaceNotification::where('user_id', auth()->id())->latest()->paginate(20);
        return view('developer.notifications', compact('notifications'));
    }

    public function readNotification(MarketplaceNotification $notification)
    {
        abort_unless($notification->user_id === auth()->id(), 403);
        $notification->update(['read_at' => now()]);
        return back();
    }

    public function create()
    {
        $projects = $this->projects();
        $first = $projects->first();
        $defaultType = $first?->ecosystemProfile?->item_types[0] ?? 'plugin';

        return view('developer.marketplace.item-form', [
            'projects' => $projects,
            'item' => new MarketplaceItem(['item_type' => $defaultType]),
            'mode' => 'create',
        ]);
    }

    public function store(MarketplaceItemRequest $request)
    {
        $data = $request->validated();
        $project = $this->marketplace->projectForMarketplace((int)$data['software_project_id'], true);
        $this->marketplace->assertItemAllowed($project, $data['item_type']);

        $meta = $this->marketplace->itemPayload($request);
        $item = MarketplaceItem::create(array_merge($data, $meta, [
            'owner_user_id' => auth()->id(),
            'slug' => $data['slug'],
                        'is_official' => false,
            'is_verified' => false,
            'is_published' => false,
        ]));

        return redirect()->route('developer.marketplace.releases.create', $item)
            ->with('success', 'Marketplace item created as a draft. Add a release and submit it for review.');
    }

    public function edit(MarketplaceItem $item)
    {
        $this->authorize('update', $item);
        $item->load(['project.ecosystemProfile', 'releases' => fn ($q) => $q->with(['submissions' => fn ($sq) => $sq->latest()])->latest('id')]);

        return view('developer.marketplace.item-form', [
            'projects' => $this->projects(),
            'item' => $item,
            'mode' => 'edit',
        ]);
    }

    public function update(MarketplaceItemRequest $request, MarketplaceItem $item)
    {
        $this->authorize('update', $item);
        $data = $request->validated();
        $project = $this->marketplace->projectForMarketplace((int)$data['software_project_id'], true);
        $this->marketplace->assertItemAllowed($project, $data['item_type']);

        if ($item->releases()->exists() && (int) $item->software_project_id !== (int) $project->id) {
            return back()->withErrors(['software_project_id' => 'The target project cannot be changed after a release has been created. Create a new marketplace item instead.']);
        }

        $meta = $this->marketplace->itemPayload($request);
        $item->update(array_merge($data, $meta, [
            'slug' => $data['slug'],
                        'is_published' => $item->is_published,
        ]));

        return back()->with('success', 'Marketplace item updated.');
    }

    public function createRelease(MarketplaceItem $item)
    {
        $this->authorize('update', $item);
        $item->load('project.ecosystemProfile');
        $profile = $item->project->ecosystemProfile;

        return view('developer.marketplace.release-form', [
            'item' => $item,
            'profile' => $profile,
            'release' => new MarketplaceRelease([
                'platform' => $profile?->platforms[0] ?? 'All',
                'architecture' => $profile?->architectures[0] ?? 'All',
                'channel' => $profile?->channels[0] ?? 'Stable',
                'package_type' => $profile?->package_types[0] ?? 'zip',
            ]),
        ]);
    }

    public function storeRelease(MarketplaceReleaseRequest $request, MarketplaceItem $item)
    {
        $this->authorize('update', $item);
        $item->load('project.ecosystemProfile');
        $data = $request->validated();
        $this->marketplace->assertReleaseAllowed($item, $data);

        $existing = MarketplaceRelease::where('marketplace_item_id', $item->id)
            ->where('version', $data['version'])
            ->where('platform', $data['platform'])
            ->where('architecture', $data['architecture'])
            ->where('channel', $data['channel'])
            ->first();

        if ($existing) {
            $latestSubmission = $existing->submissions()->latest('id')->first();
            if ($existing->is_published || ($latestSubmission && in_array($latestSubmission->status, [MarketplaceSubmission::SUBMITTED, MarketplaceSubmission::UNDER_REVIEW, MarketplaceSubmission::APPROVED, MarketplaceSubmission::PUBLISHED], true))) {
                return redirect()->route('developer.marketplace.release.edit', $existing)
                    ->withErrors(['release' => 'This release version already exists and is currently published or under review. Create a new version instead.']);
            }

            $oldPath = $existing->file_path;
            try {
                $package = $this->package($request, $item, $data);
            } catch (Throwable $e) {
                throw ValidationException::withMessages(['package' => $e->getMessage()]);
            }

            $existing->update(array_merge($data, $package, [
                'is_published' => false,
                'is_mandatory' => $request->boolean('is_mandatory'),
                'published_at' => null,
                'dependencies' => $this->dependencies($request),
            ]));
            if ($oldPath && $oldPath !== $existing->file_path) $this->storage->delete($oldPath);

            return redirect()->route('developer.marketplace.release.edit', $existing)->with('success', 'Existing draft updated.');
        }

        try {
            $package = $this->package($request, $item, $data);
        } catch (Throwable $e) {
            throw ValidationException::withMessages(['package' => $e->getMessage()]);
        }

        try {
            $release = MarketplaceRelease::create(array_merge($data, $package, [
                'marketplace_item_id' => $item->id,
                'is_published' => false,
                'is_mandatory' => $request->boolean('is_mandatory'),
                'published_at' => null,
                'dependencies' => $this->dependencies($request),
            ]));
        } catch (\Illuminate\Database\QueryException $e) {
            if ((string) $e->getCode() === '23000') {
                if (!empty($package['file_path'])) $this->storage->delete($package['file_path']);
                $duplicate = MarketplaceRelease::where('marketplace_item_id', $item->id)->where('version', $data['version'])->where('platform', $data['platform'])->where('architecture', $data['architecture'])->where('channel', $data['channel'])->first();
                if ($duplicate) return redirect()->route('developer.marketplace.release.edit', $duplicate)->with('success', 'This release already existed, so the existing draft was opened.');
            }
            throw $e;
        }

        return redirect()->route('developer.marketplace.release.edit', $release)->with('success', 'Release saved as DRAFT.');
    }

    public function editRelease(MarketplaceRelease $release)
    {
        $release->load('item.project.ecosystemProfile');
        $this->authorize('update', $release);
        return view('developer.marketplace.release-form', ['item' => $release->item, 'profile' => $release->item->project->ecosystemProfile, 'release' => $release]);
    }

    public function updateRelease(MarketplaceReleaseRequest $request, MarketplaceRelease $release)
    {
        $release->load('item.project.ecosystemProfile');
        $this->authorize('update', $release);
        $data = $request->validated();
        $this->marketplace->assertReleaseAllowed($release->item, $data);
        $old = $release->file_path;
        if ($request->hasFile('package') || $request->filled('upload_token')) {
            try { $data = array_merge($data, $this->package($request, $release->item, $data)); }
            catch (Throwable $e) { throw ValidationException::withMessages(['package' => $e->getMessage()]); }
        }
        unset($data['package'], $data['upload_token']);
        $release->update(array_merge($data, ['is_published' => false, 'published_at' => null, 'dependencies' => $this->dependencies($request)]));
        if ($old && $old !== $release->file_path) $this->storage->delete($old);
        $release->submissions()->whereIn('status', [MarketplaceSubmission::SUBMITTED, MarketplaceSubmission::UNDER_REVIEW])->update(['status' => MarketplaceSubmission::DRAFT]);
        return back()->with('success', 'Release updated and kept unpublished. Submit it again for review.');
    }

    public function submit(Request $request, MarketplaceRelease $release)
    {
        $release->load('item.project.ecosystemProfile');
        $this->authorize('update', $release);
        if (!$release->file_path) return back()->withErrors(['release' => 'Upload a package before submitting.']);

        $existing = $release->submissions()->latest()->first();
        if ($existing && in_array($existing->status, [MarketplaceSubmission::SUBMITTED, MarketplaceSubmission::UNDER_REVIEW], true)) return back()->withErrors(['release' => 'This release is already in the review queue.']);

        if ($existing && $existing->status === MarketplaceSubmission::NEEDS_CHANGES) {
            $submission = $existing;
            $submission->update(['developer_message' => $request->input('developer_message'), 'decision_reason' => null, 'reviewer_notes' => null, 'reviewed_by' => null, 'reviewed_at' => null, 'resubmission_count' => $submission->resubmission_count + 1]);
        } else {
            $submission = MarketplaceSubmission::create(['marketplace_item_id' => $release->marketplace_item_id, 'marketplace_release_id' => $release->id, 'submitted_by' => auth()->id(), 'status' => MarketplaceSubmission::DRAFT, 'risk_level' => 'LOW', 'risk_score' => 0, 'developer_message' => $request->input('developer_message')]);
        }

        $assessment = $this->risk->assess($submission->load(['item', 'release']));
        $submission->update(['risk_level' => $assessment['level'], 'risk_score' => $assessment['score'], 'risk_summary' => $assessment['summary']]);
        $this->moderation->transition($submission, MarketplaceSubmission::SUBMITTED, auth()->user(), $request->input('developer_message'));
        $this->notifyAdmins($submission, 'Marketplace review requested', 'New marketplace submission', "{$release->item->name} v{$release->version} is ready for review.");

        return redirect()->route('developer.dashboard')->with('success', 'Submitted for admin review.');
    }

    public function submissions()
    {
        $submissions = MarketplaceSubmission::where('submitted_by', auth()->id())->with(['item.project', 'release', 'risks', 'logs.actor'])->latest()->paginate(15);
        return view('developer.submissions', compact('submissions'));
    }

    public function submission(MarketplaceSubmission $submission)
    {
        $this->authorize('view', $submission);
        $submission->load(['item.project', 'release', 'risks', 'logs.actor']);
        return view('developer.submission', compact('submission'));
    }

    public function resubmit(Request $request, MarketplaceSubmission $submission)
    {
        $this->authorize('update', $submission);
        if ($submission->status !== MarketplaceSubmission::NEEDS_CHANGES) return back()->withErrors(['submission' => 'Only submissions requiring changes can be resubmitted.']);
        $submission->update(['developer_message' => $request->input('developer_message'), 'reviewer_notes' => null, 'decision_reason' => null]);
        $this->risk->assess($submission->load(['item', 'release']));
        $this->moderation->transition($submission, MarketplaceSubmission::SUBMITTED, auth()->user(), $request->input('developer_message'));
        $this->notifyAdmins($submission, 'Marketplace resubmitted', 'Developer resubmitted a marketplace release', "{$submission->item->name} v{$submission->release?->version} was resubmitted.");
        return back()->with('success', 'Resubmitted for review.');
    }

    private function lines(?string $value): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $value))));
    }

    private function compatibility(Request $r): array
    {
        return ['targets' => $this->lines($r->input('compatibility_text')), 'minimumProjectVersion' => trim((string) $r->input('minimum_project_version')) ?: null];
    }

    private function dependencies(Request $r): array
    {
        $out = [];
        foreach ($this->lines($r->input('dependencies_text')) as $line) {
            [$id, $min] = array_pad(explode('@', $line, 2), 2, null);
            $out[] = ['itemId' => trim($id), 'minimumVersion' => $min ? trim($min) : null];
        }
        return $out;
    }

    private function package(Request $r, MarketplaceItem $item, array $metadata): array { return $this->marketplace->package($r, $item, $metadata, $this->storage); }

    private function notifyAdmins(MarketplaceSubmission $submission, string $type, string $title, string $message): void
    {
        foreach (\App\Models\User::where('is_admin', true)->get() as $admin) $this->moderation->notify($admin, $submission, $type, $title, $message, route('admin.marketplace.review.show', $submission));
    }
}
