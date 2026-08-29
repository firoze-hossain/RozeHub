<?php
namespace App\Http\Controllers;
use App\Models\{GithubWebhookDelivery,SoftwareProject};
use App\Services\GithubService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class GithubController extends Controller {
 public function __construct(private GithubService $github) {}
 public function contribute(SoftwareProject $project){ if(!$project->github_url) abort(404); $repo=$project->githubRepository ?: null; return view('github.contribute',compact('project','repo')); }
 public function sync(SoftwareProject $project){ try{$this->github->sync($project); return back()->with('success','GitHub repository data synchronized.');}catch(\Throwable $e){return back()->with('error',$e->getMessage());} }
 public function webhook(Request $request){ $raw=$request->getContent(); $valid=$this->github->webhookValid($raw,$request->header('X-Hub-Signature-256')); $payload=json_decode($raw,true) ?: []; $event=$request->header('X-GitHub-Event','unknown'); $delivery=$request->header('X-GitHub-Delivery'); $project=null; if($valid){$project=$this->github->handleWebhook($event,$payload);} GithubWebhookDelivery::create(['software_project_id'=>$project?->id,'event'=>$event,'delivery_id'=>$delivery,'signature_valid'=>$valid,'payload'=>$payload,'processed_at'=>now()]); if(!$valid)return response()->json(['message'=>'Invalid signature'],401); return response()->json(['ok'=>true]); }
}
