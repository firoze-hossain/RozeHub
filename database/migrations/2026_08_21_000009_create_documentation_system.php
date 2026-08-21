<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('documentation_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('software_project_id')->constrained('software_projects')->cascadeOnDelete();
            $table->string('title', 160);
            $table->string('slug', 180);
            $table->string('description', 500)->nullable();
            $table->string('icon', 20)->default('◈');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
            $table->unique(['software_project_id', 'slug']);
            $table->index(['software_project_id', 'sort_order']);
        });

        Schema::create('documentation_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('software_project_id')->constrained('software_projects')->cascadeOnDelete();
            $table->foreignId('documentation_section_id')->nullable()->constrained('documentation_sections')->nullOnDelete();
            $table->string('title', 180);
            $table->string('slug', 200);
            $table->string('kind', 40)->default('guide');
            $table->string('version', 50)->nullable();
            $table->string('summary', 500)->nullable();
            $table->longText('content');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
            $table->unique(['software_project_id', 'slug']);
            $table->index(['software_project_id', 'is_published', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentation_pages');
        Schema::dropIfExists('documentation_sections');
    }
};
