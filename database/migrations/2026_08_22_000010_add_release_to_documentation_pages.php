<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Return true when a foreign-key constraint with the supplied name exists.
     */
    private function foreignKeyExists(string $table, string $constraint): bool
    {
        return DB::table('information_schema.REFERENTIAL_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', $table)
            ->where('CONSTRAINT_NAME', $constraint)
            ->exists();
    }

    /**
     * Return true when an index with the supplied name exists.
     */
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
        $table = 'documentation_pages';
        $oldUnique = 'documentation_pages_software_project_id_slug_unique';
        $projectForeign = 'documentation_pages_software_project_id_foreign';
        $releaseForeign = 'documentation_pages_release_id_foreign';
        $releaseUnique = 'documentation_pages_project_release_slug_unique';

        /*
         * The first version of this migration added release_id before
         * changing the old unique index. If that migration stopped at the
         * index operation, release_id can already exist. Keep this migration
         * safe for that partially-applied state as well as a clean install.
         */
        if (!Schema::hasColumn($table, 'release_id')) {
            Schema::table($table, function (Blueprint $table) {
                $table->foreignId('release_id')
                    ->nullable()
                    ->after('software_project_id');
            });
        }

        /*
         * The release FK may already exist when the previous attempt reached
         * the first Schema::table() call. Remove it before rebuilding the
         * project/release indexes.
         */
        if ($this->foreignKeyExists($table, $releaseForeign)) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropForeign('documentation_pages_release_id_foreign');
            });
        }

        /*
         * MySQL can use the old project_id + slug unique index to satisfy the
         * existing project foreign key. Therefore the project FK must be
         * dropped BEFORE the old unique index is dropped.
         */
        if ($this->foreignKeyExists($table, $projectForeign)) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropForeign('documentation_pages_software_project_id_foreign');
            });
        }

        if ($this->indexExists($table, $oldUnique)) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropUnique('documentation_pages_software_project_id_slug_unique');
            });
        }

        /*
         * Recreate the project FK. The new composite index below is not
         * required for this FK, because MySQL can use its left-most
         * software_project_id column.
         */
        if (!$this->foreignKeyExists($table, $projectForeign)) {
            Schema::table($table, function (Blueprint $table) {
                $table->foreign('software_project_id', 'documentation_pages_software_project_id_foreign')
                    ->references('id')
                    ->on('software_projects')
                    ->cascadeOnDelete();
            });
        }

        if (!$this->indexExists($table, $releaseUnique)) {
            Schema::table($table, function (Blueprint $table) {
                $table->unique(
                    ['software_project_id', 'release_id', 'slug'],
                    'documentation_pages_project_release_slug_unique'
                );
            });
        }

        if (!$this->foreignKeyExists($table, $releaseForeign)) {
            Schema::table($table, function (Blueprint $table) {
                $table->foreign('release_id', 'documentation_pages_release_id_foreign')
                    ->references('id')
                    ->on('releases')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        $table = 'documentation_pages';
        $releaseForeign = 'documentation_pages_release_id_foreign';
        $projectForeign = 'documentation_pages_software_project_id_foreign';
        $releaseUnique = 'documentation_pages_project_release_slug_unique';
        $oldUnique = 'documentation_pages_software_project_id_slug_unique';

        if ($this->foreignKeyExists($table, $releaseForeign)) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropForeign('documentation_pages_release_id_foreign');
            });
        }

        if ($this->foreignKeyExists($table, $projectForeign)) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropForeign('documentation_pages_software_project_id_foreign');
            });
        }

        if ($this->indexExists($table, $releaseUnique)) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropUnique('documentation_pages_project_release_slug_unique');
            });
        }

        if (Schema::hasColumn($table, 'release_id')) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('release_id');
            });
        }

        if (!$this->indexExists($table, $oldUnique)) {
            Schema::table($table, function (Blueprint $table) {
                $table->unique(
                    ['software_project_id', 'slug'],
                    'documentation_pages_software_project_id_slug_unique'
                );
            });
        }

        if (!$this->foreignKeyExists($table, $projectForeign)) {
            Schema::table($table, function (Blueprint $table) {
                $table->foreign('software_project_id', 'documentation_pages_software_project_id_foreign')
                    ->references('id')
                    ->on('software_projects')
                    ->cascadeOnDelete();
            });
        }
    }
};
