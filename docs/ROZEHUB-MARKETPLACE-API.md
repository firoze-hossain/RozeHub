# RozeHub Marketplace API

RozeHub supports two first-class marketplace item types:

- `plugin`
- `extension`

The application decides what the package can extend. RozeHub manages discovery,
versions, compatibility, distribution, publishing, checksums and release metadata.

## Public endpoints

### List items

`GET /api/v1/marketplace/{project}`

Optional query parameters:

- `type=plugin|extension`
- `platform=Windows|macOS|Linux`
- `architecture=x64|ARM64`
- `channel=Stable|Beta|Nightly`
- `appVersion=2.1.0`

Example:

```text
GET /api/v1/marketplace/dbnavigator?type=extension&platform=Windows&architecture=x64&channel=Stable
```

### Item details

`GET /api/v1/marketplace/{project}/{slug}`

Returns item metadata, permissions and all published compatible release
metadata.

### Package download

`GET /api/v1/marketplace/releases/{release}/download`

The server streams the package from external release storage. The package
binary is never stored in MySQL.

## Storage model

Application packages:

`project/version/platform/architecture/channel/file`

Marketplace packages:

`marketplace/project/plugin-or-extension/item/version/platform/architecture/channel/file`

MySQL stores only metadata, including `file_path`, `file_size`, and `sha256`.

## Compatibility

A marketplace release may define:

- `minimum_app_version`
- `maximum_app_version`
- `platform`
- `architecture`
- `channel`

The desktop client should perform the final compatibility check before
installation.

## Security

The client should verify the returned SHA-256 and, for production releases,
verify the publisher's digital signature before loading or installing a
package.

## Suggested client flow

1. Start application.
2. Read locally installed plugins/extensions.
3. Call the marketplace endpoint.
4. Filter releases compatible with the current application version.
5. Compare installed versions.
6. Show available updates.
7. Download the package.
8. Verify SHA-256/signature.
9. Install through the application's plugin manager.
10. Restart only when the plugin architecture requires it.
