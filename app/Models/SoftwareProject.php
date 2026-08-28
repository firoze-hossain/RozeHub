<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SoftwareProject extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'tagline', 'description', 'category', 'accent', 'icon', 'website', 'github_url'];

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

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
