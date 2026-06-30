# Necoyoad Architecture Blueprint

This folder contains the three-volume reverse-engineered architecture
blueprint of the Necoyoad web application.

## Three Volumes

| Volume | Focus | Pages | Based on |
|--------|-------|-------|----------|
| **v1** | The whole platform from the SQL schema alone | 35 | `necoyoad_db.sql` (87 tables) |
| **v2** | v1 verified + corrected against the source | 35 | Full PHP source (1,450 files) |
| **v3** | Deep dive: the theme/widget rendering pipeline | 37 | The presentation layer in detail |

## Files

| File | Description |
|------|-------------|
| `necoyoad_architecture_blueprint_v1_sql_only.pdf` | **v1** final PDF (SQL-only reconstruction). |
| `necoyoad_architecture_blueprint_v1_sql_only.tex` | v1 LaTeX source. |
| `cover_v1.html` | v1 cover HTML. |
| `necoyoad_architecture_blueprint_v2_source_verified.pdf` | **v2** final PDF (source-verified). |
| `necoyoad_architecture_blueprint_v2_source_verified.tex` | v2 LaTeX source. |
| `cover_v2.html` | v2 cover HTML. |
| `necoyoad_architecture_blueprint_v3_rendering_pipeline.pdf` | **v3** final PDF (presentation layer deep dive). |
| `necoyoad_architecture_blueprint_v3_rendering_pipeline.tex` | v3 LaTeX source. |
| `cover_v3.html` | v3 cover HTML. |

## Stance

All three documents are **descriptive, not prescriptive**. They document
how the system *is*. No improvements or modernisations are proposed.

## v3: The Rendering Pipeline Deep Dive

v3 zooms in on a single subsystem: how a controller's return value
becomes the final HTML that reaches the browser. It covers:

- **The rendering engine**: `Controller::render()` (8 steps) and
  `Controller::fetch()` (12 steps), with full source listings.
- **The composite-view pattern**: layout children (numeric keys via
  `addChild()`) vs widget children (string keys via `loadWidgets()`).
- **The `{%widget_name%}` placeholder substitution**: a string-based
  post-render substitution, not a tree-based composition.
- **The widget data-access object**: `NecoWidget` (785 lines), the
  `property` EAV table, and the `LIKE '%key=value%'` query pattern.
- **The route-aware asset loader**: `?r=store/product` →
  `storeproduct.css` / `storeproduct.js` by string convention.
- **The `deps.php` manifest**: four asset manifests
  (`js_assets`, `js_header_assets`, `jsx_assets`, `css_assets`).
- **The six layout composers**: home, header, footer, column_left,
  column_right, maintenance.
- **The `choroni` theme**: 14 top-level folders, 87+ `.tpl` files,
  the `$tpl` inheritance mechanism, shared fragments.
- **Device detection and theme switching**: mobile/tablet/Facebook
  detection with redirect-or-swap strategies.
- **The visual theme editor**: `nt-editable` hooks, inline CSS markers,
  the `theme_style` CSS-rule override table.
- **The 18 Hooks and 10 Events** fired in the rendering pipeline.
- **A 33-step rendering pipeline trace**: from `web/index.php` entry
  to `Response::output()` final emission, naming every method call
  and every table query.
- **The 69 admin modules** (full listing in Appendix).

## v2: Source Verification Summary

| v1 inference | v2 status |
|---|---|
| `?r=common/home` route mapping | ✅ confirmed |
| Admin route convention | ✅ confirmed |
| Module route prefix | ✅ confirmed |
| `user_group.permission` serialised array | ✅ confirmed |
| Customer password is bcrypt/argon2 | ❌ **corrected** — double-MD5 with per-row salt |
| Order placement is 9-step flow | ✅ confirmed |
| `store_id` from `store.folder` + subdomain | ✅ confirmed |
| `url_alias` table maps SEO keywords | ✅ confirmed |
| `product.viewed` increment on page view | ✅ confirmed |
| API v1.0.0 is REST | ⚠️ **partial** — pseudo-REST |
| API auth uses public/private keys | ❌ **corrected** — `validateTokens()` is a stub |

## v2 New Findings

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
