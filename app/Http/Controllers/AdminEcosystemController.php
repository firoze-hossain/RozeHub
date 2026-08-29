<?php
namespace App\Http\Controllers;

use App\Http\Requests\EcosystemProfileRequest;
use App\Models\ProjectEcosystemProfile;
use App\Models\SoftwareProject;
use Illuminate\Support\Str;

class AdminEcosystemController extends Controller
{
    public function index()
    {
        return view('admin.ecosystem.index', [
            'projects' => SoftwareProject::with('ecosystemProfile')->orderBy('name')->get(),
        ]);
    }

    public function edit(SoftwareProject $project)
    {
        $profile = $project->ecosystemProfile ?: new ProjectEcosystemProfile([
            'ecosystem_type'=>'desktop_application', 'title'=>$project->name.' ecosystem',
            'item_types'=>['plugin'], 'capabilities'=>[], 'package_types'=>['zip'], 'platforms'=>['Windows','macOS','Linux'],
            'architectures'=>['x64','ARM64'], 'channels'=>['Stable','Beta','Nightly'], 'integration_targets'=>[],
            'marketplace_enabled'=>true, 'community_contributions'=>true, 'moderation_required'=>true,
        ]);
        return view('admin.ecosystem.form', compact('project','profile'));
    }

    public function update(EcosystemProfileRequest $request, SoftwareProject $project)
    {
        $data=$request->validated();
        foreach (['item_types','capabilities','package_types','platforms','architectures','channels','integration_targets'] as $key) {
            $data[$key]=array_values(array_unique(array_filter(array_map(fn($v)=>trim((string)$v), $data[$key] ?? []))));
        }
        foreach (['marketplace_enabled','community_contributions','moderation_required'] as $key) $data[$key]=$request->boolean($key);
        $project->ecosystemProfile()->updateOrCreate(['software_project_id'=>$project->id],$data);
        return redirect()->route('admin.ecosystem.edit',$project)->with('success','Marketplace ecosystem policy updated.');
    }
}
