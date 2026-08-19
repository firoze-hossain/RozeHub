<?php
namespace App\Http\Controllers;
use App\Models\Release; use App\Models\Review; use App\Models\SoftwareProject;
class AdminDashboardController extends Controller {
 public function index(){return view('admin.dashboard',['projectCount'=>SoftwareProject::count(),'releaseCount'=>Release::count(),'publishedCount'=>Release::where('is_published',true)->count(),'downloadCount'=>Release::sum('downloads_count'),'pendingReviews'=>Review::where('is_approved',false)->count(),'projects'=>SoftwareProject::withCount('releases')->latest()->take(8)->get(),'releases'=>Release::with('project')->latest()->take(8)->get()]);}
}
