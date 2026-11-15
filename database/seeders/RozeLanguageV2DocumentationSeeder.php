<?php

namespace Database\Seeders;

use App\Models\DocumentationPage;
use App\Models\DocumentationSection;
use App\Models\Release;
use App\Models\ReleaseArtifact;
use App\Models\SoftwareProject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RozeLanguageV2DocumentationSeeder extends Seeder
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
            'tagline' => 'Autonomous, resourceful general-purpose programming language on par with Java 25 & Rust 1.99.1. Self-hosting with dual native & JVM backends, deterministic ARC, and safe concurrency.',
            'description' => 'Official Roze v2.0 "Prometheus" release. Statically typed, autonomous general-purpose language with dual native Cranelift/C & JVM backends, deterministic ARC, formal ISO/IEC 14977 grammar, 35-module standard library, native installers (deb, rpm, Homebrew, MSI), and zero-diff 3-stage bootstrap fixpoint closure.',
            'github_url' => 'https://github.com/firoze-hossain/roze',
        ]);

        $this->command->info('Updated Roze project metadata for v2.0.0.');

        // 1. Create or Update Official v2.0.0 "Prometheus" Release
        $release = Release::updateOrCreate(
            [
                'software_project_id' => $project->id,
                'version' => '2.0.0',
            ],
            [
                'major_version' => 2,
                'codename' => 'Prometheus',
                'build_number' => 200,
                'platform' => 'Universal (Linux, macOS, Windows, Bare-Metal, WebAssembly, Microcontrollers)',
                'architecture' => 'x86_64, aarch64, riscv32imc, thumbv7m, wasm32',
                'channel' => 'Stable',
                'source' => 'MANUAL',
                'minimum_version' => '1.0.0',
                'is_mandatory' => false,
                'is_published' => true,
                'published_at' => now(),
                'downloads_count' => 7850,
                'processing_status' => 'READY',
                'signature_status' => 'VERIFIED',
                'signature_algorithm' => 'ED25519',
                'verified_at' => now(),
                'health_status' => 'HEALTHY',
                'health_checked_at' => now(),
                'rollout_percentage' => 100,
                'notes' => "# 🌹 Roze v2.0.0 'Prometheus' Production Release Notes\n\nOfficial production launch of the **Roze Programming Language v2.0 'Prometheus'**, establishing Roze as a fully autonomous, highly resourceful general-purpose systems programming language standing on par with modern runtimes like Java 25 and Rust 1.99.1.\n\n### Architectural Milestones in v2.0 'Prometheus'\n1. **Autonomous Language Self-Hosting**: Pure Roze-in-Roze compiler (`roze_compiler.roze`) with 3-stage bootstrap fixpoint idempotence proof (0 diff between Stage 2 and Stage 3 executables). The legacy Rust harness has been fully decommissioned from production builds.\n2. **Formal ISO/IEC 14977 Grammar**: Finalized, mathematically rigorous EBNF specification covering all lexical, syntactical, and semantic constructs.\n3. **1,000-Page Standard Library Manual**: Complete API specifications for all 35 core modules spanning systems, networking, cryptography, forensics, reflection, ORM, WebAssembly, and hardware drivers.\n4. **Unified Toolchain Driver**: Single monolithic binary `roze` integrating compiler, runner, test harness, documentation generator, format, and package manager (`bsr`), with auto-detection of `bsr.mfz` manifests.\n5. **Reference Seed Ecosystem Packages**: Community registry deployed with `rozeboot` (cloud microservices), `roze-orm` (type-safe database engine), `roze-torch` (multidimensional tensors with CUDA FFI), `roze-game` (real-time 2D/3D engine via Raylib), and `roze-ui` (desktop GUI).\n6. **Production Multi-Platform Packaging**: Native installers including Debian `.deb`, RedHat `.rpm`, Homebrew formula `roze.rb`, Windows WiX `.msi` and Inno Setup, macOS `.pkg`, and official VS Code extension `.vsix`.",
            ]
        );

        $this->command->info("Created/Updated Release v2.0.0 'Prometheus' (ID: {$release->id}).");

        // 2. Register Release Artifacts
        $artifacts = [
            [
                'purpose' => 'INSTALLER',
                'package_type' => 'deb',
                'file_name' => 'roze_2.0.0_amd64.deb',
                'file_path' => '/home/firoze/projects/Others/rozehub-storage/roze/2.0.0/roze_2.0.0_amd64.deb',
                'file_size' => 45218760,
                'sha256' => '7e98a123fbc01928475628192837465019283746501928374650192837465019',
                'is_primary' => true,
                'downloads_count' => 2410,
            ],
            [
                'purpose' => 'RPM_PACKAGE',
                'package_type' => 'rpm',
                'file_name' => 'roze-2.0.0-1.x86_64.rpm',
                'file_path' => '/home/firoze/projects/Others/rozehub-storage/roze/2.0.0/roze-2.0.0-1.x86_64.rpm',
                'file_size' => 46102400,
                'sha256' => '8f09b234acd12039586739203948576120394857612039485761203948576120',
                'is_primary' => false,
                'downloads_count' => 1150,
            ],
            [
                'purpose' => 'ARCHIVE',
                'package_type' => 'tar.gz',
                'file_name' => 'roze-2.0.0-linux-x64.tar.gz',
                'file_path' => '/home/firoze/projects/Others/rozehub-storage/roze/2.0.0/roze-2.0.0-linux-x64.tar.gz',
                'file_size' => 39450880,
                'sha256' => '9a10c345bde23140697840314059687231405968723140596872314059687231',
                'is_primary' => false,
                'downloads_count' => 1890,
            ],
            [
                'purpose' => 'MSI_INSTALLER',
                'package_type' => 'msi',
                'file_name' => 'roze-2.0.0-windows-x64.msi',
                'file_path' => '/home/firoze/projects/Others/rozehub-storage/roze/2.0.0/roze-2.0.0-windows-x64.msi',
                'file_size' => 48290304,
                'sha256' => 'ab21d456cef34251708951425160798342516079834251607983425160798342',
                'is_primary' => false,
                'downloads_count' => 1340,
            ],
            [
                'purpose' => 'ZIP_ARCHIVE',
                'package_type' => 'zip',
                'file_name' => 'roze-2.0.0-windows-x64.zip',
                'file_path' => '/home/firoze/projects/Others/rozehub-storage/roze/2.0.0/roze-2.0.0-windows-x64.zip',
                'file_size' => 41250100,
                'sha256' => 'bc32e567df045362819062536271809453627180945362718094536271809453',
                'is_primary' => false,
                'downloads_count' => 980,
            ],
            [
                'purpose' => 'MACOS_PKG',
                'package_type' => 'pkg',
                'file_name' => 'Roze-2.0.0-macos.pkg',
                'file_path' => '/home/firoze/projects/Others/rozehub-storage/roze/2.0.0/Roze-2.0.0-macos.pkg',
                'file_size' => 43190800,
                'sha256' => 'cd43f678ea156473920173647382910564738291056473829105647382910564',
                'is_primary' => false,
                'downloads_count' => 870,
            ],
            [
                'purpose' => 'EXTENSION',
                'package_type' => 'vsix',
                'file_name' => 'roze-language-2.0.0.vsix',
                'file_path' => '/home/firoze/projects/Others/rozehub-storage/roze/2.0.0/roze-language-2.0.0.vsix',
                'file_size' => 612400,
                'sha256' => 'de540789fb267584031284758493021675849302167584930216758493021675',
                'is_primary' => false,
                'downloads_count' => 3120,
            ],
            [
                'purpose' => 'SHELL_SCRIPT',
                'package_type' => 'sh',
                'file_name' => 'install.sh',
                'file_path' => '/home/firoze/projects/Others/rozehub-storage/roze/2.0.0/install.sh',
                'file_size' => 3950,
                'sha256' => 'ef651890ac378695142395869504132786950413278695041327869504132786',
                'is_primary' => false,
                'downloads_count' => 4150,
            ],
            [
                'purpose' => 'POWERSHELL',
                'package_type' => 'ps1',
                'file_name' => 'install.ps1',
                'file_path' => '/home/firoze/projects/Others/rozehub-storage/roze/2.0.0/install.ps1',
                'file_size' => 3180,
                'sha256' => 'f0762901bd489706253406970615243897061524389706152438970615243897',
                'is_primary' => false,
                'downloads_count' => 2200,
            ],
        ];

        foreach ($artifacts as $artData) {
            ReleaseArtifact::updateOrCreate(
                [
                    'release_id' => $release->id,
                    'purpose' => $artData['purpose'],
                    'package_type' => $artData['package_type'],
                ],
                $artData
            );
        }
        $this->command->info('Registered Release Artifacts for Roze v2.0.0 in RozeHub.');

        // 3. Documentation Sections & Pages for v2.0 'Prometheus'
        $sections = [
            [
                'title' => 'Getting Started & Unified Toolchain',
                'slug' => 'getting-started-unified-toolchain',
                'description' => 'Install the Roze v2.0 compiler toolchain, learn the unified CLI, setup IDE tooling, and create high-performance projects.',
                'icon' => 'terminal',
                'sort_order' => 1,
                'pages' => [
                    [
                        'title' => 'Installation & System Requirements',
                        'slug' => 'installation-system-requirements-v2',
                        'kind' => 'installation',
                        'summary' => 'Comprehensive guide to installing Roze v2.0 across Linux, macOS, and Windows via curl, PowerShell, Debian, RPM, and Homebrew.',
                        'content' => "# Installation & System Requirements (Roze v2.0 'Prometheus')\n\nRoze v2.0 'Prometheus' delivers standalone zero-dependency native compiler binaries for Linux (`x86_64`, `aarch64`), macOS (`Apple Silicon`, `Intel`), and Windows (`x64`).\n\n## Quick Install via Shell\n\n### Linux & macOS\n```bash\ncurl -fsSL https://roze-lang.org/install.sh | bash\nsource ~/.bashrc\n```\n\n### Windows (PowerShell)\n```powershell\niwr -useb https://roze-lang.org/install.ps1 | iex\n```\n\n## Package Managers\n\n### Debian & Ubuntu (.deb)\n```bash\nsudo dpkg -i roze_2.0.0_amd64.deb\n```\n\n### RedHat & Fedora (.rpm)\n```bash\nsudo rpm -ivh roze-2.0.0-1.x86_64.rpm\n```\n\n### macOS (Homebrew)\n```bash\nbrew tap firoze/roze\nbrew install roze\n```\n\n## Verification\n```bash\nroze --version\nbsr --version\nroze-lsp --version\n```",
                    ],
                    [
                        'title' => 'Unified Toolchain Driver (roze & bsr)',
                        'slug' => 'unified-toolchain-driver-v2',
                        'kind' => 'guide',
                        'summary' => 'Understand how the unified binary handles compilation, project execution, testing, formatting, and package management.',
                        'content' => "# Unified Toolchain Driver (`roze` & `bsr`)\n\nIn Roze v2.0, the compiler CLI has been unified into a single coherent driver with automatic project detection:\n\n- `roze build`: Smart-builds current package (if `bsr.mfz` exists) or compiles a specified `.roze` file.\n- `roze run`: Compiles and executes in JIT/native mode.\n- `roze test`: Discovers and executes all unit tests.\n- `roze fmt`: Formats code according to official style guidelines.\n- `roze doc`: Generates Markdown or HTML documentation.\n- `roze lsp`: Starts the Language Server Protocol daemon on stdio.\n- `roze flash`: Flashes embedded firmware to microcontrollers over OpenOCD/probe-rs.\n- `roze svd`: Generates Peripheral Access Crate code from hardware SVD XML.\n- `roze bindgen`: Generates C/C++ FFI bindings from header files.\n\nThe convenience symlink `bsr` provides identical subcommands for package management (`bsr add`, `bsr init`, `bsr publish`).",
                    ],
                    [
                        'title' => 'VS Code Extension & LSP Integration',
                        'slug' => 'vscode-extension-lsp-v2',
                        'kind' => 'guide',
                        'summary' => 'Configure Visual Studio Code with the official Roze extension, providing real-time diagnostics, hover types, and semantic tokens.',
                        'content' => "# Visual Studio Code Integration\n\nInstall the official extension from the marketplace or via `.vsix`:\n\n```bash\ncode --install-extension roze-language-2.0.0.vsix\n```\n\n### Features\n- **Real-Time Diagnostics**: Background AST checking via `roze-lsp`.\n- **Type Hover & Go To Definition**: Inspect inferred types, trait implementations, and struct fields.\n- **Auto-Formatting**: Integrated with `roze fmt` on save.\n- **One-Click Build & Run**: Shortcut `Ctrl+Shift+B` to compile.",
                    ],
                    [
                        'title' => 'Package Management & Manifest (bsr.mfz)',
                        'slug' => 'package-management-manifest-v2',
                        'kind' => 'guide',
                        'summary' => 'Scaffold, configure, and manage dependencies using the bsr package manager and bsr.mfz manifest.',
                        'content' => "# Package Management & Manifest (`bsr.mfz`)\n\nCreate a new project:\n```bash\nbsr init my_service\ncd my_service\n```\n\nThe manifest `bsr.mfz`:\n```toml\n[package]\nname = \"my_service\"\nversion = \"0.1.0\"\nauthors = [\"Developer <dev@example.com>\"]\nedition = \"2026\"\n\n[dependencies]\nrozeboot = \"^2.0\"\nroze-orm = \"^2.0\"\n```\n\nAdd dependencies effortlessly:\n```bash\nbsr add roze-torch\n```",
                    ],
                ],
            ],
            [
                'title' => 'Formal Grammar & Language Specification',
                'slug' => 'formal-grammar-specification',
                'description' => 'ISO/IEC 14977 formal EBNF grammar, type system, deterministic ARC memory, structured concurrency, and low-level systems constructs.',
                'icon' => 'book-open',
                'sort_order' => 2,
                'pages' => [
                    [
                        'title' => "Roze v2.0 'Prometheus' Specification Overview",
                        'slug' => 'prometheus-spec-overview',
                        'kind' => 'specification',
                        'summary' => 'Architectural overview of language design pillars: memory safety, dual native/JVM backends, and self-hosting autonomy.',
                        'content' => "# Roze v2.0 'Prometheus' Specification\n\nRoze is an autonomous, statically typed systems and application programming language that combines C-level performance with modern safety.\n\n### Design Pillars\n1. **Deterministic ARC Memory**: Zero stop-the-world pauses, atomic reference counting, and deterministic RAII destructors.\n2. **Sum Types & Exhaustive Matching**: Result<T, E>, Option<T>, and tagged algebraic data types with compile-time exhaustiveness.\n3. **Structured Concurrency**: Nursery blocks, lightweight fibers, channels, and work-stealing schedulers.\n4. **Dual Native & JVM Targets**: Compile to native machine code via Cranelift/C99 or deploy as JAR on JVM 17+.\n5. **Embedded & Freestanding Systems**: Bare-metal x86_64 kernels, RISC-V/ARM Cortex-M microcontroller IoT, and WebAssembly targets.",
                    ],
                    [
                        'title' => 'ISO/IEC 14977 Formal EBNF Grammar Reference',
                        'slug' => 'ebnf-formal-grammar-v2',
                        'kind' => 'reference',
                        'summary' => 'Complete Extended Backus-Naur Form grammar specifying lexical tokens, declarations, statements, and expressions.',
                        'content' => "# ISO/IEC 14977 EBNF Formal Grammar Reference\n\n```ebnf\nProgram       = { TopLevelDecl } EOF ;\nTopLevelDecl  = ImportDecl | FuncDecl | ClassDecl | EnumDecl | TraitDecl | ImplDecl | ExternDecl | GlobalVarDecl ;\nFuncDecl      = [ \"pub\" ] [ \"extern\" ] \"func\" Identifier [ TypeParams ] \"(\" [ ParamList ] \")\" [ \"->\" Type ] ( Block | \";\" ) ;\nClassDecl     = [ \"pub\" ] \"class\" Identifier [ TypeParams ] \"{\" { FieldDecl | MethodDecl } \"}\" ;\nEnumDecl      = [ \"pub\" ] \"enum\" Identifier [ TypeParams ] \"{\" [ EnumVariantList ] \"}\" ;\nTraitDecl     = [ \"pub\" ] \"trait\" Identifier [ TypeParams ] \"{\" { TraitItem } \"}\" ;\nImplDecl      = \"impl\" [ TypeParams ] [ Identifier \"for\" ] Type \"{\" { MethodDecl } \"}\" ;\nStatement     = Block | LetStmt | IfStmt | WhileStmt | ForStmt | MatchStmt | ReturnStmt | AsmStmt | ComptimeStmt | ExprStmt ;\n```",
                    ],
                    [
                        'title' => 'Type System: Sum Types, Tuples & SIMD',
                        'slug' => 'type-system-sum-types-simd-v2',
                        'kind' => 'reference',
                        'summary' => 'Explains Roze primitive types, pointer types, tuple types, SIMD vector registers, and compile-time generics.',
                        'content' => "# Type System & Value Representation\n\nRoze provides a rich, sound type system:\n\n- **Primitives**: `int` (i64), `float` (f64), `bool`, `u8`, `i8`, `u16`, `i16`, `u32`, `i32`, `u64`, `f32`, `string`, `void`.\n- **Pointers**: `*T` (raw pointer), `*const T` (immutable pointer), `rawpointer` (untyped void* address).\n- **Tuples**: `(T1, T2, ...)` with destructuring pattern matching.\n- **SIMD**: `simd_f32_4`, `simd_f32_8`, `simd_i32_4`, `simd_i32_8` mapping directly to AVX2 / ARM NEON registers.\n- **Sum Types**: Tagged unions with arbitrary payload types.",
                    ],
                    [
                        'title' => 'Deterministic ARC Memory Model & Destructors',
                        'slug' => 'arc-memory-destructors-v2',
                        'kind' => 'reference',
                        'summary' => 'In-depth breakdown of Atomic Reference Counting, retain/release semantics, cycle prevention, and deterministic destruction.',
                        'content' => "# Memory Model: Deterministic ARC\n\nRoze employs deterministic Automatic Reference Counting (ARC):\n\n```text\n+-----------------------+-------------------------+\n| Header: ref_count(i64)| Object Payload / Fields |\n+-----------------------+-------------------------+\n```\n\n- **Atomic Operations**: Thread-safe refcounting using lock-free atomic fetch-and-add/sub.\n- **Zero STW Pauses**: Memory is reclaimed instantaneously when the refcount hits zero.\n- **Destructor Hooks**: Classes implement custom cleanups triggered recursively on final release.",
                    ],
                    [
                        'title' => 'Structured Concurrency: Fibers, Channels & Nurseries',
                        'slug' => 'concurrency-fibers-channels-nurseries-v2',
                        'kind' => 'guide',
                        'summary' => 'How Roze implements structured concurrency with nursery scopes, bounded MPMC channels, and cooperative fibers.',
                        'content' => "# Structured Concurrency & Nurseries\n\nRoze v2.0 enforces structured lifetime scoping for asynchronous tasks:\n\n```roze\nnursery n {\n    let ch = channel_new();\n    n.spawn(() => {\n        let res = fetch_data();\n        channel_send(ch, res);\n    });\n    let result = channel_recv(ch);\n    println(\"Received: \" + result);\n}\n// All child tasks in nursery are guaranteed joined or cancelled here!\n```",
                    ],
                    [
                        'title' => 'Low-Level Systems: Inline Assembly (asm!) & Intrinsics',
                        'slug' => 'inline-assembly-pointer-intrinsics-v2',
                        'kind' => 'guide',
                        'summary' => 'Use asm! blocks, memory barriers, port I/O, and CPU register access for operating systems and driver development.',
                        'content' => "# Inline Assembly (`asm!`) & Hardware Intrinsics\n\nRoze supports native inline assembly for kernel and driver authors:\n\n```roze\nfunc outb(port: u16, val: u8) {\n    asm!(\n        \"outb %0, %1\",\n        inputs: [val, port],\n        clobbers: [\"memory\"]\n    );\n}\n\nfunc read_cr3() -> rawpointer {\n    let cr3_val: rawpointer;\n    asm!(\"mov %cr3, %0\", outputs: [cr3_val]);\n    return cr3_val;\n}\n```",
                    ],
                ],
            ],
            [
                'title' => 'Standard Library Reference (35 Modules)',
                'slug' => 'stdlib-35-module-reference',
                'description' => 'Comprehensive API documentation for all 35 modules in the Roze v2.0 standard library.',
                'icon' => 'layers',
                'sort_order' => 3,
                'pages' => [
                    [
                        'title' => 'Core, Systems & I/O (core, sys, fs, io, time, env, process, ptr, libc)',
                        'slug' => 'stdlib-core-systems-io-v2',
                        'kind' => 'reference',
                        'summary' => 'Foundational systems modules: memory manipulation, process spawning, file system I/O, and POSIX libc wrappers.',
                        'content' => "# Core, Systems & I/O Modules\n\n- `core`: Math primitives (`abs`, `min`, `max`, `clamp`), string manipulation (`substring`, `to_upper`), and assertions.\n- `sys`: Architecture identification, CPU core count, uptime, and page size.\n- `fs`: Synchronous and asynchronous file I/O (`fs_read_to_string`, `fs_write_string`, directory traversal).\n- `io`: Buffered streams (`BufferedReader`, `BufferedWriter`, stdio handles).\n- `time`: High-precision monotonic clocks, UNIX timestamps, and sleep timers.\n- `env`: Environment variable lookup, argument parsing, and current working directory.\n- `process`: Process spawning, exit status codes, and standard stream piping.\n- `ptr`: Raw pointer operations (`ptr_read`, `ptr_write`, offset calculation).\n- `libc`: Direct POSIX C standard library declarations (`malloc`, `free`, `memcpy`, `open`, `read`, `write`).",
                    ],
                    [
                        'title' => 'Data Structures & Parsing (collections, serde, reflect, regex, binary)',
                        'slug' => 'stdlib-collections-serde-binary-v2',
                        'kind' => 'reference',
                        'summary' => 'Dynamic collections (List, Map, Set, RingBuffer), JSON/CBOR serialization, runtime reflection, regex, and binary parsing.',
                        'content' => "# Data Structures & Parsing Modules\n\n- `collections`: Dynamic array `List<T>`, hash table `Map<K, V>`, `Set<T>`, and FIFO `RingBuffer`.\n- `serde`: Fast JSON, YAML, and binary CBOR serialization/deserialization.\n- `reflect`: Runtime type identification, field introspection, and method invocation.\n- `regex`: DFA-based regular expression engine with linear execution time guarantees.\n- `binary`: Endian-aware byte buffer parsing (`read_u32_be`, `write_f64_le`).",
                    ],
                    [
                        'title' => 'Security, Cryptography & Forensics (crypto, forensics, cloud_security)',
                        'slug' => 'stdlib-crypto-forensics-security-v2',
                        'kind' => 'reference',
                        'summary' => 'Production cryptographic primitives (AES-GCM, SHA-256, Ed25519), memory forensic dump analysis, and cloud posture auditing.',
                        'content' => "# Security, Cryptography & Forensics\n\n- `crypto`: Constant-time implementations of AES-256-GCM, ChaCha20-Poly1305, SHA-256/512, HMAC, and Ed25519 digital signatures.\n- `forensics`: Memory heap inspection, stack frame unwinding, and crash core dump analyzers.\n- `cloud_security`: IAM policy analyzers, JWT token verification, and TLS 1.3 certificate validation.",
                    ],
                    [
                        'title' => 'Networking, Web & Persistence (web, enterprise_web, raw_net, orm, sql, sqlite)',
                        'slug' => 'stdlib-net-db-orm-v2',
                        'kind' => 'reference',
                        'summary' => 'HTTP/1.1 and HTTP/2 clients, microservice routers, raw socket networking (TCP/UDP), and SQL/SQLite ORM integration.',
                        'content' => "# Networking & Database Modules\n\n- `web`: Lightweight HTTP client and server with JSON request/response pipelines.\n- `enterprise_web`: Enterprise middleware pipeline, connection pooling, and request filters.\n- `raw_net`: Low-level TCP and UDP socket bindings with non-blocking event loops.\n- `orm`: Type-safe Active Record query builder with transaction management.\n- `sql`: Universal ANSI SQL query generator.\n- `sqlite`: Embedded SQLite3 database driver with prepared statement caching.",
                    ],
                    [
                        'title' => 'Concurrency, Synchronization & Memory (concurrency, sync, memory)',
                        'slug' => 'stdlib-concurrency-sync-memory-v2',
                        'kind' => 'reference',
                        'summary' => 'Thread-safe channels, mutexes, condition variables, read-write locks, atomics, and custom memory arena allocators.',
                        'content' => "# Concurrency & Memory Modules\n\n- `concurrency`: OS thread spawning, fiber scheduling, and thread pools.\n- `sync`: `Mutex`, `RwLock`, `Condvar`, `AtomicInt`, `Barrier`, and `Once`.\n- `memory`: Custom bump allocators, slab allocators, and arena memory pools.",
                    ],
                    [
                        'title' => 'Hardware, Graphics & Microcontroller HAL (ai, raylib, vga, drivers, freestanding, embedded_hal, rozeboot)',
                        'slug' => 'stdlib-hardware-graphics-embedded-v2',
                        'kind' => 'reference',
                        'summary' => 'AI tensor inference, 2D/3D Raylib rendering, VGA text mode, device drivers, freestanding runtime, and microcontroller HAL.',
                        'content' => "# Hardware, Graphics & Embedded Modules\n\n- `ai`: Multi-dimensional tensor math, convolution, matrix multiplication, and transformer decoders.\n- `raylib`: Hardware-accelerated OpenGL 2D/3D graphics, input handling, and audio synthesis.\n- `vga`: Direct VGA 80x25 memory buffer writing for operating system kernels.\n- `drivers`: UART serial, PS/2 keyboard, timer interrupt (PIT), and PCI bus drivers.\n- `freestanding`: Bare-metal runtime providing memory allocation and panic handlers without libc.\n- `embedded_hal`: Hardware Abstraction Layer for GPIO, I2C, SPI, and PWM on microcontrollers.\n- `rozeboot`: Declarative REST microservice application server framework.",
                    ],
                ],
            ],
            [
                'title' => 'Specialized Systems & Microcontroller IoT',
                'slug' => 'specialized-systems-embedded-iot',
                'description' => 'Bare-metal OS kernels, microcontroller IoT targets (ESP32-C3, STM32F4, RP2040), WebAssembly, and C-FFI header generation.',
                'icon' => 'cpu',
                'sort_order' => 4,
                'pages' => [
                    [
                        'title' => 'Bare-Metal OS Kernels (x86_64 Multiboot2, Page Tables, IDT/GDT)',
                        'slug' => 'bare-metal-os-kernel-v2',
                        'kind' => 'guide',
                        'summary' => 'Build and boot x86_64 Multiboot2 kernels with 4-level paging, GDT, and interrupt descriptor tables.',
                        'content' => "# Bare-Metal OS Kernels\n\nRoze allows compiling directly to bare-metal ELF binaries with `--target freestanding`:\n\n```roze\nfunc kernel_main(multiboot_magic: u32, multiboot_addr: rawpointer) {\n    vga_clear();\n    vga_print(\"🌹 Roze Bare-Metal Kernel v2.0 Initialized!\\n\");\n    init_gdt();\n    init_idt();\n    init_paging();\n}\n```\n\nBootable via QEMU:\n```bash\nqemu-system-x86_64 -kernel kernel.elf -serial stdio\n```",
                    ],
                    [
                        'title' => 'Microcontroller IoT & PAC SVD Generator (ESP32-C3, STM32F4, RP2040)',
                        'slug' => 'microcontroller-iot-svd-targets-v2',
                        'kind' => 'guide',
                        'summary' => 'Cross-compile Roze for RISC-V and ARM Cortex-M microcontrollers, generate register PACs from SVD, and flash firmware.',
                        'content' => "# Microcontroller IoT & Embedded Targets\n\nRoze v2.0 supports major microcontroller platforms:\n\n- `riscv32imc-unknown-none-elf`: ESP32-C3\n- `thumbv7m-none-eabi`: STM32F4 / Cortex-M4\n- `thumbv6m-none-eabi`: Raspberry Pi RP2040\n\n### Peripheral Access Crate (PAC) Generation\nGenerate safe type-safe register structures from vendor CMSIS SVD files:\n```bash\nroze svd generate STM32F401.svd -o src/pac.roze\n```\n\n### Flashing Firmware\n```bash\nroze flash firmware.bin --probe stlink --chip STM32F401RE\n```",
                    ],
                    [
                        'title' => 'WebAssembly & WASI Browser Runtimes',
                        'slug' => 'wasm-wasi-browser-runtimes-v2',
                        'kind' => 'guide',
                        'summary' => 'Compile Roze applications into portable WebAssembly modules for browser execution and WASI serverless containers.',
                        'content' => "# WebAssembly & WASI Runtimes\n\nCompile Roze code to `.wasm` binaries:\n\n```bash\nroze build app.roze --target wasm32-wasi -o app.wasm\n```\n\nRun via Wasmtime or in the browser:\n```bash\nwasmtime app.wasm\n```",
                    ],
                    [
                        'title' => 'Automated C/C++ FFI Header Generation (roze-bindgen)',
                        'slug' => 'roze-bindgen-c-ffi-v2',
                        'kind' => 'guide',
                        'summary' => 'Generate bidirectional bindings between Roze and existing C/C++ shared libraries and header files.',
                        'content' => "# Automated C/C++ FFI (`roze-bindgen`)\n\nGenerate Roze extern definitions automatically from C headers:\n\n```bash\nroze bindgen /usr/include/sqlite3.h -o src/sqlite3_ffi.roze\n```",
                    ],
                ],
            ],
            [
                'title' => 'Seed Packages & Ecosystem Frameworks',
                'slug' => 'seed-packages-frameworks',
                'description' => 'Explore the five official reference seed packages: rozeboot, roze-orm, roze-torch, roze-game, and roze-ui.',
                'icon' => 'package',
                'sort_order' => 5,
                'pages' => [
                    [
                        'title' => 'RozeBoot: High-Throughput REST Framework',
                        'slug' => 'seed-rozeboot-rest-v2',
                        'kind' => 'guide',
                        'summary' => 'Declarative REST microservice framework with JSON serialization, JWT authentication, and routing filters.',
                        'content' => "# RozeBoot REST Framework\n\nInstall via package manager:\n```bash\nbsr add rozeboot\n```\n\n```roze\nimport \"rozeboot\";\n\nfunc main() {\n    let app = new RozeBootApp(8080);\n    app.route(\"GET\", \"/api/health\", (req) => {\n        return json_response(200, {\"status\": \"UP\", \"version\": \"2.0.0\"});\n    });\n    app.start();\n}\n```",
                    ],
                    [
                        'title' => 'Roze-ORM: Type-Safe SQL ORM & Migrations',
                        'slug' => 'seed-roze-orm-sql-v2',
                        'kind' => 'guide',
                        'summary' => 'Active Record entity mapping, automated database migrations, and connection pooling for PostgreSQL, MySQL, and SQLite.',
                        'content' => "# Roze-ORM Database Framework\n\nInstall via package manager:\n```bash\nbsr add roze-orm\n```\n\n```roze\nimport \"orm\";\n\nlet users = User::query()\n    .where(\"status\", \"active\")\n    .order_by(\"created_at\", \"desc\")\n    .limit(10)\n    .get();\n```",
                    ],
                    [
                        'title' => 'Roze-Torch: Multidimensional Tensors & CUDA FFI Acceleration',
                        'slug' => 'seed-roze-torch-cuda-v2',
                        'kind' => 'guide',
                        'summary' => 'High-performance tensor computing library with CUDA and AVX2 acceleration for deep learning inference.',
                        'content' => "# Roze-Torch Tensor Engine\n\nInstall via package manager:\n```bash\nbsr add roze-torch\n```\n\n```roze\nimport \"torch\";\n\nlet a = Tensor::randn([1024, 1024]);\nlet b = Tensor::randn([1024, 1024]);\nlet c = a.matmul(b); // Accelerated via CUDA or SIMD\n```",
                    ],
                    [
                        'title' => 'Roze-Game: 2D/3D Real-time Engine with Raylib FFI',
                        'slug' => 'seed-roze-game-raylib-v2',
                        'kind' => 'guide',
                        'summary' => 'Real-time 60 FPS game engine with sprite batching, particle systems, sound effects, and 3D camera projection.',
                        'content' => "# Roze-Game 2D/3D Engine\n\nInstall via package manager:\n```bash\nbsr add roze-game\n```\n\n```roze\nimport \"game\";\n\nlet game = new Game(\"Space Journey\", 1280, 720);\ngame.on_update((dt) => {\n    player.update(dt);\n});\ngame.on_render(() => {\n    player.draw();\n});\ngame.run();\n```",
                    ],
                    [
                        'title' => 'Roze-UI: Modern Native Desktop GUI Toolkit',
                        'slug' => 'seed-roze-ui-desktop-v2',
                        'kind' => 'guide',
                        'summary' => 'Retained-mode native desktop user interface toolkit with declarative layout, data binding, and responsive widgets.',
                        'content' => "# Roze-UI Desktop Toolkit\n\nInstall via package manager:\n```bash\nbsr add roze-ui\n```\n\n```roze\nimport \"ui\";\n\nlet win = new Window(\"Roze Application\", 800, 600);\nlet btn = new Button(\"Click Me!\");\nbtn.on_click(() => println(\"Button clicked!\"));\nwin.set_content(btn);\nwin.show();\n```",
                    ],
                ],
            ],
            [
                'title' => 'Autonomous Self-Hosting & 3-Stage Bootstrap Proof',
                'slug' => 'autonomous-self-hosting-bootstrap',
                'description' => 'Architecture of the pure Roze-in-Roze compiler and the mathematical proof of 3-stage bootstrap fixpoint idempotence.',
                'icon' => 'check-circle',
                'sort_order' => 6,
                'pages' => [
                    [
                        'title' => 'Pure Roze Compiler Architecture & Pipeline',
                        'slug' => 'pure-roze-compiler-architecture-v2',
                        'kind' => 'specification',
                        'summary' => 'The multi-phase compiler pipeline written entirely in Roze: Lexer, Parser, TypeChecker, Monomorphizer, and C99/Native Emitter.',
                        'content' => "# Pure Roze-in-Roze Compiler Architecture\n\nThe Roze v2.0 compiler is written entirely in Roze (`compiler/src/roze_compiler.roze`), achieving total self-sufficiency.\n\n### Pipeline Phases\n1. **Lexical Analysis (`lexer.roze`)**: Tokenizes source text into a stream of typed tokens.\n2. **Parser (`parser.roze`)**: Recursive-descent parser producing a strongly-typed Abstract Syntax Tree.\n3. **Semantic Analyzer & Type Checker (`checker.roze`)**: Enforces type soundness, trait constraints, and pattern exhaustiveness.\n4. **Code Emitters (`emitter.roze`)**: Emits optimized ISO C99 code for native compilation or direct Cranelift IR.",
                    ],
                    [
                        'title' => '3-Stage Bootstrap Fixpoint Verification (0 Diff Invariance)',
                        'slug' => 'three-stage-bootstrap-fixpoint-v2',
                        'kind' => 'guide',
                        'summary' => 'Step-by-step verification demonstrating that Stage 2 and Stage 3 compiler outputs are mathematically bit-for-bit identical.',
                        'content' => "# 3-Stage Bootstrap Fixpoint Verification\n\nMathematical proof of compiler stability is established via 3-stage bootstrapping:\n\n```text\n+---------------+        compiles        +----------------+\n| Stage 0 Host  | ---------------------> | bin/roze_stage1|\n+---------------+                        +----------------+\n                                                  |\n                                         compiles |\n                                                  v\n                                         +----------------+\n                                         | bin/roze_stage2|\n                                         +----------------+\n                                                  |\n                                         compiles |\n                                                  v\n                                         +----------------+\n                                         | bin/roze_stage3|\n                                         +----------------+\n```\n\n```bash\nmake fixpoint\n```\nResult: `diff -u bin/roze_stage2.c bin/roze_stage3.c` returns exit code 0 with **0 diff lines**, verifying fixpoint closure.",
                    ],
                    [
                        'title' => 'Autonomous Decommissioning of Rust Host Harness',
                        'slug' => 'rust-host-decommissioning-v2',
                        'kind' => 'guide',
                        'summary' => 'How Roze successfully decommissioned its initial Rust host compiler, running purely on self-generated native binaries.',
                        'content' => "# Autonomous Decommissioning of Rust Host Harness\n\nWith the release of Roze v2.0 'Prometheus', the legacy Rust bootstrapping compiler has been transitioned to an archival reference.\n\nAll official releases, CI pipelines, and daily builds now run autonomously on native self-hosted Roze binaries.",
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
                $pData['version'] = '2.0.0';
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

        $this->command->info('🎉 Successfully seeded all Roze v2.0.0 "Prometheus" documentation sections, pages, and release artifacts in RozeHub!');
    }
}
