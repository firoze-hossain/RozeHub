<?php
namespace Tests\Feature;

use App\Models\MarketplaceItem;
use App\Models\ProjectEcosystemProfile;
use App\Models\SoftwareProject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketplaceEcosystemTest extends TestCase
{
    use RefreshDatabase;

    private function project(array $profile=[]): SoftwareProject
    {
        $p=SoftwareProject::create(['name'=>'Test App','slug'=>'test-app','tagline'=>'Test','description'=>'Test','category'=>'Desktop','accent'=>'mint','icon'=>'T']);
        ProjectEcosystemProfile::create(array_merge(['software_project_id'=>$p->id,'ecosystem_type'=>'desktop_application','title'=>'Test ecosystem','item_types'=>['plugin'],'capabilities'=>['network'],'package_types'=>['zip'],'platforms'=>['Linux'],'architectures'=>['x64'],'channels'=>['Stable'],'integration_targets'=>[],'marketplace_enabled'=>true,'community_contributions'=>true,'moderation_required'=>true],$profile));
        return $p;
    }

    public function test_ecosystem_policy_controls_supported_item_types(): void
    {
        $project=$this->project(['item_types'=>['driver']]);
        $user=User::factory()->create();
        $this->actingAs($user)->post(route('developer.marketplace.store'),[
            'software_project_id'=>$project->id,'item_type'=>'plugin','name'=>'Wrong','item_id'=>'wrong.item',
        ])->assertSessionHasErrors('item_type');
    }

    public function test_marketplace_api_exposes_dynamic_ecosystem_schema(): void
    {
        $project=$this->project(['item_types'=>['driver','plugin'],'channels'=>['Stable','Preview'],'platforms'=>['Linux','NOVAOS']]);
        $response=$this->getJson(route('api.marketplace.index',$project->slug));
        $response->assertOk()->assertJsonPath('filters.types',['driver','plugin'])->assertJsonPath('filters.channels',['Stable','Preview'])->assertJsonPath('filters.platforms',['Linux','NOVAOS']);
    }

    public function test_item_policy_allows_owner_and_rejects_other_developer(): void
    {
        $project=$this->project(); $owner=User::factory()->create(); $other=User::factory()->create();
        $item=MarketplaceItem::create(['software_project_id'=>$project->id,'owner_user_id'=>$owner->id,'item_type'=>'plugin','name'=>'Community Plugin','slug'=>'community-plugin','item_id'=>'community.plugin']);
        $this->actingAs($other)->get(route('developer.marketplace.edit',$item))->assertForbidden();
        $this->actingAs($owner)->get(route('developer.marketplace.edit',$item))->assertOk();
    }
}
