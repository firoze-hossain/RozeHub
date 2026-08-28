<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DocumentationSeeder extends Seeder
{
    public function run(): void
    {
        $projects = DB::table('software_projects')->pluck('id', 'slug');

        $docs = [
            'novaos' => [
                'Overview' => [
                    'description' => 'The operating-system concepts, goals, and release model behind NOVAOS.',
                    'icon' => '◉',
                    'pages' => [
                        ['title'=>'NOVAOS at a glance','kind'=>'overview','summary'=>'Understand the NOVAOS architecture, distribution model, and project vocabulary.','content'=><<<'MD'
# NOVAOS at a glance

NOVAOS is an **independent operating system** in the RozeHub ecosystem. It is distributed as an operating-system image and is not treated as a Windows, macOS, or Linux application.

## Project goals

- Provide a focused developer-friendly operating environment.
- Keep the base system understandable and inspectable.
- Ship reproducible release images with a clear build identity.
- Separate operating-system concerns from application distribution.

## Release identity

Every public NOVAOS build has a **major version**, **version**, **build number**, **codename**, **channel**, **architecture**, and **SHA-256 checksum**.

| Field | Meaning |
| --- | --- |
| Major version | Product family, such as `2026.2` |
| Version | Public release, such as `2026.2.1` |
| Build number | Internal build identity |
| Codename | Human-friendly release name |
| Channel | Stable, Beta, or Nightly |
| Architecture | x64 or ARM64 |

> Treat the checksum as part of the release identity. Verify it before installing an image obtained from a mirror.
MD
],
                        ['title'=>'Architecture overview','kind'=>'architecture','summary'=>'A practical map of the NOVAOS system layers and their responsibilities.','content'=><<<'MD'
# Architecture overview

NOVAOS can be reasoned about as a layered system:

1. **Firmware / boot environment** — starts the machine and transfers control to the OS boot path.
2. **Bootloader** — selects the operating-system image and prepares the kernel hand-off.
3. **Kernel** — manages CPU scheduling, memory, devices, processes, and privileged services.
4. **Core services** — provide initialization, logging, networking, storage, and process supervision.
5. **User space** — shells, utilities, package tooling, and desktop or developer applications.
6. **Roze developer layer** — language tooling and ecosystem applications built for the platform.

## Design principle

The boundary between the base OS and user applications should stay explicit. A system update should not require rebuilding unrelated application packages.

## Build identity

A NOVAOS image should be reproducible from a known source revision and configuration. Record the source revision, build toolchain, architecture, and image checksum in release notes.
MD
],
                    ]
                ],
                'Getting Started' => [
                    'description'=>'Installation, first boot, system setup, and daily workflow.', 'icon'=>'→',
                    'pages'=>[
                        ['title'=>'First boot checklist','kind'=>'guide','summary'=>'A safe sequence for validating a fresh NOVAOS installation.','content'=><<<'MD'
# First boot checklist

After installing NOVAOS, verify the basics before customizing the system.

## 1. Confirm the release

Open the system information tool and confirm the version, build number, architecture, and channel match the image you installed.

## 2. Check storage

Confirm the root filesystem is mounted and has enough free space for updates, logs, and user data.

## 3. Check networking

```text
network status
network list
```

The exact commands may evolve with the command-line tooling; use the versioned command reference shipped with your build.

## 4. Update metadata

Refresh package metadata before installing additional software.

## 5. Verify time

Correct system time is important for TLS, package signatures, logs, and release verification.

## 6. Create a recovery plan

Before experimenting with system-level changes, keep a known-good installer image and backup of important data.
MD
],
                        ['title'=>'Installation guide','kind'=>'installation','summary'=>'Recommended installation flow for a NOVAOS system image.','content'=><<<'MD'
# Installation guide

NOVAOS is installed from an operating-system image rather than an application installer.

## Before installation

- Confirm your hardware architecture: **x64** or **ARM64**.
- Download the intended release from RozeHub.
- Verify the published SHA-256 checksum.
- Back up important data.
- Prepare a bootable installation medium using a trusted imaging tool.

## Verify the image

On Linux:

```bash
sha256sum novaos-2026.2.1-x86_64.iso
```

Compare the result with the checksum shown on the RozeHub release page.

## Installation phases

1. Boot the installation medium.
2. Select the target disk and partitioning strategy.
3. Install the base system.
4. Configure the bootloader.
5. Create the initial user account.
6. Reboot from the installed disk.
7. Complete first-boot configuration.

> Installation details are release-specific. The release notes should always take precedence over this general guide.
MD
],
                    ]
                ],
                'System Reference' => [
                    'description'=>'Core system concepts for users and contributors.', 'icon'=>'⌘',
                    'pages'=>[
                        ['title'=>'Processes and services','kind'=>'reference','summary'=>'How NOVAOS organizes running programs and long-lived services.','content'=>"# Processes and services\n\nA process is a running program with its own execution context. A service is a long-running component normally supervised by the operating system.\n\n## Service lifecycle\n\nA typical service moves through **installed → configured → enabled → running → stopped** states.\n\nUse the service manager supplied by your NOVAOS release to inspect status and logs. Avoid terminating critical services blindly; identify dependencies first.\n\n## Logs\n\nSystem logs should include timestamps, service identity, severity, and a useful message. For production troubleshooting, preserve the relevant log window before restarting a failing service."],
                        ['title'=>'Storage and filesystems','kind'=>'reference','summary'=>'Filesystem layout, persistent data, and safe storage practices.','content'=>"# Storage and filesystems\n\nKeep operating-system files, user data, caches, and temporary files logically separated.\n\n## Recommended layout\n\n- `/boot` — boot artifacts where applicable.\n- `/etc` — system configuration.\n- `/var` — mutable system data and logs.\n- `/home` — user data.\n- `/opt` — optional software where supported.\n\nNever assume a path is identical across future releases; consult the release-specific filesystem reference."],
                        ['title'=>'Networking','kind'=>'reference','summary'=>'Networking concepts, diagnostics, and secure defaults.','content'=>"# Networking\n\nNOVAOS networking should provide predictable interface discovery, address configuration, DNS, routing, and diagnostics.\n\n## Diagnostic sequence\n\n1. Check the interface state.\n2. Check the assigned address.\n3. Check the default route.\n4. Test local gateway reachability.\n5. Test DNS resolution.\n6. Test the remote service.\n\nThis layered approach helps distinguish a link problem from a routing, DNS, or application problem."],
                    ]
                ],
                'Development' => [
                    'description'=>'Build, package, and contribute to NOVAOS.', 'icon'=>'⌁',
                    'pages'=>[
                        ['title'=>'Building NOVAOS','kind'=>'development','summary'=>'A contributor-oriented model for producing reproducible system images.','content'=>"# Building NOVAOS\n\nA NOVAOS build should record its source revision, configuration, architecture, toolchain, and resulting checksum.\n\n## Recommended pipeline\n\n```text\nsource → configure → build base → assemble filesystem → create image → verify → publish\n```\n\n## Build metadata\n\nStore the build number and source revision with the generated image. Generate the SHA-256 checksum only after the final image is complete."],
                        ['title'=>'Release channels','kind'=>'release','summary'=>'How Stable, Beta, and Nightly builds should be used.','content'=>"# Release channels\n\n**Stable** is intended for normal use. **Beta** is for broader testing before a stable release. **Nightly** represents active development and may contain breaking changes.\n\n| Channel | Purpose | Risk |\n| --- | --- | --- |\n| Stable | General users | Low |\n| Beta | Validation and feedback | Medium |\n| Nightly | Development | High |\n\nAlways record the channel in bug reports."],
                    ]
                ],
                'Troubleshooting' => [
                    'description'=>'Recovery, diagnostics, and common failure patterns.', 'icon'=>'?',
                    'pages'=>[
                        ['title'=>'Troubleshooting guide','kind'=>'troubleshooting','summary'=>'A structured approach to boot, network, storage, and update failures.','content'=>"# Troubleshooting guide\n\nStart with the smallest failing layer.\n\n## Boot failure\n\nCheck firmware mode, boot media integrity, bootloader configuration, and the selected root filesystem.\n\n## Network failure\n\nFollow the networking diagnostic sequence from the reference section.\n\n## Update failure\n\nRecord the exact version, channel, package metadata timestamp, and error message. Do not repeatedly retry a failed system transaction without checking whether a partial state was created.\n\n## Recovery\n\nKeep the previous known-good release available so you can roll back when a new build is not usable."],
                        ['title'=>'Release notes','kind'=>'release-notes','summary'=>'The living release-history page for NOVAOS.','content'=>"# NOVAOS release notes\n\nRelease notes are maintained from the Admin NOVAOS Release Center.\n\nFor every release, record:\n\n- User-visible changes\n- Hardware or architecture changes\n- Installer changes\n- Known issues\n- Upgrade notes\n- Security fixes\n- Build number and checksum\n\nThe public release archive is the source of truth for published builds."]
                    ]
                ]
            ],
            'roze-language' => [
                'Overview'=>['description'=>'The Roze language, compiler, runtime, and developer workflow.','icon'=>'{}','pages'=>[
                    ['title'=>'Roze language overview','kind'=>'overview','summary'=>'A conceptual introduction to the Roze programming language.','content'=>"# Roze language overview\n\nRoze is a systems-oriented programming language focused on **clarity, explicitness, and predictable performance**.\n\n## Core ideas\n\n- Strong, readable syntax\n- Explicit data ownership and resource boundaries\n- Low-level control where it matters\n- Tooling designed around fast feedback\n- A small, understandable standard library\n\n## A first program\n\n```roze\nfn main() {\n    print(\"Hello, Roze!\")\n}\n```\n\nThe exact compiler commands are versioned; use the installation guide for your installed toolchain."],
                    ['title'=>'Installation and toolchain','kind'=>'installation','summary'=>'Install the compiler, verify the toolchain, and build a first project.','content'=>"# Installation and toolchain\n\nInstall the Roze toolchain appropriate to your host operating system.\n\n## Verify\n\n```text\nroze --version\nroze --help\n```\n\n## Project flow\n\n```text\nroze init my-app\ncd my-app\nroze build\nroze run\n```\n\nKeep the compiler version pinned for reproducible builds."],
                ]],
                'Language Guide'=>['description'=>'Syntax and semantics from first principles.','icon'=>'Aa','pages'=>[
                    ['title'=>'Syntax and declarations','kind'=>'guide','summary'=>'Variables, functions, expressions, and control flow.','content'=>"# Syntax and declarations\n\nRoze favors declarations that make types and ownership visible.\n\n## Variables\n\n```roze\nlet name: String = \"Roze\"\nlet count: Int = 10\n```\n\n## Functions\n\n```roze\nfn add(a: Int, b: Int) -> Int {\n    return a + b\n}\n```\n\nUse small functions with explicit inputs and outputs to keep systems code easy to audit."],
                    ['title'=>'Types and memory','kind'=>'reference','summary'=>'Primitive types, aggregates, ownership, and resource lifetimes.','content'=>"# Types and memory\n\nThe type system should communicate the shape and lifetime of data.\n\nImportant concepts include **values**, **references**, **ownership**, and **resource cleanup**.\n\nWhen working with files, sockets, or native resources, make the lifetime explicit and deterministic."],
                    ['title'=>'Concurrency','kind'=>'guide','summary'=>'Tasks, synchronization, and safe concurrent programs.','content'=>"# Concurrency\n\nPrefer message passing and explicit synchronization over hidden shared state.\n\n```text\nproducer → channel → consumer\n```\n\nDocument which component owns mutable state and which operations may run concurrently."],
                ]],
                'Tooling & Standard Library'=>['description'=>'Compiler, packages, modules, diagnostics, and libraries.','icon'=>'⌘','pages'=>[
                    ['title'=>'Compiler and diagnostics','kind'=>'reference','summary'=>'Compiler phases, errors, warnings, and reproducible builds.','content'=>"# Compiler and diagnostics\n\nA useful compiler diagnostic should identify the source location, the violated rule, and a practical next step.\n\nUse a locked compiler version in CI and publish the compiler version with release artifacts."],
                    ['title'=>'Modules and packages','kind'=>'guide','summary'=>'Organize reusable Roze code into stable modules and packages.','content'=>"# Modules and packages\n\nKeep public APIs small. A package should expose stable interfaces while keeping implementation details private.\n\nUse semantic versioning for packages when compatibility matters."],
                    ['title'=>'Release notes','kind'=>'release-notes','summary'=>'Versioned language and compiler changes.','content'=>"# Roze release notes\n\nRecord language changes, compiler diagnostics, standard-library changes, compatibility notes, and migration guidance for every release."]
                ]]
            ],
            'stratosdb'=>[
                'Overview'=>['description'=>'Understand StratosDB as a database engine and its execution model.','icon'=>'◉','pages'=>[
                    ['title'=>'StratosDB architecture','kind'=>'architecture','summary'=>'The storage, SQL, transaction, and execution layers of the engine.','content'=>"# StratosDB architecture\n\nStratosDB is a **database engine**, not simply a database client.\n\nA useful mental model is:\n\n```text\nclient → parser → planner → executor → transaction layer → storage engine\n```\n\nThe storage layer owns durable data. The transaction layer provides consistency boundaries. The planner turns SQL intent into an execution strategy."],
                    ['title'=>'Installation and first database','kind'=>'installation','summary'=>'Create an instance, initialize storage, and run the first SQL statement.','content'=>"# Installation and first database\n\nInstall the StratosDB distribution for your operating system, initialize a data directory, and start the engine.\n\n```sql\nCREATE DATABASE demo;\nUSE demo;\nCREATE TABLE users (\n  id INTEGER PRIMARY KEY,\n  name TEXT NOT NULL\n);\n```\n\nThe exact launcher and configuration flags are release-specific."],
                ]],
                'SQL Reference'=>['description'=>'SQL syntax, data types, schemas, indexes, and transactions.','icon'=>'SQL','pages'=>[
                    ['title'=>'SQL quick start','kind'=>'guide','summary'=>'The shortest path from an empty database to useful queries.','content'=>"# SQL quick start\n\n```sql\nCREATE TABLE departments (\n  id INTEGER PRIMARY KEY,\n  name TEXT NOT NULL\n);\n\nINSERT INTO departments VALUES (1, 'Engineering');\n\nSELECT * FROM departments;\n```\n\nPrefer explicit column lists for inserts in production code."],
                    ['title'=>'Data types','kind'=>'reference','summary'=>'Choosing numeric, text, temporal, and structured values.','content'=>"# Data types\n\nUse the smallest type that accurately represents the domain.\n\n| Type family | Example |\n| --- | --- |\n| Integer | `INTEGER` |\n| Decimal | `DECIMAL` |\n| Text | `TEXT` |\n| Boolean | `BOOLEAN` |\n| Date/time | `TIMESTAMP` |\n\nCheck the version-specific type matrix before relying on advanced types."],
                    ['title'=>'Transactions and isolation','kind'=>'reference','summary'=>'Atomic changes, commit/rollback, and concurrency behavior.','content'=>"# Transactions and isolation\n\nUse transactions to make a group of related changes atomic.\n\n```sql\nBEGIN;\nUPDATE accounts SET balance = balance - 100 WHERE id = 1;\nUPDATE accounts SET balance = balance + 100 WHERE id = 2;\nCOMMIT;\n```\n\nIf any step cannot be completed safely, roll back the transaction."],
                    ['title'=>'Indexes and query planning','kind'=>'reference','summary'=>'How indexes affect read performance and query planning.','content'=>"# Indexes and query planning\n\nIndexes trade write and storage cost for faster access paths.\n\n```sql\nCREATE INDEX idx_users_name ON users(name);\n```\n\nUse the query planner or explain facility before adding indexes blindly. Measure representative workloads."],
                ]],
                'Engine Operations'=>['description'=>'Storage, backup, recovery, CLI, configuration, and troubleshooting.','icon'=>'⌘','pages'=>[
                    ['title'=>'Storage engine','kind'=>'architecture','summary'=>'Durability, pages, logs, and recovery concepts.','content'=>"# Storage engine\n\nThe storage engine is responsible for durable representation of records and indexes.\n\nA production design should define how writes become durable, how corruption is detected, and how recovery replays or rolls back incomplete work."],
                    ['title'=>'Backup and recovery','kind'=>'operations','summary'=>'Operational guidance for protecting and restoring StratosDB data.','content'=>"# Backup and recovery\n\nA backup is useful only if it can be restored.\n\n## Minimum practice\n\n- Take regular consistent backups.\n- Store backups separately from the primary data directory.\n- Test restoration regularly.\n- Record engine version and configuration with each backup.\n\nDocument recovery objectives before choosing backup frequency."],
                    ['title'=>'CLI and configuration','kind'=>'reference','summary'=>'Command-line operation and configuration strategy.','content'=>"# CLI and configuration\n\nKeep configuration explicit and version controlled where secrets are not involved.\n\nTypical operational tasks include starting the engine, checking status, opening a SQL shell, inspecting storage, and exporting diagnostics."],
                    ['title'=>'Troubleshooting','kind'=>'troubleshooting','summary'=>'A repeatable method for diagnosing database failures.','content'=>"# Troubleshooting\n\nCapture the engine version, configuration, recent logs, query, and reproduction steps.\n\nSeparate:\n\n1. Connection failures\n2. SQL parsing errors\n3. Planning errors\n4. Transaction failures\n5. Storage or recovery failures\n\nDo not delete database files while investigating a recovery problem."],
                    ['title'=>'Release notes','kind'=>'release-notes','summary'=>'Engine changes, compatibility notes, and migration guidance.','content'=>"# StratosDB release notes\n\nRecord SQL compatibility changes, storage format changes, planner improvements, recovery fixes, and migration requirements for each release."]
                ]]
            ],
        ];

        // General products: a compact but useful starter set that admins can expand.
        $generic = [
            'dbnavigator'=>[
                'Overview'=>['description'=>'Learn the database client workflow.','icon'=>'◉','pages'=>[
                    ['title'=>'DBNavigator overview','kind'=>'overview','summary'=>'Connections, schema exploration, SQL editing, and data workflows.','content'=>"# DBNavigator overview\n\nDBNavigator is a desktop database client for exploring schemas, writing SQL, and inspecting data.\n\n## Core workflow\n\n```text\nconnect → explore schema → write query → inspect result → save/share\n```\n\nUse the documentation version that matches your installed release."],
                    ['title'=>'Connections','kind'=>'guide','summary'=>'Create, test, save, and organize database connections.','content'=>"# Connections\n\nCreate a connection with the minimum credentials required. Test the connection before saving it and use environment-specific profiles for development and production."],
                    ['title'=>'SQL editor and schema explorer','kind'=>'guide','summary'=>'Navigate schemas and work efficiently with SQL.','content'=>"# SQL editor and schema explorer\n\nUse the schema tree to inspect tables, columns, indexes, and relationships. Keep queries small while exploring and save important SQL in version control."],
                ]]
            ],
            'thundercall'=>[
                'Overview'=>['description'=>'Build, test, save, and share API requests.','icon'=>'↗','pages'=>[
                    ['title'=>'ThunderCall overview','kind'=>'overview','summary'=>'A calm workflow for API request development and inspection.','content'=>"# ThunderCall overview\n\nThunderCall is an API client for building requests, inspecting responses, and organizing reusable collections.\n\n## Request flow\n\n```text\nrequest → auth/environment → send → inspect → save\n```"],
                    ['title'=>'Requests and responses','kind'=>'guide','summary'=>'Configure methods, URLs, headers, query parameters, and bodies.','content'=>"# Requests and responses\n\nStart with the HTTP method and URL, then add headers, query parameters, authentication, and a request body as required. Inspect status, headers, timing, and response content after sending."],
                    ['title'=>'Collections and environments','kind'=>'guide','summary'=>'Organize requests and keep environment values reusable.','content'=>"# Collections and environments\n\nUse collections for related endpoints and environments for values such as base URLs, tokens, and IDs. Never commit real secrets into a shared collection."],
                ]]
            ],
            'lumina'=>[
                'Overview'=>['description'=>'Learn the Lumina development environment.','icon'=>'⌘','pages'=>[
                    ['title'=>'Lumina overview','kind'=>'overview','summary'=>'Projects, editor, terminal, debugging, and extensions.','content'=>"# Lumina overview\n\nLumina is a developer environment designed to keep code, project navigation, terminals, and diagnostics in one focused workspace."],
                    ['title'=>'Projects and workspace','kind'=>'guide','summary'=>'Open projects, configure workspace settings, and navigate code.','content'=>"# Projects and workspace\n\nKeep project-specific configuration close to the repository and avoid committing machine-specific paths."],
                    ['title'=>'Debugging and terminal','kind'=>'guide','summary'=>'Run commands and inspect application state while developing.','content'=>"# Debugging and terminal\n\nUse the integrated terminal for repeatable project commands and the debugger for controlled inspection of program state."],
                ]]
            ],
            'trackeye'=>[
                'Overview'=>['description'=>'Responsible activity visibility and reporting.','icon'=>'◌','pages'=>[
                    ['title'=>'TrackEye overview','kind'=>'overview','summary'=>'Understand the activity tracker and its reporting model.','content'=>"# TrackEye overview\n\nTrackEye provides workplace activity visibility with an emphasis on clear reporting and configurable capture behavior.\n\nUse it transparently, with appropriate organizational policies and user notice."],
                    ['title'=>'Configuration and monitoring','kind'=>'guide','summary'=>'Configure tracking behavior, retention, and reporting.','content'=>"# Configuration and monitoring\n\nChoose only the telemetry required for the intended operational purpose. Define retention and access rules before enabling collection."],
                    ['title'=>'Reports and exports','kind'=>'guide','summary'=>'Turn activity data into useful summaries.','content'=>"# Reports and exports\n\nPrefer aggregated summaries over unnecessary raw data. Export only what is needed for the reporting purpose."],
                ]]
            ],
        ];

        foreach ($docs + $generic as $slug => $sections) {
            $projectId = $projects[$slug] ?? null;
            if (!$projectId) continue;
            $sectionOrder = 0;
            foreach ($sections as $sectionTitle => $sectionData) {
                $sectionSlug = Str::slug($sectionTitle);
                // Seed documentation idempotently. The section identity is the
                // project + slug pair, which is protected by a unique index.
                // updateOrInsert keeps `php artisan db:seed` safe on an existing
                // installation instead of attempting a duplicate insert.
                $sortOrder = $sectionOrder++;
                $sectionDataRow = [
                    'title' => $sectionTitle,
                    'description' => $sectionData['description'] ?? null,
                    'icon' => $sectionData['icon'] ?? '◈',
                    'sort_order' => $sortOrder,
                    'is_published' => true,
                    'updated_at' => now(),
                ];

                DB::table('documentation_sections')->updateOrInsert(
                    [
                        'software_project_id' => $projectId,
                        'slug' => $sectionSlug,
                    ],
                    array_merge($sectionDataRow, [
                        'created_at' => now(),
                    ])
                );

                $sectionId = DB::table('documentation_sections')
                    ->where('software_project_id', $projectId)
                    ->where('slug', $sectionSlug)
                    ->value('id');
                $pageOrder = 0;
                foreach ($sectionData['pages'] as $page) {
                    $pageSlug = Str::slug($page['title']);
                    $existingPage = DB::table('documentation_pages')
                        ->where('software_project_id', $projectId)
                        ->whereNull('release_id')
                        ->where('slug', $pageSlug)
                        ->first();

                    $pageData = [
                        'documentation_section_id' => $sectionId,
                        'title' => $page['title'],
                        'kind' => $page['kind'] ?? 'guide',
                        'version' => 'Current',
                        'summary' => $page['summary'] ?? null,
                        'content' => $page['content'],
                        'sort_order' => $pageOrder++,
                        'is_published' => true,
                        'updated_at' => now(),
                    ];

                    if ($existingPage) {
                        DB::table('documentation_pages')
                            ->where('id', $existingPage->id)
                            ->update($pageData);
                    } else {
                        DB::table('documentation_pages')->insert(array_merge([
                            'software_project_id' => $projectId,
                            'release_id' => null,
                            'slug' => $pageSlug,
                            'created_at' => now(),
                        ], $pageData));
                    }
                }
            }
        }

        // Product-wide documentation enhancements. These are intentionally
        // idempotent so `php artisan db:seed` can safely be run on an existing
        // RozeHub installation without duplicating sections or pages.
        $enhancements = [
            'dbnavigator' => [
                'Use Cases' => ['description' => 'Practical database workflows where DBNavigator is useful.', 'icon' => '◎', 'pages' => [
                    ['title' => 'Database development workflows', 'kind' => 'tutorial', 'summary' => 'Use DBNavigator for schema exploration, SQL development, and data inspection.', 'content' => <<<'MD'
# Database development workflows

DBNavigator is useful anywhere a developer needs a focused database workspace.

## Common use cases

- Explore an unfamiliar schema before changing application code.
- Develop and verify SQL queries against development databases.
- Inspect relationships, indexes, and table structure.
- Troubleshoot data issues without switching between multiple tools.
- Validate migration results after a deployment.

## A practical workflow

```text
connect → inspect → query → verify → document
```

Keep production credentials restricted and use the least privilege required for the task.
MD
],
                ]],
                'Contributing' => ['description' => 'Build DBNavigator, propose changes, and contribute through GitHub.', 'icon' => '⌘', 'pages' => [
                    ['title' => 'Contributing to DBNavigator', 'kind' => 'development', 'summary' => 'Set up the project locally and submit focused contributions.', 'content' => <<<'MD'
# Contributing to DBNavigator

DBNavigator is an open-source project. Contributions should be small, reviewable, and focused on improving the database-client experience.

## Suggested flow

1. Fork the GitHub repository.
2. Create a feature or fix branch.
3. Build and run the application locally.
4. Add or update tests where appropriate.
5. Keep commits focused and descriptive.
6. Open a pull request with a clear problem statement and verification steps.

See the project's GitHub repository for the current build instructions and contribution discussions.
MD
],
                ]],
            ],
            'thundercall' => [
                'Use Cases' => ['description' => 'API development and testing workflows for ThunderCall.', 'icon' => '◎', 'pages' => [
                    ['title' => 'API development workflows', 'kind' => 'tutorial', 'summary' => 'Use ThunderCall to build, validate, document, and troubleshoot HTTP APIs.', 'content' => <<<'MD'
# API development workflows

ThunderCall fits naturally into API development from the first endpoint to regression testing.

## Common use cases

- Explore a new REST endpoint.
- Validate authentication and authorization behavior.
- Reproduce a production API issue safely in a test environment.
- Build reusable request collections for a team.
- Compare responses across environments.

Never place production secrets directly into shared collections.
MD
],
                ]],
                'Contributing' => ['description' => 'Contribute features, fixes, documentation, and test improvements.', 'icon' => '⌘', 'pages' => [
                    ['title' => 'Contributing to ThunderCall', 'kind' => 'development', 'summary' => 'A practical contribution workflow for the open-source API client.', 'content' => <<<'MD'
# Contributing to ThunderCall

Start by reading the repository README and existing issues before implementing a change.

## Contribution checklist

- Reproduce the problem when fixing a bug.
- Keep UI changes consistent with the existing design language.
- Add regression coverage where practical.
- Document new request, environment, or collection behavior.
- Explain compatibility impact in the pull request.
MD
],
                ]],
            ],
            'lumina' => [
                'Use Cases' => ['description' => 'Developer workflows supported by the Lumina workspace.', 'icon' => '◎', 'pages' => [
                    ['title' => 'Development workflows', 'kind' => 'tutorial', 'summary' => 'Use Lumina as a focused workspace for application and language development.', 'content' => <<<'MD'
# Development workflows

Lumina brings project navigation, editing, terminal work, and debugging into one workspace.

## Common use cases

- Work on multi-module applications.
- Develop Roze programs alongside language tooling.
- Run build and test commands from the integrated terminal.
- Debug a local application without leaving the project workspace.
- Explore unfamiliar repositories through structured navigation.
MD
],
                ]],
                'Contributing' => ['description' => 'Extend the IDE and improve the developer experience.', 'icon' => '⌘', 'pages' => [
                    ['title' => 'Contributing to Lumina', 'kind' => 'development', 'summary' => 'Guidance for contributors working on the IDE.', 'content' => <<<'MD'
# Contributing to Lumina

Prefer focused pull requests that improve one part of the developer workflow.

Before opening a pull request, verify the application starts cleanly, existing functionality still works, and UI changes remain consistent across supported platforms.
MD
],
                ]],
            ],
            'roze-language' => [
                'Use Cases' => ['description' => 'Where the Roze language is intended to be useful.', 'icon' => '◎', 'pages' => [
                    ['title' => 'Language use cases', 'kind' => 'tutorial', 'summary' => 'Explore the kinds of software Roze is intended to make understandable.', 'content' => <<<'MD'
# Language use cases

Roze is designed around understandable systems software and explicit developer intent.

## Potential use cases

- Command-line tools.
- Developer utilities.
- Systems-oriented applications.
- Educational experiments in compiler and language design.
- Tools that benefit from a small, explicit language runtime.

The language is evolving, so always check the version-specific reference before relying on experimental features.
MD
],
                ]],
                'Contributing' => ['description' => 'Help evolve the compiler, language tooling, and standard library.', 'icon' => '⌘', 'pages' => [
                    ['title' => 'Contributing to Roze', 'kind' => 'development', 'summary' => 'A contribution model for language, compiler, and documentation work.', 'content' => <<<'MD'
# Contributing to Roze

Language changes should explain the problem they solve and the effect on existing programs.

## Good contribution areas

- Compiler diagnostics.
- Parser and semantic analysis.
- Runtime and standard library improvements.
- Documentation and examples.
- Build and developer tooling.

For syntax or semantic changes, include examples showing both valid and invalid programs.
MD
],
                ]],
            ],
            'stratosdb' => [
                'Use Cases' => ['description' => 'Developer-first database-engine use cases for StratosDB.', 'icon' => '◎', 'pages' => [
                    ['title' => 'Database engine use cases', 'kind' => 'tutorial', 'summary' => 'Understand where an experimental database engine can fit.', 'content' => <<<'MD'
# Database engine use cases

StratosDB is intended as a developer-first database engine experiment.

## Potential use cases

- Local application data stores.
- Database-engine experimentation and research.
- Developer tools that need embedded or controlled storage.
- Learning about SQL execution, transactions, and storage engines.

Production suitability should always be evaluated against the release maturity and documented compatibility guarantees.
MD
],
                ]],
                'Contributing' => ['description' => 'Contribute to SQL execution, storage, recovery, tooling, and documentation.', 'icon' => '⌘', 'pages' => [
                    ['title' => 'Contributing to StratosDB', 'kind' => 'development', 'summary' => 'A contributor workflow for database-engine development.', 'content' => <<<'MD'
# Contributing to StratosDB

Database-engine changes can affect correctness, durability, and compatibility. Explain those effects clearly in every contribution.

## Useful contribution areas

- SQL parser and planner.
- Execution engine.
- Transactions and concurrency.
- Storage and recovery.
- CLI and diagnostics.
- Documentation and compatibility tests.

For storage changes, include recovery and failure-mode reasoning where applicable.
MD
],
                ]],
            ],
            'novaos' => [
                'Use Cases' => ['description' => 'The environments and experiments NOVAOS is designed to support.', 'icon' => '◎', 'pages' => [
                    ['title' => 'Operating-system use cases', 'kind' => 'tutorial', 'summary' => 'Understand NOVAOS as an independent operating-system project.', 'content' => <<<'MD'
# Operating-system use cases

NOVAOS is an independent operating-system project in the RozeHub ecosystem.

## Potential use cases

- Personal developer environments.
- Operating-system research and experimentation.
- Reproducible system-image experiments.
- Exploring the boundary between a base OS and application tooling.

NOVAOS is not positioned as a drop-in replacement for every general-purpose operating system workload.
MD
],
                ]],
                'Contributing' => ['description' => 'Contribute to the OS, build system, tooling, and documentation.', 'icon' => '⌘', 'pages' => [
                    ['title' => 'Contributing to NOVAOS', 'kind' => 'development', 'summary' => 'How to approach operating-system contributions safely.', 'content' => <<<'MD'
# Contributing to NOVAOS

System-level changes should be reproducible and include clear hardware and architecture assumptions.

Document the source revision, build environment, architecture, and test procedure for image-related changes.
MD
],
                ]],
            ],
            'trackeye' => [
                'Use Cases' => ['description' => 'Responsible workplace monitoring and operational reporting scenarios.', 'icon' => '◎', 'pages' => [
                    ['title' => 'Workplace monitoring use cases', 'kind' => 'tutorial', 'summary' => 'Examples of responsible ways to use TrackEye for operational visibility.', 'content' => <<<'MD'
# Workplace monitoring use cases

TrackEye should be deployed with a clear purpose, appropriate employee notice, and access controls.

## Common use cases

### Remote team visibility

Understand working-session patterns when teams are distributed across locations and time zones.

### Productivity analysis

Use aggregated activity summaries to identify workflow friction and improve processes rather than relying only on raw event volume.

### IT operations

Investigate application usage and workstation activity when troubleshooting operational issues.

### Reporting

Create periodic summaries for managers or operations teams while limiting access to sensitive raw telemetry.

## Responsible deployment

Define what is collected, why it is collected, who can access it, and how long it is retained before enabling monitoring.
MD
],
                ]],
                'Privacy & Security' => ['description' => 'Privacy-aware deployment, access control, retention, and employee notice.', 'icon' => '◆', 'pages' => [
                    ['title' => 'Privacy and responsible monitoring', 'kind' => 'operations', 'summary' => 'Principles for deploying TrackEye transparently and responsibly.', 'content' => <<<'MD'
# Privacy and responsible monitoring

Activity monitoring can expose sensitive information. Configure TrackEye around a documented organizational purpose.

## Before deployment

- Tell affected users what categories of information are collected.
- Define the purpose of collection.
- Minimize collection to what is necessary.
- Restrict access using least privilege.
- Define retention and deletion rules.
- Protect exported reports and backups.

## Security

Treat captured screenshots, activity records, and other telemetry as sensitive operational data. Protect storage and transport, rotate credentials, and audit administrative access.

RozeHub documentation describes product behavior; organizations remain responsible for meeting the laws, policies, and agreements applicable to their deployment.
MD
],
                ]],
                'Contributing' => ['description' => 'Develop TrackEye openly while keeping monitoring behavior clear and reviewable.', 'icon' => '⌘', 'pages' => [
                    ['title' => 'Contributing to TrackEye', 'kind' => 'development', 'summary' => 'Contribution guidance for the activity-tracking project.', 'content' => <<<'MD'
# Contributing to TrackEye

TrackEye contributions should prioritize correctness, transparency, security, and responsible data handling.

## Good contribution areas

- Activity collection reliability.
- Configuration and retention controls.
- Reporting and aggregation.
- Cross-platform support.
- Security improvements.
- Documentation and deployment tooling.

When changing telemetry behavior, explain what data is collected, when it is collected, where it is stored, and how administrators can control it.
MD
],
                ]],
            ],
        ];

        foreach ($enhancements as $slug => $sections) {
            $projectId = $projects[$slug] ?? null;
            if (!$projectId) {
                continue;
            }

            foreach ($sections as $sectionTitle => $sectionData) {
                $sectionSlug = Str::slug($sectionTitle);
                $sortOrder = (int) DB::table('documentation_sections')
                    ->where('software_project_id', $projectId)
                    ->where('slug', '!=', $sectionSlug)
                    ->max('sort_order') + 10;

                DB::table('documentation_sections')->updateOrInsert(
                    [
                        'software_project_id' => $projectId,
                        'slug' => $sectionSlug,
                    ],
                    [
                        'title' => $sectionTitle,
                        'description' => $sectionData['description'] ?? null,
                        'icon' => $sectionData['icon'] ?? '◈',
                        'sort_order' => $sortOrder,
                        'is_published' => true,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );

                $sectionId = DB::table('documentation_sections')
                    ->where('software_project_id', $projectId)
                    ->where('slug', $sectionSlug)
                    ->value('id');

                foreach ($sectionData['pages'] as $page) {
                    $pageSlug = Str::slug($page['title']);
                    $exists = DB::table('documentation_pages')
                        ->where('software_project_id', $projectId)
                        ->where('slug', $pageSlug)
                        ->whereNull('release_id')
                        ->exists();
                    if ($exists) {
                        continue;
                    }
                    DB::table('documentation_pages')->insert([
                        'software_project_id' => $projectId,
                        'documentation_section_id' => $sectionId,
                        'title' => $page['title'],
                        'slug' => $pageSlug,
                        'kind' => $page['kind'] ?? 'guide',
                        'version' => 'Current',
                        'summary' => $page['summary'] ?? null,
                        'content' => $page['content'],
                        'sort_order' => (int) DB::table('documentation_pages')->where('documentation_section_id', $sectionId)->max('sort_order') + 1,
                        'is_published' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
