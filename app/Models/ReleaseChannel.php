<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ReleaseChannel extends Model {
    protected $fillable=['software_project_id','key','name','description','is_enabled','is_default','sort_order'];
    protected function casts():array{return ['is_enabled'=>'boolean','is_default'=>'boolean'];}
    public function project():BelongsTo{return $this->belongsTo(SoftwareProject::class,'software_project_id');}
}
