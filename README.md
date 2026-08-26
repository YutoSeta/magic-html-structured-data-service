# Magic HTML Structured Data Service

The private, typed document store for Magic HTML authoring projects. Every document is project-scoped, JSON-Schema validated, versioned on update, and protected by service authentication.

This service is intentionally different from Content and Collection services: it stores production inputs and intermediate authoring artifacts, never browser-facing published content.

## API

All `/api/v1` operations require `Authorization: Bearer <MAGIC_HTML_SERVICE_TOKEN>`.

- `GET /api/v1/projects/{project}/documents`
- `GET /api/v1/projects/{project}/documents/{document}`
- `PUT /api/v1/projects/{project}/documents/{document}`
- `DELETE /api/v1/projects/{project}/documents/{document}`

A document contains `name`, `kind`, `schema`, `value`, optional `metadata`, and a monotonically increasing `version`.

## Verification

```bash
composer install
php artisan test --compact
composer validate --strict
composer audit --no-dev
```
