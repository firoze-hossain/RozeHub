<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class GithubRelease extends Model { protected $fillable=['github_repository_id','github_id','tag_name','name','body','html_url','prerelease','draft','published_at_github','assets','raw']; protected function casts():array{return ['raw'=>'array','assets'=>'array','prerelease'=>'boolean','draft'=>'boolean','published_at_github'=>'datetime'];} public function repository(){return $this->belongsTo(GithubRepository::class,'github_repository_id');} }
