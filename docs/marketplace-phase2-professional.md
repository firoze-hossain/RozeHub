# RozeHub Marketplace — Phase 2

## `rozehub.json` manifest
Every marketplace package should contain a root-level `rozehub.json` (ZIP/JAR/VSIX are inspected automatically).

```json
{
  "schema": "1.0",
  "id": "com.example.dbnavigator.sql-tools",
  "name": "SQL Tools",
  "version": "1.2.0",
  "type": "plugin",
  "dependencies": {
    "com.example.dbnavigator.core": ">=2.0.0"
  },
  "optionalDependencies": {
    "com.example.dbnavigator.ai": "^1.0.0"
  }
}
```

The server verifies that `id`, `name`, `version`, and `type` agree with the marketplace item/release. The manifest becomes part of the release metadata exposed by the API.

## Semantic versions
Releases use `MAJOR.MINOR.PATCH` with optional prerelease/build metadata. Dependency constraints currently support exact versions, `>=`, `>`, `<=`, `<`, `^`, and `~`.

## Dependencies
Manifest dependencies are resolved to published marketplace items and stored in `marketplace_dependencies`. Required dependencies must exist; optional dependencies may be absent. Before publication, the release can be checked for a published version satisfying each constraint.

## Publisher profiles
Developers can maintain a publisher identity from `/developer/publisher`. Public item pages and API responses expose the publisher identity, GitHub, website, biography, and verification status.

## Ratings
Authenticated users can submit one 1–5 star review per marketplace item. Public/API results expose average rating, count, and distribution.

## Project-specific categories
Administrators manage categories at `/admin/marketplace/categories`. Categories belong to a software project, so DBNavigator, Thundercall, Lumina, Roze, TrackEye, StratosDB, and NOVAOS can have completely different marketplace taxonomies.
