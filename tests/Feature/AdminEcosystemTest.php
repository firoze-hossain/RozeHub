<?php
namespace Tests\Feature;
use App\Models\ProjectEcosystemProfile;
use App\Models\SoftwareProject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class AdminEcosystemTest extends TestCase
{
    use RefreshDatabase;
    public function test_admin_can_update_project_ecosystem_policy(): void
    {
        $admin=User::factory()->create(['is_admin'=>true]);
        $project=SoftwareProject::create(['name'=>'Policy App','slug'=>'policy-app','tagline'=>'Test','description'=>'Test','category'=>'Desktop','accent'=>'mint','icon'=>'P']);
        ProjectEcosystemProfile::create(['software_project_id'=>$project->id,'ecosystem_type'=>'desktop_application','title'=>'Old','item_types'=>['plugin'],'capabilities'=>[],'package_types'=>['zip'],'platforms'=>['Linux'],'architectures'=>['x64'],'channels'=>['Stable'],'integration_targets'=>[],'marketplace_enabled'=>true,'community_contributions'=>true,'moderation_required'=>true]);
        $this->actingAs($admin)->put(route('admin.ecosystem.update',$project),['ecosystem_type'=>'desktop_application','title'=>'New policy','description'=>'Updated','item_types'=>['driver','plugin'],'capabilities'=>['database.read'],'package_types'=>['zip','jar'],'platforms'=>['Linux','Windows'],'architectures'=>['x64'],'channels'=>['Stable','Beta'],'integration_targets'=>['PostgreSQL'],'marketplace_enabled'=>1,'community_contributions'=>1,'moderation_required'=>1])->assertRedirect();
        $this->assertSame(['driver','plugin'],$project->ecosystemProfile()->first()->item_types);
        $this->assertSame(['Stable','Beta'],$project->ecosystemProfile()->first()->channels);
    }
}
