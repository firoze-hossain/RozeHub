<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NovaOSDocumentationSeeder extends Seeder
{
    public function run(): void
    {
        $project = DB::table('software_projects')->where('slug', 'novaos')->first();
        if (!$project) {
            $this->command->error("NOVAOS project not found in database.");
            return;
        }

        $projectId = $project->id;
        $now = Carbon::now();

        // 1. Ensure Release 1.0.0 is created and published for NOVAOS
        $releaseHash = hash('sha256', implode('|', [
            (string) $projectId,
            '1.0.0',
            'NOVAOS',
            'i686',
            'Stable',
        ]));

        DB::table('releases')->updateOrInsert(
            [
                'software_project_id' => $projectId,
                'version' => '1.0.0',
            ],
            [
                'platform' => 'NOVAOS',
                'architecture' => 'i686',
                'channel' => 'Stable',
                'release_identity_hash' => $releaseHash,
                'notes' => "NovaOS 1.0.0 - Complete 6-Month Build Plan Milestone Release.\n\n" .
                    "- 26 Kernel Track features: Paging, Ext2 & FAT32 with journaling, MBR disk partitioning, SMP 2-core scheduling, " .
                    "Shared memory IPC, App-to-App messaging, AC97 audio mixing, RTL8139 networking (DHCP, ARP, IPv4, ICMP, UDP, TCP, DNS, TFTP), " .
                    "Stateful packet filter firewall, Mandatory Access Control (MAC), Resource limits (rlimit), Structured kernel logging (klog), " .
                    "and Dual display backends (Bochs VBE 1024x768x32bpp linear framebuffer & VirtIO-GPU 2D).\n" .
                    "- 26 Interface Track features: Pure #![no_std] Rust ring-3 userland with zero-syscall BufferSurface32 rasterization, " .
                    "Window Manager with 8-zone window snapping and dynamic tiling, File Browser, VT100 Terminal, Monospace Text Editor, " .
                    "Package Store, Modular Settings Shell (Display, Services, Devices, Firewall, Updates), Login & Lockscreen (PBKDF2-HMAC-SHA256), " .
                    "Activity Monitor, Kernel Log Viewer, Graphical Installer Wizard, Crash Reporter, and First-Run Setup Wizard.",
                'is_published' => true,
                'published_at' => $now,
                'processing_status' => 'READY',
                'rollout_percentage' => 100,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        // Fetch Release ID
        $release = DB::table('releases')
            ->where('software_project_id', $projectId)
            ->where('version', '1.0.0')
            ->first();

        // 2. Define User Documentation Sections and Pages
        $sections = [
            [
                'title' => 'User Guide & Desktop Applications',
                'slug' => 'user-guide-and-applications',
                'description' => 'Comprehensive user manual for the NovaOS desktop environment, window manager, and ring-3 applications.',
                'icon' => '🖥️',
                'sort_order' => 15,
                'pages' => [
                    [
                        'title' => 'Desktop Environment & Window Manager',
                        'slug' => 'desktop-environment-and-window-manager',
                        'kind' => 'guide',
                        'summary' => 'Navigating the NovaOS desktop, window controls, 8-zone window snapping, dynamic tiling, and taskbar.',
                        'content' => <<<'MD'
# Desktop Environment & Window Manager

NovaOS features a complete, modern windowing desktop environment powered by `WM.ELF`. Built entirely in `#![no_std]` Rust, it provides fluid 32-bit graphical window management with zero per-pixel system call overhead.

```text
+--------------------------------------------------------------------------------+
| [NovaOS]  File  Edit  View  Window  Help                     12:34 PM [Sound]  |  <- Top Menu Bar
+--------------------------------------------------------------------------------+
|                                                                                |
|   +--------------------------+          +----------------------------------+   |
|   | Terminal - /home/firoze  |          | Text Editor - NOTES.TXT          |   |
|   +--------------------------+          +----------------------------------+   |
|   | $ cat /etc/hostname      |          | Welcome to NovaOS!               |   |
|   | novaos                   |          | Pure Rust #![no_std] userland.   |   |
|   | $ _                      |          |                                  |   |
|   +--------------------------+          +----------------------------------+   |
|                                                                                |
+--------------------------------------------------------------------------------+
| [Start] | [Files] [Terminal] [Editor] [Store] [Settings] [ActMon] | Taskbar  |  <- Bottom Dock
+--------------------------------------------------------------------------------+
```

## Window Controls & Geometry

Every application window provides intuitive controls in its titlebar:
- **Minimize (`-`)**: Minimizes the window to the bottom dock taskbar.
- **Maximize / Restore (`[]`)**: Toggles the window between full workspace geometry (`1024x708`) and its previous floating size.
- **Close (`x`)**: Closes the application gracefully.
- **Window Dragging**: Click and hold any window's titlebar to move it smoothly across the desktop.

## Advanced Window Snapping

Dragging a window towards screen edges triggers visual snap zones:
- **Left Edge**: Snaps the window to the left half of the display (`512x708`).
- **Right Edge**: Snaps the window to the right half of the display (`512x708`).
- **Top Edge**: Snaps the window to full maximized workspace.
- **Corners**: Snaps the window into quarter quadrants (`TopLeft`, `TopRight`, `BottomLeft`, `BottomRight`).
- **Smart Restore**: Pulling a snapped window away from an edge smoothly restores its original pre-snap floating geometry, maintaining cursor offset.

## Automated Tiling Modes

Access automated window arrangement from the **Window** menu:
- **Tile Side-by-Side**: Places the two most active windows side-by-side.
- **Tile 2x2 Grid**: Organizes active windows into a 4-window quadrant grid.
- **Cascade Windows**: Arranges open windows in an overlapping cascading diagonal stack.

## Top Menu Bar & Dock

- **Top Menu Bar (`y = 0..24`)**: Persistent application menu dispatch, battery/network telemetry, live clock, and audio volume indicator.
- **Bottom Dock (`y = 732..768`)**: Launches pinned core applications and displays running tasks with status indicators.
MD
                    ],
                    [
                        'title' => 'Core Desktop Applications',
                        'slug' => 'core-desktop-applications',
                        'kind' => 'guide',
                        'summary' => 'Guide to NovaOS core applications: File Manager, Terminal Emulator, Text Editor, and Software Store.',
                        'content' => <<<'MD'
# Core Desktop Applications

NovaOS includes an integrated suite of core productivity applications designed to run safely within ring-3 unprivileged space.

## File Manager (`FILES.ELF`)

The NovaOS File Manager provides fast, reliable access to the FAT32 system partition and Ext2 filesystem:
- **Interactive Breadcrumbs**: One-click folder navigation along the current directory path.
- **File Listings**: Lists file names, sizes in KB, file permissions, and modification dates.
- **Monospace File Preview**: Instant preview of text documents, configuration files, and ELF binary headers in a dedicated sidebar.
- **File Management**: Open files in designated applications, create new folders, and delete unwanted files.

## In-Window Terminal Emulator (`TERM.ELF`)

The Terminal app provides a Unix-compatible command-line environment:
- **VT100 ANSI Support**: Full color rendering, cursor positioning, and screen clearing escape sequences.
- **Interactive Line Editing**: Command history traversal with `Up`/`Down`, word skipping, and tab completion.
- **Pipes & Redirection**: Chain commands together (`cat /etc/hosts | grep local`), redirect outputs (`> out.txt`, `>> log.txt`), and launch background tasks.

## Monospace Text Editor (`EDIT.ELF`)

A clean, responsive text editor for code, shell scripts, and configuration files:
- **Editor Canvas**: Monospace font rendering with line numbering and syntax-aware formatting.
- **Navigation**: Arrow key navigation, word jumping, and page scrolling.
- **File Operations**: Open existing documents, edit with undo/redo capabilities, and save with `Ctrl + S`.

## Software Store (`STORE.ELF`)

The graphical package manager for NovaOS:
- **Repository Catalog**: Browses packages from `/REPO/PACKAGES.LST` across Utilities, System, Development, and Games.
- **Package Details**: Displays package version, size, dependencies, description, and cryptographic signatures.
- **One-Click Actions**: Install, update, or remove software with real-time download and installation progress bars.
MD
                    ],
                    [
                        'title' => 'System Settings & Accessibility',
                        'slug' => 'system-settings-and-accessibility',
                        'kind' => 'guide',
                        'summary' => 'Configuring system preferences, font scaling, high-contrast mode, services, firewall, and updates.',
                        'content' => <<<'MD'
# System Settings & Accessibility

The NovaOS Settings application (`SETTINGS.ELF`) provides a centralized, modular control panel for system configuration and accessibility options.

## Display & Accessibility

NovaOS treats accessibility as a foundational, non-optional standard:
- **Typography Font Scaling**: Choose between 4 dynamic font scaling tiers:
  - **Normal (100%)**: Default 8x12 font metrics for standard displays.
  - **Medium (125%)**: Enlarged line height and glyph spacing.
  - **Large (150%)**: High-readability font scaling for low-vision users.
  - **Extra Large (200%)**: Double-scale rendering for maximum legibility.
- **WCAG 2.1 AAA High-Contrast Mode**:
  - High-contrast color palette: pure solid black background (`#000000`), crisp white text (`#FFFFFF`), vibrant yellow focus rings (`#FFFF00`).
  - Solid 2px window borders; window drop shadows disabled.
- **Cursor Themes**:
  - Classic White arrow cursor.
  - Amber / Retro cursor.
  - High-Contrast Yellow cursor with black outline.
- **Persistence**: Saved to the 64-byte binary `ACCESS.CFG` (`NVAC` magic) on Partition 1 with CRC checksum validation.

## Service Manager

Inspect and control system daemons managed by `NOVAINIT.ELF`:
- View running services, dependencies, and execution binaries.
- Monitor restart policies (`always`, `on-failure`).
- Start, stop, and restart services dynamically.

## Device Manager

Explore connected system hardware via the interactive device tree:
- Processor model, vendor ID, and SMP core configuration.
- PCI bus devices, vendor/device IDs, and BAR allocations.
- Storage controllers (ATA, VirtIO-Blk, NVMe).
- Network controllers (RTL8139, VirtIO-Net) and video adapters.

## Firewall & Security

Inspect and adjust kernel stateful packet filter rules:
- Default policies for incoming and outgoing network traffic (`drop` vs `accept`).
- Rule table inspection with packet and byte counters.
- Connection tracking table (conntrack) displaying active TCP/UDP/ICMP sessions.
MD
                    ],
                    [
                        'title' => 'System Diagnostics & Recovery',
                        'slug' => 'system-diagnostics-and-recovery',
                        'kind' => 'guide',
                        'summary' => 'Real-time monitoring with Activity Monitor, structured syslog with Kernel Log Viewer, and crash recovery.',
                        'content' => <<<'MD'
# System Diagnostics & Recovery

NovaOS includes dedicated diagnostic and telemetry utilities to monitor performance and recover gracefully from kernel or application failures.

## Activity Monitor (`ACTMON.ELF`)

Real-time task manager and system resource monitor:
- **Performance Meters**: Live real-time graphs showing CPU core utilization and physical memory consumption.
- **Process Table**: Comprehensive process listing with PID, PPID, User ID, CPU%, memory footprint, priority, and state (`RUNNING`, `READY`, `BLOCKED`, `ZOMBIE`).
- **Process Controls**: Safely terminate (`SIGTERM`), force kill (`SIGKILL`), or adjust priority (renice) of any active task.

## Kernel Log Viewer (`KLOG.ELF`)

Structured graphical log inspection:
- **Kernel Syslog Stream**: Direct queries into the kernel's structured ring buffer.
- **Severity Filtering**: Filter log entries by severity level: `EMERG`, `ALERT`, `CRIT`, `ERR`, `WARNING`, `NOTICE`, `INFO`, and `DEBUG`.
- **Search & Highlighting**: Monospace text search with real-time matching and export capabilities.

## Problem Reporter & Crash Dump Recovery (`CRASHREP.ELF`)

If an unrecoverable kernel panic or CPU exception occurs:
- The kernel's crash dump handler preserves the CPU register state and backtrace into Partition 4 (crash dump partition) and exports `/CRASHDMP.TXT` and `/CRASHDMP.DAT`.
- On reboot, NovaOS detects the pending dump and opens the Graphical Crash Reporter:
  - **Overview**: Panic reason, faulting EIP address, and CR2 memory access address.
  - **CPU Registers**: Full snapshot of 16 CPU registers (`EAX`, `EBX`, `ECX`, `EDX`, `ESI`, `EDI`, `EBP`, `ESP`, `EIP`, `EFLAGS`).
  - **Call Stack**: Decoded stack frame backtrace identifying the faulting kernel module or userland address.
  - **Action Toolbar**: `[Clear Record]` clears the pending panic record via `SYS_CRASHDUMP_CTL` (80); `[Reboot]` safely restarts the system.
MD
                    ],
                    [
                        'title' => 'First-Run Setup & Account Management',
                        'slug' => 'first-run-setup-and-account-management',
                        'kind' => 'guide',
                        'summary' => 'Initial out-of-the-box system personalization, user account authentication, and login screen.',
                        'content' => <<<'MD'
# First-Run Setup & Account Management

NovaOS includes an intuitive onboarding flow for initial machine personalization and multi-user security.

## First-Run Setup Wizard (`FIRSTRUN.ELF` / `SETUP.ELF`)

On first boot of a newly deployed disk, the First-Run Setup Wizard guides you through 7 structured steps:
1. **Welcome & Hardware Audit**: Displays CPU architecture, RAM capacity, detected storage media, and network adapters.
2. **Machine Identity**: Set the system hostname (`novaos`) and select the default init executable (`NOVAINIT.ELF`). Serialized into `SYSTEM.CFG` (`NVCF` magic).
3. **Administrator Account**: Creates the primary administrator (UID 0 / GID 0). Passwords are cryptographically hashed using salted RFC 8018 PBKDF2-HMAC-SHA256 with 4096 iterations. Serialized into `USERS.CFG` (`NVUS` magic).
4. **Network & Firewall**: Configure DHCP or static IPv4 network settings and choose initial firewall filter policies (`FIREWALL.CFG`).
5. **Visual Preferences & Accessibility**: Select color theme, typography scaling (100% to 200%), and cursor styling (`ACCESS.CFG`).
6. **Review & Deployment**: Progressive atomic commit writing all configuration files with step-by-step verification.
7. **Setup Complete**: Choose to launch the Graphical Desktop (`WM.ELF`) or Developer Shell (`SHELL.ELF`).

## Graphical Login & Lockscreen (`LOGIN.ELF`)

NovaOS features a multi-user graphical login and session manager:
- **User Selection**: Interactive user avatar cards populated directly from `USERS.CFG`.
- **Secure Authentication**: Pure `#![no_std]` PBKDF2-HMAC-SHA256 hash verification with constant-time equality checks preventing timing attacks.
- **Session Dispatch**: Launches unprivileged user sessions into `WM.ELF` or `SHELL.ELF`.
MD
                    ],
                    [
                        'title' => 'Atomic Updates & Boot-Slot Management',
                        'slug' => 'atomic-updates-and-boot-slot-management',
                        'kind' => 'guide',
                        'summary' => 'Dual-slot A/B atomic system updates, signature verification, watchdog monitoring, and rollback.',
                        'content' => <<<'MD'
# Atomic Updates & Boot-Slot Management

NovaOS provides robust, zero-downtime atomic operating system updates with automatic rollback protection.

## Dual-Slot A/B Architecture

NovaOS maintains two distinct kernel boot slots:
- **Slot A**: `/boot/novaos.bin` (Primary boot partition)
- **Slot B**: `/boot/novaos_b.bin` (Secondary staging/fallback partition)

The active slot is tracked in non-volatile boot configuration.

## Update Workflow

System updates are applied via **Settings > Software Updates**:
1. **Update Discovery**: The system scans for signed update packages (`OSUPDATE.PKG`).
2. **Cryptographic Verification**: Validates the update package using HMAC-SHA256 signatures against the built-in system key (`PKG_VERIFY_KEY`).
3. **Staging & Commit**: The update binary is written to the inactive boot slot, and a write-ahead journal entry is committed.
4. **Trial Boot**: The bootloader arms a hardware watchdog timer and boots into the staging slot in `Testing` mode.
5. **Health Confirmation**: Once the system successfully boots and validates core services, the user confirms health (`[Confirm System Healthy]`), promoting the slot to `Normal`.
6. **Watchdog Auto-Rollback**: If the new slot fails to boot or crashes within 3 attempts, the bootloader automatically reverts to the previous known-good slot without data loss.
MD
                    ],
                    [
                        'title' => 'Keyboard Shortcuts & Quick Reference',
                        'slug' => 'keyboard-shortcuts-and-quick-reference',
                        'kind' => 'reference',
                        'summary' => 'Comprehensive keyboard shortcuts cheat sheet and system configuration file formats.',
                        'content' => <<<'MD'
# Keyboard Shortcuts & Quick Reference

## Desktop & Window Navigation

| Shortcut | Description | Scope |
|:---|:---|:---|
| `Alt + F4` | Close active window | Desktop |
| `Alt + Tab` | Switch between open windows | Desktop |
| `Win + Left` | Snap active window to left half | Window Manager |
| `Win + Right` | Snap active window to right half | Window Manager |
| `Win + Up` | Maximize active window | Window Manager |
| `Win + Down` | Restore / minimize active window | Window Manager |
| `Ctrl + Alt + T` | Open Terminal | Global |
| `Ctrl + S` | Save current file | Text Editor |
| `Ctrl + Q` | Quit application | General |
| `Ctrl + C` | Send interrupt signal | Terminal |
| `Ctrl + L` | Clear terminal screen | Terminal |

## System Configuration Files

| File | Magic / Size | Description |
|:---|:---|:---|
| `SYSTEM.CFG` | `NVCF` (81 bytes) | Hostname, default user, and init target. |
| `USERS.CFG` | `NVUS` (716 bytes) | 8 user records with UID/GID, salt, and PBKDF2-HMAC-SHA256 hash. |
| `ACCESS.CFG` | `NVAC` (64 bytes) | Theme, font scaling, cursor styling, and CRC16 checksum. |
| `FIREWALL.CFG` | Text | Stateful packet filter policies, incoming/outgoing rules. |
| `SERVICES.CFG` | Text | Service definitions, execution binaries, dependencies, restart rules. |
MD
                    ],
                ],
            ],
            [
                'title' => 'Getting Started',
                'slug' => 'getting-started',
                'description' => 'Essential guides to boot, install, and configure NovaOS.',
                'icon' => '🚀',
                'sort_order' => 10,
                'pages' => [
                    [
                        'title' => 'Graphical Installation Guide',
                        'slug' => 'graphical-installation-guide',
                        'kind' => 'installation',
                        'summary' => 'Step-by-step walkthrough of installing NovaOS using the Graphical Installer Wizard.',
                        'content' => <<<'MD'
# Graphical Installation Guide

NovaOS includes a graphical installer wizard (`INSTALL.ELF`) to provision bare-metal systems or virtual machines.

## Prerequisites

- **Processor**: x86-compatible CPU (32-bit protected mode or 64-bit capable).
- **Memory**: Minimum 64MB RAM (128MB+ recommended).
- **Storage**: Minimum 128MB storage disk (`/dev/ata0`, `/dev/vda`, or `/dev/nvme0n1`).
- **Display**: VESA VBE 2.0+ compatible linear framebuffer or VirtIO-GPU.

## Installation Steps

1. **Boot Installer**: Boot from `novaos.iso`. The hybrid bootloader launches the Graphical Installer automatically.
2. **Select Destination Disk**: Choose the target storage device from the detected block devices.
3. **Choose Partition Preset**:
   - **Standard Preset**: Partition 1 (FAT32, 64MB), Partition 2 (Ext2, 32MB), Partition 3 (Journal, 4MB), Partition 4 (Crash Dump, 1MB).
   - **Minimal Preset**: 32MB / 16MB / 2MB / 1MB.
   - **Workstation Preset**: 128MB / 64MB / 8MB / 2MB.
4. **Configure System Identity**: Enter your desired computer hostname and create the administrator account credentials.
5. **Review & Confirm**: Verify the disk partition blueprint and safety authorization toggle.
6. **Apply Installation**: The installer drives `SYS_DISK_INSTALL` to format partitions, copy core binaries, and configure boot sectors.
7. **Reboot**: Click `[Reboot System]` to boot into your newly installed NovaOS environment.
MD
                    ],
                    [
                        'title' => 'Release Notes & Sign-Off Checklist (v1.0.0)',
                        'slug' => 'release-notes-and-signoff-checklist-v1-0',
                        'kind' => 'guide',
                        'summary' => 'Official v1.0.0 release certification, 56-milestone build plan completion report, and cryptographic checksums.',
                        'content' => <<<'MD'
# NovaOS v1.0.0 Release Notes & Official Sign-Off Checklist

NovaOS v1.0.0 marks the successful completion of the entire 6-Month Build Plan. All 56 planned milestones across both the Kernel Track and Interface Track have been delivered, verified, and certified regression-free.

## 1. Release Highlights & Architecture
- **Complete Kernel Foundation (26 Features Delivered)**:
  - 32-bit x86 protected mode kernel with dynamic paging and high-memory virtual split (`0xC0000000`).
  - SMP multi-core scheduler (2 to 4 cores) driven by ACPI MADT parsing and inter-processor interrupts (IPI).
  - Multi-filesystem VFS: Ext2 and FAT32 with write-ahead logging journal and atomic crash recovery.
  - Complete TCP/IP network stack: socket API, DHCP client, DNS resolver, stateful firewall with 256 tracked flows, and in-kernel package repo server.
  - Device drivers: ATA PIO (LBA28), NVMe PCIe block storage, xHCI USB 3.0, Wi-Fi 802.11 MAC, AC97 audio mixer, and dual graphics backends (Bochs VBE 1024x768x32bpp and VirtIO-GPU 2D).
  - Lifecycle: ACPI S5 shutdown, dual-slot atomic OS updates with fallback, and automated crash dump exports.

- **Complete Interface Track (26 Features Delivered)**:
  - 13 pure `#![no_std]` Rust ring-3 applications operating with zero-syscall `BufferSurface32` rendering:
    - `ui-rs`: Unified design system, widgets, and anti-aliased vector typography.
    - `novainit-rs`: PID 1 service supervisor with dependency ordering and crash restart policies.
    - `wm-rs`: Compositor with 8-zone window snapping, dynamic tiling, menu bar, and dock.
    - `files-rs`, `term-rs`, `edit-rs`, `store-rs`, `login-rs`, `settings-rs`, `klog-rs`, `actmon-rs`, `installer-rs`, and `crashrep-rs`.

- **Release & Cross-Cutting (4 Features Delivered)**:
  - Automated test suite scaling: 319 assertions on Desktop profile and 248 assertions on Server profile.
  - Two install editions from one shared kernel: Desktop (`disk.img`) and Server (`disk-server.img`).
  - Exhaustive cross-track security review across all 84 system calls, MAC profiles, and credential zeroing.
  - Release packaging with SHA-256 cryptographic signatures.

## 2. Release Artifacts & Checksums
- `novaos.iso` (Hybrid bootable ISO: CD + USB BIOS hard drive image)
- `novaos-desktop.img` (Desktop Edition partitioned disk image)
- `novaos-server.img` (Server Edition headless partitioned disk image)
- `novaos.bin` (Shared kernel ELF binary)

Verify downloaded release packages:
```bash
sha256sum -c SHA256SUMS
```

## 3. Official Sign-Off Status
- **Tasks Complete**: 56 / 56 (100.0%)
- **Test Pass Rate**: 100% across all suites (319/319 Desktop, 248/248 Server)
- **Status**: **CERTIFIED FOR PRODUCTION RELEASE**
MD
                    ],
                ],
            ],
            [
                'title' => 'Security Architecture & Capability Review',
                'slug' => 'security-architecture-and-capability-review',
                'description' => 'Comprehensive technical security review covering syscall authorization, memory isolation, least-privilege matrix, and verification.',
                'icon' => '🛡️',
                'sort_order' => 20,
                'pages' => [
                    [
                        'title' => 'Syscall Authorization & Security Matrix',
                        'slug' => 'syscall-authorization-and-security-matrix',
                        'kind' => 'guide',
                        'summary' => 'Complete audit of all 84 NovaOS system calls, privilege gates, capability allowlists, and pointer validation bounds.',
                        'content' => <<<'MD'
# Syscall Authorization & Security Matrix

NovaOS exposes 84 numbered system calls via software interrupt `int 0x80`. Every system call is gated according to strict defense-in-depth principles:

## 1. Trust Boundaries & Address Space Split
- **High-Memory Kernel Split (`0xC0000000`)**: The kernel resides in high memory (`0xC0000000 - 0xFFFFFFFF`). User space operates in the lower 3GB (`0x00000000 - 0xBFFFFFFF`).
- **User-Pointer Sanitization (`paging_user_range_ok`)**: Every pointer passed into a syscall handler is validated before dereferencing:
  1. Rejects pointers into kernel virtual address space (`>= 0xC0000000`).
  2. Rejects Page 0 (`0x00000000 - 0x00000FFF`, IVT & BIOS data area).
  3. Rejects integer arithmetic wrap-around (`addr + len < addr`).
  4. Checks write permissions on destination user buffers.

## 2. Syscall Gate Categories
1. **Open Syscalls**: Low-risk operations available to all ring-3 tasks (`SYS_WRITE`, `SYS_EXIT`, `SYS_YIELD`, `SYS_SBRK`, `SYS_RTC_READ`, `SYS_LSPCI`, `SYS_BEEP`).
2. **Capability-Gated Syscalls**: Access granted only if the process's capability descriptor allowlist explicitly includes the target resource:
   - `SYS_OPEN`, `SYS_WRITE_FILE`, `SYS_DELETE_FILE`: gated by `allowed_files[]` or `can_open_any_file`.
   - `SYS_NET_SEND`, `SYS_TFTP_FETCH`: gated by `allowed_hosts[]`.
   - `SYS_SPAWN`, `SYS_EXEC`, `SYS_FORK`: gated by `can_spawn`.
   - `SYS_EXEC_TRUSTED`: gated by caller's current possession of `can_open_any_file`.
3. **Root-Gated Syscalls**: Mutating system administration interfaces strictly require `cur->uid == 0` and unconfined MAC profile:
   - `SYS_OS_UPDATE`: applying update packages, committing slots, rolling back.
   - `SYS_CRASHDUMP_CTL`: triggering diagnostic exports and clearing records.
   - `SYS_DISK_INSTALL`: live partition creation and formatting.
   - `SYS_SHUTDOWN`: ACPI S5 system power-off.
   - `SYS_FW_CTL` & `SYS_FW_INFO`: stateful firewall rule manipulation.
   - `SYS_PKGREPO_CTL`: in-kernel repository HTTP server control.
4. **Owner-Gated Resources**:
   - `SYS_FB_ACQUIRE`: enforces single exclusive owner (`-EBUSY` for competitors).
   - `SYS_SHM_*`: access controlled by per-object Access Control Lists (ACLs).
   - `SYS_MSG_*`: sender PID and UID are stamped immutably from kernel process records.
MD
                    ],
                    [
                        'title' => 'Application Least-Privilege & Sandboxing',
                        'slug' => 'application-least-privilege-and-sandboxing',
                        'kind' => 'guide',
                        'summary' => 'Detailed least-privilege analysis for all 13 ring-3 applications, MAC enforcement, IPC boundaries, and credential zeroing.',
                        'content' => <<<'MD'
# Application Least-Privilege & Sandboxing

Every user-facing application in NovaOS is designed to operate under the principle of least privilege:

## 1. Application Privilege Matrix
- **`NOVAINIT.ELF` (PID 1)**: Supervisor init process. Runs as UID 0, drops privileges when launching user services, restarts crashed essential services.
- **`LOGIN.ELF`**: Graphical authentication screen. Starts as UID 0, verifies credentials via `SYS_LOGIN`, immediately drops process identity to authenticated user UID/GID.
- **`FIRSTRUN.ELF`**: Setup wizard. Configures machine hostname, creates root credentials with PBKDF2-HMAC-SHA256, and deploys initial configuration.
- **`WM.ELF`**: Window manager and compositor. Sole exclusive owner of the display framebuffer (`SYS_FB_ACQUIRE`).
- **`FILES.ELF`**: Sandboxed file manager. Bound to user directories; cannot access raw disk partitions or kernel configuration files without capability.
- **`TERM.ELF`**: Terminal emulator. Confined to pipe I/O and process execution within user shell environment.
- **`EDIT.ELF`**: Monospace text editor. Sandboxed to explicitly opened file handles; cannot spawn processes or perform network I/O.
- **`STORE.ELF`**: Package store client. Confined to `/PACKAGES` directory; verifies all package payloads with HMAC-SHA256 before installation.
- **`SETTINGS.ELF`**: System configuration. Standard tabs read-only; administrative changes require explicit elevation via `sudo`.
- **`KLOG.ELF`**: Structured kernel log viewer. Read-only inspection via `SYS_KLOG_READ`.
- **`ACTMON.ELF`**: Activity monitor. Read-only process table telemetry via `SYS_PROC_INFO`.
- **`INSTALL.ELF`**: Partitioning wizard. Requires explicit confirmation and safety switch before driving `SYS_DISK_INSTALL`.
- **`CRASHREP.ELF`**: Crash dump reporter. Read-only inspection of `/CRASHDMP.DAT` and `/CRASHDMP.TXT`.

## 2. Plaintext Credential Sanitization
In `LOGIN.ELF`, `FIRSTRUN.ELF`, `firstrun.c`, and `users.rs`, plaintext user password buffers are scrubbed immediately after digest derivation:
- Temporary buffers are overwritten with zero bytes using volatile memory loops (`memset`).
- Eliminates credential retention in physical page frames or swap space.
- Constant-time 32-byte digest comparisons prevent timing side-channel attacks.

## 3. Verification & Compliance
Validated continuously via:
- In-kernel boot self-test Phase 98 (`kernel_security_audit_selftest` -> result=0).
- Host-side security test suite (`make test-security` -> 10/10 checks passed).
- End-to-end automated QEMU boot test (`make test` -> 319 assertions passed).
MD
                    ],
                ],
            ],
        ];

        // 3. Insert or Update Sections and Pages
        foreach ($sections as $secData) {
            DB::table('documentation_sections')->updateOrInsert(
                [
                    'software_project_id' => $projectId,
                    'slug' => $secData['slug'],
                ],
                [
                    'title' => $secData['title'],
                    'description' => $secData['description'],
                    'icon' => $secData['icon'],
                    'sort_order' => $secData['sort_order'],
                    'is_published' => true,
                    'updated_at' => $now,
                ]
            );

            $section = DB::table('documentation_sections')
                ->where('software_project_id', $projectId)
                ->where('slug', $secData['slug'])
                ->first();

            $pageOrder = 1;
            foreach ($secData['pages'] as $pageData) {
                DB::table('documentation_pages')->updateOrInsert(
                    [
                        'software_project_id' => $projectId,
                        'slug' => $pageData['slug'],
                    ],
                    [
                        'release_id' => $release ? $release->id : null,
                        'documentation_section_id' => $section->id,
                        'title' => $pageData['title'],
                        'kind' => $pageData['kind'],
                        'version' => '1.0.0',
                        'summary' => $pageData['summary'],
                        'content' => $pageData['content'],
                        'sort_order' => $pageOrder++,
                        'is_published' => true,
                        'updated_at' => $now,
                    ]
                );
            }
        }

        $this->command->info("NovaOS 1.0.0 user documentation and release successfully seeded in RozeHub!");
    }
}
