<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('marketplace_items', 'owner_user_id')) {
            Schema::table('marketplace_items', function (Blueprint $table) {
                $table->foreignId('owner_user_id')->nullable()->after('software_project_id')->constrained('users')->nullOnDelete();
                $table->index(['owner_user_id', 'is_published'], 'market_item_owner_pub_idx');
            });
        }

        if (!Schema::hasTable('marketplace_submissions')) {
            Schema::create('marketplace_submissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('marketplace_item_id')->constrained('marketplace_items')->cascadeOnDelete();
                $table->foreignId('marketplace_release_id')->nullable()->constrained('marketplace_releases')->nullOnDelete();
                $table->foreignId('submitted_by')->constrained('users')->cascadeOnDelete();
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->string('status', 30)->default('DRAFT');
                $table->string('risk_level', 20)->default('LOW');
                $table->unsignedSmallInteger('risk_score')->default(0);
                $table->string('risk_summary', 500)->nullable();
                $table->text('developer_message')->nullable();
                $table->text('reviewer_notes')->nullable();
                $table->text('decision_reason')->nullable();
                $table->unsignedInteger('resubmission_count')->default(0);
                $table->timestamp('submitted_at')->nullable();
                $table->timestamp('review_started_at')->nullable();
                $table->timestamp('reviewed_at')->nullable();
                $table->timestamp('published_at')->nullable();
                $table->timestamps();

                $table->index(['status', 'risk_level'], 'market_sub_status_risk_idx');
                $table->index(['submitted_by', 'status'], 'market_sub_owner_status_idx');
                $table->index(['marketplace_item_id', 'status'], 'market_sub_item_status_idx');
            });
        }

        if (!Schema::hasTable('marketplace_submission_risks')) {
            Schema::create('marketplace_submission_risks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('submission_id')->constrained('marketplace_submissions')->cascadeOnDelete();
                $table->string('category', 50);
                $table->string('status', 20)->default('PASS');
                $table->unsignedSmallInteger('score')->default(0);
                $table->string('summary', 255)->nullable();
                $table->text('notes')->nullable();
                $table->foreignId('checked_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('checked_at')->nullable();
                $table->timestamps();
                $table->unique(['submission_id', 'category'], 'market_risk_submission_category_uq');
            });
        }

        if (!Schema::hasTable('marketplace_submission_logs')) {
            Schema::create('marketplace_submission_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('submission_id')->constrained('marketplace_submissions')->cascadeOnDelete();
                $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('action', 40);
                $table->string('from_status', 30)->nullable();
                $table->string('to_status', 30)->nullable();
                $table->text('comment')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->index(['submission_id', 'created_at'], 'market_log_submission_created_idx');
            });
        }

        if (!Schema::hasTable('marketplace_notifications')) {
            Schema::create('marketplace_notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('submission_id')->nullable()->constrained('marketplace_submissions')->cascadeOnDelete();
                $table->string('type', 50);
                $table->string('title', 180);
                $table->text('message');
                $table->string('action_url', 500)->nullable();
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'read_at'], 'market_notif_user_read_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_notifications');
        Schema::dropIfExists('marketplace_submission_logs');
        Schema::dropIfExists('marketplace_submission_risks');
        Schema::dropIfExists('marketplace_submissions');
        if (Schema::hasColumn('marketplace_items', 'owner_user_id')) {
            Schema::table('marketplace_items', function (Blueprint $table) {
                $table->dropForeign(['owner_user_id']);
                $table->dropIndex('market_item_owner_pub_idx');
                $table->dropColumn('owner_user_id');
            });
        }
    }
};
