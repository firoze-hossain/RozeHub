<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class MarketplaceSubmissionRisk extends Model
{
    protected $fillable=['submission_id','category','status','score','summary','notes','checked_by','checked_at'];
    protected function casts(): array { return ['checked_at'=>'datetime']; }
    public function submission(): BelongsTo { return $this->belongsTo(MarketplaceSubmission::class,'submission_id'); }
    public function checker(): BelongsTo { return $this->belongsTo(User::class,'checked_by'); }
}
