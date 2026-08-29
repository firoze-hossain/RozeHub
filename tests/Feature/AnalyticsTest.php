<?php
namespace Tests\Feature;

use App\Models\AnalyticsEvent;
use App\Models\SoftwareProject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_download_event_is_recorded(): void
    {
        $user = User::factory()->create(['is_admin'=>true]);
        $project = SoftwareProject::create(['name'=>'Analytics App','slug'=>'analytics-app','tagline'=>'Test','description'=>'Test','category'=>'Desktop','accent'=>'mint','icon'=>'A']);
        AnalyticsEvent::create(['software_project_id'=>$project->id,'user_id'=>$user->id,'event_type'=>'download','metadata'=>['version'=>'1.0.0'],'ip_hash'=>hash('sha256','test'),'created_at'=>now()]);
        $this->assertDatabaseHas('analytics_events',['software_project_id'=>$project->id,'event_type'=>'download']);
    }

    public function test_admin_analytics_requires_admin(): void
    {
        $user = User::factory()->create(['is_admin'=>false]);
        $this->actingAs($user)->get(route('admin.analytics.index'))->assertForbidden();
    }
}
