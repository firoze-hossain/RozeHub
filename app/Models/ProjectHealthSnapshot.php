<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model; class ProjectHealthSnapshot extends Model {public $timestamps=false; protected $fillable=['software_project_id','score','breakdown','captured_at']; protected $casts=['breakdown'=>'array','captured_at'=>'datetime']; public function project(){return $this->belongsTo(SoftwareProject::class,'software_project_id');}}
