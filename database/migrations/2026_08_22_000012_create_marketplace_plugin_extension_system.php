<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function indexExists(string $table, string $index): bool
    {
        return DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', $table)
            ->where('INDEX_NAME', $index)
            ->exists();
    }

    public function up(): void
    {
        if (!Schema::hasTable('marketplace_items')) {
            Schema::create('marketplace_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('software_project_id')->constrained()->cascadeOnDelete();
                $table->string('item_type', 20)->default('plugin'); // plugin | extension
                $table->string('name', 160);
                $table->string('slug', 120);
                $table->string('item_id', 160);
                $table->string('vendor', 160)->nullable();
                $table->string('category', 100)->nullable();
                $table->string('icon_path', 255)->nullable();
                $table->string('website', 255)->nullable();
                $table->string('repository_url', 255)->nullable();
                $table->string('summary', 500)->nullable();
                $table->text('description')->nullable();
                $table->json('permissions')->nullable();
                $table->boolean('is_official')->default(false);
                $table->boolean('is_verified')->default(false);
                $table->boolean('is_published')->default(false);
                $table->unsignedBigInteger('downloads_count')->default(0);
                $table->timestamps();

                $table->index(['software_project_id', 'item_type'], 'market_item_project_type_idx');
                $table->index(['software_project_id', 'is_published'], 'market_item_project_pub_idx');
                $table->unique(['software_project_id', 'slug'], 'market_item_project_slug_uq');
                $table->unique(['software_project_id', 'item_id'], 'market_item_project_id_uq');
            });
        }

        if (!Schema::hasTable('marketplace_releases')) {
            Schema::create('marketplace_releases', function (Blueprint $table) {
                $table->id();
                $table->foreignId('marketplace_item_id')->constrained('marketplace_items')->cascadeOnDelete();
                $table->string('version', 80);
                $table->string('platform', 30)->default('All');
                $table->string('architecture', 20)->default('All');
                $table->string('channel', 20)->default('Stable');
                $table->string('minimum_app_version', 80)->nullable();
                $table->string('maximum_app_version', 80)->nullable();
                $table->string('package_type', 30)->default('zip');
                $table->string('file_path', 500)->nullable();
                $table->string('file_name', 255)->nullable();
                $table->unsignedBigInteger('file_size')->nullable();
                $table->char('sha256', 64)->nullable();
                $table->char('release_identity_hash', 64)->nullable();
                $table->text('release_notes')->nullable();
                $table->json('dependencies')->nullable();
                $table->boolean('is_mandatory')->default(false);
                $table->boolean('is_published')->default(false);
                $table->timestamp('published_at')->nullable();
                $table->unsignedBigInteger('downloads_count')->default(0);
                $table->timestamps();

                $table->index(['marketplace_item_id', 'is_published'], 'market_rel_item_pub_idx');
                $table->index(['marketplace_item_id', 'platform'], 'market_rel_item_platform_idx');
                $table->unique('release_identity_hash', 'market_rel_identity_uq');
            });
        }

        if (!Schema::hasTable('marketplace_dependencies')) {
            Schema::create('marketplace_dependencies', function (Blueprint $table) {
                $table->id();
                $table->foreignId('marketplace_release_id')->constrained('marketplace_releases')->cascadeOnDelete();
                $table->foreignId('dependency_item_id')->constrained('marketplace_items')->cascadeOnDelete();
                $table->string('minimum_version', 80)->nullable();
                $table->boolean('optional')->default(false);
                $table->timestamps();

                $table->unique(
                    ['marketplace_release_id', 'dependency_item_id'],
                    'market_dep_release_item_uq'
                );
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_dependencies');
        Schema::dropIfExists('marketplace_releases');
        Schema::dropIfExists('marketplace_items');
    }
};
