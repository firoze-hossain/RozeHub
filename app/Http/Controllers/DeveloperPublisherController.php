<?php
namespace App\Http\Controllers;
use App\Models\PublisherProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class DeveloperPublisherController extends Controller {
 public function edit(){return view('developer.publisher', ['profile'=>auth()->user()->publisherProfile]);}
 public function update(Request $r){$d=$r->validate(['display_name'=>'required|string|max:160','avatar_url'=>'nullable|url|max:500','website'=>'nullable|url|max:500','github_url'=>'nullable|url|max:500','bio'=>'nullable|string|max:3000']); $profile=auth()->user()->publisherProfile; $data=array_merge($d,['user_id'=>auth()->id(),'slug'=>Str::slug($d['display_name'])]); if($profile)$profile->update($data);else PublisherProfile::create($data); return back()->with('success','Publisher profile saved.');}
}
