# RozeHub

RozeHub is a Laravel + Livewire software distribution portal for DBNavigator,
ThunderCall, StratosDB, Lumina, Roze, Roze OS, and Trackline.

## Hosting target

This version is deliberately **Node.js/npm-free** for cPanel/shared hosting.

- Laravel + PHP only
- Blade + CSS for the UI
- Existing public RozeHub design retained
- Livewire uses the Laravel package already included in `vendor/`; no npm build is required
- CSS is served directly from `public/css/rozehub.css`
- No Vite, Tailwind build, Node.js or npm is required on the server
- Release packages are stored privately and downloaded through Laravel

## Admin panel

Open:

`/admin/login`

The database seeder creates the administrator from:

```dotenv
ADMIN_EMAIL=admin@example.com
ADMIN_PASSWORD=ChangeThisStrongPassword!
```

Change both values in `.env` before running the seeder.

The admin panel controls:

- Dashboard and download statistics
- Software projects: create, edit and delete
- Releases: upload, edit, publish, unpublish and delete
- Windows / macOS / Linux and x64 / ARM64 packages
- Release notes and channels
- Community reviews: approve, hide and delete

## cPanel deployment

1. Upload the project, including the existing `vendor/` directory.
2. Point the domain/subdomain document root to the Laravel `public` directory.
3. Create a MySQL database and user in cPanel.
4. Copy `.env.example` to `.env` and set `APP_KEY`, database credentials, `APP_URL`, `ADMIN_EMAIL` and `ADMIN_PASSWORD`.
5. Run the migrations/seeder if your hosting provides Laravel/SSH access:

```bash
php artisan migrate --seed --force
```

### No-terminal cPanel option

If your cPanel account has **no terminal/SSH**, import `database/cpanel_install.sql` through phpMyAdmin. It creates the core tables and starter catalog, including the initial administrator:

- Email: `admin@example.com`
- Password: `ChangeThisStrongPassword!`

Immediately change the administrator password from **Admin → Account & security**.

The application itself does not need npm or Node.

### Important PHP upload settings

Large installers are limited by the server's PHP settings. For packages up to 1 GB, configure the cPanel PHP INI editor with appropriate values for:

```ini
upload_max_filesize = 1024M
post_max_size = 1024M
max_execution_time = 300
max_input_time = 300
```

The actual maximum is controlled by the hosting provider.

### Storage permissions

Laravel needs write access to:

- `storage/app/private`
- `storage/framework`
- `storage/logs`

Release packages are intentionally kept outside the public web directory and streamed through `/download/{release}`.


## NOVAOS Release Center

NOVAOS is managed separately from application releases.

- Admin overview: `/admin/novaos`
- NOVAOS releases: `/admin/novaos/releases`
- New NOVAOS build: `/admin/novaos/releases/create`
- Application releases remain under `/admin/releases`

NOVAOS releases support: major version, version, codename, build number, Stable/Beta/Nightly channels, x64/ARM64, ISO/system-image upload, automatic SHA-256 calculation, release notes, publishing/unpublishing, and download counts.

After extracting the project in development, run `php artisan migrate` to add the NOVAOS release metadata columns.


## Version-aware documentation

Documentation pages can now be assigned to a specific software release. Use **General · all releases** for concepts that remain stable, or select a release for installation instructions, APIs, commands, compatibility notes, architecture changes, and release notes.

Public documentation defaults to the latest published release and lets visitors switch versions. PDF/print and Markdown exports follow the selected release.

Development setup remains PHP/Laravel/Blade/Livewire/CSS only; no Node.js, npm, Vite, or frontend build step is required.

### Rich editor selection persistence
The documentation editor now preserves the selected text while repeatedly changing font size. The selection remains active after `+`, `−`, font-size dropdown changes, and `Ctrl/Cmd +` / `Ctrl/Cmd -`. Clicking outside the editor clears the saved selection as expected.

## Desktop release and update distribution

RozeHub includes a version-aware update API for all software projects. Desktop clients can check their installed version, platform, architecture and release channel, then receive a signed/hashed package URL and update policy. Large installers are stored outside the Laravel project; MySQL stores only release metadata and the relative package path.

See `docs/desktop-update-api.md` for the API contract.


## Marketplace: plugins and extensions

RozeHub now provides a first-class marketplace for both **plugins** and
**extensions**. The same system supports Lumina and DBNavigator without
hard-coding either application to one package type.

Admin:

- `/admin/marketplace`
- create/edit/publish marketplace items
- create versioned releases
- define application compatibility
- define package type, platform, architecture and channel
- define permissions and dependencies
- safely delete external packages

Public:

- `/marketplace`
- `/marketplace/{slug}`

Desktop API:

- `/api/v1/marketplace/{project}`
- `/api/v1/marketplace/{project}/{slug}`
- `/api/v1/marketplace/releases/{release}/download`

Actual packages are stored outside the Laravel project and outside MySQL.
Only metadata and a relative storage path are stored in the database.

## Community Marketplace Moderation

RozeHub now supports a moderated developer marketplace for **Lumina** and **DBNavigator** only.

### Publishing lifecycle

`DRAFT → SUBMITTED → UNDER_REVIEW → NEEDS_CHANGES / REJECTED / APPROVED → PUBLISHED`

Developers can create accounts at `/developer/register`, create plugins/extensions, upload versioned packages, declare permissions and compatibility, and submit releases. Packages are stored in external release storage; MySQL stores metadata and the relative storage path.

### Security review

Every submission receives automated checks for package integrity, declared permissions, package type and archive contents. Risk is classified as `LOW`, `MEDIUM`, `HIGH` or `CRITICAL`. Administrators can manually adjust each risk check and must record a reason when requesting changes, rejecting or approving a submission.

The automated assessment is advisory; it does not claim to prove that a package is malware-free.

### Admin moderation

Use `/admin/marketplace/review` for the review queue. Administrators can filter by status, risk, application and item type, inspect package metadata/checksum/permissions, review the audit trail, request changes, reject, approve & publish, or unpublish a published release.

### Developer portal

Use `/developer` to manage marketplace items, releases, submissions and notifications. Only `lumina` and `dbnavigator` software projects are accepted for community submissions.
