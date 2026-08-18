<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SoftwareProject extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'tagline', 'description', 'category', 'accent', 'icon', 'website'];

    public function releases(): HasMany
    {
        return $this->hasMany(Release::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
