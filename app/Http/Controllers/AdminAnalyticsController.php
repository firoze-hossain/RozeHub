<?php
namespace App\Http\Controllers;

use App\Models\{AnalyticsEvent, GithubRepository, MarketplaceItem, Release, SoftwareProject};
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminAnalyticsController extends Controller
{
    public function index(Request $request, AnalyticsService $analytics)
    {
        $days = max(7, min(365, (int)$request->input('days',30)));
        $summary = $analytics->summary($days);
        $projects = $analytics->projectStats($days);
        $topDownloads = $analytics->topDownloads($days,10);
        $recent = AnalyticsEvent::with('project')->latest('created_at')->limit(30)->get();
        $github = GithubRepository::with('project')->get()->map(fn($r)=>[
            'project'=>$r->project?->name,'stars'=>(int)$r->stars,'forks'=>(int)$r->forks,
            'issues'=>(int)$r->open_issues,'contributors'=>$r->contributors()->count(),
            'pull_requests'=>$r->pullRequests()->count(),'releases'=>$r->releases()->count(),'synced_at'=>$r->synced_at,
        ]);
        $docs = AnalyticsEvent::where('created_at','>=',now()->subDays($days-1)->startOfDay())->where('event_type','documentation_view')
            ->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(metadata,'$.page_slug')) page_slug, COUNT(*) views")
            ->groupBy('page_slug')->orderByDesc('views')->limit(15)->get();
        return view('admin.analytics.index', compact('days','summary','projects','topDownloads','recent','github','docs'));
    }

    public function project(Request $request, SoftwareProject $project, AnalyticsService $analytics)
    {
        $days=max(7,min(365,(int)$request->input('days',30))); $from=now()->subDays($days-1)->startOfDay();
        $base=AnalyticsEvent::where('software_project_id',$project->id)->where('created_at','>=',$from);
        $stats=['events'=>(clone $base)->count(),'downloads'=>(clone $base)->where('event_type','download')->count(),'marketplace'=>(clone $base)->where('event_type','marketplace_item_view')->count(),'documentation'=>(clone $base)->where('event_type','documentation_view')->count(),'github'=>(clone $base)->whereIn('event_type',['github_sync','github_webhook'])->count()];
        $daily=(clone $base)->selectRaw('DATE(created_at) day, COUNT(*) total')->groupBy('day')->orderBy('day')->get();
        return view('admin.analytics.project',compact('project','days','stats','daily'));
    }
}
