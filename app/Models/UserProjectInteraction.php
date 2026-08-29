<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model; class UserProjectInteraction extends Model {protected $fillable=['user_id','software_project_id','event_type','count','last_occurred_at']; protected $casts=['last_occurred_at'=>'datetime'];}
