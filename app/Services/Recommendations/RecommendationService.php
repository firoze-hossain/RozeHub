<?php
namespace App\Services\Recommendations;
use App\Models\{SoftwareProject,UserProjectInteraction};
class RecommendationService { public function for(?int $userId=null,int $limit=6){$projects=SoftwareProject::query()->withCount(['analyticsEvents','releases','documentationPages'])->get(); if($userId){$seen=UserProjectInteraction::where('user_id',$userId)->pluck('software_project_id');$projects=$projects->sortByDesc(fn($p)=>($seen->contains($p->id)?-100:0)+$p->analytics_events_count+$p->releases_count+$p->documentation_pages_count);}else{$projects=$projects->sortByDesc(fn($p)=>$p->analytics_events_count+$p->releases_count+$p->documentation_pages_count);} return $projects->take($limit)->values();}}
