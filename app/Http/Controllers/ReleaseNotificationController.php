<?php
namespace App\Http\Controllers;
use App\Models\ReleaseUpdateNotification;
use Illuminate\Http\Request;
class ReleaseNotificationController extends Controller {
 public function index(Request $request){return response()->json(['count'=>$request->user()->releaseUpdateNotifications()->whereNull('read_at')->count(),'notifications'=>$request->user()->releaseUpdateNotifications()->with('release.project')->latest()->limit(50)->get()->map(fn($n)=>['id'=>$n->id,'type'=>$n->type,'message'=>$n->message,'readAt'=>optional($n->read_at)->toIso8601String(),'releaseId'=>$n->release_id,'version'=>$n->release?->version,'project'=>$n->release?->project?->slug,'createdAt'=>$n->created_at->toIso8601String()])]);}
 public function read(Request $request, ReleaseUpdateNotification $notification){abort_unless($notification->user_id===$request->user()->id,403);$notification->update(['read_at'=>now()]);return response()->json(['success'=>true]);}
 public function readAll(Request $request){$request->user()->releaseUpdateNotifications()->whereNull('read_at')->update(['read_at'=>now()]);return response()->json(['success'=>true]);}
}
