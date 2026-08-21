<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;



class DocumentationPage extends Model
{
    use HasFactory;

    protected $fillable = ['software_project_id', 'release_id', 'documentation_section_id', 'title', 'slug', 'kind', 'version', 'summary', 'content', 'sort_order', 'is_published'];

    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(SoftwareProject::class, 'software_project_id');
    }

    public function release(): BelongsTo
    {
        return $this->belongsTo(Release::class, 'release_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(DocumentationSection::class, 'documentation_section_id');
    }
}
