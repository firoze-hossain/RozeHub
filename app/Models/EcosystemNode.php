<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model; class EcosystemNode extends Model {protected $fillable=['node_type','label','slug','url','metadata']; protected $casts=['metadata'=>'array']; public function outgoing(){return $this->hasMany(EcosystemEdge::class,'source_node_id');} public function incoming(){return $this->hasMany(EcosystemEdge::class,'target_node_id');}}
