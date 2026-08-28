<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('software_projects', 'github_url')) {
            Schema::table('software_projects', function (Blueprint $table) {
                $table->string('github_url', 500)->nullable()->after('website');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('software_projects', 'github_url')) {
            Schema::table('software_projects', function (Blueprint $table) {
                $table->dropColumn('github_url');
            });
        }
    }
};
