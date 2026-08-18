<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('software_projects', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('tagline');
            $table->text('description');
            $table->string('category');
            $table->string('accent', 24)->default('mint');
            $table->string('icon', 8)->default('R');
            $table->string('website')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('software_projects'); }
};
