# 9 · Multi-Store & the Descriptions DTO

> Necoyoad is a **tenant platform**: one installation serves many stores, resolved by subdomain
> (`store.necoyoad.com`), URL folder (`/storecode/...`), or `?store_id=`. Content is localized
> through a polymorphic **`description` DTO** (one row per entity × language). This chapter maps
> tenant resolution, store-scoped data, and the description pipeline in both stacks. Companion PDF:
> blueprint v5; appendix: [`appendix-research/3b3-multistore-dto.md`](appendix-research/3b3-multistore-dto.md).

## 9.1 Legacy Store Resolution

```mermaid
flowchart TD
    REQ["HTTP request *.necoyoad.com"] --> HTA[".htaccess<br/>rewrite → /web/index.php<br/>?_route_=path"]
    HTA --> CANON[".htaccess:215-227<br/>www→non-www 301<br/>subdomain canonicalization 301"]
    CANON --> IDX["web/index.php"]
    IDX --> R1{"_route_ set?"}
    R1 -- yes --> PROBE["For EACH URI segment:<br/>SELECT * FROM store WHERE folder = segment<br/>(last match wins)"]
    R1 -- no --> R2{"?store_id set?"}
    R2 -- yes --> BYID["SELECT * FROM store<br/>WHERE store_id = ?store_id"]
    R2 -- no --> SUB["preg_match /([^.]+)\\.necoyoad\\.com/<br/>on SERVER_NAME"]
    PROBE --> HAVEM{"$matches[1] set & != www?"}
    BYID --> HAVEM
    SUB --> HAVEM
    HAVEM -- "yes" --> CFGEX{"app/<folder>/config.php exists?"}
    CFGEX -- yes --> STORECFG["require app/<folder>/config.php<br/>STORE_ID = store's id (constant)"]
    CFGEX -- no --> SHOPCFG["require app/shop/config.php<br/>STORE_ID = 0"]
    HAVEM -- "no" --> SHOPCFG
    STORECFG --> START["system/startup.php<br/>→ app/shop|<app>/map.php"]
    SHOPCFG --> START
    START --> SET["map.php:47-55<br/>settings WHERE store_id = STORE_ID<br/>(session cache ntConfig_{STORE_ID})"]
    SET --> LANG["map.php:62-121 language cascade"]
    LANG --> CURR["map.php:165 Currency lib<br/>(?currency→session→cookie→config)"]
    CURR --> DISP["Front controller dispatch<br/>store-scoped model queries<br/>p2s.store_id = STORE_ID"]
```

- **`store` table** (L1252-1260): `store_id, owner_id, name, folder, status, dates` — **no
  `domain` column** (the folder doubles as the subdomain label), no `is_default` (store 0 is the
  de-facto default). The dump has zero rows.
- `web/index.php`: folder probing (every `/`-segment probed, **last match wins**) → `?store_id=`
  (`elseif` — **ignored on SEO/routed URLs**) → subdomain regex
  `preg_match('/([^.]+)\.necoyoad\.com/', SERVER_NAME)` → `app/{folder}/config.php` if it exists,
  else `app/shop/config.php`.
- **`STORE_ID` is a compile-time constant** in the per-app config: `app/shop/config.php:4` → `0`;
  `app/admin/config.php:10` → `0` (admin always store 0); **`app/m/config.php:3` → the string
  `'9'`** (mobile app = store 9, reusing shop controllers via `$privatePath`).
- `.htaccess:215-227` canonicalization: www→non-www 301 + subdomain 301 (both emit scheme-relative
  `//host/...` targets).

## 9.2 Per-Store Settings & Scoping

- **`setting` table** `(store_id, group='config', key, value)` — `map.php:47-55` loads **store 0
  only** (or the whole `Config` object from session `ntConfig_0`, written back at :323).
  **No store-0 fallback / no default+override merge** — each store must carry a complete settings set.
- **17 tables carry a `store_id` column**: customer, menu, notification, object_to_store,
  order_payment, product_attribute, property, review, review_likes, search, setting, stat, store,
  task, template, theme, widget. ⚠ **`order` has NO store_id** — only denormalized
  `store_name`/`store_url` snapshots.
- Everything else (products, categories, posts, pages, banners, manufacturers…) is scoped through
  the **`object_to_store`** pivot (`UNIQUE(object_id, object_type, store_id)`; store 0 = "Default"
  checkbox in admin). `Model::__setStores` (:1086-1116) DELETE-then-REPLACE; auto-wired in
  add/update/copy; `buildSQLQuery` LEFT-JOINs the pivot when `stores ∈ $relations` and `$data
  ['store_id']` is present (empty → defaults to STORE_ID).
- **Store CRUD + per-store app generator** (`app/admin/controller/store/store.php`): slug
  validation (accent-strip, reserved words), `createStandardApp()` → `createFolder()` (mkdir
  `app/<folder>` + `web/<folder>`), optional `copyFiles()` (recursive copy of `app/shop` + assets —
  standalone app mode), `createConfig()` from `system/config/config_shared.txt` /
  `config_custom.txt` templates with **`%folder% %store_id% %admin_path% %package% %version%`
  placeholders** → writes `app/<folder>/config.php` + `web/<folder>/index.php`.

## 9.3 The Description DTO (Legacy)

**`nts8sd4fd_description`** (L452-465): `description_id, object_id, object_type, language_id,
title(255), description(text), seo_title(60), meta_description(160), meta_keywords(255), params,
dates` — a polymorphic + language-keyed DTO. **No unique key on (object, type, language)**
(dupes prevented only by delete-then-insert code). The edit-form `keyword` is NOT stored here — it
lives in `url_alias` (same addressing shape).

**Model API** (`system/engine/model.php`):

- `__getDescriptions` (:1193-1228) — criteria type + id (+ optional language); reshapes rows to
  `[language_id => [language_id, title, description, seo_title, meta_keywords, meta_description]]`;
  then queries `url_alias` with the same criteria and merges `keyword` per language.
- `__setDescriptions` (:1280-1348) — per language: delete-then-INSERT (UPDATE branch commented
  out); `params` PHP-serialized; **if `keyword` → `REPLACE INTO url_alias (language_id, object_id,
  object_type, query='object_type=ID', keyword)`**.
- Wrappers use `$this->description_object_type ?: $this->object_type` — banner → `banner_item`,
  menu → `menu_link` (child-type descriptions).
- CRUD auto-wiring: add :322, update :415, copy :481, delete :548-563.

**The read path — LEFT JOIN + row merge:**

```mermaid
flowchart TD
    CTRL["Storefront controller<br/>model->getAll(data)"] --> CACHE{"file cache hit?<br/>key: table-type_STORE_serialize(data)<br/>_langid.hl.cc.currency.storeid<br/>(bypassed if admin logged in)"}
    CACHE -- hit --> ROWS["cached rows"]
    CACHE -- miss --> SQL["SELECT * FROM entity t<br/>LEFT JOIN description td ON t.pkey=td.object_id<br/>WHERE td.object_type=OT<br/>AND td.language_id=config_language_id"]
    SQL --> MERGE["PDO FETCH_ASSOC: td.title/description/<br/>seo_title/meta_* overwrite entity row<br/>(product model also aliases pd.title AS name)"]
    MERGE --> ROWS
    ROWS --> NOTE["⚠ No language fallback:<br/>missing translation ⇒ row excluded<br/>(WHERE turns LEFT JOIN into INNER)"]
```

`buildSQLQuery:834-837` LEFT-JOINs `description td` + `WHERE td.object_type = X`; language criteria
only when `$data['language_id']` isset (not set → rows duplicate per language, `GROUP BY t.pkey`
picks an arbitrary one). Because the language filter sits in the WHERE clause, the LEFT JOIN
behaves as INNER: **an entity lacking a description in the current language vanishes from
listings** — there is **no default-language fallback anywhere in the legacy read path**. The merge
itself is `SELECT *` + `FETCH_ASSOC` — description columns **overwrite** entity columns (last-wins);
the storefront product model also aliases explicitly (`pd.title AS name`, …).

**Entities with descriptions (object_type catalog):** product, category, post_category, post, page
(same `post` table), manufacturer, banner + banner_item, menu_link, download, review, coupon,
newsletter, campaign, theme + localisation tables (country, currency, zone, length/weight class,
stock_status, order_status, order_payment_status).

**Language negotiation** (`map.php:62-121`): `?language=` → `?hl=` → session → cookie →
`HTTP_ACCEPT_LANGUAGE` (split on `,`, matched against each language's `locale` comma-list, **last
match wins**, no q-values) → `config_language`; persisted session + cookie. Only
`app/shop/language/spanish/**` ships; `Language::load` falls back to the `spanish/` directory.
Admin has no cascade — the `config_admin_language` setting. Language-create clones all description
rows into the new language; language-delete removes them.

## 9.4 DTO Class Diagram

```mermaid
classDiagram
    class LegacyDescriptionRow {
        +int description_id PK
        +int object_id
        +string object_type
        +int language_id
        +string title
        +text description
        +string seo_title (60)
        +string meta_description (160)
        +string meta_keywords (255)
        +text params (PHP serialize)
        +timestamp date_added
        +datetime date_modified
    }
    class Model_Base {
        <<abstract>>
        #string table
        #string pkey
        #string object_type
        #string description_object_type
        #array relations
        +getDescriptions(id, lang_id?) map[lang]=>fields
        +setDescriptions(id, data)
        +__getDescriptions / __setDescriptions / __deleteDescriptions
        +getStores(id) int[]
        +setStores(id, data)
    }
    class Description_next {
        +int id PK
        +string describable_type
        +int describable_id
        +int language_id FK
        +string? title
        +longText? description
        +string? seo_title
        +string? meta_description
        +string? meta_keywords
        +json params
        +timestamps
        +describable() MorphTo
        +language() BelongsTo
    }
    class HasDescriptions {
        <<trait>>
        +descriptions() MorphMany
        +getDescription(langId?) Description?
        +getTitle() / getBody() / getSeoTitle()
        +getMetaDescription() / getMetaKeywords()
    }
    class HasStoreAssignment {
        <<trait>>
        +stores() MorphToMany(store_assignments)
        +scopeForStore(id) / scopeForCurrentStore()
        +assignToStore(id) / removeFromStore(id)
        +bootHasStoreAssignment() global scope
    }
    Model_Base ..> LegacyDescriptionRow : "LEFT JOIN td + PDO assoc overwrite"
    Description_next ..> HasDescriptions : used by Product/Post/Category/...
    HasStoreAssignment ..> Description_next : orthogonal (store × language)
```

## 9.5 Store-Scoped ER

### 9.5.1 Legacy

```mermaid
erDiagram
    nts8sd4fd_store ||--o{ nts8sd4fd_setting : "store_id (no fallback merge)"
    nts8sd4fd_store ||--o{ nts8sd4fd_widget : "store_id column"
    nts8sd4fd_store ||--o{ nts8sd4fd_menu : "store_id column"
    nts8sd4fd_store ||--o{ nts8sd4fd_theme : "store_id column"
    nts8sd4fd_store ||--o{ nts8sd4fd_template : "store_id column"
    nts8sd4fd_store ||--o{ nts8sd4fd_stat : "store_id column"
    nts8sd4fd_store ||--o{ nts8sd4fd_search : "store_id column"
    nts8sd4fd_store ||--o{ nts8sd4fd_review : "store_id column"
    nts8sd4fd_store ||--o{ nts8sd4fd_task : "store_id column"
    nts8sd4fd_store ||--o{ nts8sd4fd_notification : "store_id column"
    nts8sd4fd_store ||--o{ nts8sd4fd_customer : "home store_id"
    nts8sd4fd_store ||--o{ nts8sd4fd_property : "nullable store_id"
    nts8sd4fd_object_to_store }o--|| nts8sd4fd_store : "store_id"
    nts8sd4fd_object_to_store }o--|| nts8sd4fd_OBJECTS : "object_id + object_type"
    nts8sd4fd_OBJECTS ||..|| PRODUCTS : "product, category, post, page,<br/>post_category, banner, banner_item,<br/>manufacturer, menu, menu_link, download,<br/>coupon, customer, user, theme..."
    nts8sd4fd_description }o--|| nts8sd4fd_OBJECTS : "object_id + object_type"
    nts8sd4fd_description }o--|| nts8sd4fd_language : "language_id"
    nts8sd4fd_url_alias }o--|| nts8sd4fd_OBJECTS : "object_id + object_type"
    nts8sd4fd_url_alias }o--|| nts8sd4fd_language : "language_id"
    nts8sd4fd_object_to_category }o--|| nts8sd4fd_OBJECTS : "object_id + object_type"
    nts8sd4fd_property }o--|| nts8sd4fd_OBJECTS : "object_id + object_type"
    nts8sd4fd_order }o..|| nts8sd4fd_store : "NO store_id — denormalized<br/>store_name + store_url only"
```

### 9.5.2 necoyoad-next

```mermaid
erDiagram
    stores ||--o{ store_assignments : "store_id"
    store_assignments }o--|| ASSIGNED : "assignable_type + assignable_id (morph)"
    ASSIGNED ||..|| MODELS : "Product, Category, Post,<br/>Banner, Manufacturer"
    stores ||--o{ store_languages : ""
    languages ||--o{ store_languages : ""
    descriptions }o--|| languages : "language_id FK cascade"
    descriptions }o--|| DESCRIBABLE : "describable_type + describable_id (morph)"
    DESCRIBABLE ||..|| MODELS : "Product, Post, Category,<br/>Manufacturer, Banner, BannerItem, MenuLink"
    stores ||--o{ widgets : "store_id column (widget tree)"
    stores ||--o{ menus : "store_id column"
    orders }o--|| stores : "store_id"
    orders }o--|| currencies : "currency_id"
    MODELS ||..|| MODELS : "categorizables morph pivot,<br/>properties (propertiable morph),<br/>url_aliases (aliasable morph)"
```

## 9.6 necoyoad-next — StoreContext + HasDescriptions

### 9.6.1 Store & language resolution

```mermaid
flowchart TD
    REQ["HTTP request"] --> MW["global middleware<br/>bootstrap/app.php:18"]
    MW --> SC["StoreContext::resolve() (singleton)"]
    SC --> S1{"Store.domain == host?"}
    S1 -- yes --> ST["Store resolved"]
    S1 -- no --> S2{"?store_id present?"}
    S2 -- yes --> S2Q["Store::find(store_id)"] --> ST
    S2 -- no --> S3{"host has &gt;2 labels<br/>and first != www?"}
    S3 -- yes --> S3Q["Store::where(folder, subdomain)"] --> ST
    S3 -- no --> S4{"any path segment ==<br/>a store folder?"}
    S4 -- yes --> S4Q["Store::where(folder, segment)"] --> ST
    S4 -- no --> FB["is_default=true →<br/>config default_store_id →<br/>Store::first()"]
    FB --> ST
    ST --> BIND["app()->instance('store.context')<br/>view()->share('store')"]
    BIND --> LMW["ResolveLanguageContext"]
    LMW --> LC["LanguageContext::resolve()<br/>?language → ?hl → session →<br/>cookie → Accept-Language →<br/>store.setting(config_language)"]
    LC --> LOCALE["app()->setLocale(code)<br/>view()->share('language')"]
    LOCALE --> Q["Eloquent queries auto-scoped by<br/>HasStoreAssignment global scope:<br/>store-assigned OR unassigned"]
```

- **`StoreContext`** (per-request singleton): `resolve()` memoized — ① exact `stores.domain` match
  (new concept, highest priority) → ② `?store_id=` → ③ subdomain (>2 host labels, non-www →
  `folder` match; generalizes the legacy necoyoad.com regex) → ④ path segment matching a folder
  (**does NOT consume the segment** — would need `Route::prefix('{store?}')`, not implemented) →
  ⑤ fallback `is_default` → `config('necoyoad.default_store_id')` → `Store::first()`. Accessors:
  `id()/model()/folder()/setting($key, $default)` → `Store->settings` JSON (replacing ~200 legacy
  `config_*` rows with one JSON blob). No cache layer.
- **Middleware** (global, in order): `ResolveStoreContext` (bind + `view()->share('store')`) →
  `ResolveLanguageContext` (bind + `app()->setLocale` + share). `StoreNotResolvedException` (503)
  exists but is **never thrown**.
- **`LanguageContext`**: 6-level cascade mirroring legacy — `?language=` → `?hl=` → session →
  cookie → `Accept-Language` (improved: strips q-values, first match breaks) → store setting;
  final fallback `$languages->first() ?? new Language(['id'=>1,'code'=>'en'])`; persists session +
  30-day cookie.
- **`HasStoreAssignment`** trait — pivot `store_assignments` (morph `assignable`, UNIQUE per
  store×entity): **global scope `whereHas('stores', id) OR orDoesntHave('stores')`** —
  assigned-to-current-store OR unassigned (= global) content is visible; a deliberate semantic
  change from legacy store-0-default. Used by Banner, Category, Manufacturer, Post, Product.
  Admin bypass: `NecoyoadResource::getEloquentQuery()` → `withoutGlobalScope('store')`.
- **`Store` model + Filament StoreResource**: name/folder(unique)/domain/is_default/status +
  **Settings tab = KeyValue JSON editor**; `store_languages` pivot (per-store language subset).
- **Multi-currency:** `Currency` model (code/symbol/decimal_place/value decimal(15,8)) + seeder
  (USD 1.0, VES 36.5, EUR 0.92) — **no conversion layer ported** (`?cc=`/session/cookie cascade
  and `format()/convert()` gone; product-list blade prints the global default code + unconverted
  price). `orders` persist `store_id` + `currency_id` FKs — data model ready, runtime conversion is
  a gap. Legacy `Currency` lib cascade: `?currency=` → session → cookie → `config_currency`; the
  real `?cc=` switch is a header-controller redirect.
- **Seeders** — 5 demo stores: Necoyoad Demo (default, USD/en), TechWorld, Moda Latina (VES/es),
  Home & Garden (EUR/en), Gadgets Pro — each with 5 customers, 3 categories, 5 products, 1 page +
  2 posts, 1 banner (3 slides), 1 menu (4 links), widget tree, contacts, newsletter; passwords all
  `password`.

### 9.6.2 Description resolution

```mermaid
flowchart TD
    VIEW["Blade / controller: $product->getTitle()"] --> HD["HasDescriptions::getTitle()"]
    HD --> GD["getDescription(languageId ?? app('language.context')->id())"]
    GD --> Q["descriptions()->where('language_id', id)->first()"]
    Q --> FOUND{"row?"}
    FOUND -- yes --> RET["Description DTO: title, description,<br/>seo_title, meta_description, meta_keywords, params(JSON)"]
    FOUND -- no --> NULL["null → caller fallback<br/>(e.g. ?? $product->sku)"]
    NULL --> NOTE2["⚠ No default-language fallback either;<br/>entity itself still loads (global scope only<br/>filters by store, not language)"]
```

- **`Description` model**: `describable` morph + `language_id` FK + title/description/seo_title/
  meta_description/meta_keywords/params (JSON cast); migration has
  **UNIQUE(describable_type, describable_id, language_id)** — fixes the legacy duplicate hole;
  **no store_id** (localization orthogonal to tenancy).
- **`HasDescriptions`**: `descriptions(): MorphMany`; `getDescription(?lang)` — single-language
  fetch (language context default), **no fallback chain** (null if missing; callers do `?? sku`);
  helpers `getTitle/getBody/getSeoTitle/getMetaDescription/getMetaKeywords`.
- **Morph map** reuses legacy object_type strings ('page' → Post::class) for 1:1 data migration.
- **Filament consumption** (`NecoyoadResource::sharedTabs()`): **Descriptions tab** =
  `Repeater::make('descriptions')->relationship()` per language (language_id select, title,
  description, SEO fields) — the direct descendant of legacy per-language `<div id="languageN">`
  tabs; **Stores tab** = multi-select ("Leave empty for all stores"); **SEO tab** = keyword.
- Storefront: route-model binding + global scope → cross-store deep links 404; `getTitle() ??
  $product->sku`; search queries descriptions via whereHas; CartDrawer eager-loads descriptions
  filtered by LanguageContext; cart = `session('cart')` — **not store-scoped** (quirk preserved
  from legacy, where the cart key uses install-wide `C_CODE`).

## 9.7 Legacy ↔ Next Mapping

| Concern | Legacy | Next |
|---|---|---|
| Store identity | `store` (folder only) | `stores` + domain + is_default + settings JSON |
| Resolution | folder-probe / `?store_id=` / subdomain regex / app-config require | domain / `?store_id=` / subdomain / path segment / default fallback |
| STORE_ID | PHP constant | `StoreContext` singleton |
| Settings | `setting` rows per store, session-cached, no merge | `stores.settings` JSON via `setting()` |
| Store CRUD | controller + file generator (.txt templates) | Filament StoreResource, no codegen |
| Store pivot | `object_to_store` (store 0 = default) | `store_assignments` morph; unassigned = global (scope) |
| Assignment UI | `stores[]` checkbox scrollbox | Filament multi-select shared tab |
| Descriptions | `description` + `url_alias`, delete+INSERT, LEFT-JOIN overwrite merge | `descriptions` morph (UNIQUE), `updateOrCreate`, explicit relation |
| Language fallback | none (entity hidden) | none (null field, entity still loads) |
| Language detection | 6-level map.php cascade | 6-level LanguageContext (q-values fixed) |
| Currency | Currency lib + `?cc=` + convert/format | model only; **conversion not ported** |
| Cart | not store-scoped (C_CODE) | not store-scoped (session) |
| Order↔store | denormalized store_name/url | `orders.store_id` + `currency_id` FKs |
| Cache keys | store×lang×currency | `widgets:{store}:{lang}:…` only |

## 9.8 Verified Defects (selection)

**Legacy:** `saveContent()` bulk-assignment SQL is broken (non-existent columns on 3 blocks; 7
blocks omit `store_id` → rows land on store 0; per-block DELETEs wipe other types' assignments);
store-delete references 10 phantom `*_to_store` tables; no settings fallback merge; `?store_id=`
ignored on SEO URLs; folder probe scans all segments; hard-coded necoyoad.com; `STORE_ID='9'`
string in app/m; no unique index on description(object, language); language-missing ⇒ entity
invisible; `SELECT *` column collisions (description overwrites `date_modified`/`params`); order
lacks store_id; cart/customer not store-scoped.

**Next:** no currency conversion; `StoreNotResolvedException` never thrown; path-segment strategy
doesn't consume the segment (404 risk); no language fallback in HasDescriptions; cart not
store-scoped; `store_languages` pivot not enforced by LanguageContext; Menu keeps a legacy
`store_id` column while siblings use the morph pivot (mixed mechanisms); singleton contexts capture
the request at first resolution (Octane caveat).

---

Next: [Chapter 10 — Caching & Rendering](10-caching-rendering.md) · [Back to index](README.md)
