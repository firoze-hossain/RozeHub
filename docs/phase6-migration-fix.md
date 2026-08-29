# Phase 6 Migration Fix

## MySQL index-name fix

The Phase 6 migration now uses an explicit short index name for the composite index on `user_project_interactions`:

- `user_project_event_idx`

This avoids MySQL error 1059 caused by Laravel's automatically generated index name exceeding MySQL's 64-character identifier limit.

No application behavior changes are introduced by this fix; it only changes the database index identifier.
