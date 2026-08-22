# RozeHub Release & Desktop Update System

This update adds a generic release/update layer for every RozeHub software project.

## 1. Storage model

Installers are **not stored in MySQL** and new release packages are **not stored inside the Laravel project**.

By default, RozeHub uses a sibling directory next to the Laravel project:

```text
projects/others/
├── RozeHub/
├── rozehub-release-storage/
└── rozehub-release-upload-temp/
```

The release storage layout is generated automatically:

```text
rozehub-release-storage/
├── dbnavigator/2.2.0/windows/x64/stable/DBNavigator-2.2.0.exe
├── dbnavigator/2.2.0/linux/x64/stable/DBNavigator-2.2.0.deb
├── lumina/1.5.0/macos/ARM64/stable/Lumina-1.5.0.dmg
├── roze/1.0.0/windows/x64/stable/Roze-1.0.0.exe
├── stratosdb/0.9.0/linux/x64/beta/StratosDB-0.9.0.deb
├── trackline/...
└── novaos/2026.2.1/novaos/x64/stable/NOVAOS-2026.2.1.iso
```

MySQL stores only metadata such as version, target, package filename, package path, size, SHA-256, channel and update policy.

## 2. Migration

Run:

```bash
php artisan migrate
```

The new migration is:

```text
database/migrations/2026_08_22_000011_add_release_update_policy.php
```

It adds:

- `minimum_version`
- `is_mandatory`
- channel-aware release uniqueness
- an update lookup index

## 3. Large-file uploads

The admin release form automatically switches to sequential **4 MB chunk uploads** for packages larger than 6 MB.

This is important for multi-hundred-MB or multi-GB desktop installers: the complete file is not sent as one PHP multipart request.

The final assembled package is moved directly into external release storage.

Clean abandoned upload chunks with:

```bash
php artisan rozehub:release-upload-cleanup
```

The command is also registered for daily scheduling.

## 4. Optional storage configuration

The defaults are already outside the project. To use fixed production paths, add absolute paths to `.env`:

```dotenv
ROZEHUB_RELEASE_STORAGE_PATH=/opt/rozehub-release-storage
ROZEHUB_RELEASE_UPLOAD_TEMP_PATH=/opt/rozehub-release-upload-temp
```

The web/PHP user must have read/write permission to those directories.

## 5. Update API

Desktop applications check for updates with:

```http
GET /api/v1/updates/{project}?version=2.1.0&platform=windows&architecture=x64&channel=stable
```

Examples:

```text
/api/v1/updates/dbnavigator?version=2.1.0&platform=windows&architecture=x64&channel=stable
/api/v1/updates/lumina?version=1.4.0&platform=macos&architecture=ARM64&channel=stable
/api/v1/updates/stratosdb?version=0.9.0&platform=linux&architecture=x64&channel=beta
```

The response includes:

- whether an update is available
- latest version
- mandatory flag
- minimum supported version
- release notes
- package filename and size
- SHA-256
- download URL
- channel and target information

There is also a release history endpoint:

```http
GET /api/v1/updates/{project}/releases?platform=windows&architecture=x64&channel=stable
```

And the package download endpoint:

```http
GET /api/v1/releases/{release}/download
```

## 6. Desktop application flow

```text
DBNavigator / Lumina / Roze / StratosDB / TrackEye
                    │
                    ▼
            RozeHub Update API
                    │
             newer version?
              /           \
            no             yes
            │               │
         continue       show update
                            │
                            ▼
                     download package
                            │
                            ▼
                     verify SHA-256
                            │
                            ▼
                         updater
                            │
                            ▼
                      restart app
```

The running desktop application should not overwrite its own executable. Use a small updater/launcher process to perform installation and restart, matching the desktop auto-update architecture in `Desktop Auto Update Strategy.pdf`.
