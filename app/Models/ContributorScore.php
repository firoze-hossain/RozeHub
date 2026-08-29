<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model; class ContributorScore extends Model {protected $fillable=['user_id','points','merged_prs','issues','documentation','marketplace_items','calculated_at']; protected $casts=['calculated_at'=>'datetime']; public function user(){return $this->belongsTo(User::class);}}
