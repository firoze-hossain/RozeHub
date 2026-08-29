<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class GithubRepository extends Model {
 protected $fillable=['software_project_id','owner','name','full_name','html_url','default_branch','description','homepage','license_name','stars','forks','open_issues','watchers','language','is_fork','is_archived','topics','raw','synced_at'];
 protected function casts():array{return ['topics'=>'array','raw'=>'array','synced_at'=>'datetime','is_fork'=>'boolean','is_archived'=>'boolean'];}
 public function project():BelongsTo{return $this->belongsTo(SoftwareProject::class,'software_project_id');}
 public function contributors():HasMany{return $this->hasMany(GithubContributor::class);}
 public function issues():HasMany{return $this->hasMany(GithubIssue::class);}
 public function pullRequests():HasMany{return $this->hasMany(GithubPullRequest::class);}
 public function releases():HasMany{return $this->hasMany(GithubRelease::class);}
}
