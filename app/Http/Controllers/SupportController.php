<?php

namespace App\Http\Controllers;

use App\Models\SoftwareProject;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index(Request $request)
    {
        $projects = SoftwareProject::query()
            ->withCount(['documentationPages as published_docs_count' => fn ($q) => $q->where('is_published', true)])
            ->orderBy('name')
            ->get();

        return view('support.index', compact('projects'));
    }

    public function project(Request $request, SoftwareProject $project)
    {
        $allProjects = SoftwareProject::orderBy('name')->get();
        $faqs = $this->getFaqsForProject($project->slug);

        return view('support.project', compact('project', 'allProjects', 'faqs'));
    }

    private function getFaqsForProject(string $slug): array
    {
        return match ($slug) {
            'thundercall' => [
                [
                    'q' => 'How do I fix "Connection Refused" when testing localhost APIs?',
                    'a' => 'Verify that your backend service (e.g. Spring Boot, Node.js, FastAPI) is running and bound to 0.0.0.0 or 127.0.0.1. If using Docker, ensure the port mapping (e.g. -p 8080:8080) is properly exposed to the host machine.'
                ],
                [
                    'q' => 'How does "Clear Cache and Reload" work?',
                    'a' => 'Under Help > Clear Cache and Reload, ThunderCall safely flushes transient in-memory caches (collection variables, comment threads, schema introspections, and temporary response bodies) and refetches all collections and environments freshly from the backend server. Unsaved edits in open tabs will prompt before being discarded.'
                ],
                [
                    'q' => 'How do I use dynamic environment variables?',
                    'a' => 'Wrap variable names with double curly braces like {{baseUrl}} or {{token}} in URLs, headers, or request bodies. ThunderCall will resolve them hierarchically: Local → Environment → Collection → Global.'
                ],
                [
                    'q' => 'Can I import my existing Postman collections?',
                    'a' => 'Yes! ThunderCall natively supports the Postman Collection v2.1.0 schema. Click File > Import… or the top bar Import button and select your exported JSON file.'
                ],
                [
                    'q' => 'How do I handle self-signed SSL certificates?',
                    'a' => 'Open File > Settings… and toggle "SSL Certificate Verification" off for local and staging environments.'
                ],
            ],
            'novaos' => [
                [
                    'q' => 'How do I verify a downloaded NOVAOS image?',
                    'a' => 'Compare the SHA-256 checksum of your downloaded ISO/IMG image with the official hash published in the RozeHub releases section before flashing to a bootable drive.'
                ],
                [
                    'q' => 'What architectures are supported by NOVAOS?',
                    'a' => 'NOVAOS ships separate optimized builds for x86_64 (Intel/AMD) and AArch64 (ARM64) platforms.'
                ],
                [
                    'q' => 'How do I report a kernel panic or hardware driver issue?',
                    'a' => 'Collect the dmesg output or system log and file an issue on the NOVAOS GitHub repository.'
                ],
            ],
            'stratosdb' => [
                [
                    'q' => 'How do I configure StratosDB storage paths?',
                    'a' => 'Edit stratosdb.conf to set the data directory and write-ahead log (WAL) location before starting the daemon.'
                ],
                [
                    'q' => 'Does StratosDB support ACID transactions?',
                    'a' => 'Yes, StratosDB provides strict ACID guarantees with multi-version concurrency control (MVCC).'
                ],
            ],
            'lumina' => [
                [
                    'q' => 'How do I install plugins or language servers in Lumina?',
                    'a' => 'Use the integrated Lumina Extension Marketplace or configure LSP servers in ~/.lumina/config.json.'
                ],
                [
                    'q' => 'How do I configure keybindings?',
                    'a' => 'Lumina supports VS Code, Sublime, and Vim keymap emulations selectable in Preferences > Keymaps.'
                ],
            ],
            'roze-language' => [
                [
                    'q' => 'How do I compile and run a Roze program?',
                    'a' => 'Run "roze build main.rz" to compile to a native binary, or "roze run main.rz" for instant JIT/interpreter execution.'
                ],
                [
                    'q' => 'Where can I find the standard library documentation?',
                    'a' => 'Check the Roze documentation under /docs/roze-language for complete standard library APIs.'
                ],
            ],
            default => [
                [
                    'q' => 'Where do I find official guides and reference manuals?',
                    'a' => 'Visit the documentation section at /docs for full versioned manuals and setup guides.'
                ],
                [
                    'q' => 'How do I report a bug or suggest a feature?',
                    'a' => 'Submit an issue directly on the project\'s official GitHub repository.'
                ],
                [
                    'q' => 'How do I check for the latest updates?',
                    'a' => 'Check the RozeHub releases page or use your desktop application\'s built-in update checker.'
                ],
            ]
        };
    }
}
