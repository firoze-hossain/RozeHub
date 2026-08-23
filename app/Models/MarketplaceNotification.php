<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class MarketplaceNotification extends Model
{
    protected $fillable=['user_id','submission_id','type','title','message','action_url','read_at'];
    protected function casts(): array { return ['read_at'=>'datetime']; }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function submission(): BelongsTo { return $this->belongsTo(MarketplaceSubmission::class,'submission_id'); }
}
