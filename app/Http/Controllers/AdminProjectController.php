<?php
namespace App\Http\Controllers;
use App\Models\SoftwareProject; use Illuminate\Http\Request; use Illuminate\Support\Str; use Illuminate\Validation\Rule;
class AdminProjectController extends Controller {
 public function index(){return view('admin.projects.index',['projects'=>SoftwareProject::withCount('releases')->withSum('releases','downloads_count')->orderBy('name')->get()]);}
 public function create(){return view('admin.projects.form',['project'=>new SoftwareProject(),'mode'=>'create']);}
 public function store(Request $r){$d=$this->validated($r);$d['slug']=Str::slug($d['slug']?:$d['name']);SoftwareProject::create($d);return redirect()->route('admin.projects.index')->with('success','Software project created.');}
 public function edit(SoftwareProject $project){return view('admin.projects.form',['project'=>$project,'mode'=>'edit']);}
 public function update(Request $r,SoftwareProject $project){$d=$this->validated($r,$project);$d['slug']=Str::slug($d['slug']?:$d['name']);$project->update($d);return redirect()->route('admin.projects.index')->with('success','Software project updated.');}
 public function destroy(SoftwareProject $project){$project->delete();return back()->with('success','Software project deleted.');}
 private function validated(Request $r,?SoftwareProject $p=null):array{return $r->validate(['name'=>['required','string','max:120',Rule::unique('software_projects','name')->ignore($p?->id)],'slug'=>['nullable','string','max:140',Rule::unique('software_projects','slug')->ignore($p?->id)],'tagline'=>['required','string','max:255'],'description'=>['required','string','max:5000'],'category'=>['required','string','max:120'],'accent'=>['required','in:mint,coral,lilac,gold,blue'],'icon'=>['required','string','max:8'],'website'=>['nullable','url','max:500']]);}
}
