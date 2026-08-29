<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model; class ProjectRoadmap extends Model {protected $fillable=['software_project_id','title','description','status','sort_order']; public function project(){return $this->belongsTo(SoftwareProject::class,'software_project_id');} public function items(){return $this->hasMany(RoadmapItem::class)->orderBy('sort_order');}}
