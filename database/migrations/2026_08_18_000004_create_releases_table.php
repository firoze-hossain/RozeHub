<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('releases')) {
            Schema::create('releases', function (Blueprint $table) {
                $table->id();
                $table->foreignId('software_project_id')->constrained()->cascadeOnDelete();
                $table->string('version');
                $table->string('platform');
                $table->string('architecture')->default('x64');
                $table->string('channel')->default('Stable');
                $table->string('file_path')->nullable();
                $table->string('file_name')->nullable();
                $table->unsignedBigInteger('file_size')->nullable();
                $table->text('notes')->nullable();
                $table->boolean('is_published')->default(false);
                $table->timestamp('published_at')->nullable();
                $table->unsignedBigInteger('downloads_count')->default(0);
                $table->timestamps();
            });
        }

        $hasReleaseIndex = collect(Schema::getIndexes('releases'))
            ->contains(fn (array $index) => $index['name'] === 'release_platform_version_unique');

        if (! $hasReleaseIndex) {
            Schema::table('releases', function (Blueprint $table) {
                $table->unique(['software_project_id', 'version', 'platform', 'architecture'], 'release_platform_version_unique');
            });
        }
    }

    public function down(): void { Schema::dropIfExists('releases'); }
};
