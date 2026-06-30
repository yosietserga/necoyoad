# Necoyoad Architecture Blueprint

This folder contains the seven-volume reverse-engineered architecture
blueprint of the Necoyoad web application.

## Seven Volumes

| Volume | Focus | Pages | Based on |
|--------|-------|-------|----------|
| **v1** | The whole platform from the SQL schema alone | 35 | `necoyoad_db.sql` (87 tables) |
| **v2** | v1 verified + corrected against the source | 38 | Full PHP source (1,450 files) |
| **v3** | Deep dive: the theme/widget rendering pipeline | 41 | The presentation layer (updated with async widget section) |
| **v4** | Deep dive: the marketing/campaign subsystem | 28 | The email-marketing pipeline |
| **v5** | Deep dive: multi-store/multi-language architecture | 21 | store_id + language_id composition, polymorphic object spine |
| **v6** | Deep dive: the admin back-office | 12 | AdminController, file manager, visual editors |
| **v7** | Deep dive: menu links in admin and apps | 12 | Menu/menu_link system, links widget, tree composition |

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

## Stance

All seven documents are **descriptive, not prescriptive**. They document
how the system *is*. No improvements or modernisations are proposed.

## v6: The Admin Back-Office

v6 covers the 70+ admin controllers, the AdminController declarative
CRUD base (1,244 lines), the file manager (688 lines), the visual
editors (1,853 lines), admin authentication and RBAC, and the
843-line route-aware map.php preloader. 443 PHP files across 22
controller folders.

## v7: Menu Links in Admin and Apps

v7 covers the menu and menu_link system: admin CRUD (via
AdminController), the model's on('save')/on('delete') hooks for
tree management, the storefront's recursive getLinks() method, the
links widget module, three submenu types (children, page_id,
html_content), and the EAV property + polymorphic description tables
for per-link metadata and localisation. The menu system is a microcosm
that exercises every cross-cutting pattern in the platform.
