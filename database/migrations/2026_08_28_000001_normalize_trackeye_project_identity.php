<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $project = DB::table('software_projects')
            ->where('slug', 'trackline')
            ->orWhere('name', 'Trackline')
            ->first();

        if ($project) {
            DB::table('software_projects')
                ->where('id', $project->id)
                ->update([
                    'name' => 'TrackEye',
                    'slug' => 'trackeye',
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        $project = DB::table('software_projects')->where('slug', 'trackeye')->first();
        if ($project) {
            DB::table('software_projects')
                ->where('id', $project->id)
                ->update([
                    'name' => 'Trackline',
                    'slug' => 'trackline',
                    'updated_at' => now(),
                ]);
        }
    }
};
