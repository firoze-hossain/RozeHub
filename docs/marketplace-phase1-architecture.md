# RozeHub Marketplace — Phase 1 Architecture

## Source of truth

`project_ecosystem_profiles` defines the marketplace contract for every software project:

- ecosystem type and public description
- allowed marketplace item types
- declared capabilities
- package types
- platforms and architectures
- release channels
- integration targets
- marketplace/community/moderation switches

The application no longer needs project-name conditionals to decide what a developer may publish.

## Request boundary

HTTP validation lives in `App\Http\Requests`:

- `MarketplaceItemRequest`
- `MarketplaceReleaseRequest`
- `EcosystemProfileRequest`

Business validation lives in `App\Services\MarketplaceService`.

## Authorization

Marketplace access is enforced with policies:

- `MarketplaceItemPolicy`
- `MarketplaceReleasePolicy`
- `MarketplaceSubmissionPolicy`

## Admin

`/admin/ecosystem` is the control surface for project-specific marketplace policy. Changing a project policy changes developer forms and API metadata without a controller change.

## API

`GET /api/v1/marketplace/{project}` now exposes the ecosystem contract and validates `type`, `platform`, `architecture`, and `channel` against the selected project's configured policy.

The response includes a `filters` object suitable for desktop clients to build their marketplace UI dynamically.

## Testing

Feature tests cover policy-driven item validation, dynamic API metadata, ownership authorization, and administrator ecosystem policy editing.
