<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('releases', function (Blueprint $table) {
            $table->string('major_version', 32)->nullable()->after('version');
            $table->string('codename', 80)->nullable()->after('major_version');
            $table->string('build_number', 80)->nullable()->after('codename');
            $table->string('sha256', 64)->nullable()->after('file_size');
        });
    }

    public function down(): void
    {
        Schema::table('releases', function (Blueprint $table) {
            $table->dropColumn(['major_version', 'codename', 'build_number', 'sha256']);
        });
    }
};
