<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('marketplace_items', 'capabilities')) {
            Schema::table('marketplace_items', function (Blueprint $table) {
                $table->json('capabilities')->nullable()->after('permissions');
                $table->json('compatibility')->nullable()->after('capabilities');
                $table->string('license', 80)->nullable()->after('repository_url');
                $table->string('support_url', 255)->nullable()->after('website');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('marketplace_items', 'capabilities')) {
            Schema::table('marketplace_items', function (Blueprint $table) {
                $table->dropColumn(['capabilities', 'compatibility', 'license', 'support_url']);
            });
        }
    }
};
