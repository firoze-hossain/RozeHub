<?php
namespace Tests\Feature;
use App\Models\SoftwareProject;
use App\Services\GithubService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
class GithubEcosystemTest extends TestCase {
 use RefreshDatabase;
 public function test_repository_url_is_parsed():void { $this->assertSame(['owner'=>'firoze-hossain','name'=>'DBNavigator'],app(GithubService::class)->parseRepositoryUrl('https://github.com/firoze-hossain/DBNavigator/')); }
 public function test_sync_indexes_repository_and_contributors():void {
  Http::fake(function($request){$u=$request->url(); if(str_contains($u,'/contributors'))return Http::response([['login'=>'dev','avatar_url'=>'x','html_url'=>'https://github.com/dev','contributions'=>7]],200); if(str_contains($u,'/issues'))return Http::response([],200); if(str_contains($u,'/pulls'))return Http::response([],200); if(str_contains($u,'/releases'))return Http::response([],200); return Http::response(['owner'=>['login'=>'firoze-hossain'],'name'=>'DBNavigator','full_name'=>'firoze-hossain/DBNavigator','html_url'=>'https://github.com/firoze-hossain/DBNavigator','default_branch'=>'main','description'=>'DB client','stargazers_count'=>12,'forks_count'=>2,'open_issues_count'=>1,'watchers_count'=>12,'language'=>'Java','topics'=>[]],200);});
  $p=SoftwareProject::factory()->create(['github_url'=>'https://github.com/firoze-hossain/DBNavigator']);
  $repo=app(GithubService::class)->sync($p);
  $this->assertSame('firoze-hossain/DBNavigator',$repo->full_name); $this->assertDatabaseHas('github_contributors',['github_repository_id'=>$repo->id,'login'=>'dev','contributions'=>7]);
 }
 public function test_webhook_rejects_invalid_signature():void { $this->postJson(route('github.webhook'),['repository'=>['html_url'=>'https://github.com/a/b']])->assertStatus(401); }
}
