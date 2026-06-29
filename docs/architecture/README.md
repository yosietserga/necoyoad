# Necoyoad Architecture Blueprint

This folder contains the reverse-engineered architecture blueprint of the
Necoyoad web application.

## Files

| File | Description |
|------|-------------|
| `necoyoad_architecture_blueprint_v1_sql_only.pdf` | **v1** — Reconstructed from the `necoyoad_db.sql` dump alone (87 tables). Every inference is labelled as such. 35 pages, ~14,300 words, 18 TikZ diagrams. |
| `necoyoad_architecture_blueprint_v1_sql_only.tex` | LaTeX source of v1 (Tectonic-compatible). |
| `cover_v1.html` | HTML source of the v1 cover page (Academic Template 04 style). |
| `necoyoad_architecture_blueprint_v2_source_verified.pdf` | **v2** — Verified against the PHP source code (1,450 files). Corrects, confirms, or extends every v1 inference. (Added in a later commit.) |

## Stance

Both documents are **descriptive, not prescriptive**. They document how the
system *is* (or was, at the time of analysis). No improvements or
modernisations are proposed.
