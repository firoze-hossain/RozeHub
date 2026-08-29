<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model; class RoadmapItem extends Model {protected $fillable=['project_roadmap_id','title','description','status','priority','target_version','target_date','sort_order']; protected $casts=['target_date'=>'date']; public function roadmap(){return $this->belongsTo(ProjectRoadmap::class,'project_roadmap_id');}}
