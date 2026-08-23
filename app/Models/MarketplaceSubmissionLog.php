<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class MarketplaceSubmissionLog extends Model
{
    protected $fillable=['submission_id','actor_id','action','from_status','to_status','comment','metadata'];
    protected function casts(): array { return ['metadata'=>'array']; }
    public function submission(): BelongsTo { return $this->belongsTo(MarketplaceSubmission::class,'submission_id'); }
    public function actor(): BelongsTo { return $this->belongsTo(User::class,'actor_id'); }
}
