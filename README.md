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
