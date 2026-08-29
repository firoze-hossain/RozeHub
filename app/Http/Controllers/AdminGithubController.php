<?php
namespace App\Http\Controllers;
use App\Models\SoftwareProject;
use App\Services\GithubService;
use Illuminate\Http\Request;
class AdminGithubController extends Controller {
 public function show(SoftwareProject $project){$repo=$project->githubRepository()->with(['contributors'=>fn($q)=>$q->orderByDesc('contributions'),'issues'=>fn($q)=>$q->orderByDesc('updated_at_github'),'pullRequests'=>fn($q)=>$q->orderByDesc('updated_at_github'),'releases'=>fn($q)=>$q->orderByDesc('published_at_github')])->first();return view('admin.github.show',compact('project','repo'));}
 public function sync(SoftwareProject $project,GithubService $github){try{$github->sync($project);return back()->with('success','GitHub repository synchronized successfully.');}catch(\Throwable $e){return back()->with('error',$e->getMessage());}}
 public function editDocumentation(SoftwareProject $project,GithubService $github){$parts=$github->parseRepositoryUrl($project->github_url);if(!$parts)abort(404);$repo=$project->githubRepository; $path=request('path','README.md'); $file=null;$error=null;try{$file=$github->getDocumentationFile($parts['owner'],$parts['name'],$path,$repo?->default_branch??'');}catch(\Throwable $e){$error=$e->getMessage();}return view('admin.github.documentation',compact('project','parts','file','path','error'));}
 public function updateDocumentation(Request $request,SoftwareProject $project,GithubService $github){$data=$request->validate(['path'=>'required|string|max:500','message'=>'required|string|max:500','content'=>'required|string','sha'=>'required|string','branch'=>'nullable|string|max:120']);$parts=$github->parseRepositoryUrl($project->github_url);if(!$parts)abort(404);try{$github->updateDocumentationFile($parts['owner'],$parts['name'],$data['path'],$data['message'],$data['content'],$data['sha'],$data['branch']?:null);return redirect()->route('admin.github.documentation',[$project,'path'=>$data['path']])->with('success','Documentation updated on GitHub.');}catch(\Throwable $e){return back()->withInput()->with('error',$e->getMessage());}}
}
