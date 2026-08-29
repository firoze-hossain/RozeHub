<?php
namespace App\Services;

use App\Models\AnalyticsEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function track(string $type, ?int $projectId = null, $subject = null, array $metadata = [], ?Request $request = null): void
    {
        try {
            AnalyticsEvent::create([
                'software_project_id' => $projectId,
                'user_id' => auth()->id(),
                'event_type' => $type,
                'subject_type' => $subject ? get_class($subject) : null,
                'subject_id' => $subject?->getKey(),
                'metadata' => $metadata,
                'ip_hash' => $request?->ip() ? hash('sha256', $request->ip().'|'.config('app.key')) : null,
                'user_agent' => $request ? substr((string)$request->userAgent(),0,500) : null,
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            report($e); // analytics must never break the primary request
        }
    }

    public function summary(int $days = 30): array
    {
        $from = now()->subDays($days - 1)->startOfDay();
        $events = AnalyticsEvent::where('created_at','>=',$from);
        return [
            'total_events' => (clone $events)->count(),
            'downloads' => (clone $events)->where('event_type','download')->count(),
            'marketplace_views' => (clone $events)->whereIn('event_type',['marketplace_view','marketplace_item_view'])->count(),
            'project_views' => (clone $events)->where('event_type','project_view')->count(),
            'documentation_views' => (clone $events)->where('event_type','documentation_view')->count(),
            'github_activity' => (clone $events)->whereIn('event_type',['github_sync','github_webhook'])->count(),
            'unique_visitors' => (clone $events)->whereNotNull('ip_hash')->distinct('ip_hash')->count('ip_hash'),
            'daily' => (clone $events)->selectRaw('DATE(created_at) day, COUNT(*) total')
                ->groupBy('day')->orderBy('day')->get()->map(fn($r)=>['date'=>$r->day,'events'=>(int)$r->total])->values(),
        ];
    }

    public function projectStats(int $days = 30)
    {
        $from = now()->subDays($days - 1)->startOfDay();
        return AnalyticsEvent::query()->where('analytics_events.created_at','>=',$from)->whereNotNull('analytics_events.software_project_id')
            ->join('software_projects','software_projects.id','=','analytics_events.software_project_id')
            ->select('software_projects.id','software_projects.name','software_projects.slug')
            ->selectRaw("SUM(event_type = 'download') downloads")
            ->selectRaw("SUM(event_type = 'marketplace_item_view') marketplace_views")
            ->selectRaw("SUM(event_type = 'project_view') project_views")
            ->selectRaw("SUM(event_type = 'documentation_view') documentation_views")
            ->selectRaw("SUM(event_type IN ('github_sync','github_webhook')) github_activity")
            ->selectRaw('COUNT(*) total_events')
            ->groupBy('software_projects.id','software_projects.name','software_projects.slug')
            ->orderByDesc('total_events')->get();
    }

    public function topDownloads(int $days = 30, int $limit = 10)
    {
        $from = now()->subDays($days - 1)->startOfDay();
        return AnalyticsEvent::query()->where('analytics_events.created_at','>=',$from)->where('analytics_events.event_type','download')->whereNotNull('analytics_events.software_project_id')
            ->join('software_projects','software_projects.id','=','analytics_events.software_project_id')
            ->leftJoin('releases','releases.id','=','analytics_events.subject_id')
            ->select('software_projects.name','software_projects.slug','releases.version')
            ->selectRaw('COUNT(*) downloads')->groupBy('software_projects.id','software_projects.name','software_projects.slug','releases.id','releases.version')
            ->orderByDesc('downloads')->limit($limit)->get();
    }
}
