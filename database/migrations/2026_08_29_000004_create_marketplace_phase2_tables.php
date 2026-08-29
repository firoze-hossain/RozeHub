<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('publisher_profiles')) {
            Schema::create('publisher_profiles', function (Blueprint $t) {
                $t->id(); $t->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
                $t->string('display_name',160); $t->string('slug',180)->unique();
                $t->string('avatar_url',500)->nullable(); $t->string('website',500)->nullable();
                $t->string('github_url',500)->nullable(); $t->text('bio')->nullable();
                $t->boolean('is_verified')->default(false); $t->timestamps();
            });
        }
        if (!Schema::hasTable('marketplace_categories')) {
            Schema::create('marketplace_categories', function (Blueprint $t) {
                $t->id(); $t->foreignId('software_project_id')->constrained()->cascadeOnDelete();
                $t->string('name',100); $t->string('slug',120); $t->string('description',500)->nullable();
                $t->string('icon',80)->nullable(); $t->unsignedInteger('sort_order')->default(0); $t->boolean('is_active')->default(true); $t->timestamps();
                $t->unique(['software_project_id','slug']); $t->index(['software_project_id','is_active']);
            });
        }
        if (!Schema::hasTable('marketplace_reviews')) {
            Schema::create('marketplace_reviews', function (Blueprint $t) {
                $t->id(); $t->foreignId('marketplace_item_id')->constrained()->cascadeOnDelete();
                $t->foreignId('user_id')->constrained()->cascadeOnDelete();
                $t->unsignedTinyInteger('rating'); $t->string('title',180)->nullable(); $t->text('body')->nullable();
                $t->boolean('is_approved')->default(true); $t->timestamps();
                $t->unique(['marketplace_item_id','user_id']); $t->index(['marketplace_item_id','is_approved']);
            });
        }
        if (!Schema::hasColumn('marketplace_items','manifest')) {
            Schema::table('marketplace_items', function (Blueprint $t) { $t->json('manifest')->nullable()->after('compatibility'); $t->string('manifest_version',30)->nullable()->after('manifest'); });
        }
        if (!Schema::hasColumn('marketplace_releases','manifest')) {
            Schema::table('marketplace_releases', function (Blueprint $t) { $t->json('manifest')->nullable()->after('dependencies'); $t->string('manifest_version',30)->nullable()->after('manifest'); $t->string('package_format',30)->nullable()->after('package_type'); });
        }
    }
    public function down(): void
    {
        if (Schema::hasColumn('marketplace_releases','manifest')) Schema::table('marketplace_releases', fn(Blueprint $t)=>$t->dropColumn(['manifest','manifest_version','package_format']));
        if (Schema::hasColumn('marketplace_items','manifest')) Schema::table('marketplace_items', fn(Blueprint $t)=>$t->dropColumn(['manifest','manifest_version']));
        Schema::dropIfExists('marketplace_reviews'); Schema::dropIfExists('marketplace_categories'); Schema::dropIfExists('publisher_profiles');
    }
};
