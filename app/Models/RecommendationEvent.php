<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model; class RecommendationEvent extends Model {protected $fillable=['user_id','software_project_id','reason','action'];}
