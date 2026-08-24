<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('release_artifacts')) {
            Schema::create('release_artifacts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('release_id')->constrained('releases')->cascadeOnDelete();
                $table->string('purpose', 20)->default('INSTALLER');
                $table->string('package_type', 20)->nullable();
                $table->string('file_path', 500);
                $table->string('file_name', 255);
                $table->unsignedBigInteger('file_size')->default(0);
                $table->char('sha256', 64)->nullable();
                $table->boolean('is_primary')->default(false);
                $table->unsignedBigInteger('downloads_count')->default(0);
                $table->timestamps();
                $table->unique(['release_id', 'purpose'], 'release_artifact_release_purpose_uq');
                $table->index(['purpose', 'package_type'], 'release_artifact_purpose_type_idx');
            });
        }

        // Existing application releases already have a package in file_path.
        // Register that legacy package as the INSTALLER artifact so the new model
        // is backward-compatible without moving or duplicating the physical file.
        $rows = DB::table('releases')
            ->whereNotNull('file_path')
            ->whereNotNull('file_name')
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('release_artifacts')
                    ->whereColumn('release_artifacts.release_id', 'releases.id')
                    ->where('release_artifacts.purpose', 'INSTALLER');
            })
            ->get([
                'id', 'file_path', 'file_name', 'file_size', 'sha256',
            ]);

        foreach ($rows as $release) {
            DB::table('release_artifacts')->insert([
                'release_id' => $release->id,
                'purpose' => 'INSTALLER',
                'package_type' => strtolower(pathinfo((string) $release->file_name, PATHINFO_EXTENSION)) ?: null,
                'file_path' => $release->file_path,
                'file_name' => $release->file_name,
                'file_size' => (int) ($release->file_size ?? 0),
                'sha256' => $release->sha256,
                'is_primary' => true,
                'downloads_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('release_artifacts');
    }
};
