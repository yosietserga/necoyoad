# Necoyoad Architecture Blueprint

This folder contains the five-volume reverse-engineered architecture
blueprint of the Necoyoad web application.

## Five Volumes

| Volume | Focus | Pages | Based on |
|--------|-------|-------|----------|
| **v1** | The whole platform from the SQL schema alone | 35 | `necoyoad_db.sql` (87 tables) |
| **v2** | v1 verified + corrected against the source | 38 | Full PHP source (1,450 files) |
| **v3** | Deep dive: the theme/widget rendering pipeline | 41 | The presentation layer (updated with async widget section) |
| **v4** | Deep dive: the marketing/campaign subsystem | 28 | The email-marketing pipeline |
| **v5** | Deep dive: multi-store/multi-language architecture | 21 | store_id + language_id composition, polymorphic object spine |

## Files

| File | Description |
|------|-------------|
| `necoyoad_architecture_blueprint_v1_sql_only.pdf` | **v1** final PDF. |
| `necoyoad_architecture_blueprint_v1_sql_only.tex` | v1 LaTeX source. |
| `cover_v1.html` | v1 cover HTML. |
| `necoyoad_architecture_blueprint_v2_source_verified.pdf` | **v2** final PDF (includes expanded mobile/widget-scoping Section 12). |
| `necoyoad_architecture_blueprint_v2_source_verified.tex` | v2 LaTeX source. |
| `cover_v2.html` | v2 cover HTML. |
| `necoyoad_architecture_blueprint_v3_rendering_pipeline.pdf` | **v3** final PDF (updated with async widget rendering section). |
| `necoyoad_architecture_blueprint_v3_rendering_pipeline.tex` | v3 LaTeX source. |
| `cover_v3.html` | v3 cover HTML. |
| `necoyoad_architecture_blueprint_v4_marketing_campaign.pdf` | **v4** final PDF. |
| `necoyoad_architecture_blueprint_v4_marketing_campaign.tex` | v4 LaTeX source. |
| `cover_v4.html` | v4 cover HTML. |
| `necoyoad_architecture_blueprint_v5_multistore_language.pdf` | **v5** final PDF. |
| `necoyoad_architecture_blueprint_v5_multistore_language.tex` | v5 LaTeX source. |
| `cover_v5.html` | v5 cover HTML. |

## Stance

All five documents are **descriptive, not prescriptive**. They document
how the system *is*. No improvements or modernisations are proposed.

## v5: Multi-Store / Multi-Language Architecture

v5 covers how `store_id` and `language_id` compose, the per-store settings
model, the polymorphic object spine, and widget composition by
`object_type`:

- **Store detection**: 3 strategies (URL path folder match,
  `?store_id` GET, subdomain regex — hard-coded to `necoyoad.com`).
- **No default+override settings merge**: each store loads only its own
  `store_id` settings. No fallback to `store_id=0`.
- **Language detection**: 6-level priority (`?language=` → `?hl=` →
  session → cookie → browser → `config_language`).
- **The polymorphic object spine**: 5 tables (`object_to_store`,
  `object_to_category`, `description`, `property`, `url_alias`) all use
  the `(object_id, object_type)` pair.
- **Inconsistent `object_to_store` writes**: products/categories/
  manufacturers use legacy column names; downloads/pages/posts use
  polymorphic; posts and post-categories INSERTs forget `store_id`.
- **Widget composition by `object_type`**: product/category controllers
  set session `object_type`/`object_id`; `loadWidgets()` runs a second
  query with `LIKE '%object_type=product%'` filters; per-object widgets
  are merged into the default tree.
- **The $S \times L$ composition matrix**: 5 filtering dimensions
  (`store_id`, `language_id`, `object_type`, `object_id`,
  `landing_page`) compose at query time.
- **A multi-store request lifecycle trace**.

## v3 Update: Async Widget Rendering

v3 has been updated with a new subsection (§2.5) clarifying the three
distinct "async" concepts in the widget system:
1. The `async()` endpoint — on-demand widget refresh via AJAX (returns JSON).
2. The `data-async="1"` HTML attribute — CSS transition animation flag (not AJAX).
3. The `async=on` widget setting — admin-side label (not used during page load).

Key finding: during normal page load, all widgets are rendered inline.
The async mechanism is an on-demand refresh feature, not a lazy-loading
optimization.
