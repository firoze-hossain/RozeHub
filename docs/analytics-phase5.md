# RozeHub Phase 5 — Analytics

Phase 5 adds privacy-conscious, database-backed analytics across the RozeHub ecosystem.

## Tracked events

- `download` — release/package downloads from web and desktop update APIs
- `project_view` — public project documentation/project views
- `marketplace_view` — marketplace catalog views
- `marketplace_item_view` — individual marketplace item views
- `marketplace_review` — submitted marketplace reviews
- `documentation_view` — documentation page views
- `documentation_index_view` — documentation landing page views
- `documentation_search` — documentation searches (query truncated)
- `github_sync` — successful repository synchronization
- `github_webhook` — verified GitHub webhook activity

Analytics failures are intentionally isolated from primary requests.

## Privacy

Raw IP addresses are never persisted. A keyed SHA-256 hash is stored only for approximate unique-visitor counts. User-agent strings are truncated. Analytics are intended for product operations, not user profiling.

## Admin dashboard

`/admin/analytics` provides:

- selectable 7/30/90/365-day windows
- event, download, marketplace, documentation, GitHub and visitor metrics
- daily activity
- per-project analytics
- top downloaded releases
- GitHub repository health/activity snapshot
- popular documentation pages
- recent event stream

Project drill-down:

`/admin/analytics/projects/{project}`

## Migration

Run:

```bash
php artisan migrate
```

This creates `analytics_events` with indexes optimized for project/event/time reporting.
