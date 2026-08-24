# RozeHub Release Artifacts

A release is the logical version/target. A release may contain two distribution artifacts:

- `INSTALLER` — package used by new users. Example: macOS `.dmg`.
- `UPDATER` — package used by an installed desktop application. Example: macOS `.pkg`.

The actual files remain in the configured `releases` filesystem disk. MySQL stores only metadata and the relative path.

## API

Check for updates:

`GET /api/v1/updates/{project}?version=2.0.0&platform=macOS&architecture=ARM64&channel=Stable`

The response includes:

- `release.installerArtifact`
- `release.updateArtifact`

The updater client should use `updateArtifact.downloadUrl` when present. If no updater artifact exists, the API falls back to the installer artifact for compatibility.

Direct download:

`GET /api/v1/releases/{release}/download?purpose=installer`

or:

`GET /api/v1/releases/{release}/download?purpose=updater`

## Example

```text
DBNavigator 2.0.1 / macOS / ARM64 / Stable
├── INSTALLER -> DBNavigator-2.0.1.dmg
└── UPDATER   -> DBNavigator-2.0.1.pkg
```

This allows a new Mac user to download the DMG while an existing DBNavigator installation receives the PKG through the automatic updater.
