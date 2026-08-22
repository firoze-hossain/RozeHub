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
        if (!Schema::hasColumn('releases', 'minimum_version')) {
            Schema::table('releases', function (Blueprint $table) {
                $table->string('minimum_version', 80)->nullable()->after('channel');
                $table->boolean('is_mandatory')->default(false)->after('minimum_version');
            });
        }

        /*
         * Do not create a five-column UNIQUE index here.
         * The existing releases table uses VARCHAR(255) for version/platform/
         * architecture/channel. With utf8mb4, a composite index over those
         * columns can exceed MySQL's 3072-byte InnoDB limit.
         *
         * Instead we use one fixed-width SHA-256 identity column. This gives us
         * an exact unique identity without truncating any release metadata.
         */
        if (!Schema::hasColumn('releases', 'release_identity_hash')) {
            Schema::table('releases', function (Blueprint $table) {
                $table->char('release_identity_hash', 64)->nullable()->after('is_mandatory');
            });
        }

        // Populate the identity for existing releases.
        DB::statement("\n            UPDATE releases\n            SET release_identity_hash = SHA2(\n                CONCAT_WS('|', software_project_id, version, platform, architecture, channel),\n                256\n            )\n            WHERE release_identity_hash IS NULL\n        ");

        // Remove the old identity index when MySQL permits it. Some existing
        // databases have a foreign-key dependency on that index, so keeping it
        // is safe and preferable to making the migration fail.
        $old = 'release_platform_version_unique';
        if ($this->indexExists('releases', $old)) {
            try {
                Schema::table('releases', function (Blueprint $table) use ($old) {
                    $table->dropUnique($old);
                });
            } catch (\Throwable $e) {
                if (!str_contains($e->getMessage(), 'needed in a foreign key constraint')) {
                    throw $e;
                }
            }
        }

        if (!$this->indexExists('releases', 'release_identity_unique')) {
            Schema::table('releases', function (Blueprint $table) {
                $table->unique('release_identity_hash', 'release_identity_unique');
            });
        }

        /*
         * Keep the lookup index deliberately small. The old five-column index
         * could also exceed MySQL's 3072-byte limit because several columns are
         * VARCHAR(255). The API filters the remaining fields after this index.
         */
        if (!$this->indexExists('releases', 'release_update_lookup_idx')) {
            Schema::table('releases', function (Blueprint $table) {
                $table->index(
                    ['software_project_id', 'is_published', 'platform'],
                    'release_update_lookup_idx'
                );
            });
        }
    }

    public function down(): void
    {
        if ($this->indexExists('releases', 'release_update_lookup_idx')) {
            Schema::table('releases', fn (Blueprint $table) => $table->dropIndex('release_update_lookup_idx'));
        }

        if ($this->indexExists('releases', 'release_identity_unique')) {
            Schema::table('releases', fn (Blueprint $table) => $table->dropUnique('release_identity_unique'));
        }

        if (Schema::hasColumn('releases', 'release_identity_hash')) {
            Schema::table('releases', function (Blueprint $table) {
                $table->dropColumn('release_identity_hash');
            });
        }

        if (Schema::hasColumn('releases', 'minimum_version')) {
            Schema::table('releases', function (Blueprint $table) {
                $table->dropColumn(['minimum_version', 'is_mandatory']);
            });
        }
    }
};
