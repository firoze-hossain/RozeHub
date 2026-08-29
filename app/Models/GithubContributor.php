<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class GithubContributor extends Model { protected $fillable=['github_repository_id','login','avatar_url','html_url','contributions','raw']; protected function casts():array{return ['raw'=>'array'];} public function repository(){return $this->belongsTo(GithubRepository::class,'github_repository_id');} }
