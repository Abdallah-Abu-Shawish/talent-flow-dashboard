# System Dashboard — Laravel/Blade instructions

These rules apply in addition to the repository-root AGENTS.md.

## Scope
Build a production-oriented super-admin dashboard. Only authorized super-admins may enter this application.

## Architecture
- Use Laravel conventions and Blade components/partials/layouts.
- Prefer Controllers -> application Services/Actions -> Repository/Data-access boundary.
- Keep Supabase/database/provider calls out of Blade templates and out of fat controllers.
- Use Form Requests (or equivalent explicit validation), Policies/Gates/middleware, typed DTO/value objects where they add clarity, and server-side pagination.
- Use Alpine/vanilla JS/Chart.js or existing lightweight tooling only where Blade needs progressive interaction. Do not introduce React/Vue just to port the reference prototype.

## Data integrity
- Read actual Supabase schema before wiring each screen.
- Query real data only. If data is unavailable because the worker is not instrumented yet, show a truthful empty/not-configured state and document the required event/table contract.
- Do not use service-role credentials in client-side JavaScript.
- Avoid N+1 and unbounded table scans; add or propose indexes based on real query patterns.

## Required admin quality
- Search/filter/sort/pagination must work where relevant.
- Buttons must execute real authorized actions or not exist.
- Create/edit/delete/suspend/restore operations need validation, confirmations where destructive, error feedback, and audit entries.
- Tables need loading/empty/error states and accessible labels.
- Detailed pages/drawers must load real records, not duplicate static mock JSON.
- Preserve the reference design's light/dark visual language where practical without compromising maintainability.

## Testing
Add/maintain feature tests for auth boundaries, critical CRUD, filters, validation, and unauthorized access. Run the Laravel test suite and frontend build when relevant.
