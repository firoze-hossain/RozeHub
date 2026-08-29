<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class PublisherProfile extends Model {
    protected $fillable=['user_id','display_name','slug','avatar_url','website','github_url','bio','is_verified'];
    protected function casts(): array { return ['is_verified'=>'boolean']; }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function items(): HasMany { return $this->hasMany(MarketplaceItem::class,'owner_user_id','user_id'); }
}
