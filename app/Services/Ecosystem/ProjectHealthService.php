<?php
namespace App\Services\Ecosystem;
use App\Models\{ProjectHealthMetric,ProjectHealthSnapshot,SoftwareProject,GithubRepository,MarketplaceItem};
use Illuminate\Support\Facades\DB;
class ProjectHealthService {
 public const DEFAULTS=['github_activity'=>20,'release_freshness'=>20,'documentation'=>20,'community_activity'=>20,'marketplace_activity'=>20];
 public function calculate(SoftwareProject $project): array { $repo=$project->githubRepository; $github=$repo ? min(100, (int)$repo->stars + (int)$repo->forks*2 + max(0, 20-(int)$repo->open_issues)) : 0; $releases=min(100,$project->releases()->count()*20); $docs=min(100,$project->documentationPages()->where('is_published',true)->count()*10); $community=min(100,(int)DB::table('github_contributors')->where('github_repository_id',$repo?->id)->sum('contributions')); $market=min(100,$project->marketplaceCategories()->count()*20+$project->reviews()->count()*5); $scores=['github_activity'=>$github,'release_freshness'=>$releases,'documentation'=>$docs,'community_activity'=>$community,'marketplace_activity'=>$market]; $metrics=ProjectHealthMetric::where('software_project_id',$project->id)->get()->keyBy('metric_key'); $weighted=0;$weight=0; foreach($scores as $key=>$score){$w=$metrics->get($key)?->weight ?? 1;$weighted+=$score*$w;$weight+=$w;} $overall=$weight?round($weighted/$weight):0; return ['score'=>$overall,'breakdown'=>$scores]; }
 public function snapshot(SoftwareProject $project): ProjectHealthSnapshot {$r=$this->calculate($project); return ProjectHealthSnapshot::create(['software_project_id'=>$project->id,'score'=>$r['score'],'breakdown'=>$r['breakdown'],'captured_at'=>now()]);}
}
