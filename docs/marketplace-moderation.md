# RozeHub Marketplace Moderation

## Scope

Community publishing is enabled only for Lumina and DBNavigator. Plugins and extensions are represented by `marketplace_items`; versions/packages are represented by `marketplace_releases`.

## State machine

```text
DRAFT
  ↓
SUBMITTED
  ↓
UNDER_REVIEW
  ├── NEEDS_CHANGES → developer edits → SUBMITTED
  ├── REJECTED
  └── APPROVED → PUBLISHED
```

A release is never public merely because a developer uploaded it. Public marketplace/API queries require both the item and release to be published.

## Risk model

Automated checks create records in `marketplace_submission_risks`:

- package_integrity
- permissions
- package_type
- archive_scan

Each check has `PASS`, `REVIEW`, or `FAIL` and a score. The submission receives an aggregate score and `LOW`, `MEDIUM`, `HIGH`, or `CRITICAL` risk level.

The scanner checks archive path traversal, common executable file types, native packages and elevated declared permissions. It is a triage aid, not a malware detector.

## Storage

Large packages are stored in the existing external `releases` disk. The database stores only package metadata such as path, file name, size and SHA-256. Chunk uploads keep individual HTTP requests small enough for normal PHP `post_max_size` settings.

## Tables

- `marketplace_submissions`
- `marketplace_submission_risks`
- `marketplace_submission_logs`
- `marketplace_notifications`
- `marketplace_items.owner_user_id`

## Key URLs

Developer:

- `/developer`
- `/developer/register`
- `/developer/marketplace/create`
- `/developer/marketplace/submissions`

Admin:

- `/admin/marketplace/review`
- `/admin/marketplace/review/{submission}`

Desktop clients continue using the existing public marketplace API. Unapproved submissions never appear there.
