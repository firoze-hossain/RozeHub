<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ReleaseUpdateNotification extends Model {
    protected $fillable=['user_id','release_id','type','message','read_at'];
    protected function casts():array{return ['read_at'=>'datetime'];}
    public function user():BelongsTo{return $this->belongsTo(User::class);}
    public function release():BelongsTo{return $this->belongsTo(Release::class);}
}
