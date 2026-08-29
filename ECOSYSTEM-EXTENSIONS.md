# RozeHub Ecosystem Extension Architecture

RozeHub treats its seven projects as different product families. A marketplace item is therefore attached to a **project ecosystem**, and that ecosystem determines valid extension types, capabilities, package formats, platforms, architectures and integration targets.

## Seven extension models

| Project | Ecosystem | Examples |
|---|---|---|
| DBNavigator | Desktop application | driver, plugin, formatter, exporter, importer, theme |
| Lumina | Development environment | plugin, language support, debugger, tooling, theme, integration |
| Roze | Programming language | package, module, library, tooling, compiler plugin, integration |
| StratosDB | Database engine | extension, storage engine, index, function, driver, tooling |
| ThunderCall | API client | protocol, auth, workflow, processor, integration, theme |
| TrackEye | Monitoring platform | collector, exporter, integration, analytics, report, theme |
| NOVAOS | Operating system | application, system component, driver, service, desktop component, theme, package |

## Dynamic project policy

The `project_ecosystem_profiles` table is the source of truth for marketplace policy. The developer and administrator forms load the allowed values from this profile rather than hard-coding a universal `plugin | extension` list.

Each profile controls:

- ecosystem type and description
- allowed marketplace item types
- declared capabilities
- package formats
- supported platforms
- supported CPU architectures
- integration targets
- whether community contributions are enabled
- whether moderation is required

## Marketplace API

Desktop clients can consume:

```text
GET /api/v1/marketplace/{project}
GET /api/v1/marketplace/{project}/{item}
GET /api/v1/marketplace/releases/{release}/download
```

The collection response contains the project's ecosystem policy as well as published items. This allows a future extension manager inside Lumina, DBNavigator, ThunderCall or other clients to discover compatible packages without embedding RozeHub's policy in the application.

## Security model

Every community release remains unpublished until moderation approves it. The existing workflow records:

```text
DRAFT
  ↓
SUBMITTED
  ↓
UNDER_REVIEW
  ├── NEEDS_CHANGES → SUBMITTED
  ├── REJECTED
  └── APPROVED → PUBLISHED
```

Automated checks inspect package integrity, declared capabilities, package type and archive paths before an administrator makes the final publication decision.

## Cross-project integrations

The ecosystem profiles deliberately expose integration targets so that future official packages can connect the projects without changing the marketplace schema. Examples include:

- DBNavigator → StratosDB
- Lumina → Roze
- Lumina → StratosDB
- ThunderCall → HTTP / GraphQL / gRPC / WebSocket
- TrackEye → PostgreSQL / StratosDB / RozeHub
- NOVAOS → Lumina / DBNavigator / ThunderCall / TrackEye / StratosDB / Roze

This keeps RozeHub's catalog extensible while preserving the fact that NOVAOS is a platform, not another desktop application.

## Phase 1 architecture foundation

- **Project ecosystem admin** is the source of truth for each project's marketplace behavior.
- **Project-specific rules are data-driven** through `project_ecosystem_profiles`; controllers do not contain seven-project conditionals.
- **Marketplace API** exposes the selected ecosystem schema (`itemTypes`, `capabilities`, `packageTypes`, `platforms`, `architectures`, `channels`, `integrations`) and validates requested filters against that schema.
- **Form Requests** isolate HTTP validation from marketplace business logic.
- **Policies** authorize developer access to marketplace items, releases and submissions.
- **MarketplaceService** centralizes ecosystem eligibility, release compatibility, metadata normalization and package handling.
- **Automated tests** cover dynamic ecosystem validation, API schema exposure, developer ownership and administrator policy editing.
