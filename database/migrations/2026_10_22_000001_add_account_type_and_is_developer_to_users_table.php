<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_developer')) {
                $table->boolean('is_developer')->default(false)->after('is_admin')->index();
            }
            if (!Schema::hasColumn('users', 'account_type')) {
                $table->string('account_type', 30)->default('user')->after('is_developer')->index();
            }
        });

        // Sync existing admin accounts
        DB::table('users')->where('is_admin', true)->update(['account_type' => 'admin']);

        // Check if publisher_profiles exist and sync developers
        if (Schema::hasTable('publisher_profiles')) {
            $developerUserIds = DB::table('publisher_profiles')->pluck('user_id');
            if ($developerUserIds->isNotEmpty()) {
                DB::table('users')
                    ->whereIn('id', $developerUserIds)
                    ->where('is_admin', false)
                    ->update([
                        'is_developer' => true,
                        'account_type' => 'developer',
                    ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_developer')) {
                $table->dropColumn('is_developer');
            }
            if (Schema::hasColumn('users', 'account_type')) {
                $table->dropColumn('account_type');
            }
        });
    }
};
