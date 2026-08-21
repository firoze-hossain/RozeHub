<?php

namespace Database\Seeders;

use App\Models\Release;
use App\Models\Review;
use App\Models\SoftwareProject;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@example.com')],
            [
                'name' => 'RozeHub Administrator',
                'password' => env('ADMIN_PASSWORD', 'ChangeThisStrongPassword!'),
                'is_admin' => true,
            ]
        );

        $projects = [
            ['name' => 'DBNavigator', 'slug' => 'dbnavigator', 'tagline' => 'A focused database client for serious data work.', 'description' => 'Browse schemas, write queries, and understand your data without leaving your flow.', 'category' => 'Database client', 'accent' => 'mint', 'icon' => 'D', 'version' => '1.4.0', 'platform' => 'Windows', 'downloads' => 1842, 'notes' => 'Query history, connection profiles, and a faster table explorer.'],
            ['name' => 'ThunderCall', 'slug' => 'thundercall', 'tagline' => 'API testing with a calmer, faster workflow.', 'description' => 'Build requests, inspect responses, and share repeatable collections with your team.', 'category' => 'API client', 'accent' => 'coral', 'icon' => 'T', 'version' => '0.9.2', 'platform' => 'Linux', 'downloads' => 963, 'notes' => 'Collections now support environment variables and importable request groups.'],
            ['name' => 'StratosDB', 'slug' => 'stratosdb', 'tagline' => 'A database engine designed for clear intent.', 'description' => 'An experimental engine for reliable local data systems and developer-first control.', 'category' => 'Database engine', 'accent' => 'gold', 'icon' => 'S', 'version' => '0.5.1', 'platform' => 'Linux', 'downloads' => 427, 'notes' => 'Improved storage recovery and added an inspect command.'],
            ['name' => 'Lumina', 'slug' => 'lumina', 'tagline' => 'An IDE that keeps the code in focus.', 'description' => 'A lightweight home for projects, terminals, debugging, and the Roze language.', 'category' => 'Development environment', 'accent' => 'lilac', 'icon' => 'L', 'version' => '1.1.0', 'platform' => 'macOS', 'downloads' => 2135, 'notes' => 'New workspace search and a redesigned extension manager.'],
            ['name' => 'Roze', 'slug' => 'roze-language', 'tagline' => 'A language for understandable systems software.', 'description' => 'The compiler, package tooling, and language runtime for building in Roze.', 'category' => 'Programming language', 'accent' => 'blue', 'icon' => 'R', 'version' => '0.8.0', 'platform' => 'Windows', 'downloads' => 713, 'notes' => 'Pattern matching and improved diagnostics are now available.'],
            ['name' => 'NOVAOS', 'slug' => 'novaos', 'tagline' => 'A personal operating system experiment.', 'description' => 'A focused operating system environment built around the Roze developer ecosystem.', 'category' => 'Independent operating system', 'accent' => 'mint', 'icon' => 'N', 'version' => '0.3.0', 'platform' => 'NOVAOS', 'downloads' => 289, 'notes' => 'Hardware detection and the initial installer experience.'],
            ['name' => 'Trackline', 'slug' => 'trackline', 'tagline' => 'Activity visibility without the noise.', 'description' => 'A considered employee activity tracker for teams that need clear, respectful reporting.', 'category' => 'Workplace operations', 'accent' => 'coral', 'icon' => 'E', 'version' => '2.0.0', 'platform' => 'Windows', 'downloads' => 1568, 'notes' => 'New activity summary exports and configurable capture schedules.'],
        ];

        foreach ($projects as $item) {
            $project = SoftwareProject::updateOrCreate(['slug' => $item['slug']], collect($item)->except(['version', 'platform', 'downloads', 'notes'])->all());
            Release::updateOrCreate([
                'software_project_id' => $project->id, 'version' => $item['version'], 'platform' => $item['platform'], 'architecture' => 'x64',
            ], ['channel' => 'Stable', 'is_published' => true, 'published_at' => now()->subDays(rand(2, 30)), 'downloads_count' => $item['downloads'], 'notes' => $item['notes']]);
        }

        $this->call(DocumentationSeeder::class);

        $navigator = SoftwareProject::where('slug', 'dbnavigator')->first();
        Review::updateOrCreate(['software_project_id' => $navigator->id, 'author_name' => 'Mina R.'], ['rating' => 5, 'body' => 'The schema view is exactly the kind of calm visual feedback I want while exploring an unfamiliar database.', 'is_approved' => true]);
        Review::updateOrCreate(['software_project_id' => $navigator->id, 'author_name' => 'Dev K.'], ['rating' => 4, 'body' => 'Fast to open and pleasantly direct. I would like to see more import options in a future release.', 'is_approved' => true]);
    }
}
