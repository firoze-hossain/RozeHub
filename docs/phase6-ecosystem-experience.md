# RozeHub Phase 6 — Ecosystem Experience

This phase adds the experience layer on top of the existing project, marketplace, GitHub, release and analytics foundations.

## Included
- Dynamic ecosystem graph nodes/edges
- Project health scoring and snapshots
- Global search across projects, marketplace, docs, developers and organizations
- Recommendation service
- Contributor scoring and leaderboard
- Project roadmaps and roadmap items
- Developer organizations, members and project ownership
- Public pages and admin controls
- JSON graph/search endpoints

## Migration
Run:

```bash
php artisan migrate
php artisan optimize:clear
```

No existing project-specific rules are introduced. Projects are discovered from `software_projects` and the existing ecosystem/GitHub/marketplace/documentation tables.

## Main URLs
- `/ecosystem`
- `/ecosystem/graph`
- `/contributors`
- `/organizations`
- `/search?q=DBNavigator`
- `/admin/ecosystem-experience`
- `/admin/organizations`
