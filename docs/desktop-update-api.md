# RozeHub Desktop Update API

RozeHub is the release metadata and distribution server for DBNavigator, Lumina, Roze, StratosDB, TrackEye and NOVAOS.

Large installers are stored outside the Laravel project. MySQL stores release metadata and only the relative package path.

## Check for an update

```http
GET /api/v1/updates/{project}
```

Query parameters:

- `version` — installed application version, for example `2.1.0`
- `platform` — `windows`, `macos`, `linux`, or `novaos`
- `architecture` — `x64` or `ARM64`
- `channel` — optional: `stable`, `beta`, or `nightly`

Example:

```http
GET /api/v1/updates/dbnavigator?version=2.1.0&platform=windows&architecture=x64&channel=stable
```

Example response when an update exists:

```json
{
  "project": "dbnavigator",
  "currentVersion": "2.1.0",
  "available": true,
  "mandatory": false,
  "channel": "Stable",
  "platform": "Windows",
  "architecture": "x64",
  "latestVersion": "2.2.0",
  "minimumSupportedVersion": "1.9.0",
  "release": {
    "id": 42,
    "version": "2.2.0",
    "channel": "Stable",
    "notes": "Improved query editor and connection handling.",
    "minimumVersion": "1.9.0",
    "mandatory": false,
    "fileName": "DBNavigator-2.2.0.exe",
    "fileSize": 73400320,
    "sha256": "...",
    "downloadUrl": "https://rozehub.example/api/v1/releases/42/download"
  }
}
```

## List published releases

```http
GET /api/v1/updates/{project}/releases?platform=windows&architecture=x64&channel=stable
```

This is useful for an application's update history screen or a release picker.

## Download

```http
GET /api/v1/releases/{release}/download
```

The endpoint only serves published packages that exist in external release storage. It increments the release download counter and returns the SHA-256 checksum in `X-RozeHub-SHA256`.

## Release storage

The default local development layout is a sibling of the Laravel project:

```text
projects/others/
├── RozeHub/
└── rozehub-release-storage/
    ├── dbnavigator/
    │   └── 2.2.0/windows/x64/stable/DBNavigator-2.2.0.exe
    ├── lumina/
    ├── roze/
    ├── stratosdb/
    ├── trackeye/
    └── novaos/
```

Override this with `ROZEHUB_RELEASE_STORAGE_PATH` in `.env`. A separate `ROZEHUB_RELEASE_UPLOAD_TEMP_PATH` can be used for temporary chunk uploads.

## Large uploads

The admin release form automatically switches to sequential 4 MB chunk uploads for packages larger than 6 MB. This prevents a 3–5 GB installer from being sent as one PHP multipart request. The final package is assembled outside the Laravel project and the release row stores only metadata and its relative path.

## Desktop client flow

```text
Application starts
      ↓
GET /api/v1/updates/{project}?version=...
      ↓
available?
   /       \
 no         yes
 |           ↓
continue   show update
             ↓
       downloadUrl
             ↓
        verify SHA-256
             ↓
          updater
             ↓
        restart app
```

The desktop application should not replace its own running executable. Use a small updater/launcher process, consistent with the desktop auto-update architecture documented for the project.
