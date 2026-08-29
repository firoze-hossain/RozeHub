<?php
namespace App\Policies;
use App\Models\MarketplaceRelease;
use App\Models\User;
class MarketplaceReleasePolicy
{
    public function update(User $user, MarketplaceRelease $release): bool { return $user->is_admin || $release->item?->owner_user_id === $user->id; }
    public function view(User $user, MarketplaceRelease $release): bool { return $user->is_admin || $release->item?->owner_user_id === $user->id || ($release->is_published && $release->item?->is_published); }
}
