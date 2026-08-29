<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model; class ProjectHealthMetric extends Model {protected $fillable=['software_project_id','metric_key','weight','score','metadata']; protected $casts=['weight'=>'float','score'=>'integer','metadata'=>'array']; public function project(){return $this->belongsTo(SoftwareProject::class,'software_project_id');}}
