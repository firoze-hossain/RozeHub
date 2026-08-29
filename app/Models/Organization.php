<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Organization extends Model { protected $fillable=['owner_user_id','name','slug','logo_url','description','website','github_url','location','is_public']; protected $casts=['is_public'=>'boolean']; public function owner(){return $this->belongsTo(User::class,'owner_user_id');} public function members(){return $this->belongsToMany(User::class,'organization_members')->withPivot('role')->withTimestamps();} public function projects(){return $this->belongsToMany(SoftwareProject::class,'organization_projects')->withPivot('role')->withTimestamps();} }
