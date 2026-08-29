<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class GithubWebhookDelivery extends Model { protected $fillable=['software_project_id','event','delivery_id','signature_valid','payload','processed_at']; protected function casts():array{return ['payload'=>'array','signature_valid'=>'boolean','processed_at'=>'datetime'];} }
