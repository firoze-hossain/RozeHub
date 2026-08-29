<?php
namespace App\Policies;
use App\Models\MarketplaceItem;
use App\Models\User;
class MarketplaceItemPolicy
{
    public function update(User $user, MarketplaceItem $item): bool { return $user->is_admin || $item->owner_user_id === $user->id; }
    public function delete(User $user, MarketplaceItem $item): bool { return $user->is_admin || $item->owner_user_id === $user->id; }
    public function view(User $user, MarketplaceItem $item): bool { return $user->is_admin || $item->owner_user_id === $user->id || $item->is_published; }
}
