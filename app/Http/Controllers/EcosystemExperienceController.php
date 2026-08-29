<?php
namespace App\Http\Controllers;
use App\Models\SoftwareProject; use App\Services\Ecosystem\{EcosystemGraphService,ProjectHealthService}; use App\Services\Recommendations\RecommendationService; use App\Services\Contributors\ContributorScoreService; use App\Services\Search\GlobalSearchService; use App\Services\Roadmaps\RoadmapService;
class EcosystemExperienceController extends Controller {
 public function index(EcosystemGraphService $graph, ProjectHealthService $health, RecommendationService $rec, ContributorScoreService $scores){$projects=SoftwareProject::with('ecosystemProfile')->orderBy('name')->get();$healthData=$projects->mapWithKeys(fn($p)=>[$p->id=>$health->calculate($p)]);return view('ecosystem.index',compact('projects','healthData'));}
 public function graph(EcosystemGraphService $service){return response()->json($service->build());}
 public function project(SoftwareProject $project,EcosystemGraphService $graph,ProjectHealthService $health,RecommendationService $rec, RoadmapService $roadmaps){return view('ecosystem.project',['project'=>$project,'graph'=>$graph->build($project),'health'=>$health->calculate($project),'roadmaps'=>$roadmaps->forProject($project),'recommendations'=>$rec->for(auth()->id(),4)]);}
 public function search(\Illuminate\Http\Request $request,GlobalSearchService $service){$q=(string)$request->get('q','');return view('search.index',['q'=>$q,'results'=>$service->search($q)]);}
 public function searchApi(\Illuminate\Http\Request $request,GlobalSearchService $service){return response()->json($service->search((string)$request->get('q',''),10));}
 public function leaderboard(ContributorScoreService $service){return view('contributors.index',['contributors'=>$service->leaderboard()]);}
 public function roadmap(SoftwareProject $project, RoadmapService $service){return view('ecosystem.roadmap',['project'=>$project,'roadmaps'=>$service->forProject($project)]);}
 public function organizations(){return view('organizations.index',['organizations'=>\App\Models\Organization::where('is_public',true)->withCount(['members','projects'])->orderBy('name')->paginate(24)]);}
 public function organization(\App\Models\Organization $organization){abort_unless($organization->is_public || auth()->check() && auth()->id()===$organization->owner_user_id,404);$organization->load(['members','projects']);return view('organizations.show',compact('organization'));}
}
