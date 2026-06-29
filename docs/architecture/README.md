# Necoyoad Architecture Blueprint

This folder contains the two-volume reverse-engineered architecture
blueprint of the Necoyoad web application.

## Two Volumes

| Volume | Based on | Pages | Purpose |
|--------|----------|-------|---------|
| **v1** | `necoyoad_db.sql` only (87 tables) | 35 | Reconstruct the runtime from the database schema alone. Every inference labelled as such. |
| **v2** | Full PHP source (1,450 files) | 35 | Verify, correct, and extend v1 against the actual code. 8/11 inferences confirmed, 2 corrected, 1 partial. |

## Files

| File | Description |
|------|-------------|
| `necoyoad_architecture_blueprint_v1_sql_only.pdf` | **v1** final PDF (SQL-only reconstruction). |
| `necoyoad_architecture_blueprint_v1_sql_only.tex` | v1 LaTeX source (Tectonic-compatible). |
| `cover_v1.html` | v1 cover HTML source. |
| `necoyoad_architecture_blueprint_v2_source_verified.pdf` | **v2** final PDF (source-verified). |
| `necoyoad_architecture_blueprint_v2_source_verified.tex` | v2 LaTeX source (Tectonic-compatible). |
| `cover_v2.html` | v2 cover HTML source. |

## Stance

Both documents are **descriptive, not prescriptive**. They document how
the system *is* (or was, at the time of analysis). No improvements or
modernisations are proposed.

## v2 Verification Summary

| v1 inference | v2 status |
|---|---|
| `?r=common/home` route mapping | ✅ confirmed |
| Admin route convention | ✅ confirmed |
| Module route prefix `modules/<name>/<app>/<route>` | ✅ confirmed |
| `user_group.permission` serialised array | ✅ confirmed |
| Customer password is bcrypt/argon2 | ❌ **corrected** — double-MD5 with per-row salt |
| Order placement is 9-step flow | ✅ confirmed |
| `store_id` from `store.folder` + subdomain | ✅ confirmed (plus `?store_id` GET param) |
| `url_alias` table maps SEO keywords | ✅ confirmed |
| `product.viewed` increment on page view | ✅ confirmed |
| API v1.0.0 is REST | ⚠️ **partial** — pseudo-REST (switch on HTTP method) |
| API auth uses public/private keys | ❌ **corrected** — `validateTokens()` is a stub returning `true` |

## New Findings in v2

- The dual Hooks/Events extension system (WordPress-style + Node-style)
- The lazy salt migration in the password algorithm
- The order garbage collection (deletes unconfirmed orders >1 month old)
- The `validateTokens()` API auth stub
- The mobile app reuse trick (`app/m/` redirects to `app/shop/`)
- The mis-named `map.php` (actually a bootstrap init script)
- The two-tier controller architecture (`Controller` + `AdminController`)
- The file-cache-backed cart with three persistence layers
- The 6-stage bootstrap sequence
- The 39-endpoint admin REST API surface
