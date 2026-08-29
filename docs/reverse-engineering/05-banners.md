# 5 · Banners — From 33 jQuery Sliders to 8 Modern Engines

> The banner subsystem is the platform's showcase feature. Legacy: a `jquery_plugin` discriminator
> column selecting among **33 slider templates** with a `window.ntPlugins` client registry. Next:
> an EAV-selected engine family of **8 modern engines** (Swiper, GSAP ×3, three.js, Canvas, SVG
> morph, Ken Burns) with lazy dynamic imports and a browser→server analytics bus. Companion PDF:
> blueprint v9; appendix: [`appendix-research/3a3-banners.md`](appendix-research/3a3-banners.md).

## 5.1 Legacy Data Model

- **`nts8sd4fd_banner`** (L107-117): `banner_id`, `name`, **`jquery_plugin` varchar(150) — THE
  discriminator** (drives template + JS + CSS folder lookup), `params` text (**dead column — zero
  read/write sites**), `publish_date_start/end` (`'0000-00-00'` = open), `status`.
- **`nts8sd4fd_banner_item`** (L125-134): `banner_item_id`, `banner_id`, `image`, `link`,
  `sort_order`, `status`.
- **EAV attachment** (see [Chapter 8](08-omni-eav-properties.md)): localized title/description →
  `description` (object_type `banner_item`); per-slide settings → `property` group `settings`
  (`slidename`, `transition_delay/duration/effect in+out`); store scoping → `object_to_store`;
  **per-slide widgets** → `widget` rows (object_type `banner_item`).
- **Scheduling SQL** (shop model `banner.php:26-33`): `publish_date_start <= NOW() AND
  (publish_date_end >= NOW() OR '0000-00-00') AND status=1 AND store match` — the only visibility
  gates; language resolves at render time.

## 5.2 Legacy Storefront Rendering

```mermaid
sequenceDiagram
    autonumber
    participant Page as Page controller (loadWidgets)
    participant MC as ControllerModuleModuleController::index()
    participant B as ControllerModuleBanner (module:settings filter)
    participant M as ModelContentBanner (shop)
    participant NW as NecoWidget::getWidgets()
    participant F as Controller::fetch()
    participant T as banner/<plugin>.tpl
    participant JS as theme.js loadNTPlugins()

    Page->>MC: render widget "banner" (settings.banner_id)
    MC->>MC: default template {theme}/module/banner.tpl (MISSING - F-5)
    MC->>MC: loadDeps("module/banner/"+view) (no manifest entry)
    MC->>B: applyFilters("module:banner:module:settings", {widget,render,settings})
    B->>M: getById(banner_id)
    M->>M: SELECT banner JOIN object_to_store<br/>WHERE publish window + status=1 + store
    M-->>B: banner row + items (status=1)
    loop each banner_item
        B->>M: getDescriptions(banner_item_id)
        B->>NW: getWidgets(object_type='banner_item', object_id, position='main', landing='all')
        NW-->>B: widgets[] (settings.offsetX/offsetY)
        B->>B: children[name]=route · items[k].widgets[name]
    end
    B->>B: enqueue sliders/<jquery_plugin>/slider.js + slider.css (if exists)
    B->>B: template = theme/banner/<plugin>.tpl → choroni/banner/<plugin>.tpl → nivo-slider.tpl
    B-->>MC: {widget, render, settings(+banner)}
    alt sync render
        MC->>F: render() → fetch(template)
    else async render (F-6)
        MC->>MC: javascripts/scripts/styles = [] (wipes slider assets)
        MC->>F: render(true) → JSON payload
    end
    F->>T: extract($banner,$widgetName,$settings,$Config,$Image…)
    T->>T: foreach items → slider HTML + {%widget%} placeholders
    T->>T: ntPlugins.push({id, config, plugin}) + loadNTPlugins()
    F->>F: str_replace {%widget%} ← children HTML · applyFilters("render")
    F-->>Page: banner <li> HTML
    JS->>JS: DOM ready → for each ntPlugins entry:<br/>if $[plugin] → $(id)[plugin](config) [fn variant] → null
```

Flow through `app/shop/controller/module/banner.php` `module:settings` filter (L12-84):

1. Store-scoped `getById` → per-item descriptions + **recursive `NecoWidget::getWidgets
   (object_type='banner_item', position='main', landing='all')`** → registers per-slide widget
   children + offsetX/offsetY overlays (the "widgets on slides" concept).
2. Enqueues `sliders/<plugin>/slider.js|css` (file_exists-guarded).
3. **3-tier template resolution**: `{theme}/banner/<plugin>.tpl` → `choroni/banner/<plugin>.tpl` →
   **hard fallback `nivo-slider.tpl`**. (There is no `module/banner.tpl` at all — a banner with an
   empty `jquery_plugin` hits a debug `exit()`.)
4. Client seam: `window.ntPlugins.push({id, config, plugin, fn?})` + `loadNTPlugins()`
   (`theme.js:91-106`) — init guard on `$[plugin]` presence; `fn(slider, el)` post-init variant.

⚠ **Async pitfall:** `modulecontroller.php` **resets javascripts/styles AFTER the filter** — async
banner widgets lose their slider assets.

## 5.3 The 33 Legacy Templates (`app/shop/view/theme/choroni/banner/`)

| # | Template | Engine / effect |
|---|---|---|
| 1-2 | `nivo-slider`, `nivo-slider-v3.1` | Nivo Slider (fallback; v3.1 adds HTML captions) |
| 3 | `slick` | slick 1.5.0 (selector missing `#` → never inits) |
| 4 | `camera-v1.3.4` | Camera 1.4.0 canvas slideshow |
| 5 | `eislideshow` | codrops Elastic Image Slideshow |
| 6 | `evolution-v1.1.5` | Slider Evolution (hardcoded widget id `#widget_banner_1047204256`) |
| 7 | `layer-slider-v0.0.1` | custom vanilla JS; per-item `{%widget%}` tokens |
| 8 | `parallax-content-slider` | cslider (selector `"slider"` never inits) |
| 9 | `horizontal-parallax` | CSS-only parallax |
| 10 | `slicebox-v1.1.0` | codrops 3D cuboids |
| 11 | `jssor-vertical-nav` | jssor with vertical thumbnails |
| 12 | `necoslider` | dead static demo (ignores items) |
| 13 | `carousel` | Owl Carousel 2.2.1 |
| 14-15 | `horizontal`, `vertical` | CSS lists; per-item `{%widget%}` |
| 16-18 | `fancybox-gallery`, `fancybox-grid`, `grid-gallery` | fancyBox 3.0.47 / gridrotator (no `#` → never inits) |
| 19-33 | `horizontal-hover-effect-01..15` | pure-CSS hover galleries (only -08 ships JS = anime.js + PolarisFx; its ntPlugins entry **lacks `plugin:`** → never initializes) |

Assets: 14 JS dirs / 30 CSS dirs under `web/assets/…/sliders/`; the fallback `nivo-slider`'s own
JS+CSS are **missing** from assets.

## 5.4 Legacy Admin

```mermaid
flowchart TD
    subgraph FormSubmit["Classic list editor (form POST)"]
        A1["banner_form.tpl posts<br/>name, jquery_plugin, publish_*, stores[], items[k][image|link|descriptions]"] --> A2[ControllerAdmin::upsert]
        A2 --> A3[ModelContentBanner::add/update<br/>+ relations stores → object_to_store]
        A3 --> A4["on('save') hook:<br/>foreach items → setItem(item)"]
        A4 --> A5[INSERT/UPDATE banner_item<br/>+ __setDescriptions banner_item<br/>+ setAllItemProperties group=settings]
    end
    subgraph VisualEditor["Visual slide editor (AJAX, per keystroke)"]
        B1[loadSlideSettings vtab click] --> B2["GET api/v1/banner_items<br/>(cached in window.slideSettings_)"]
        B2 --> B3[loadSlideData fills Bg/transition inputs<br/>+ api/v1/widgets?ot=banner_item]
        B3 --> B4["mapPointer divs @offsetX/offsetY<br/>+ fancybox widget config forms"]
        C1[input onchange] --> C2["POST api/v1/banner_items<br/>(properties[image|slidename|transition_*])"]
        C3[Add Slide click] --> C4["GET content/banner/saveItem<br/>→ setItem (empty item)"]
        C5[drop widget on canvas] --> C6["POST module/&lt;ext&gt;/widget<br/>ot=banner_item oid=… offsetX/offsetY"]
        C7[dblclick pointer] --> C8[GET style/widget/delete]
    end
    subgraph Delete["Delete banner"]
        D1[delete] --> D2["addHook('delete') BEFORE:<br/>deleteItems(id)"]
        D2 --> D3["on('delete') AFTER:<br/>deleteItems(id) again (no-op, F-11)"]
        D3 --> D4["description + property (banner_item)<br/>+ banner_item deleted<br/>widget rows ORPHANED (F-8)"]
    end
```

- `ControllerContentBanner` — grid filters, sliders list from `glob(DIR_JS.'sliders/*',
  GLOB_ONLYDIR)`, AJAX `saveItem()`/`deleteItem()` (delete→re-insert item + descriptions +
  properties; id churn F-13).
- **Visual slide editor** (`banner_form.tpl`, 447 lines): vtabs editor (Bg/Contents/Preview; per-slide
  `properties[slidename|transition_*]` instant-save) + classic sortable items editor.
  `contentbanner.js` (579 lines): `loadSlideSettings` → `api/v1/banner_items`; per-keystroke POST;
  **widget drag-drop onto slide bg** computes percentage offsetX/offsetY and POSTs
  `module/<ext>/widget` with `ot=banner_item, oid=<item>`; fancybox config dialogs.
- Model self-observers: `on("save")` bulk `setItem`; `addHook("delete")` AND `on("delete")` both
  call `deleteItems` (double cascade, second no-op); delete **orphans per-slide widget rows**.
- Widget form (`module/banner/widget.php`): settings `class/width/margin/padding/float` + a single
  `banner_id` select.

## 5.5 necoyoad-next — The Modern Banner Engine

### 5.5.1 Render paths

```mermaid
sequenceDiagram
    autonumber
    participant W as Widget row render (x-dynamic-component)
    participant BC as View\Components\Widgets\Banner
    participant R as BannerRendererService
    participant E as EavService (properties table)
    participant V as view(components.banners.engines.*)
    participant L as banner-loader.js
    participant Eng as engines/<name>-engine.js
    participant API as POST /api/banner/event/*

    W->>BC: settings {banner_id}
    BC->>BC: Banner::with(items.descriptions) status+publish window query
    BC->>BC: per item → WidgetService::getTree(ot=banner_item, only=true)<br/>+ EAV slide→settings offsetX/offsetY
    BC->>R: getConfig / getSlides / getEngine  (render() BYPASSED - N-1)
    R->>E: getGroup(banner,'banner') + getGroup(item,'slide') per item
    E-->>R: engine/config/layers/transitions
    BC->>BC: resolveTemplate() → data() bug → ALWAYS components.banners.engines.swiper (N-2)
    BC->>V: wrapper include (engine name param)
    V-->>W: <div data-banner-id/-engine/-config/-slides> + Loading… placeholder
    Note over L: DOMContentLoaded
    L->>L: querySelectorAll('[data-banner-engine]')
    L->>Eng: await import('./engines/'+engine+'-engine.js')
    Eng->>Eng: build DOM (swiper/gsap/three/canvas/svg)<br/>NOTE: layers ignored (N-4)
    loop transitions / clicks
        Eng->>L: eventBus.emit('slideChanged'/'interaction') → Alpine bannerBus
        Eng->>API: dispatchSlideChanged / dispatchInteraction (keepalive, CSRF)
    end
    API-->>API: BannerEventService → Event::dispatch (0 listeners) → AuditService → user_activity + audit log
```

- **`BannerRendererService`** (204 lines, singleton): `ENGINES` const (8); `getEngine()` = EAV
  `banner→engine ?? 'swiper'`; `getConfig()` = defaults (autoplay 5000 / transition 800 / loop /
  nav / pagination / parallax_depth / ken_burns_intensity) merged with EAV group `banner`;
  `getSlides()` = items → EAV group `slide` (layers JSON, link_url/target, background
  video/gradient, transition_in/out, ken_burns). `render()` = 6 steps: `BannerRendering` dispatch
  (mutable `addSlide()`/`overrideEngine()`) → engine resolution (unknown → swiper) → slides merge
  (empty → `BannerRenderException`) → blade render → `BannerRendered(html, engine, renderTimeMs)` →
  audit. ⚠ **`render()` has zero call sites** — the widget component calls getConfig/getSlides/
  getEngine directly, so both render events + audit + exception paths are dead in practice.
- **The 8 engines** — all Blades are 6-line wrapper includes; real markup is the hydration shell
  (`wrapper.blade.php`: `data-banner-id/-engine/-config(JSON)/-slides(JSON)/-name` + "Loading
  banner…"). `banner-loader.js` scans the DOM, lazily `import()`s the engine module, and passes
  `(el, config, slides, bannerId, eventBus)`:

| Engine | Library | Effect |
|---|---|---|
| `swiper` | Swiper 11 (+6 effect modules) | the full-featured default: nav/pagination/lazy/video/gradient slides |
| `gsap-cube` | GSAP | ≤6 cube faces rotateY (drops >6 slides) |
| `gsap-flip` | GSAP | Y-axis flip cards |
| `gsap-coverflow` | GSAP | tilt/scale/x-offset coverflow |
| `ken-burns` | none | random pan/zoom presets × intensity + crossfade |
| `three-distort` | three.js | GLSL noise-distortion crossfade shader, rAF loop, full dispose |
| `canvas-particles` | none | 3000-pixel particle dissolve |
| `svg-morph` | GSAP + flubber | path morphing (hardcoded circle paths — images ignored) |

All engines emit `slideChanged` + click interactions; all fall back to swiper-engine on missing
lib; on loader failure → `banner_engine_error` audit + plain `<img>` fallback list.

### 5.5.2 Composer + analytics pipeline

```mermaid
flowchart LR
    subgraph Composer["Livewire BannerComposer /admin/banner-composer/{id}"]
        S1[canvas drag → updateLayerPosition] --> S2[addLayer text/image/button/shape]
        S2 --> S3["save(): EavService.setMany(banner→group banner)<br/>+ per item setMany(item→group slide: layers, transitions, ken_burns)<br/>+ BannerItem::update(image, link, sort_order)"]
        S3 --> S4["AuditService.logModel('banner_composer_saved')<br/>+ dispatch('banner-saved') browser event"]
    end
    subgraph Pipeline["Browser → backend analytics"]
        P1[engine JS slide change / click] --> P2[BannerEventBus fetch keepalive<br/>X-CSRF-TOKEN]
        P2 --> P3["POST /api/banner/event/slide-changed|interaction<br/>throttle:120,1 no auth"]
        P3 --> P4["BannerEventController validate<br/>interaction_type in click,hover,swipe,cta_click<br/>user = auth(customer) ?? auth(web)"]
        P4 --> P5[BannerEventService::dispatch*]
        P5 --> P6["Event::dispatch(BannerSlideChanged|BannerInteraction)<br/>(0 listeners)"]
        P5 --> P7["AuditService.logModel → user_activity row<br/>(event=model_banner_*) + audit log channel"]
    end
    subgraph RenderAudit["Renderer path (dormant, N-1)"]
        R1[BannerRendering → addSlide/overrideEngine] --> R2[render engine blade]
        R2 --> R3[BannerRendered html/engine/renderTimeMs]
        R3 --> R4[audit banner_rendered / BannerRenderException→500]
    end
```

- **`BannerComposer`** (Livewire, `/admin/banner-composer/{bannerId}`): 3-panel UI — slide list /
  layer canvas with vanilla-JS drag + z-order / properties + engine config. Layer schema:
  `text|image|button|shape` with x/y/z/w/h + animation in/out/delay/duration/easing. `save()`
  writes EAV groups `banner` + `slide` per item + `banner_composer_saved` audit + browser
  `banner-saved` dispatch. ⚠ Layers are **write-only** — no engine renders them (design doc
  features like live iframe preview / timeline editor / presets remain unimplemented).
- **Analytics**: engine JS → `Alpine.store('bannerBus')` local pub/sub + `BannerEventBus` fetch
  (CSRF header + `keepalive`) → `POST /api/banner/event/{slide-changed|interaction}`
  (`throttle:120,1`, no auth) → `BannerEventController` validation → `BannerEventService::dispatch*`
  → `Event::dispatch` + `AuditService::logModel` → **`user_activity` rows** (morph activitable →
  Banner, ip/browser). No dedicated analytics tables, no impression tracking.
- **ER comparison**:

```mermaid
erDiagram
    LEGACY_banner ||--o{ LEGACY_banner_item : "banner_id"
    LEGACY_banner_item ||--o{ LEGACY_description : "object_type=banner_item"
    LEGACY_banner_item ||--o{ LEGACY_property : "object_type=banner_item (group=settings: slidename, transition_*)"
    LEGACY_banner ||--o{ LEGACY_property : "object_type=banner"
    LEGACY_banner ||--o{ LEGACY_object_to_store : "object_type=banner"
    LEGACY_banner_item ||--o{ LEGACY_widget : "object_type=banner_item (serialized settings incl. offsetX/offsetY)"
    LEGACY_banner {
        int banner_id PK
        varchar name
        varchar jquery_plugin "discriminator → template+JS+CSS"
        text params "DEAD (F-1)"
        date publish_date_start
        date publish_date_end
        int status
    }

    NEXT_banners ||--o{ NEXT_banner_items : "FK cascade"
    NEXT_banner_items ||--o{ NEXT_descriptions : "morph describable (language_id)"
    NEXT_banner_items ||--o{ NEXT_properties : "morph propertiable group=slide (layers JSON, link_url, transitions, ken_burns)"
    NEXT_banners ||--o{ NEXT_properties : "morph propertiable group=banner (engine, autoplay…)"
    NEXT_banners ||--o{ NEXT_store_assignments : "morph assignable"
    NEXT_banner_items ||--o{ NEXT_widgets : "object_type=banner_item (WidgetService tree)"
    NEXT_banners {
        bigint id PK
        varchar name
        varchar jquery_plugin "legacy discriminator (5-option select)"
        json params
        date publish_date_start
        date publish_date_end
        bool status
    }
    NEXT_banner_items {
        bigint id PK
        bigint banner_id FK
        varchar image
        varchar link
        int sort_order
        bool status
    }
```

## 5.6 Legacy ↔ Next Mapping (headline)

| Legacy | Next |
|---|---|
| 33 jQuery templates + `jquery_plugin` column | 8 engines + EAV `banner→engine` + wrapper data-attributes |
| `window.ntPlugins` / `loadNTPlugins()` registry | `banner-loader.js` lazy dynamic import per engine |
| Per-item **widgets** (offsetX/offsetY overlays; only 2 templates rendered them) | per-slide **layers** JSON (stored, never rendered) |
| No analytics | full browser→server event pipeline (throttled, `user_activity`-backed) |
| Model self-observers | `Banner*` events + `Auditable` |
| Missing-template debug `exit()` | `BannerRenderException` |
| Scheduling / store scoping / localized descriptions / EAV settings | **all preserved** |
| `carousel()` JSON endpoint | dropped |

## 5.7 Known Issues Register

**Legacy (15, full list in research):** dead `params` column; 6 templates with broken/missing
ntPlugins selectors (slick/gridrotator/cslider/fancybox never init); evolution hardcoded widget id;
hhe-08 missing `plugin:` key; no default module template → debug exit; async render wipes slider
assets; orphaned per-item widgets on delete; item cache never invalidated; saveItem id churn.

**Next (15, full list in research):** `render()` zero call sites (events/audit dead);
`resolveTemplate()` `data()` bug → always swiper wrapper; events dispatch-only / not broadcastable;
layers write-only; cube drops >6 slides; `direction` always 'next'; svg-morph hardcoded paths;
vestigial `public/sliders` enqueue; `config banner_plugins` never read (5-option list duplicated
inline, 8 engines in neither); no impressions + unauthenticated throttled write surface; composer
saves `banner_items.link` but renderer reads EAV `slide.link_url` (links ignored); no explicit
store scoping in widget query.

---

Next: [Chapter 6 — Menus](06-menus.md) · [Back to index](README.md)
