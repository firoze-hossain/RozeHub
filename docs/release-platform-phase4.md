# RozeHub Phase 4 — Release Platform

Phase 4 turns RozeHub's release system into a controlled distribution platform.

## Capabilities

- GitHub release synchronization and optional installer import.
- Queue-backed artifact processing (`QUEUE_CONNECTION=database`).
- SHA-256 integrity verification.
- Optional RSA-SHA256 detached-signature verification.
- Release health checks.
- Project-specific release channels (Stable/Beta/Nightly by default; custom channels supported).
- Safe rollback to the previous published release for the same project/platform/architecture/channel.
- In-app release update notifications.
- Update API excludes failed releases and releases below 100% rollout.

## Configuration

```env
GITHUB_TOKEN=
GITHUB_WEBHOOK_SECRET=
ROZEHUB_RELEASE_SIGNING_PUBLIC_KEY=
ROZEHUB_RELEASE_REQUIRE_SIGNATURE=false
ROZEHUB_RELEASE_ROLLOUT_PERCENTAGE=100
QUEUE_CONNECTION=database
```

Run workers:

```bash
php artisan queue:work
```

Queue pending release processing:

```bash
php artisan rozehub:process-releases
```

The signing key is optional. When `ROZEHUB_RELEASE_REQUIRE_SIGNATURE=true`, a release must have a valid detached `.sig`/`.asc` asset matching the installer name; otherwise processing fails.
