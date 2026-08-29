# Necoyoad — Reverse-Engineering Documentation

> **Source of truth:** https://github.com/yosietserga/necoyoad — every claim in these chapters was
> **verified against the actual source code** (12,354 files, ~243 MB, 1,450+ PHP files in the legacy
> app plus a full Laravel 11 rewrite). File paths and line numbers quoted are real.
>
> **Companion volumes:** the 12 earlier blueprint PDFs live in [`docs/architecture/`](../architecture/README.md)
> (v1–v12, LaTeX sources included). This folder is the Markdown + Mermaid successor: richer,
> diagram-first, and focused on the 11 specialized chapters requested.

## What is Necoyoad?

Necoyoad is a **multi-store, multi-language e-commerce + CMS platform written in PHP**, historically
deployed at `*.necoyoad.com` (production artifacts reference `www.mudancer.com`). The repository
contains **two complete codebases**:

| Codebase | Location | Stack | Status |
|---|---|---|---|
| **Legacy platform** | repo root (`web/`, `app/`, `system/`) | Custom MVC framework (OpenCart-lineage Registry/Loader/Front engine), PHP 8.0+ (despite a PHP 5.1 gate), MyISAM MySQL, jQuery-era frontend | Production system |
| **necoyoad-next** | `necoyoad-next/` | Laravel 11 + Filament 3 + Livewire 3 + Sanctum + Vite, PHP 8.3, FrankenPHP/Caddy, MySQL 8 + Redis | Modern rewrite (partial parity) |

Both stacks implement the same conceptual product: widget-composed storefronts, banner engines,
menu trees, EAV properties, multi-store tenancy, and a marketing/campaign engine.

## Chapter Index

| # | Chapter | Focus |
|---|---|---|
| 1 | [General Architecture](01-general-architecture.md) | The whole platform: both codebases, layered view, module map, the 87-table schema by domain |
| 2 | [Homepage Boot-Stack Walkthrough](02-boot-stack-walkthrough.md) | `.htaccess → cconfig.php → config.php → startup.php → map.php → Front → dispatch → Response`, line by line |
| 3 | [Events & Hooks Blueprint](03-events-hooks-blueprint.md) | WordPress-style `Hooks` + static `Events` bus, every interception point, the full event catalog |
| 4 | [Widgets System](04-widgets-system.md) | Rows/columns/widgets tree, `{%widget%}` tokens, 67 widget modules, the three composition modes |
| 5 | [Banners](05-banners.md) | Legacy 33 slider templates vs the 8 modern engines, per-slide widgets/layers, analytics pipeline |
| 6 | [Menus](06-menus.md) | Adjacency-list menu trees, per-link EAV metadata, 3 submenu types, page embedding |
| 7 | [Templates Blueprint](07-templates-blueprint.md) | Theme resolution chains, `default_view_*`, visual CSS editor, the modern ThemeEditor with versioning |
| 8 | [Omni EAV Properties](08-omni-eav-properties.md) | The one-table-attaches-to-everything property system, full group/key catalog |
| 9 | [Multi-Store & Descriptions DTO](09-multistore-descriptions-dto.md) | Tenant resolution (subdomain/folder/domain), `description` DTO, language negotiation |
| 10 | [Caching & Rendering](10-caching-rendering.md) | Cache layers, TTL-in-filename, the `.pc` poisoning bug, render/minify/compress pipeline |
| 11 | [186 Killer Features](11-100-killer-features.md) | The complete, source-verified feature catalog (target was 100 — delivered 186) |

## Reading Guide

- **Diagrams** are [Mermaid](https://mermaid.js.org/) — GitHub renders them natively in this folder.
- **"Legacy"** always means the PHP app at the repo root; **"next"** always means `necoyoad-next/`.
- Each chapter ends with a **Legacy ↔ Next mapping table** and a **verified defects** section
  (bugs were confirmed in source, several with production cache artifacts as evidence).
- The research notes backing these chapters (with additional per-file line references) were produced
  by ten parallel deep-analysis passes over the repository.

## Headline Numbers

| Metric | Legacy | necoyoad-next |
|---|---|---|
| PHP files | ~1,450 | ~120 |
| DB tables | 87 (all MyISAM, no FKs, dump is schema-only) | 49 (InnoDB, FKs, morph maps) |
| Storefront widget modules | 67 | 7 components |
| Banner slider engines | 33 templates (jQuery era) | 8 engines (Swiper/GSAP/three.js/Canvas/SVG) |
| Widget positions | 7 | 7 (same names) |
| Event/hook points | ~34 hooks + ~39 filters + ~26 events | Laravel events + FilterPipeline port |
| Admin back-office | ~160 controllers (71 module dirs) | 16 Filament resources + 3 pages |
| Deploy | Apache + `.htaccess` + `system/cron/cron.php` | Docker: FrankenPHP + Caddy + MySQL 8 + Redis 7 |

## Stance

Like the earlier blueprints, these documents are **descriptive, not prescriptive** — they document
how the system *is*, including its defects. Where the rewrite diverges from the legacy semantics,
both behaviors are documented side by side.
