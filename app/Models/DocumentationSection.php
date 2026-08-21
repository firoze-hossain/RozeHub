<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentationSection extends Model
{
    use HasFactory;

    protected $fillable = ['software_project_id', 'title', 'slug', 'description', 'icon', 'sort_order', 'is_published'];

    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(SoftwareProject::class, 'software_project_id');
    }

    public function pages(): HasMany
    {
        return $this->hasMany(DocumentationPage::class, 'documentation_section_id');
    }
}
