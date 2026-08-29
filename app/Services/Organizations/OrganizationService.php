<?php
namespace App\Services\Organizations;
use App\Models\{Organization,User}; use Illuminate\Support\Str;
class OrganizationService { public function create(User $owner,array $data):Organization { $data['owner_user_id']=$owner->id;$data['slug']=Str::slug($data['name']).'-'.Str::lower(Str::random(5));$o=Organization::create($data);$o->members()->attach($owner->id,['role'=>'owner']);return $o;} }
