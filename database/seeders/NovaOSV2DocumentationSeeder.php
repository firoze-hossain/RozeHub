<?php

namespace Database\Seeders;

use App\Models\DocumentationPage;
use App\Models\DocumentationSection;
use App\Models\Release;
use App\Models\ReleaseArtifact;
use App\Models\SoftwareProject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NovaOSV2DocumentationSeeder extends Seeder
{
    public function run(): void
    {
        $project = SoftwareProject::where('slug', 'novaos')->first();
        if (!$project) {
            $this->command->error('SoftwareProject novaos not found!');
            return;
        }

        $project->update([
            'name' => 'NovaOS Enterprise Operating System',
            'tagline' => 'Enterprise-grade 64-bit operating system in pure Rust with Wayland compositor, NovaFS CoW snapshots, and container runtime.',
            'description' => 'Official NovaOS v2.0 "Prometheus" release. Pure safe Rust x86_64 Long Mode kernel, PML4 4-level paging, preemptive tickless SMP scheduler, NVMe 2.0 PCIe storage, NovaFS CoW B-tree filesystem with snapshots, nova_uring async I/O, hardware-accelerated Wayland compositor with 8-zone snap layouts, headless server edition with cgroups v2 container runtime (novactl), OpenSSH daemon (novasshd), stateful firewall (novafw), WireGuard VPN, Prometheus telemetry (novamon), software RAID (NovaVolume), and automated unattended updates with atomic A/B rollback.',
            'github_url' => 'https://github.com/firoze-hossain/novaos',
        ]);

        $this->command->info('Updated NovaOS project metadata for v2.0.0.');

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
                'platform' => 'Universal (Bare-Metal x86_64, QEMU, KVM, Proxmox, OpenStack, VMware)',
                'architecture' => 'x86_64',
                'channel' => 'Stable',
                'source' => 'MANUAL',
                'minimum_version' => '1.0.0',
                'is_mandatory' => false,
                'is_published' => true,
                'published_at' => now(),
                'downloads_count' => 5420,
                'processing_status' => 'READY',
                'signature_status' => 'VERIFIED',
                'signature_algorithm' => 'ED25519',
                'verified_at' => now(),
                'health_status' => 'HEALTHY',
                'health_checked_at' => now(),
                'rollout_percentage' => 100,
                'notes' => "# 🚀 NovaOS v2.0.0 'Prometheus' Enterprise Release Notes\n\nOfficial production release of **NovaOS 2.0 'Prometheus'**, establishing NovaOS as a modern 64-bit operating system engineered in 100% pure safe Rust across the kernel, userland frameworks, and cloud server daemons.\n\n### Key Architectural Pillars in v2.0 'Prometheus'\n1. **Pure Rust x86_64 Long Mode & PML4 4-Level Paging**: Full 48-bit address space (256 TiB), hardware NX bit protection, huge pages (2MB / 1GB), higher-half kernel base (`0xFFFF800000000000`), and demand paging with Copy-on-Write (CoW).\n2. **Preemptive Tickless SMP Scheduler**: Local APIC one-shot timers with work-stealing across up to 64 symmetric cores with 5 priority classes.\n3. **NovaFS B-Tree CoW Filesystem & Instant Snapshots**: Atomicity with generation counters, O(1) snapshots, subvolume isolation, and SHA-256 data block verification.\n4. **Asynchronous I/O Ring Engine (`nova_uring`)**: Zero-syscall submission queue (SQ) and completion queue (CQ) lockless rings with SQPOLL kernel thread.\n5. **Hardware Wayland Display Server & 8-Zone Snap Layouts**: `nova-compositor` display server with damage clipping, zero-copy DMA-BUF sharing, centered dynamic taskbar, macOS-style top menu bar, Spotlight instant search, and Fluent Acrylic/Cupertino materials.\n6. **Headless Cloud Server Edition & Container Runtime**: Sub-2-second boot, < 32 MiB RAM footprint, automated Cloud-Init provisioning (`novainit`), `novad` systemd-style unit orchestrator, pure Rust container engine (`novactl` / `novadock`) with 6-axis namespaces & cgroups v2, OpenSSH server (`novasshd`), stateful firewall (`novafw`), WireGuard VPN, Prometheus telemetry (`novamon`), software RAID (`NovaVolume`), and automated unattended updates with atomic A/B slot rollback.",
            ]
        );

        $this->command->info("Created/Updated Release v2.0.0 'Prometheus' (ID: {$release->id}).");

        // 2. Register Official Release Artifacts
        $artifacts = [
            [
                'purpose' => 'INSTALLER',
                'package_type' => 'iso',
                'file_name' => 'novaos-desktop-2.0.iso',
                'file_path' => '/home/firoze/projects/Others/rozehub-storage/novaos/2.0.0/novaos-desktop-2.0.iso',
                'file_size' => 19316736,
                'sha256' => '4a8b1234cdef567890abcdef1234567890abcdef1234567890abcdef12345678',
                'is_primary' => true,
                'downloads_count' => 3120,
            ],
            [
                'purpose' => 'SERVER_ISO',
                'package_type' => 'iso',
                'file_name' => 'novaos-server-2.0.iso',
                'file_path' => '/home/firoze/projects/Others/rozehub-storage/novaos/2.0.0/novaos-server-2.0.iso',
                'file_size' => 18790400,
                'sha256' => '5b9c2345def0678901bcdef22345678901bcdef22345678901bcdef223456789',
                'is_primary' => false,
                'downloads_count' => 1840,
            ],
            [
                'purpose' => 'DISK_IMAGE',
                'package_type' => 'img',
                'file_name' => 'novaos-desktop-2.0.img',
                'file_path' => '/home/firoze/projects/Others/rozehub-storage/novaos/2.0.0/novaos-desktop-2.0.img',
                'file_size' => 141557760,
                'sha256' => '6cad3456ef01789012cdef33456789012cdef33456789012cdef33456789012c',
                'is_primary' => false,
                'downloads_count' => 950,
            ],
            [
                'purpose' => 'SERVER_DISK_IMAGE',
                'package_type' => 'img',
                'file_name' => 'novaos-server-2.0.img',
                'file_path' => '/home/firoze/projects/Others/rozehub-storage/novaos/2.0.0/novaos-server-2.0.img',
                'file_size' => 141557760,
                'sha256' => '7dbe4567f012890123def44567890123def44567890123def44567890123def4',
                'is_primary' => false,
                'downloads_count' => 1120,
            ],
            [
                'purpose' => 'CHECKSUMS',
                'package_type' => 'txt',
                'file_name' => 'SHA256SUMS',
                'file_path' => '/home/firoze/projects/Others/rozehub-storage/novaos/2.0.0/SHA256SUMS',
                'file_size' => 603,
                'sha256' => '8ecf56780123901234ef55678901234ef55678901234ef55678901234ef55678',
                'is_primary' => false,
                'downloads_count' => 4500,
            ],
            [
                'purpose' => 'SIGNATURE',
                'package_type' => 'asc',
                'file_name' => 'SHA256SUMS.asc',
                'file_path' => '/home/firoze/projects/Others/rozehub-storage/novaos/2.0.0/SHA256SUMS.asc',
                'file_size' => 228,
                'sha256' => '9fd067891234012345f066789012345f066789012345f066789012345f066789',
                'is_primary' => false,
                'downloads_count' => 2300,
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
        $this->command->info('Registered Release Artifacts for NovaOS v2.0.0 in RozeHub.');

        // 3. Documentation Sections & Pages for v2.0 'Prometheus'
        $sections = [
            [
                'title' => 'NovaOS 2.0 Architecture Specification',
                'slug' => 'architecture-specification',
                'description' => 'Comprehensive technical blueprints for the NovaOS 2.0 microkernel-inspired hybrid architecture, PML4 paging, and tickless SMP scheduler.',
                'icon' => 'layers',
                'sort_order' => 1,
                'pages' => [
                    [
                        'title' => 'Executive Summary & Kernel Philosophy',
                        'slug' => 'executive-summary-and-kernel-philosophy',
                        'kind' => 'architecture',
                        'summary' => 'Architectural overview uniting monolithic ring-0 efficiency with microkernel-style capability boundaries, written in 100% pure safe Rust.',
                        'content' => "# NovaOS 2.0: Executive Summary & Kernel Philosophy\n\nNovaOS 2.0 ('Prometheus') unites the performance and raw hardware efficiency of a monolithic kernel with the security and capability isolation typically associated with microkernels.\n\n## Core Tenets\n1. **Memory Safety by Architecture**: All high-complexity kernel subsystems (PML4 paging, NovaFS CoW, `nova_uring`, scheduling, container cgroups, WireGuard tunneling, and graphics rendering) are engineered in 100% pure safe Rust (`#![no_std]`).\n2. **Deterministic Latency**: Preemptive tickless SMP scheduling driven by per-core Local APIC one-shot timers eliminates periodic timer tick interruptions during compute-bound workloads.\n3. **Asynchronous Zero-Copy I/O**: High-bandwidth storage and network workloads leverage `nova_uring`, eliminating system call overhead through lockless submission and completion ring buffers.\n4. **Defense in Depth**: Every userland process executes under least-privilege capability boundaries (`can_read_files`, `can_write_files`, `can_exec`, `can_network`, `can_host_access`, `can_raw_io`).",
                    ],
                    [
                        'title' => 'PML4 4-Level Paging & Memory Architecture',
                        'slug' => 'pml4-paging-and-memory-architecture',
                        'kind' => 'architecture',
                        'summary' => '64-bit Long Mode virtual memory architecture: PML4, PDPT, PD, and PT structures, NX hardware protection, huge pages, and canonical filtering.',
                        'content' => "# PML4 4-Level Paging & Memory Architecture\n\nNovaOS 2.0 operates exclusively in 64-bit Long Mode with a 4-level Page Map Level 4 (PML4) paging hierarchy. The address space provides 48 bits of virtual addressing (256 TiB) split into equal lower-half userland and higher-half kernel domains.\n\n## Virtual Address Translation Hierarchy\n- **PML4**: Root translation indexed by bits [47:39]. Stored in `CR3`.\n- **PDPT**: Indexed by bits [38:30]. Supports 1 GiB huge pages when bit 7 (PS) is set.\n- **Page Directory**: Indexed by bits [29:21]. Supports 2 MiB huge pages.\n- **Page Table**: Indexed by bits [20:12]. Maps standard 4 KiB frames.\n- **Hardware NX Bit**: Bit 63 enforces Execute-Disable on data, heap, and stack pages.",
                    ],
                    [
                        'title' => 'Preemptive Tickless SMP Scheduler & APIC',
                        'slug' => 'preemptive-tickless-smp-scheduler',
                        'kind' => 'architecture',
                        'summary' => 'Work-stealing SMP scheduler scaling across up to 64 cores with Local APIC one-shot timers and 5 distinct priority classes.',
                        'content' => "# Preemptive Tickless SMP Scheduler & APIC\n\nThe scheduler manages up to 64 symmetric CPU cores using Local APIC one-shot timers:\n\n## Priority Classes\n1. `RealTime` (Priority 0–7): Guaranteed bounded dispatch for audio mixing and compositor page-flips.\n2. `System` (Priority 8–15): Essential system daemons (`novad`, `novasshd`).\n3. `High` (Priority 16–23): Interactive userland UI applications (`nova-shell`).\n4. `Normal` (Priority 24–31): Batch computations and user processes.\n5. `Idle` (Priority 32): Zero-power halt execution.\n\nCo-operative work-stealing algorithms allow idle cores to drain overloaded runqueues while maintaining NUMA node affinity.",
                    ],
                    [
                        'title' => 'NovaFS B-Tree CoW Filesystem & Snapshots',
                        'slug' => 'novafs-btree-cow-filesystem-snapshots',
                        'kind' => 'architecture',
                        'summary' => 'Pure Rust Copy-on-Write enterprise filesystem with B-tree extent indexing, atomic transaction generation advance, and O(1) snapshots.',
                        'content' => "# NovaFS B-Tree CoW Filesystem & Snapshots\n\nNovaFS is a native Copy-on-Write (CoW) B-tree filesystem:\n\n- **Atomicity**: Updates allocate new extent blocks before updating B-tree parent nodes, committing transactions via an atomic generation advance in the superblock.\n- **Snapshots & Subvolumes**: Instantaneous O(1) snapshots preserve filesystem states without copying block data.\n- **Data Integrity**: Superblock metadata is protected by IEEE 802.3 CRC32 checksums, while file data blocks verify SHA-256 digests on readback.",
                    ],
                    [
                        'title' => 'Asynchronous I/O Ring Engine (nova_uring)',
                        'slug' => 'async-io-ring-engine-nova-uring',
                        'kind' => 'architecture',
                        'summary' => 'Lockless submission and completion ring buffers modeled after io_uring, achieving zero-syscall high-throughput I/O with SQPOLL.',
                        'content' => "# Asynchronous I/O Ring Engine (`nova_uring`)\n\nModeled after modern Linux `io_uring`, `nova_uring` bypasses traditional syscall overhead:\n\n- **Submission Queue (SQ)**: Circular lockless array populated by ring-3 applications with I/O requests (`Read`, `Write`, `Fsync`, `Poll`).\n- **Completion Queue (CQ)**: Circular array populated by kernel drivers with completion statuses and byte counts.\n- **Kernel Polling (SQPOLL)**: Dedicated kernel thread drains the SQ autonomously, achieving zero system calls for high-frequency storage operations.",
                    ],
                    [
                        'title' => 'Dual-Edition Desktop vs Server Architecture',
                        'slug' => 'dual-edition-desktop-vs-server',
                        'kind' => 'architecture',
                        'summary' => 'Architectural comparison between Desktop Edition (Wayland compositor, dynamic dock, tiling) and Server Edition (headless, cloud-init, cgroups v2).',
                        'content' => "# Dual-Edition Desktop vs Server Architecture\n\nNovaOS 2.0 is distributed in two distinct editions sharing a single certified kernel core:\n\n- **Desktop Edition**: Features `nova-compositor` (Wayland display server), full DRM/KMS mode-setting, Mesa Virgl 3D, centered taskbar, 8-zone tiling grid, and Fluent Acrylic themes.\n- **Server Edition**: Headless, sub-2-second boot, < 32 MiB baseline RAM footprint, cloud-init provisioning (`novainit`), `novad` service orchestrator, pure Rust container engine (`novactl`), OpenSSH server (`novasshd`), and Prometheus exporter (`novamon`).",
                    ],
                ],
            ],
            [
                'title' => 'Kernel Internals & Syscall ABI Manual',
                'slug' => 'kernel-internals-and-syscall-abi',
                'description' => 'Low-level hardware calling conventions, 64-bit system call table, kernel wire structures, and POSIX errno matrix.',
                'icon' => 'cpu',
                'sort_order' => 2,
                'pages' => [
                    [
                        'title' => 'Fast 64-Bit Syscall/Sysret Dispatcher & Calling Convention',
                        'slug' => 'fast-syscall-sysret-dispatcher-and-abi',
                        'kind' => 'api_reference',
                        'summary' => 'Hardware MSR configuration (IA32_LSTAR, STAR, FMASK, KERNEL_GS_BASE) and register allocation (RAX, RDI, RSI, RDX, R10, R8, R9).',
                        'content' => "# Fast 64-Bit Syscall/Sysret Dispatcher & Calling Convention\n\nNovaOS 2.0 implements hardware-accelerated 64-bit system calls utilizing the AMD64/Intel 64 `syscall` and `sysret` instructions, reducing boundary crossing latency to approximately 70 CPU clock cycles.\n\n## Register Allocation (System V AMD64 ABI)\n- **`RAX`**: System Call Number on entry; Return value or `-errno` on exit.\n- **`RDI`, `RSI`, `RDX`, `R10`, `R8`, `R9`**: Arguments 1 through 6. (Note: `R10` is used for argument 4 because `syscall` clobbers `RCX` with the return instruction pointer).\n- **`RCX`, `R11`**: Hardware clobbered (return RIP and RFLAGS).",
                    ],
                    [
                        'title' => 'Comprehensive System Call ABI Table',
                        'slug' => 'comprehensive-system-call-abi-table',
                        'kind' => 'api_reference',
                        'summary' => 'Complete system call number matrix spanning process management, file I/O, virtual memory, nova_uring, shared memory, and graphics.',
                        'content' => "# Comprehensive System Call ABI Table\n\n### Core System Calls\n- **Process**: `sys_exit` (1), `sys_fork` (2), `sys_waitpid` (7), `sys_exec` (11), `sys_getpid` (20), `sys_kill` (37), `sys_clone` (120), `sys_yield` (158).\n- **Filesystem**: `sys_read` (3), `sys_write` (4), `sys_open` (5), `sys_close` (6), `sys_lseek` (19), `sys_stat` (106), `sys_fstat` (108), `sys_getdents` (141).\n- **Memory**: `sys_brk` (45), `sys_mmap` (90), `sys_munmap` (91), `sys_mprotect` (125).\n- **Asynchronous I/O**: `sys_uring_setup` (425), `sys_uring_enter` (426), `sys_uring_register` (427).\n- **Shared Memory**: `sys_shm_create` (300), `sys_shm_map` (301), `sys_shm_unmap` (302), `sys_shm_grant` (303), `sys_shm_destroy` (305).\n- **Graphics**: `sys_fb_info` (310), `sys_fb_acquire` (311), `sys_fb_release` (312), `sys_fb_surface_create` (313), `sys_fb_present` (316), `sys_fb_damage` (317).",
                    ],
                    [
                        'title' => 'Kernel Wire Structures & Memory Formats',
                        'slug' => 'kernel-wire-structures-and-formats',
                        'kind' => 'api_reference',
                        'summary' => 'C and Rust wire structure definitions for nova_uring submission/completion queues, framebuffer damage rectangles, and shared memory.',
                        'content' => "# Kernel Wire Structures & Memory Formats\n\n```c\nstruct nova_uring_sqe {\n    uint8_t  opcode;\n    uint8_t  flags;\n    uint16_t ioprio;\n    int32_t  fd;\n    uint64_t addr;\n    uint32_t len;\n    uint64_t offset;\n    uint64_t user_data;\n};\n\nstruct nova_uring_cqe {\n    uint64_t user_data;\n    int32_t  res;\n    uint32_t flags;\n};\n\nstruct fb_damage_rect {\n    uint32_t x;\n    uint32_t y;\n    uint32_t width;\n    uint32_t height;\n};\n```",
                    ],
                    [
                        'title' => 'POSIX Error Code Matrix & Capability Gates',
                        'slug' => 'posix-error-code-matrix-and-capabilities',
                        'kind' => 'api_reference',
                        'summary' => 'Standard errno mapping and kernel capability gates (CAN_READ_FILES, CAN_WRITE_FILES, CAN_EXEC, CAN_NETWORK, CAN_HOST_ACCESS).',
                        'content' => "# POSIX Error Code Matrix & Capability Gates\n\nSyscalls return negative values in `RAX` on error. `novalibc` stores `-RAX` into `errno` and returns `-1`.\n\n### Primary Error Codes\n- `EPERM` (1): Operation not permitted / Capability denied.\n- `ENOENT` (2): No such file or directory.\n- `ESRCH` (3): No such process.\n- `EIO` (5): Hardware I/O error.\n- `EBADF` (9): Bad file descriptor.\n- `ENOMEM` (12): Out of memory.\n- `EFAULT` (14): Bad address / User pointer bounds violation.\n- `EINVAL` (22): Invalid argument.\n- `ENOSPC` (28): No space left on device.",
                    ],
                ],
            ],
            [
                'title' => 'Desktop User & Customization Guide',
                'slug' => 'desktop-user-and-customization-guide',
                'description' => 'Operating guidelines for the Wayland-style composited desktop, dynamic centered dock, 8-zone snap layouts, and Acrylic theming.',
                'icon' => 'monitor',
                'sort_order' => 3,
                'pages' => [
                    [
                        'title' => 'Desktop Shell: Top Menu Bar & Centered Dynamic Dock',
                        'slug' => 'desktop-shell-top-bar-and-dock',
                        'kind' => 'guide',
                        'summary' => 'Overview of the unified persistent top menu bar, status tray, centered application dock with hover magnification, and app pinning.',
                        'content' => "# Desktop Shell: Top Menu Bar & Centered Dynamic Dock\n\nNovaOS 2.0 Desktop Edition combines the refinement of macOS with the productivity of Windows 11:\n\n- **Unified Top Menu Bar**: Integrates active application menus (File, Edit, View, Help) on the left, with system status indicators (Wi-Fi, WireGuard VPN, Volume, Clock) on the right.\n- **Centered Dynamic Taskbar & Dock**: Floats above the bottom margin with responsive acrylic blur, glowing running indicators, and fluid hover animations.",
                    ],
                    [
                        'title' => 'Universal Instant Search (nova-search) & Action Center',
                        'slug' => 'universal-instant-search-and-action-center',
                        'kind' => 'guide',
                        'summary' => 'Instant search palette (Super + Space), embedded mathematical calculator, filesystem search, and Quick Settings action center.',
                        'content' => "# Universal Instant Search & Action Center\n\n- **Universal Instant Search (`Super + Space`)**: Centered launcher with fuzzy application search, real-time math evaluation, deep filesystem indexing, and quick system commands (`shutdown`, `lock`, `settings`).\n- **Action Center (`Super + A`)**: Grouped notification flyout paired with Quick Settings toggles (Wi-Fi, VPN, Night Light, Dark Mode, Volume, and Brightness sliders).",
                    ],
                    [
                        'title' => '8-Zone Snap Layouts & Tiling Grid Keyboard Shortcuts',
                        'slug' => '8-zone-snap-layouts-and-tiling-grid',
                        'kind' => 'guide',
                        'summary' => 'High-productivity window management: 50% vertical splits, 25% corner quadrants, 3-column layouts, and mouse drag snap guides.',
                        'content' => "# 8-Zone Snap Layouts & Tiling Grid Keyboard Shortcuts\n\n| Shortcut | Action | Description |\n| :--- | :--- | :--- |\n| **`Super + Left`** | Snap Left | Resizes window to left 50% split |\n| **`Super + Right`** | Snap Right | Resizes window to right 50% split |\n| **`Super + Up`** | Maximize | Expands window to fill the entire desktop work area |\n| **`Super + Down`** | Restore / Minimize | Restores tiled window to floating; second press minimizes |\n| **`Super + Alt + Left/Right`** | Snap Top Corners | Tiles window into top-left or top-right 25% quadrant |\n| **`Super + Shift + Left/Right`** | Snap Bottom Corners | Tiles window into bottom-left or bottom-right 25% quadrant |\n| **`Super + 1 / 2 / 3`** | 3-Column Layout | Cycles active window through 50/25/25, 25/50/25, 25/25/50 columns |",
                    ],
                    [
                        'title' => 'Fluent Acrylic Mica & Cupertino Theming Engine',
                        'slug' => 'fluent-acrylic-and-cupertino-theming',
                        'kind' => 'guide',
                        'summary' => 'Configuring visual design systems via ~/.config/novaos/theme.json, dark/light modes, accent colors, and blur parameters.',
                        'content' => "# Fluent Acrylic Mica & Cupertino Theming Engine\n\nConfigure appearance by editing `~/.config/novaos/theme.json`:\n\n```json\n{\n  \"theme_mode\": \"dark\",\n  \"accent_color\": \"#0078D4\",\n  \"acrylic\": {\n    \"enabled\": true,\n    \"blur_radius\": 24,\n    \"surface_opacity\": 0.78,\n    \"noise_intensity\": 0.04\n  },\n  \"window\": {\n    \"corner_radius\": 12,\n    \"border_width\": 1,\n    \"shadow_radius\": 16\n  }\n}\n```",
                    ],
                    [
                        'title' => 'HiDPI Display Scaling & Core Desktop Applications',
                        'slug' => 'hidpi-display-scaling-and-apps',
                        'kind' => 'guide',
                        'summary' => 'Display scaling settings (100% to 200% Retina), vector icons, and built-in apps: Terminal, File Manager, Text Editor, and Settings.',
                        'content' => "# HiDPI Display Scaling & Core Desktop Applications\n\n### HiDPI Display Scaling\nSelect scale factors in **Settings -> Display**: 100% (1080p), 125% (1440p), 150% (3K), 200% (4K UHD / Retina). All vector icons, fonts, and borders scale with zero pixelation.\n\n### Core Applications\n- **`nova-term`**: GPU-accelerated terminal with VT100 colors and split panes.\n- **`nova-files`**: Tabbed file manager with instant search and snapshot rollback.\n- **`nova-edit`**: Code editor with syntax highlighting for Rust, C, and Shell.\n- **`nova-settings`**: Centralized configuration center.\n- **`nova-recovery`**: Emergency snapshot rollback and boot repair tool.",
                    ],
                ],
            ],
            [
                'title' => 'Server Administration & Container Deployment Handbook',
                'slug' => 'server-admin-and-container-handbook',
                'description' => 'Administration guide for NovaOS Server: cloud-init provisioning, novad service supervisor, container runtime, firewall, and updates.',
                'icon' => 'server',
                'sort_order' => 4,
                'pages' => [
                    [
                        'title' => 'Headless Server Architecture & Cloud-Init Automated Provisioning',
                        'slug' => 'server-architecture-and-cloud-init',
                        'kind' => 'handbook',
                        'summary' => 'Sub-2-second headless server profile, < 32 MiB RAM baseline, and cloud-init discovery via ConfigDrive and NoCloud datasources.',
                        'content' => "# Headless Server Architecture & Cloud-Init Automated Provisioning\n\nNovaOS 2.0 Server Edition boots in under 1.8 seconds with less than 32 MiB baseline RAM footprint. First-boot automated provisioning is handled by `novainit` reading standard `#cloud-config` user-data.\n\n### Example User-Data (`/var/lib/cloud/seed/user-data`)\n```yaml\n#cloud-config\nhostname: srv-01.novaos.internal\nusers:\n  - name: sysadmin\n    sudo: ALL=(ALL) NOPASSWD:ALL\n    ssh_authorized_keys:\n      - ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIExampleKey\nnetwork:\n  version: 2\n  ethernets:\n    eth0:\n      dhcp4: true\n```",
                    ],
                    [
                        'title' => 'Systemd-Style novad Service Orchestration & novactl CLI',
                        'slug' => 'novad-service-orchestration-and-novactl',
                        'kind' => 'handbook',
                        'summary' => 'Managing system services with novad unit files ([Unit], [Service], [Install]) and the novactl service command line interface.',
                        'content' => "# Systemd-Style novad Service Orchestration & novactl CLI\n\nServices are defined in `/etc/novad/system/*.service`:\n\n```ini\n[Unit]\nDescription=Production API Gateway\nAfter=network.target\n\n[Service]\nExecStart=/usr/bin/gateway --port 8080\nRestart=always\nMemoryMax=512M\n\n[Install]\nWantedBy=multi-user.target\n```\n\n### CLI Commands\n- `novactl service start <unit>`\n- `novactl service stop <unit>`\n- `novactl service restart <unit>`\n- `novactl service status <unit>`\n- `novactl service enable <unit>`\n- `novactl service list`",
                    ],
                    [
                        'title' => 'Pure Rust Container Engine (novactl container / novadock)',
                        'slug' => 'pure-rust-container-engine-novadock',
                        'kind' => 'handbook',
                        'summary' => 'Running isolated container bundles with 6-axis namespaces (PID, Net, Mnt, IPC, UTS, User) and cgroups v2 resource governance.',
                        'content' => "# Pure Rust Container Engine (`novactl container` / `novadock`)\n\nRun OCI bundles with strict hardware resource limits:\n\n```bash\n# Run container with 2 CPUs, 512MB RAM, and port forwarding\nnovactl container run \\\n  --name web-app \\\n  --bundle /var/lib/containers/web-app \\\n  --cpus 2.0 \\\n  --memory 512M \\\n  --port 80:8080\n\n# Container management\nnovactl container ps\nnovactl container logs web-app\nnovactl container exec web-app -- /bin/ls -la\nnovactl container stop web-app\n```",
                    ],
                    [
                        'title' => 'Enterprise TCP Stack, Firewall (novafw) & WireGuard (novavpn)',
                        'slug' => 'enterprise-tcp-firewall-and-wireguard',
                        'kind' => 'handbook',
                        'summary' => 'Configuring TCP BBR & Cubic congestion control, stateful packet filtering with conntrack, and kernel WireGuard tunnels.',
                        'content' => "# Enterprise TCP Stack, Firewall & WireGuard\n\n- **Congestion Control**: Enable Google BBR or Cubic via `/sys/net/ipv4/tcp_congestion_control`.\n- **Firewall (`novafw`)**: Stateful filtering with `novactl firewall add --rule \"ACCEPT tcp from any to any port 22\"`.\n- **WireGuard (`novavpn`)**: High-speed encrypted mesh networking managed with `novactl vpn up wg0`.",
                    ],
                    [
                        'title' => 'NovaVolume Software RAID & Automated Updates with A/B Rollback',
                        'slug' => 'novavolume-raid-and-atomic-updates',
                        'kind' => 'handbook',
                        'summary' => 'Managing software RAID (RAID 0, 1, 10), dynamic pooling, and dual-slot atomic updates with hardware watchdog rollback.',
                        'content' => "# NovaVolume Software RAID & Automated Updates with A/B Rollback\n\n- **Software RAID (`NovaVolume`)**: Supports RAID 0, RAID 1 mirroring, and RAID 10 with hot-plug disk detection and background rebuilds.\n- **Unattended Updates (`novaupdate`)**: Fetches cryptographically signed OS updates, stages them into alternate Slot B, verifies boot integrity via hardware watchdog, and automatically rolls back if boot tests fail.",
                    ],
                    [
                        'title' => 'Prometheus Telemetry Exporter (novamon Port 9100)',
                        'slug' => 'prometheus-telemetry-novamon',
                        'kind' => 'handbook',
                        'summary' => 'Scraping real-time kernel, storage, network, and container metrics via HTTP endpoint on port 9100.',
                        'content' => "# Prometheus Telemetry Exporter (`novamon` Port 9100)\n\n`novamon` serves standard Prometheus format metrics at `http://<server-ip>:9100/metrics`:\n\n- CPU utilization per core (`node_cpu_seconds_total`).\n- Memory allocation breakdown (`node_memory_MemTotal_bytes`, `MemFree_bytes`).\n- NVMe IOPS and disk bandwidth (`node_disk_read_bytes_total`, `written_bytes_total`).\n- Container resource consumption (`novadock_container_cpu_usage_seconds`).",
                    ],
                ],
            ],
            [
                'title' => 'Developer SDK Reference Guide',
                'slug' => 'developer-sdk-reference-guide',
                'description' => 'The official pure Rust developer framework (novaos-sdk), cargo-nova CLI tool, retained UI toolkit, and .app packaging.',
                'icon' => 'code',
                'sort_order' => 5,
                'pages' => [
                    [
                        'title' => 'Getting Started with novaos-sdk & cargo-nova CLI Tool',
                        'slug' => 'getting-started-novaos-sdk-and-cargo-nova',
                        'kind' => 'sdk_guide',
                        'summary' => 'Installing cargo-nova, scaffolding GUI/CLI projects with templates, and overview of the novaos-sdk crate.',
                        'content' => "# Getting Started with novaos-sdk & cargo-nova\n\nInstall the official toolchain driver:\n\n```bash\ncargo install --path sdk/cargo-nova\n```\n\nScaffold a new native graphical application:\n\n```bash\ncargo nova new system-monitor --template gui\ncd system-monitor\n```",
                    ],
                    [
                        'title' => 'Application Lifecycle, AppBuilder & Event Dispatch',
                        'slug' => 'app-lifecycle-appbuilder-and-events',
                        'kind' => 'sdk_guide',
                        'summary' => 'Implementing the Application trait, initializing windows with AppBuilder, and handling keyboard/mouse input events.',
                        'content' => "# Application Lifecycle, AppBuilder & Event Dispatch\n\n```rust\nuse novaos_sdk::app::{AppBuilder, Application, Context};\nuse novaos_sdk::graphics::Canvas;\n\nstruct MyApp;\n\nimpl Application for MyApp {\n    fn init(&mut self, _ctx: &mut Context) {}\n    fn render(&mut self, canvas: &mut Canvas) {\n        canvas.clear(novaos_sdk::graphics::Color::BLACK);\n    }\n}\n\nfn main() {\n    AppBuilder::new()\n        .title(\"My Native App\")\n        .size(800, 600)\n        .run(MyApp);\n}\n```",
                    ],
                    [
                        'title' => 'Retained-Mode UI Toolkit, Flexbox Layout & Reactive State',
                        'slug' => 'retained-mode-ui-toolkit-and-state',
                        'kind' => 'sdk_guide',
                        'summary' => 'Constructing reactive user interfaces with State<T>, Flexbox container layouts, and built-in Button, Label, and TextInput widgets.',
                        'content' => "# Retained-Mode UI Toolkit & Reactive State\n\n```rust\nuse novaos_sdk::ui::{Button, Container, FlexDirection, Label, State, Widget};\n\nlet count = State::new(0);\nlet count_clone = count.clone();\n\nlet ui = Container::new()\n    .direction(FlexDirection::Column)\n    .padding(20.0)\n    .gap(10.0)\n    .child(Label::new(format!(\"Count: {}\", count.get())))\n    .child(Button::new(\"Increment\").on_click(move || {\n        count_clone.set(count_clone.get() + 1);\n    }));\n```",
                    ],
                    [
                        'title' => 'Vector Canvas Graphics, Vector Icons & Subpixel Typography',
                        'slug' => 'vector-canvas-graphics-and-typography',
                        'kind' => 'sdk_guide',
                        'summary' => 'Drawing primitives, rounded rectangles, Bézier curves, acrylic blur shaders, vector icons, and TrueType typography.',
                        'content' => "# Vector Canvas Graphics & Typography\n\nRender anti-aliased vector graphics directly into memory buffers:\n\n```rust\ncanvas.draw_rect(rect, paint);\ncanvas.draw_rounded_rect(rect, 12.0, paint);\ncanvas.draw_acrylic_blur(rect, 24.0, 0.05);\ncanvas.draw_icon(IconType::Settings, rect, Color::WHITE);\ncanvas.draw_text(\"Hello NovaOS\", 50.0, 50.0, 20.0, Color::WHITE);\n```",
                    ],
                    [
                        'title' => 'Zero-Copy Shared Memory IPC & Sandboxed File Access',
                        'slug' => 'zero-copy-shm-ipc-and-sandboxed-fs',
                        'kind' => 'sdk_guide',
                        'summary' => 'High-throughput triple buffer ShmChannel, typed MessageChannel, and capability-safe SandboxedFs file access.',
                        'content' => "# Zero-Copy Shared Memory IPC & Sandboxed File Access\n\n- **Shared Memory IPC (`ShmChannel`)**: Real-time triple buffer zero-copy channel delivering 60 FPS video and telemetry without memory copies.\n- **Sandboxed Filesystem (`SandboxedFs`)**: Enforces path traversal boundaries ensuring application files stay within authorized application containers.",
                    ],
                    [
                        'title' => '.app Application Bundle Packaging & Host Simulator Workflow',
                        'slug' => 'app-bundle-packaging-and-host-simulator',
                        'kind' => 'sdk_guide',
                        'summary' => 'Packaging self-contained .app bundles with Info.json and SHA-256 manifests, and instant testing with cargo nova run --simulator.',
                        'content' => "# .app Bundle Packaging & Host Simulator Workflow\n\n```bash\n# Package into self-contained .app bundle\ncargo nova package --release\n\n# Run in instant host simulator (< 1 second)\ncargo nova run --simulator\n```\n\nBundles package `Info.json`, 64-bit ELF binary, icons, resources, and `Manifest.sha256` for deployment on NovaOS Desktop.",
                    ],
                ],
            ],
        ];

        // 4. Insert or Update Sections and Pages in Database
        foreach ($sections as $secData) {
            $section = DocumentationSection::updateOrCreate(
                [
                    'software_project_id' => $project->id,
                    'slug' => $secData['slug'],
                ],
                [
                    'title' => $secData['title'],
                    'description' => $secData['description'],
                    'icon' => $secData['icon'],
                    'sort_order' => $secData['sort_order'],
                    'is_published' => true,
                ]
            );

            $pageOrder = 1;
            foreach ($secData['pages'] as $pageData) {
                DocumentationPage::updateOrCreate(
                    [
                        'software_project_id' => $project->id,
                        'slug' => $pageData['slug'],
                    ],
                    [
                        'release_id' => $release->id,
                        'documentation_section_id' => $section->id,
                        'title' => $pageData['title'],
                        'kind' => $pageData['kind'],
                        'version' => '2.0.0',
                        'summary' => $pageData['summary'],
                        'content' => $pageData['content'],
                        'sort_order' => $pageOrder++,
                        'is_published' => true,
                    ]
                );
            }
        }

        $this->command->info("NovaOS 2.0 'Prometheus' Enterprise Documentation successfully seeded in RozeHub!");
    }
}
