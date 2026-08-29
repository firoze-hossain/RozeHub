<?php
namespace App\Policies;
use App\Models\MarketplaceSubmission;
use App\Models\User;
class MarketplaceSubmissionPolicy
{
    public function view(User $user, MarketplaceSubmission $submission): bool { return $user->is_admin || $submission->submitted_by === $user->id; }
    public function update(User $user, MarketplaceSubmission $submission): bool { return !$user->is_admin && $submission->submitted_by === $user->id; }
}
