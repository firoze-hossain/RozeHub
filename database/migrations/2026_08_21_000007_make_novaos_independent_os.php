<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Keep existing installations compatible while making NOVAOS a
        // first-class independent operating-system project.
        DB::table('software_projects')
            ->whereIn('slug', ['roze-os', 'novaos'])
            ->update([
                'name' => 'NOVAOS',
                'slug' => 'novaos',
                'tagline' => 'An independent operating system with limitless possibilities.',
                'description' => 'NOVAOS is an independent operating system project. It is distributed as its own operating-system builds rather than as a Windows, macOS, or Linux application.',
                'category' => 'Independent operating system',
                'icon' => 'N',
            ]);

        $novaosId = DB::table('software_projects')->where('slug', 'novaos')->value('id');

        if ($novaosId) {
            DB::table('releases')
                ->where('software_project_id', $novaosId)
                ->update(['platform' => 'NOVAOS']);
        }
    }

    public function down(): void
    {
        $novaosId = DB::table('software_projects')->where('slug', 'novaos')->value('id');

        if ($novaosId) {
            DB::table('releases')
                ->where('software_project_id', $novaosId)
                ->update(['platform' => 'Linux']);

            DB::table('software_projects')
                ->where('id', $novaosId)
                ->update([
                    'name' => 'Roze OS',
                    'slug' => 'roze-os',
                    'tagline' => 'A personal operating system experiment.',
                    'description' => 'A focused operating system environment built around the Roze developer ecosystem.',
                    'category' => 'Operating system',
                    'icon' => 'O',
                ]);
        }
    }
};
