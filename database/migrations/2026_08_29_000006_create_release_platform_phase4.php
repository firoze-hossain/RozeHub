<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('releases', function (Blueprint $table) {
            $table->string('source', 20)->default('MANUAL')->after('channel');
            $table->foreignId('github_release_id')->nullable()->after('source')->constrained('github_releases')->nullOnDelete();
            $table->string('processing_status', 20)->default('READY')->after('downloads_count');
            $table->text('processing_error')->nullable()->after('processing_status');
            $table->string('signature_status', 20)->default('NOT_CONFIGURED')->after('processing_error');
            $table->string('signature_algorithm', 40)->nullable()->after('signature_status');
            $table->string('signature_path', 500)->nullable()->after('signature_algorithm');
            $table->timestamp('verified_at')->nullable()->after('signature_path');
            $table->string('health_status', 20)->default('UNKNOWN')->after('verified_at');
            $table->timestamp('health_checked_at')->nullable()->after('health_status');
            $table->foreignId('rollback_of_release_id')->nullable()->after('health_checked_at')->constrained('releases')->nullOnDelete();
            $table->timestamp('rolled_back_at')->nullable()->after('rollback_of_release_id');
            $table->unsignedTinyInteger('rollout_percentage')->default(100)->after('rolled_back_at');
            $table->index(['software_project_id', 'channel', 'is_published', 'processing_status'], 'release_distribution_idx');
        });

        Schema::create('release_channels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('software_project_id')->constrained()->cascadeOnDelete();
            $table->string('key', 30);
            $table->string('name', 80);
            $table->text('description')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->boolean('is_default')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['software_project_id', 'key']);
        });

        Schema::create('release_update_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('release_id')->constrained('releases')->cascadeOnDelete();
            $table->string('type', 40)->default('release_available');
            $table->string('message', 500);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'release_id', 'type'], 'release_notification_unique');
            $table->index(['user_id', 'read_at']);
        });

        foreach (DB::table('software_projects')->pluck('id') as $projectId) {
            foreach ([
                ['stable', 'Stable', 'Production-ready releases.', 1, 0],
                ['beta', 'Beta', 'Preview releases for testers.', 1, 1],
                ['nightly', 'Nightly', 'Frequent development builds.', 1, 2],
            ] as $channel) {
                DB::table('release_channels')->updateOrInsert(
                    ['software_project_id' => $projectId, 'key' => $channel[0]],
                    ['name' => $channel[1], 'description' => $channel[2], 'is_enabled' => $channel[3], 'is_default' => $channel[0] === 'stable', 'sort_order' => $channel[4], 'created_at' => now(), 'updated_at' => now()]
                );
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('release_update_notifications');
        Schema::dropIfExists('release_channels');
        Schema::table('releases', function (Blueprint $table) {
            $table->dropForeign(['github_release_id']);
            $table->dropForeign(['rollback_of_release_id']);
            $table->dropIndex('release_distribution_idx');
            $table->dropColumn(['source','github_release_id','processing_status','processing_error','signature_status','signature_algorithm','signature_path','verified_at','health_status','health_checked_at','rollback_of_release_id','rolled_back_at','rollout_percentage']);
        });
    }
};
