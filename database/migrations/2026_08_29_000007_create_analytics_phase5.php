<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('analytics_events', function(Blueprint $table){
   $table->id();
   $table->foreignId('software_project_id')->nullable()->constrained()->nullOnDelete();
   $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
   $table->string('event_type',60);
   $table->string('subject_type',180)->nullable();
   $table->unsignedBigInteger('subject_id')->nullable();
   $table->json('metadata')->nullable();
   $table->string('ip_hash',64)->nullable();
   $table->string('user_agent',500)->nullable();
   $table->timestamp('created_at')->index();
   $table->index(['software_project_id','event_type','created_at'],'analytics_project_type_time_idx');
   $table->index(['event_type','created_at'],'analytics_type_time_idx');
   $table->index(['subject_type','subject_id','created_at'],'analytics_subject_idx');
  });
 }
 public function down(): void { Schema::dropIfExists('analytics_events'); }
};
