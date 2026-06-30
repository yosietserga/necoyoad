# Necoyoad Architecture Blueprint

This folder contains the four-volume reverse-engineered architecture
blueprint of the Necoyoad web application.

## Four Volumes

| Volume | Focus | Pages | Based on |
|--------|-------|-------|----------|
| **v1** | The whole platform from the SQL schema alone | 35 | `necoyoad_db.sql` (87 tables) |
| **v2** | v1 verified + corrected against the source | 38 | Full PHP source (1,450 files) |
| **v3** | Deep dive: the theme/widget rendering pipeline | 37 | The presentation layer in detail |
| **v4** | Deep dive: the marketing/campaign subsystem | 28 | The email-marketing pipeline in detail |

## Files

| File | Description |
|------|-------------|
| `necoyoad_architecture_blueprint_v1_sql_only.pdf` | **v1** final PDF (SQL-only reconstruction). |
| `necoyoad_architecture_blueprint_v1_sql_only.tex` | v1 LaTeX source. |
| `cover_v1.html` | v1 cover HTML. |
| `necoyoad_architecture_blueprint_v2_source_verified.pdf` | **v2** final PDF (source-verified, includes expanded mobile/widget-scoping Section 12). |
| `necoyoad_architecture_blueprint_v2_source_verified.tex` | v2 LaTeX source. |
| `cover_v2.html` | v2 cover HTML. |
| `necoyoad_architecture_blueprint_v3_rendering_pipeline.pdf` | **v3** final PDF (presentation layer deep dive). |
| `necoyoad_architecture_blueprint_v3_rendering_pipeline.tex` | v3 LaTeX source. |
| `cover_v3.html` | v3 cover HTML. |
| `necoyoad_architecture_blueprint_v4_marketing_campaign.pdf` | **v4** final PDF (marketing subsystem deep dive). |
| `necoyoad_architecture_blueprint_v4_marketing_campaign.tex` | v4 LaTeX source. |
| `cover_v4.html` | v4 cover HTML. |

## Stance

All four documents are **descriptive, not prescriptive**. They document
how the system *is*. No improvements or modernisations are proposed.

## v4: The Marketing/Campaign Subsystem Deep Dive

v4 zooms in on the email-marketing pipeline: how a newsletter becomes
a sent, tracked, personalised email. It covers:

- **Newsletter composition** with WYSIWYG HTML body, base64 image
  extraction, and `{%placeholder%}` personalisation tokens (7
  per-recipient + 13 store-level).
- **The campaign composer** (Phase 1): DOMDocument parsing, tracking
  pixel append, link rewriting with md5 nonces, spam scoring, caching.
- **The campaign commit** (Phase 2): INSERT into campaign,
  campaign_contact, campaign_link, property, task, task_queue.
- **The cron sender** (`CronSend::sendCampaign`): 50 emails per pass
  with 15-minute defer, `task_exec` locking, per-recipient cache,
  PHPMailer 5.0 SMTP, AUTH LOGIN only.
- **The tracking endpoints**: `trace` (open pixel, writes
  `campaign_stat`) and `link` (click redirect, writes
  `campaign_link_stat`), both with full HTTP-context snapshot.
- **The double link rewriting finding**: admin commit + cron send both
  rewrite links and insert `campaign_link` rows; the commit-phase rows
  are dead data if the per-recipient cache hits.
- **The vestigial bounce processor**: commented out of cron, references
  non-existent Interspire SendStudio classes. No working bounce
  detection.
- **The auxiliary tasks**: birthday (with AND/OR semantic bug),
  promoter (product recommendations via `stat` table), seller (stub),
  update (stub).
- **The hidden `link2Campaign` mechanism**: a follow-up-campaign
  trigger fired by `?sendCampaign=<id>` on the tracking endpoints.
- **The spam rules** (1,177 lines): 3-layer content-scoring pipeline.
- **The 5 admin API marketing endpoints** (all PUT branches broken).
- **26 cross-cutting findings** including: PHPMailer 5.0 lineage, no
  unsubscribe mechanism, hard-coded Necoyoad-CDN tracking pixel,
  Venezuelan locale hard-coding, `campaign_contact.task_queue_id`
  declared but never written, `campaign_link` lookup not scoped by
  `campaign_id`.
- **A full 6-stage campaign lifecycle trace**: newsletter create →
  campaign compose → campaign commit → cron send → email open → email
  click, with every table write and read named.

## v3: The Rendering Pipeline Deep Dive

v3 covers the presentation layer: `Controller::render()` (8 steps) and
`Controller::fetch()` (12 steps), the composite-view pattern, the
`{%widget_name%}` placeholder substitution, NecoWidget (785 lines),
the `LIKE '%key=value%'` query pattern, the route-aware asset loader,
the `deps.php` manifest, device detection, the visual theme editor,
and a 33-step rendering pipeline trace.

## v2: Source Verification + Mobile Widget Scoping

v2 verifies v1's 11 inferences (8 confirmed, 2 corrected, 1 partial)
and adds: the bootstrap, the engine, the dual Hooks/Events system,
the password algorithm (double-MD5 with lazy salt migration), the
order flow, the cron CLI, the 39-endpoint admin REST API, and the
mobile app strategy (expanded with the 4-layer widget/template
scoping detail from v3).

## v1: SQL-Only Reconstruction

v1 reconstructs the entire platform from the 87-table database dump
alone, with 18 TikZ diagrams and 15 functional domains.
