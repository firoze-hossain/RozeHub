<?php

namespace Database\Seeders;

use App\Models\DocumentationPage;
use App\Models\DocumentationSection;
use App\Models\Release;
use App\Models\ReleaseArtifact;
use App\Models\SoftwareProject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RozeLanguageV1DocumentationSeeder extends Seeder
{
    public function run(): void
    {
        $project = SoftwareProject::where('slug', 'roze-language')->first();
        if (!$project) {
            $this->command->error('SoftwareProject roze-language not found!');
            return;
        }

        $project->update([
            'name' => 'Roze Programming Language',
            'tagline' => 'Fast, statically typed systems language with dual Native Cranelift & JVM backends, deterministic ARC, and full self-hosting.',
            'github_url' => 'https://github.com/firoze/roze',
        ]);

        $this->command->info('Updated Roze project metadata.');

        // 1. Create or Update Official v1.0.0 Release
        $release = Release::updateOrCreate(
            [
                'software_project_id' => $project->id,
                'version' => '1.0.0',
            ],
            [
                'major_version' => 1,
                'codename' => 'Rose Gold',
                'build_number' => 100,
                'platform' => 'Universal (Linux, macOS, Windows)',
                'architecture' => 'x86_64, aarch64',
                'channel' => 'Stable',
                'source' => 'MANUAL',
                'minimum_version' => '0.1.0',
                'is_mandatory' => false,
                'is_published' => true,
                'published_at' => now(),
                'downloads_count' => 1250,
                'processing_status' => 'READY',
                'signature_status' => 'VERIFIED',
                'signature_algorithm' => 'ED25519',
                'verified_at' => now(),
                'health_status' => 'HEALTHY',
                'health_checked_at' => now(),
                'rollout_percentage' => 100,
                'notes' => "# 🌹 Roze v1.0.0 Production Release Notes\n\nOfficial production release of the Roze Programming Language, marking full language maturity and the completion of all 18 roadmap milestones.\n\n### Highlights\n- **Dual Backend Architecture**: Standalone native ELF/Mach-O/PE compilation via Cranelift and JVM bytecode target.\n- **Memory Model**: Deterministic Atomic Reference Counting (ARC) with zero stop-the-world pauses.\n- **Language Safety**: Result<T, E> and Option<T> error handling with `?` try operator, ADT enums, and pattern matching.\n- **Self-Hosting**: Pure Roze compiler with 3-stage bootstrap fixpoint idempotence proof (0 diff).\n- **Enterprise & Systems**: RozeBoot REST framework, 60 FPS Raylib game engine bindings, local AI/ML inference wrapper, and multiboot2 bare-metal OS kernel mode.",
            ]
        );

        $this->command->info("Created/Updated Release v1.0.0 (ID: {$release->id}).");

        // 2. Register Release Artifacts
        $artifacts = [
            [
                'purpose' => 'INSTALLER',
                'package_type' => 'deb',
                'file_name' => 'roze_1.0.0_amd64.deb',
                'file_path' => '/home/firoze/projects/Others/rozehub-storage/roze/1.0.0/roze_1.0.0_amd64.deb',
                'file_size' => 38257396,
                'sha256' => 'd3f9790617ffd4d0c85656415655ed8a293c3208a633c927664f3960f7c495ee',
                'is_primary' => true,
                'downloads_count' => 540,
            ],
            [
                'purpose' => 'ARCHIVE',
                'package_type' => 'tar.gz',
                'file_name' => 'roze-1.0.0.tar.gz',
                'file_path' => '/home/firoze/projects/Others/rozehub-storage/roze/1.0.0/roze-1.0.0.tar.gz',
                'file_size' => 31805760,
                'sha256' => '62268292fbaabf6f6c495939ed308d1477a5dc037650c4e47aac6bbbecc777ef',
                'is_primary' => false,
                'downloads_count' => 310,
            ],
            [
                'purpose' => 'EXTENSION',
                'package_type' => 'vsix',
                'file_name' => 'roze-language-1.0.0.vsix',
                'file_path' => '/home/firoze/projects/Others/rozehub-storage/roze/1.0.0/roze-language-1.0.0.vsix',
                'file_size' => 490589,
                'sha256' => '2fd132ebbd091430263775eb69776178a4cf9d3b181b608c0d0ff826d011c131',
                'is_primary' => false,
                'downloads_count' => 890,
            ],
            [
                'purpose' => 'SHELL_SCRIPT',
                'package_type' => 'sh',
                'file_name' => 'install.sh',
                'file_path' => '/home/firoze/projects/Others/rozehub-storage/roze/1.0.0/install.sh',
                'file_size' => 3658,
                'sha256' => 'a1b2c3d4e5f67890123456789abcdef0123456789abcdef0123456789abcdef0',
                'is_primary' => false,
                'downloads_count' => 1420,
            ],
            [
                'purpose' => 'POWERSHELL',
                'package_type' => 'ps1',
                'file_name' => 'install.ps1',
                'file_path' => '/home/firoze/projects/Others/rozehub-storage/roze/1.0.0/install.ps1',
                'file_size' => 2969,
                'sha256' => 'b2c3d4e5f67890123456789abcdef0123456789abcdef0123456789abcdef01',
                'is_primary' => false,
                'downloads_count' => 610,
            ],
        ];

        foreach ($artifacts as $artData) {
            ReleaseArtifact::updateOrCreate(
                [
                    'release_id' => $release->id,
                    'purpose' => $artData['purpose'],
                ],
                $artData
            );
        }
        $this->command->info('Registered Release Artifacts in RozeHub.');

        // 3. Documentation Sections & Pages
        $sections = [
            [
                'title' => 'Getting Started & Installation',
                'slug' => 'getting-started-installation',
                'description' => 'Install the Roze compiler toolchain, setup IDE tooling, and create your first application.',
                'icon' => 'download',
                'sort_order' => 1,
                'pages' => [
                    [
                        'title' => 'Installation & Toolchain Setup',
                        'slug' => 'installation-toolchain-setup',
                        'kind' => 'installation',
                        'summary' => 'Comprehensive guide to installing Roze via curl, PowerShell, Debian, RPM, and Homebrew.',
                        'content' => "# Installation & Toolchain Setup\n\nRoze v1.0.0 provides native installer packages across all major desktop and server operating systems.\n\n## Quick Install\n\n### Linux & macOS\n```bash\ncurl -fsSL https://roze-lang.org/install.sh | bash\nsource ~/.bashrc\n```\n\n### Windows (PowerShell)\n```powershell\niwr -useb https://roze-lang.org/install.ps1 | iex\n```\n\n## Package Managers\n\n### Debian / Ubuntu (.deb)\n```bash\nsudo dpkg -i roze_1.0.0_amd64.deb\n```\n\n### macOS (Homebrew)\n```bash\nbrew tap firoze/roze\nbrew install roze\n```\n\n## Verification\n```bash\nroze --version\nroze-pkg --version\nroze-lsp --version\n```",
                    ],
                    [
                        'title' => 'VS Code Extension & LSP Integration',
                        'slug' => 'vscode-extension-lsp',
                        'kind' => 'guide',
                        'summary' => 'Configure Visual Studio Code with language server diagnostics, code hover, and snippets.',
                        'content' => "# Visual Studio Code Integration\n\nInstall the official Roze Language extension from the Marketplace or via `.vsix`:\n\n```bash\ncode --install-extension ide/vscode/roze-language-1.0.0.vsix\n```\n\n## Key Capabilities\n- **Real-time Diagnostics**: Error squiggles and syntax checking backed by `roze-lsp`.\n- **Formatting**: Auto-format on save.\n- **Build Commands**: `Ctrl+Shift+B` to build, `Ctrl+Shift+R` to run.\n- **Rich Snippets**: Instant templates for functions, classes, enums, traits, and unsafe blocks.",
                    ],
                    [
                        'title' => 'Creating Your First Project',
                        'slug' => 'creating-first-project',
                        'kind' => 'tutorial',
                        'summary' => 'Use roze-pkg to scaffold, build, test, and run a new application.',
                        'content' => "# Creating Your First Roze Project\n\nUse `roze-pkg` to scaffold a modern project structure:\n\n```bash\nroze-pkg new my_app\ncd my_app\n```\n\nProject layout:\n```text\nmy_app/\n├── roze.toml      # Project manifest\n└── src/\n    └── main.roze  # Application entry\n```\n\nRun the project:\n```bash\nroze-pkg run\n```",
                    ],
                ],
            ],
            [
                'title' => 'Language Specification & Reference',
                'slug' => 'language-specification-reference',
                'description' => 'Complete formal language specification, EBNF grammar, memory semantics, and type system rules.',
                'icon' => 'book-open',
                'sort_order' => 2,
                'pages' => [
                    [
                        'title' => 'Official v1.0.0 Language Specification',
                        'slug' => 'language-specification-v1',
                        'kind' => 'specification',
                        'summary' => 'Formal specification covering dual backends, ARC memory, FFI, and syntax rules.',
                        'content' => "# Roze v1.0.0 Language Specification\n\nRoze is a statically typed, dual-backend systems language designed for high-performance and safety.\n\n## Design Pillars\n1. **Dual Native & JVM Backends**: Compile to standalone native ELF binaries via Cranelift or deploy on enterprise Java 17+ JVM.\n2. **Deterministic ARC**: Zero-pause automatic reference counting with atomic operations.\n3. **Sum Types & Pattern Matching**: Enums with payload variants and exhaustive match statements.\n4. **First-Class Error Handling**: Result and Option sum types with try operator `?`.",
                    ],
                    [
                        'title' => 'Formal EBNF Grammar Reference',
                        'slug' => 'ebnf-grammar-reference',
                        'kind' => 'reference',
                        'summary' => 'Complete Extended Backus-Naur Form (EBNF) definition of lexical and syntactic tokens.',
                        'content' => "# Formal EBNF Grammar Reference\n\n```ebnf\nProgram       = { TopLevelItem } EOF ;\nTopLevelItem  = ImportDecl | FunctionDecl | ClassDecl | EnumDecl | TraitDecl | ImplDecl | ExternDecl | Statement ;\nFunctionDecl  = \"func\" Identifier [ TypeParams ] \"(\" [ ParameterList ] \")\" [ \"->\" Type ] Block ;\nClassDecl     = \"class\" Identifier [ TypeParams ] \"{\" { FieldDecl } \"}\" ;\nEnumDecl      = \"enum\" Identifier [ TypeParams ] \"{\" { EnumVariant } \"}\" ;\nStatement     = Block | LetStatement | IfStatement | WhileStatement | ForStatement | MatchStatement | ReturnStatement | UnsafeStatement ;\n```",
                    ],
                    [
                        'title' => 'Type System & Memory Model',
                        'slug' => 'type-system-memory-model',
                        'kind' => 'reference',
                        'summary' => 'Primitive types, classes, atomic reference counting, and deterministic destructors.',
                        'content' => "# Type System & Memory Model\n\n## Primitive Types\n- `int`: 64-bit signed integer (`i64`)\n- `float`: 64-bit IEEE 754 floating point (`f64`)\n- `bool`: 1-bit boolean (`true` / `false`)\n- `string`: UTF-8 ARC immutable string\n- `void`: Unit type\n- `u8`: 8-bit unsigned integer\n- `rawpointer`: Unmanaged memory address\n\n## Atomic Reference Counting (ARC)\nHeap objects maintain an atomic refcount header. Destructors fire recursively when the refcount reaches zero.",
                    ],
                    [
                        'title' => 'Error Handling with Result, Option & Try Operator',
                        'slug' => 'error-handling-result-option',
                        'kind' => 'guide',
                        'summary' => 'Modern sum-type error handling without unchecked runtime exceptions.',
                        'content' => "# Error Handling with Result & Option\n\nRoze replaces exceptions with safe, explicit sum types:\n\n```roze\nfunc divide(a: int, b: int) -> Result<int, string> {\n    if b == 0 { return Err(\"division by zero\"); }\n    return Ok(a / b);\n}\n\nfunc calculate() -> Result<int, string> {\n    let val = divide(100, 4)?; // Unwraps 25 or early-returns Err\n    return Ok(val * 2);\n}\n```",
                    ],
                    [
                        'title' => 'Algebraic Data Types & Exhaustive Match',
                        'slug' => 'enums-pattern-matching',
                        'kind' => 'guide',
                        'summary' => 'Enums with variant payloads, tuple patterns, and exhaustive compiler verification.',
                        'content' => "# Algebraic Data Types & Pattern Matching\n\n```roze\nenum Command {\n    Quit,\n    Move(x: int, y: int),\n    Write(msg: string)\n}\n\nfunc handle(cmd: Command) {\n    match cmd {\n        Command::Quit => println(\"Quitting\"),\n        Command::Move(x, y) => println(\"Move to \" + x + \", \" + y),\n        Command::Write(msg) => println(\"Message: \" + msg)\n    }\n}\n```",
                    ],
                ],
            ],
            [
                'title' => 'Systems Programming & Multi-Domain Demos',
                'slug' => 'systems-multi-domain',
                'description' => 'Real-world guides for bare-metal kernels, enterprise microservices, 60 FPS gaming, and AI inference.',
                'icon' => 'cpu',
                'sort_order' => 3,
                'pages' => [
                    [
                        'title' => 'Bare-Metal OS Kernel & Freestanding Mode',
                        'slug' => 'bare-metal-os-kernel',
                        'kind' => 'guide',
                        'summary' => 'How Roze builds bootable x86_64 Multiboot2 kernels with zero OS dependencies.',
                        'content' => "# Bare-Metal OS Kernel (--target freestanding)\n\nCompile without libc or JVM to produce bootable multiboot2 ELF kernels:\n\n```roze\nfunc kernel_main() {\n    vga_clear();\n    vga_set_color(14, 0); // Yellow on black\n    vga_print(\"🌹 ROZE BARE-METAL KERNEL INITIALIZED\\n\");\n}\n```\n\nBoot via QEMU:\n```bash\nqemu-system-x86_64 -kernel kernel.bin -serial stdio\n```",
                    ],
                    [
                        'title' => 'Enterprise Web Framework (RozeBoot)',
                        'slug' => 'rozeboot-enterprise-framework',
                        'kind' => 'guide',
                        'summary' => 'Spring-Boot-style declarative microservices with connection pooling and query builder.',
                        'content' => "# Enterprise Web Framework ('RozeBoot')\n\nBuild high-throughput services with connection pooling and routing:\n\n```roze\nimport \"rozeboot\";\n\nfunc main() {\n    let app = new RozeBootApp(8080);\n    app.route(\"GET\", \"/api/users\", (req) => {\n        return json_response(200, users);\n    });\n    app.start();\n}\n```",
                    ],
                    [
                        'title' => '2D/3D Game Engine Platform via Raylib',
                        'slug' => 'raylib-game-platform',
                        'kind' => 'guide',
                        'summary' => '60 FPS real-time rendering, sprite animation, audio, and physics loop via C-FFI.',
                        'content' => "# 2D/3D Game Platform (Raylib FFI)\n\nDemonstrates 60 FPS game loop and particle effects:\n\n```roze\nInitWindow(1280, 720, \"Cosmic Roze\");\nSetTargetFPS(60);\nwhile !WindowShouldClose() {\n    BeginDrawing();\n    ClearBackground(0x181824FF);\n    DrawCircle(player.x, player.y, 20.0, 0xFF4488FF);\n    EndDrawing();\n}\nCloseWindow();\n```",
                    ],
                    [
                        'title' => 'Local AI & ML Inference Wrapper',
                        'slug' => 'ai-ml-tensor-inference',
                        'kind' => 'guide',
                        'summary' => 'Multidimensional tensor mathematics, activations, and LLaMA-style decoder transformer.',
                        'content' => "# Local AI & ML Inference Engine\n\nRun local AI models and deep neural networks directly from Roze apps:\n\n```roze\nimport \"ai\";\n\nlet tokens = [1, 15043, 29892, 1244];\nlet logits = tensor_transformer_forward(tokens);\nprintln(\"Generation completed with KV-cache acceleration!\");\n```",
                    ],
                    [
                        'title' => 'Self-Hosted Compiler & Bootstrap Proof',
                        'slug' => 'self-hosted-compiler-bootstrap',
                        'kind' => 'guide',
                        'summary' => 'Pure Roze-in-Roze compiler architecture and 3-stage bootstrap fixpoint verification.',
                        'content' => "# Self-Hosting Compiler (Roze-in-Roze)\n\nThe Roze compiler is rewritten entirely in Roze itself, proving language independence.\n\n## 3-Stage Bootstrap Verification\n1. Stage 0 (Rust) compiles `src/main.roze` -> `bin/roze_stage1`.\n2. Stage 1 compiles `src/roze_compiler.roze` -> `bin/roze_stage2.c` and builds `bin/roze_stage2`.\n3. Stage 2 compiles `src/roze_compiler.roze` -> `bin/roze_stage3.c` and builds `bin/roze_stage3`.\n4. `diff -u bin/roze_stage2.c bin/roze_stage3.c` produces **zero diff lines**, proving mathematical fixpoint closure.",
                    ],
                ],
            ],
            [
                'title' => 'Standard Library Reference',
                'slug' => 'standard-library-reference',
                'description' => 'API specifications for core, collections, io, web, sql, concurrency, and ai modules.',
                'icon' => 'layers',
                'sort_order' => 4,
                'pages' => [
                    [
                        'title' => 'Core, Math & Strings (core.roze)',
                        'slug' => 'stdlib-core-math-strings',
                        'kind' => 'reference',
                        'summary' => 'Fundamental functions: abs, min, max, clamp, string transformations and predicates.',
                        'content' => "# core.roze Reference\n\n```roze\nfunc abs(x: int) -> int\nfunc min(a: int, b: int) -> int\nfunc max(a: int, b: int) -> int\nfunc clamp(val: int, low: int, high: int) -> int\nfunc string_length(s: string) -> int\nfunc string_to_upper(s: string) -> string\n```",
                    ],
                    [
                        'title' => 'Dynamic Collections: List & Map',
                        'slug' => 'stdlib-collections-list-map',
                        'kind' => 'reference',
                        'summary' => 'List<T> dynamic resizing array and Map<K, V> hash table APIs.',
                        'content' => "# collections.roze Reference\n\n```roze\n// List operations:\nlet list = list_new();\nlist_push(list, 42);\nlet v = list_get(list, 0);\nlet len = list_length(list);\n\n// Map operations:\nlet map = map_new();\nmap_put(map, \"key\", 100);\nlet val = map_get(map, \"key\");\n```",
                    ],
                    [
                        'title' => 'Concurrency: Threads & Channels',
                        'slug' => 'stdlib-threads-channels',
                        'kind' => 'reference',
                        'summary' => 'Native OS threads and thread-safe multi-producer multi-consumer channels.',
                        'content' => "# concurrency.roze Reference\n\n```roze\nlet chan = channel_new();\nlet thread = thread_spawn(() => {\n    channel_send(chan, 100);\n});\nlet msg = channel_recv(chan);\nthread_join(thread);\n```",
                    ],
                ],
            ],
        ];

        foreach ($sections as $secData) {
            $pagesData = $secData['pages'];
            unset($secData['pages']);

            $section = DocumentationSection::updateOrCreate(
                [
                    'software_project_id' => $project->id,
                    'slug' => $secData['slug'],
                ],
                $secData
            );

            $this->command->info("Created/Updated Section: {$section->title}");

            foreach ($pagesData as $index => $pData) {
                $pData['software_project_id'] = $project->id;
                $pData['release_id'] = $release->id;
                $pData['documentation_section_id'] = $section->id;
                $pData['version'] = '1.0.0';
                $pData['sort_order'] = $index;
                $pData['is_published'] = true;

                DocumentationPage::updateOrCreate(
                    [
                        'software_project_id' => $project->id,
                        'slug' => $pData['slug'],
                    ],
                    $pData
                );
            }
        }

        $this->command->info('Successfully seeded all Roze v1.0.0 documentation sections and pages in RozeHub!');
    }
}
