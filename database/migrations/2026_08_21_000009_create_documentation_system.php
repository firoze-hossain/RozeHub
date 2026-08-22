<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
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
        if (! Schema::hasTable('documentation_sections')) {
            Schema::create('documentation_sections', function (Blueprint $table) {
                $table->id();
                $table->foreignId('software_project_id')
                    ->constrained('software_projects')
                    ->cascadeOnDelete();
                $table->string('title', 160);
                $table->string('slug', 180);
                $table->string('description', 500)->nullable();
                $table->string('icon', 20)->default('◈');
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_published')->default(true);
                $table->timestamps();

                // Explicit names keep MySQL identifiers below 64 characters.
                $table->unique(
                    ['software_project_id', 'slug'],
                    'documentation_sections_project_slug_unique'
                );
                $table->index(
                    ['software_project_id', 'sort_order'],
                    'doc_sections_project_sort_idx'
                );
            });
        }

        if (! Schema::hasTable('documentation_pages')) {
            Schema::create('documentation_pages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('software_project_id')
                    ->constrained('software_projects')
                    ->cascadeOnDelete();
                $table->foreignId('documentation_section_id')
                    ->nullable()
                    ->constrained('documentation_sections')
                    ->nullOnDelete();
                $table->string('title', 180);
                $table->string('slug', 200);
                $table->string('kind', 40)->default('guide');
                $table->string('version', 50)->nullable();
                $table->string('summary', 500)->nullable();
                $table->longText('content');
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_published')->default(true);
                $table->timestamps();

                // Kept under this exact name because migration 000010 upgrades it.
                $table->unique(
                    ['software_project_id', 'slug'],
                    'documentation_pages_software_project_id_slug_unique'
                );
                $table->index(
                    ['software_project_id', 'is_published', 'sort_order'],
                    'doc_pages_project_pub_sort_idx'
                );
            });
        }

        // Repair a table that was created by an older package without the indexes.
        if (Schema::hasTable('documentation_sections') && ! $this->indexExists('documentation_sections', 'documentation_sections_project_slug_unique')) {
            Schema::table('documentation_sections', function (Blueprint $table) {
                $table->unique(
                    ['software_project_id', 'slug'],
                    'documentation_sections_project_slug_unique'
                );
            });
        }

        if (Schema::hasTable('documentation_pages') && ! $this->indexExists('documentation_pages', 'documentation_pages_software_project_id_slug_unique')) {
            Schema::table('documentation_pages', function (Blueprint $table) {
                $table->unique(
                    ['software_project_id', 'slug'],
                    'documentation_pages_software_project_id_slug_unique'
                );
            });
        }

        if (Schema::hasTable('documentation_pages') && ! $this->indexExists('documentation_pages', 'doc_pages_project_pub_sort_idx')) {
            Schema::table('documentation_pages', function (Blueprint $table) {
                $table->index(
                    ['software_project_id', 'is_published', 'sort_order'],
                    'doc_pages_project_pub_sort_idx'
                );
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('documentation_pages');
        Schema::dropIfExists('documentation_sections');
    }
};
