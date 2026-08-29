<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SoftwareProject extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'tagline', 'description', 'category', 'accent', 'icon', 'website', 'github_url'];

    public function ecosystemProfile(): HasOne
    {
        return $this->hasOne(ProjectEcosystemProfile::class, 'software_project_id');
    }

    public function releaseChannels(): HasMany
    { return $this->hasMany(ReleaseChannel::class, 'software_project_id'); }

    public function analyticsEvents(): HasMany
    {
        return $this->hasMany(AnalyticsEvent::class, 'software_project_id');
    }

    public function releases(): HasMany
    {
        return $this->hasMany(Release::class);
    }

    public function documentationSections(): HasMany
    {
        return $this->hasMany(DocumentationSection::class, 'software_project_id');
    }

    public function documentationPages(): HasMany
    {
        return $this->hasMany(DocumentationPage::class, 'software_project_id');
    }

    public function marketplaceCategories(): HasMany { return $this->hasMany(MarketplaceCategory::class, 'software_project_id'); }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function githubRepository(): HasOne
    {
        return $this->hasOne(GithubRepository::class, 'software_project_id');
    }
}
