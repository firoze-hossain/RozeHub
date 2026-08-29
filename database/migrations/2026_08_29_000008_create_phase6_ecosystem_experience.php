<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // This migration is intentionally idempotent. MySQL DDL is not fully
        // transactional, so a failed run may leave tables behind.
        if (!Schema::hasTable('organizations')) {
            Schema::create('organizations', function (Blueprint $t) {
                $t->id();
                $t->foreignId('owner_user_id')->constrained('users')->cascadeOnDelete();
                $t->string('name');
                $t->string('slug')->unique();
                $t->string('logo_url')->nullable();
                $t->text('description')->nullable();
                $t->string('website')->nullable();
                $t->string('github_url')->nullable();
                $t->string('location')->nullable();
                $t->boolean('is_public')->default(true);
                $t->timestamps();
            });
        }

        if (!Schema::hasTable('organization_members')) {
            Schema::create('organization_members', function (Blueprint $t) {
                $t->id();
                $t->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
                $t->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $t->string('role', 30)->default('member');
                $t->timestamps();
                $t->unique(['organization_id', 'user_id']);
            });
        }

        if (!Schema::hasTable('organization_projects')) {
            Schema::create('organization_projects', function (Blueprint $t) {
                $t->id();
                $t->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
                $t->foreignId('software_project_id')->constrained('software_projects')->cascadeOnDelete();
                $t->string('role', 30)->default('maintainer');
                $t->timestamps();
                $t->unique(['organization_id', 'software_project_id']);
            });
        }

        if (!Schema::hasTable('ecosystem_nodes')) {
            Schema::create('ecosystem_nodes', function (Blueprint $t) {
                $t->id();
                $t->string('node_type', 40);
                $t->string('label');
                $t->string('slug')->nullable();
                $t->string('url')->nullable();
                $t->json('metadata')->nullable();
                $t->timestamps();
                $t->index(['node_type', 'slug']);
            });
        }

        if (!Schema::hasTable('ecosystem_edges')) {
            Schema::create('ecosystem_edges', function (Blueprint $t) {
                $t->id();
                $t->foreignId('source_node_id')->constrained('ecosystem_nodes')->cascadeOnDelete();
                $t->foreignId('target_node_id')->constrained('ecosystem_nodes')->cascadeOnDelete();
                $t->string('relationship', 50);
                $t->json('metadata')->nullable();
                $t->timestamps();
                $t->unique(
                    ['source_node_id', 'target_node_id', 'relationship'],
                    'ecosystem_edge_unique'
                );
            });
        }

        if (!Schema::hasTable('project_health_metrics')) {
            Schema::create('project_health_metrics', function (Blueprint $t) {
                $t->id();
                $t->foreignId('software_project_id')->constrained('software_projects')->cascadeOnDelete();
                $t->string('metric_key', 60);
                $t->decimal('weight', 6, 3)->default(1);
                $t->unsignedTinyInteger('score')->default(0);
                $t->json('metadata')->nullable();
                $t->timestamps();
                $t->unique(['software_project_id', 'metric_key']);
            });
        }

        if (!Schema::hasTable('project_health_snapshots')) {
            Schema::create('project_health_snapshots', function (Blueprint $t) {
                $t->id();
                $t->foreignId('software_project_id')->constrained('software_projects')->cascadeOnDelete();
                $t->unsignedTinyInteger('score')->default(0);
                $t->json('breakdown')->nullable();
                $t->timestamp('captured_at')->index();
                $t->index(['software_project_id', 'captured_at']);
            });
        }

        if (!Schema::hasTable('project_roadmaps')) {
            Schema::create('project_roadmaps', function (Blueprint $t) {
                $t->id();
                $t->foreignId('software_project_id')->constrained('software_projects')->cascadeOnDelete();
                $t->string('title');
                $t->text('description')->nullable();
                $t->string('status', 30)->default('active');
                $t->unsignedInteger('sort_order')->default(0);
                $t->timestamps();
            });
        }

        if (!Schema::hasTable('roadmap_items')) {
            Schema::create('roadmap_items', function (Blueprint $t) {
                $t->id();
                $t->foreignId('project_roadmap_id')->constrained('project_roadmaps')->cascadeOnDelete();
                $t->string('title');
                $t->text('description')->nullable();
                $t->string('status', 30)->default('planned');
                $t->string('priority', 20)->default('normal');
                $t->string('target_version')->nullable();
                $t->date('target_date')->nullable();
                $t->unsignedInteger('sort_order')->default(0);
                $t->timestamps();
            });
        }

        if (!Schema::hasTable('contributor_scores')) {
            Schema::create('contributor_scores', function (Blueprint $t) {
                $t->id();
                $t->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $t->unsignedInteger('points')->default(0);
                $t->unsignedInteger('merged_prs')->default(0);
                $t->unsignedInteger('issues')->default(0);
                $t->unsignedInteger('documentation')->default(0);
                $t->unsignedInteger('marketplace_items')->default(0);
                $t->timestamp('calculated_at')->nullable();
                $t->timestamps();
                $t->unique('user_id');
            });
        }

        if (!Schema::hasTable('contributor_activities')) {
            Schema::create('contributor_activities', function (Blueprint $t) {
                $t->id();
                $t->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $t->foreignId('software_project_id')->nullable()->constrained('software_projects')->nullOnDelete();
                $t->string('activity_type', 50);
                $t->string('source', 50)->nullable();
                $t->string('external_id')->nullable();
                $t->unsignedInteger('points')->default(0);
                $t->json('metadata')->nullable();
                $t->timestamp('occurred_at')->index();
                $t->timestamps();
                $t->index(['user_id', 'occurred_at']);
            });
        }

        if (!Schema::hasTable('user_project_interactions')) {
            Schema::create('user_project_interactions', function (Blueprint $t) {
                $t->id();
                $t->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $t->foreignId('software_project_id')->constrained('software_projects')->cascadeOnDelete();
                $t->string('event_type', 40);
                $t->unsignedInteger('count')->default(1);
                $t->timestamp('last_occurred_at')->index();
                $t->timestamps();

                // Explicit short name: MySQL limits identifiers to 64 characters.
                $t->index(
                    ['user_id', 'software_project_id', 'event_type'],
                    'user_project_event_idx'
                );
            });
        }

        if (!Schema::hasTable('recommendation_events')) {
            Schema::create('recommendation_events', function (Blueprint $t) {
                $t->id();
                $t->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $t->foreignId('software_project_id')->constrained('software_projects')->cascadeOnDelete();
                $t->string('reason', 100);
                $t->string('action')->nullable();
                $t->timestamps();
            });
        }
    }

    public function down(): void
    {
        foreach ([
            'recommendation_events',
            'user_project_interactions',
            'contributor_activities',
            'contributor_scores',
            'roadmap_items',
            'project_roadmaps',
            'project_health_snapshots',
            'project_health_metrics',
            'ecosystem_edges',
            'ecosystem_nodes',
            'organization_projects',
            'organization_members',
            'organizations',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
