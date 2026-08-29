<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class GithubPullRequest extends Model { protected $fillable=['github_repository_id','github_id','number','title','state','author_login','html_url','merged','opened_at','updated_at_github','raw']; protected function casts():array{return ['raw'=>'array','merged'=>'boolean','opened_at'=>'datetime','updated_at_github'=>'datetime'];} public function repository(){return $this->belongsTo(GithubRepository::class,'github_repository_id');} }
