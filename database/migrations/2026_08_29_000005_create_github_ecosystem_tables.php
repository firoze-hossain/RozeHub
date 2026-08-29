<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('github_repositories')) Schema::create('github_repositories', function(Blueprint $t){
            $t->id(); $t->foreignId('software_project_id')->unique()->constrained()->cascadeOnDelete();
            $t->string('owner',120); $t->string('name',200); $t->string('full_name',320)->unique(); $t->string('html_url',500); $t->string('default_branch',120)->nullable();
            $t->text('description')->nullable(); $t->string('homepage',500)->nullable(); $t->string('license_name',160)->nullable(); $t->unsignedBigInteger('stars')->default(0); $t->unsignedBigInteger('forks')->default(0); $t->unsignedBigInteger('open_issues')->default(0); $t->unsignedBigInteger('watchers')->default(0); $t->string('language',80)->nullable(); $t->boolean('is_fork')->default(false); $t->boolean('is_archived')->default(false); $t->json('topics')->nullable(); $t->json('raw')->nullable(); $t->timestamp('synced_at')->nullable(); $t->timestamps();
        });
        if (!Schema::hasTable('github_contributors')) Schema::create('github_contributors', function(Blueprint $t){
            $t->id(); $t->foreignId('github_repository_id')->constrained()->cascadeOnDelete(); $t->string('login',120); $t->string('avatar_url',500)->nullable(); $t->string('html_url',500)->nullable(); $t->unsignedBigInteger('contributions')->default(0); $t->json('raw')->nullable(); $t->timestamps(); $t->unique(['github_repository_id','login']); $t->index(['github_repository_id','contributions']);
        });
        if (!Schema::hasTable('github_issues')) Schema::create('github_issues', function(Blueprint $t){
            $t->id(); $t->foreignId('github_repository_id')->constrained()->cascadeOnDelete(); $t->unsignedBigInteger('github_id'); $t->unsignedInteger('number'); $t->string('title',500); $t->string('state',30); $t->string('author_login',120)->nullable(); $t->string('html_url',500); $t->timestamp('opened_at')->nullable(); $t->timestamp('updated_at_github')->nullable(); $t->json('raw')->nullable(); $t->timestamps(); $t->unique(['github_repository_id','github_id']); $t->index(['github_repository_id','state']);
        });
        if (!Schema::hasTable('github_pull_requests')) Schema::create('github_pull_requests', function(Blueprint $t){
            $t->id(); $t->foreignId('github_repository_id')->constrained()->cascadeOnDelete(); $t->unsignedBigInteger('github_id'); $t->unsignedInteger('number'); $t->string('title',500); $t->string('state',30); $t->string('author_login',120)->nullable(); $t->string('html_url',500); $t->boolean('merged')->default(false); $t->timestamp('opened_at')->nullable(); $t->timestamp('updated_at_github')->nullable(); $t->json('raw')->nullable(); $t->timestamps(); $t->unique(['github_repository_id','github_id']); $t->index(['github_repository_id','state','merged']);
        });
        if (!Schema::hasTable('github_releases')) Schema::create('github_releases', function(Blueprint $t){
            $t->id(); $t->foreignId('github_repository_id')->constrained()->cascadeOnDelete(); $t->unsignedBigInteger('github_id'); $t->string('tag_name',180); $t->string('name',500)->nullable(); $t->text('body')->nullable(); $t->string('html_url',500); $t->boolean('prerelease')->default(false); $t->boolean('draft')->default(false); $t->timestamp('published_at_github')->nullable(); $t->json('assets')->nullable(); $t->json('raw')->nullable(); $t->timestamps(); $t->unique(['github_repository_id','github_id']); $t->index(['github_repository_id','published_at_github']);
        });
        if (!Schema::hasTable('github_webhook_deliveries')) Schema::create('github_webhook_deliveries', function(Blueprint $t){
            $t->id(); $t->foreignId('software_project_id')->nullable()->constrained()->nullOnDelete(); $t->string('event',100); $t->string('delivery_id',180)->nullable()->unique(); $t->boolean('signature_valid')->default(false); $t->json('payload')->nullable(); $t->timestamp('processed_at')->nullable(); $t->timestamps(); $t->index(['event','created_at']);
        });
    }
    public function down(): void { Schema::dropIfExists('github_webhook_deliveries'); Schema::dropIfExists('github_releases'); Schema::dropIfExists('github_pull_requests'); Schema::dropIfExists('github_issues'); Schema::dropIfExists('github_contributors'); Schema::dropIfExists('github_repositories'); }
};
