<?php
namespace App\Providers;

use App\Models\MarketplaceItem;
use App\Models\MarketplaceRelease;
use App\Models\MarketplaceSubmission;
use App\Policies\MarketplaceItemPolicy;
use App\Policies\MarketplaceReleasePolicy;
use App\Policies\MarketplaceSubmissionPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}
    public function boot(): void
    {
        Gate::policy(MarketplaceItem::class, MarketplaceItemPolicy::class);
        Gate::policy(MarketplaceRelease::class, MarketplaceReleasePolicy::class);
        Gate::policy(MarketplaceSubmission::class, MarketplaceSubmissionPolicy::class);
    }
}
