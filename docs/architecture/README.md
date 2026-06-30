# Necoyoad Architecture Blueprint

This folder contains the eight-volume reverse-engineered architecture
blueprint of the Necoyoad web application.

## Eight Volumes

| Volume | Focus | Pages | Based on |
|--------|-------|-------|----------|
| **v1** | The whole platform from the SQL schema alone | 35 | `necoyoad_db.sql` (87 tables) |
| **v2** | v1 verified + corrected against the source | 38 | Full PHP source (1,450 files) |
| **v3** | Deep dive: the theme/widget rendering pipeline | 41 | The presentation layer (updated with async widget section) |
| **v4** | Deep dive: the marketing/campaign subsystem | 28 | The email-marketing pipeline |
| **v5** | Deep dive: multi-store/multi-language architecture | 21 | store_id + language_id composition, polymorphic object spine |
| **v6** | Deep dive: the admin back-office | 12 | AdminController, file manager, visual editors |
| **v7** | Deep dive: menu links in admin and apps | 12 | Menu/menu_link system, links widget, tree composition |
| **v8** | Deep dive: CMS posts/pages and widget composition | 15 | How posts/pages share a table, dynamic vs manual widget composition |

## Files

| File | Description |
|------|-------------|
| `necoyoad_architecture_blueprint_v1_sql_only.pdf` | **v1** final PDF. |
| `necoyoad_architecture_blueprint_v1_sql_only.tex` | v1 LaTeX source. |
| `cover_v1.html` | v1 cover HTML. |
| `necoyoad_architecture_blueprint_v2_source_verified.pdf` | **v2** final PDF. |
| `necoyoad_architecture_blueprint_v2_source_verified.tex` | v2 LaTeX source. |
| `cover_v2.html` | v2 cover HTML. |
| `necoyoad_architecture_blueprint_v3_rendering_pipeline.pdf` | **v3** final PDF (updated with async widget section). |
| `necoyoad_architecture_blueprint_v3_rendering_pipeline.tex` | v3 LaTeX source. |
| `cover_v3.html` | v3 cover HTML. |
| `necoyoad_architecture_blueprint_v4_marketing_campaign.pdf` | **v4** final PDF. |
| `necoyoad_architecture_blueprint_v4_marketing_campaign.tex` | v4 LaTeX source. |
| `cover_v4.html` | v4 cover HTML. |
| `necoyoad_architecture_blueprint_v5_multistore_language.pdf` | **v5** final PDF. |
| `necoyoad_architecture_blueprint_v5_multistore_language.tex` | v5 LaTeX source. |
| `cover_v5.html` | v5 cover HTML. |
| `necoyoad_architecture_blueprint_v6_admin_backoffice.pdf` | **v6** final PDF. |
| `necoyoad_architecture_blueprint_v6_admin_backoffice.tex` | v6 LaTeX source. |
| `cover_v6.html` | v6 cover HTML. |
| `necoyoad_architecture_blueprint_v7_menu_links.pdf` | **v7** final PDF. |
| `necoyoad_architecture_blueprint_v7_menu_links.tex` | v7 LaTeX source. |
| `cover_v7.html` | v7 cover HTML. |
| `necoyoad_architecture_blueprint_v8_cms_widget_composition.pdf` | **v8** final PDF. |
| `necoyoad_architecture_blueprint_v8_cms_widget_composition.tex` | v8 LaTeX source. |
| `cover_v8.html` | v8 cover HTML. |

## Stance

All eight documents are **descriptive, not prescriptive**. They document
how the system *is*. No improvements or modernisations are proposed.

## v8: CMS Posts/Pages and Widget Composition

v8 covers how posts and pages share the `post` table (discriminated by
`post_type`), how every CMS page is a widget composition surface, and
how widgets can be composed in three modes:

- **Dynamic composition**: the admin configures widgets via the visual
  editor; `widgets-rows.tpl` emits `{%widget_name%}` placeholders;
  `Controller::fetch()` substitutes them at render time.
- **Manual composition**: a template author hardcodes
  `{%widget_name%}` tokens directly in a `.tpl` file, bypassing the
  admin widget manager. Requires matching widget instances in the admin.
- **Hybrid composition**: a template combines dynamic sections (via
  `widgets-common.tpl`) with hardcoded sections (custom HTML, SVG, etc.).

Also covers: per-entity template override via EAV
`property('style', 'view')`, the `widgets-common.tpl` universal layout
fragment, the `only:` position prefix for embedded pages, and the
`page_embed.tpl` simplified layout used by the menu system's
`submenu_type = 'page_id'`.
