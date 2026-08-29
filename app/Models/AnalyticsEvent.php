<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyticsEvent extends Model
{
    public $timestamps = false;
    protected $fillable = ['software_project_id','user_id','event_type','subject_type','subject_id','metadata','ip_hash','user_agent','created_at'];
    protected function casts(): array { return ['metadata'=>'array','created_at'=>'datetime']; }
    public function project(): BelongsTo { return $this->belongsTo(SoftwareProject::class,'software_project_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
