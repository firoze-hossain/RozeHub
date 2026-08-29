<?php
namespace App\Services\Ecosystem;
use App\Models\EcosystemEdge; use App\Models\EcosystemNode; use App\Models\SoftwareProject; use Illuminate\Support\Facades\DB;
class EcosystemGraphService {
 public function build(?SoftwareProject $focus=null): array {
  $nodes=EcosystemNode::query()->get(); $edges=EcosystemEdge::query()->with(['source','target'])->get();
  if($nodes->isEmpty()) $this->syncProjectNodes();
  $nodes=EcosystemNode::query()->get(); $edges=EcosystemEdge::query()->get();
  if($focus){$projectNode=$nodes->first(fn($n)=>$n->node_type==='project' && $n->slug===$focus->slug); if($projectNode){$ids=collect([$projectNode->id]); $edges->each(function($e)use($ids){if($ids->contains($e->source_node_id)||$ids->contains($e->target_node_id)){$ids->push($e->source_node_id);$ids->push($e->target_node_id);}}); $nodes=$nodes->whereIn('id',$ids->unique()); $edges=$edges->whereIn('source_node_id',$ids)->whereIn('target_node_id',$ids);}}
  return ['nodes'=>$nodes->values()->map(fn($n)=>['id'=>$n->id,'label'=>$n->label,'type'=>$n->node_type,'url'=>$n->url,'metadata'=>$n->metadata])->all(),'edges'=>$edges->values()->map(fn($e)=>['source'=>$e->source_node_id,'target'=>$e->target_node_id,'relationship'=>$e->relationship])->all()];
 }
 public function syncProjectNodes(): void {SoftwareProject::query()->each(function($p){EcosystemNode::updateOrCreate(['node_type'=>'project','slug'=>$p->slug],['label'=>$p->name,'url'=>route('hub').'#project-'.$p->slug,'metadata'=>['project_id'=>$p->id,'category'=>$p->category]]);});}
}
