<?php
namespace App\Services\Roadmaps;
use App\Models\{ProjectRoadmap,SoftwareProject};
class RoadmapService { public function forProject(SoftwareProject $p){return ProjectRoadmap::with('items')->where('software_project_id',$p->id)->orderBy('sort_order')->get();} }
