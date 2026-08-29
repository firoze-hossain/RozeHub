<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void { if (!Schema::hasColumn('project_ecosystem_profiles','channels')) Schema::table('project_ecosystem_profiles', fn(Blueprint $t)=>$t->json('channels')->nullable()->after('architectures')); }
    public function down(): void { if (Schema::hasColumn('project_ecosystem_profiles','channels')) Schema::table('project_ecosystem_profiles', fn(Blueprint $t)=>$t->dropColumn('channels')); }
};
