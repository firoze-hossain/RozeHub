<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class MarketplaceReview extends Model {
    protected $fillable=['marketplace_item_id','user_id','rating','title','body','is_approved'];
    protected function casts(): array { return ['rating'=>'integer','is_approved'=>'boolean']; }
    public function item(): BelongsTo { return $this->belongsTo(MarketplaceItem::class,'marketplace_item_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
