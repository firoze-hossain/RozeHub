<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('project_ecosystem_profiles')) {
            Schema::create('project_ecosystem_profiles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('software_project_id')->unique()->constrained()->cascadeOnDelete();
                $table->string('ecosystem_type', 40);
                $table->string('title', 180);
                $table->text('description')->nullable();
                $table->json('item_types')->nullable();
                $table->json('capabilities')->nullable();
                $table->json('package_types')->nullable();
                $table->json('platforms')->nullable();
                $table->json('architectures')->nullable();
                $table->json('integration_targets')->nullable();
                $table->boolean('marketplace_enabled')->default(true);
                $table->boolean('community_contributions')->default(true);
                $table->boolean('moderation_required')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('project_ecosystem_profiles');
    }
};
