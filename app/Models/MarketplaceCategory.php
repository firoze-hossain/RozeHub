<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class MarketplaceCategory extends Model {
    protected $fillable=['software_project_id','name','slug','description','icon','sort_order','is_active'];
    protected function casts(): array { return ['is_active'=>'boolean']; }
    public function project(): BelongsTo { return $this->belongsTo(SoftwareProject::class,'software_project_id'); }
}
