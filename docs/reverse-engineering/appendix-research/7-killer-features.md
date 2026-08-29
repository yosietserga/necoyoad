# Necoyoad — Killer Features Catalog (Research 7)

Research agent: Explore-features. Research-only — no repo modifications.
Every feature below was **verified in source** at `/home/z/necoyoad` (paths are real).
Cross-referenced with prior research (2-architecture, 3a1-events-hooks, 3a2-widgets, 3a3-banners, 3a4-menus, 3b1-templates, 3b2-eav, 3b3-multistore-dto, 3b4-caching-rendering) — mined for bullets, re-verified where cited.
Legend: `[legacy]` = PHP app at repo root · `[next]` = `necoyoad-next/` Laravel rewrite · `[both]` = exists on both sides.

---

## A. Storefront Catalog

**1. Multi-view product catalog (widget-driven)**
Products render through a family of composable display widgets — overview, list, price, title, images, model, stock, tabs, attributes — each independently placeable on any page position.
Where: `app/shop/controller/module/product_{overview,list,price,title,images,model,stock,tabs,attributes,description,tags}.php`, `app/shop/controller/store/product.php`
Tag: [legacy]

**2. Product Quick View modal**
AJAX endpoint returns a dedicated quick-view product template (own configurable default view `default_view_product_quickview`) for lightbox-style browsing without leaving the listing.
Where: `app/shop/controller/store/product.php:396` (`quickViewJson()`), `app/shop/view/theme/choroni/store/product_quickview.tpl`
Tag: [legacy]

**3. Related products**
Products link to cross-sell companions served as JSON for dynamic injection on the product page.
Where: `app/shop/controller/store/product.php:353` (`relatedJson()`), `app/shop/model/store/product.php` (`getProductRelated`), table `nts8sd4fd_product_related`
Tag: [legacy]

**4. Product comparison**
Session-based compare list (`products_to_compare`) with add/remove endpoints and a header link ("Comparar Productos").
Where: `app/shop/controller/store/product.php:298-336` (`addProductToCompare` / `removeProductToCompare`), `app/shop/language/spanish/common/header.php:13`
Tag: [legacy]

**5. Faceted search across 13 dimensions**
Search URLs are parsed into criteria for categories, zones, sellers, manufacturers, stores, prices, shipping, payments, status, stock status, dates and attributes (`generateCriterias()`).
Where: `app/shop/controller/store/search.php:67-84`, `app/shop/model/store/search.php`
Tag: [legacy]

**6. SEO-friendly search URLs (`/buscar/<query>`)**
Search runs on pretty URLs in both Spanish and English (`buscar/` and `search/` prefixes), with sort/order/limit preserved as query params.
Where: `app/shop/controller/common/seo_url.php:18-21`, `app/shop/controller/store/search.php:22-28`
Tag: [legacy]

**7. Search-term analytics**
Every executed search is logged with browser, OS, IP, full `$_SERVER`/session/request dumps into the `search` table for the admin visits report.
Where: `app/shop/model/store/search.php` (`add()`), `app/admin/controller/report/visits.php`, table `nts8sd4fd_search`
Tag: [legacy]

**8. Manufacturer / brand directory**
Brand listing page plus per-manufacturer product pages with SEO aliases (`fabricantes` / `manufacturers`).
Where: `app/shop/controller/store/manufacturer.php`, `app/shop/controller/common/seo_url.php:99-100`
Tag: [legacy]

**9. Special offers page**
Dedicated specials page (`special` / `ofertas` alias) fed by the `product_special` price table.
Where: `app/shop/controller/store/special.php`, table `nts8sd4fd_product_special`
Tag: [legacy]

**10. Product attribute dictionary with form-field types**
`product_attribute` rows define typed attributes (label, type, regex pattern, default, required, group, per-store) usable both as filters and structured data.
Where: `app/admin/controller/store/attribute.php`, table `nts8sd4fd_product_attribute` (cols: type/pattern/default/required)
Tag: [legacy]

**11. Attribute-group filter widget**
A widget that renders a chosen attribute group as a search filter panel.
Where: `app/shop/controller/module/product_filter_attributes.php`
Tag: [legacy]

**12. Product tags**
Free-form tag display per product backed by the `product_tags` table and a `product_tags` widget.
Where: `app/shop/controller/module/product_tags.php`, table `nts8sd4fd_product_tags`
Tag: [legacy]

**13. Stock status & inventory display**
Stock status labels come from a localisation table; quantity/subtract rules drive cart validation and `product_stock` widget display.
Where: `app/shop/controller/module/product_stock.php`, `app/admin/controller/localisation/stock_status.php`, tables `nts8sd4fd_stock_status`-backed, `product.quantity/subtract`
Tag: [legacy]

**14. Reviews with star ratings + likes/dislikes**
Customers rate products and posts; reviews expose like/dislike counters with dedicated JSON endpoints.
Where: `app/shop/controller/store/review.php:100-135` (`likeReview`, `dislikeReview`, `write`), tables `nts8sd4fd_review`, `nts8sd4fd_review_likes`
Tag: [legacy]

**15. Threaded comments & reviews**
Reviews and comments are polymorphic (`object_type` + `parent_id`) so they thread under products, posts and pages alike.
Where: table `nts8sd4fd_review` (parent_id/object_type), `app/shop/controller/module/comments.php`, `app/shop/controller/account/review.php`
Tag: [legacy]

**16. Product options with price prefixes**
Selectable product options (size, color…) carry price modifiers (prefix `+/-`) that flow through cart and order snapshot.
Where: tables `nts8sd4fd_product_option`, `product_option_value`, `order_option`; `app/shop/controller/checkout/confirm.php:141-150`
Tag: [legacy]

---

## B. Cart & Checkout

**17. Persistent shopping cart**
Session cart syncs into `customer.cart` (serialized) so logged-in customers keep their basket across devices; totals are stock- and tax-aware.
Where: `system/library/cart.php`, `system/library/customer.php` (cart glue), `nts8sd4fd_customer.cart`
Tag: [legacy]

**18. AJAX cart updates with live totals**
Quantity refresh/delete endpoints recompute the whole order-totals pipeline and return JSON rows for in-place rendering.
Where: `app/shop/controller/checkout/cart.php:54-120` (`refresh`, `delete`, `updateCart`)
Tag: [legacy]

**19. Pluggable order-totals pipeline**
Seven sortable total extensions — sub_total, shipping, coupon, tax, handling, low_order_fee, total — each an installable extension with its own model and sort order.
Where: `app/shop/controller/total/*`, `app/shop/model/total/*`, `app/admin/controller/total/*`, `app/shop/model/checkout/extension.php`
Tag: [legacy]

**20. Coupons with restrictions & usage history**
Coupon engine supports per-product and per-category restrictions plus a redemption history table.
Where: `app/shop/controller/total/coupon.php`, `app/shop/model/checkout/coupon.php`, `app/admin/controller/sale/coupon.php` (`category()`, `products()`), tables `nts8sd4fd_coupon`, `coupon_category`, `coupon_product`, `coupon_history`
Tag: [legacy]

**21. Reward points redemption**
Customers accumulate points and can apply them at checkout with server-side validation against balance and cart maximum.
Where: `app/shop/controller/total/reward.php` (`reward()` validation: error_points/error_maximum)
Tag: [legacy]

**22. Gift vouchers**
Voucher code entry at checkout validates against the voucher store and stores the code in session.
Where: `app/shop/controller/total/voucher.php`
Tag: [legacy]

**23. Out-of-stock checkout toggle & store/catalog mode gating**
`config_stock_checkout` permits ordering out-of-stock items; `config_store_mode` can turn the whole shop into a catalog (checkout routes redirect home).
Where: `app/shop/controller/checkout/confirm.php:17`, `app/shop/controller/checkout/cart.php:12-14`
Tag: [legacy]

**24. Five built-in payment methods**
Bank transfer, cheque, cash on delivery, debit and free checkout ship as installable payment extensions with admin settings.
Where: `app/shop/controller/payment/{bank_transfer,cheque,cod,debit,free_checkout}.php`, `app/admin/controller/payment/*`
Tag: [legacy]

**25. PayU payment gateway (WebCheckout)**
OpenPayU SDK v2 bundled, with WebCheckout form, redirect and standard templates in the theme.
Where: `system/library/payu/openpayu.php` + `OpenPayU/v2/{Order,Refund}.php`, `app/shop/view/theme/choroni/payment/{payu,payu_webcheckout,payu_redirect}.tpl`
Tag: [legacy]

**26. Bank-deposit payment evidence**
Customers report bank transfers (transaction number, bank, amount, account) that admins verify against configurable bank accounts; full history in the account area.
Where: `app/shop/controller/account/payment.php`, `app/admin/controller/sale/{payment,bank,bank_account}.php`, tables `nts8sd4fd_order_payment`, `bank`, `bank_account`
Tag: [legacy]

**27. Five shipping methods incl. weight-based rates**
Flat, free, per-item, weight-based and store-pickup shipping extensions, all configurable per store.
Where: `app/shop/model/shipping/{free,weight,flat,item,pickup}.php`, `app/admin/controller/shipping/*`
Tag: [legacy]

**28. Geo-zone tax engine**
Tax class/rate resolution based on shipping country+zone mapped through geo zones (store default fallback).
Where: `system/library/tax.php`, `app/shop/model/total/tax.php`, tables `nts8sd4fd_tax_class`, `tax_rate`, `zone_to_geo_zone`
Tag: [legacy]

**29. Fully denormalized order snapshot**
Checkout writes a 49-column `order` row (store snapshot, both addresses, currency+rate, coupon, IP) plus order_product/order_total/order_option rows — orders survive later catalog edits.
Where: `app/shop/controller/checkout/confirm.php:67-190`, `app/shop/model/checkout/order.php` (`create`, `confirm`), table `nts8sd4fd_order`
Tag: [legacy]

**30. JSON checkout API**
`?resp=json` on confirm returns `{order_id}` for AJAX/SPA-style checkouts.
Where: `app/shop/controller/checkout/confirm.php:194-198`
Tag: [legacy]

**31. Customer balance wallet in checkout context**
Multi-currency `balance` table backs a store-credit wallet surfaced in the account area (statement + register).
Where: `app/shop/controller/account/balance.php` (`report`, `register`), `app/admin/controller/sale/balance.php`, table `nts8sd4fd_balance`
Tag: [legacy]

---

## C. Customer Account

**32. Registration with email activation**
Accounts carry activation codes and a `complete` flag; activation flow with its own language pack.
Where: `app/shop/controller/account/register.php`, `app/shop/language/spanish/account/complete_activation.php`, `nts8sd4fd_customer.activation_code/complete`
Tag: [legacy]

**33. Facebook OAuth login**
Full Facebook SDK v5 login flow (email/profile scopes, token persistence, re-request handling) creating/linking customer accounts.
Where: `app/shop/controller/api/facebook.php`, `system/library/facebook/`
Tag: [legacy]

**34. Google OAuth login + Contacts import**
Google client with m8 contacts, userinfo and plus scopes; `invitefriends()` pulls up to 1000 contacts for referrals.
Where: `app/shop/controller/api/google.php:78-87`, `system/library/google/`
Tag: [legacy]

**35. Microsoft Live (Windows Live) OAuth login**
login.live.com OAuth2 handshake with promote/newsletter hooks and contact invites.
Where: `app/shop/controller/api/live.php` (`index`, `invitefriends`, `promote`, `login`)
Tag: [legacy]

**36. MercadoLibre OAuth login**
MercadoLibre SDK integration storing oauth id/token/refresh/expire on the customer record (marketplace account linking).
Where: `app/shop/controller/api/meli.php`, `system/library/meli/meli.php`
Tag: [legacy]

**37. Invite-a-friend widget**
Front-office referral widget pre-wired to Google/Live client IDs for OAuth-based contact importing.
Where: `app/shop/controller/module/invitefriends.php`, `app/admin/controller/module/invitefriends/`
Tag: [legacy]

**38. Referral attribution & tracking**
`referenced_by` builds a referral tree; `ref_cid`/`ref_email` session keys attribute visits and auto-create marketing contacts from referred visitors.
Where: `system/library/customer.php` (`setRefByCustomer`/`setRefCustomer`), `system/library/tracker.php:28-60`, `nts8sd4fd_customer.referenced_by`
Tag: [legacy]

**39. Customer messaging inbox**
Full private-messaging center: inbox, sent box, read receipts, reply, attachments (upload) and customer picker.
Where: `app/shop/controller/account/message.php` (`sent`, `read`, `reply`, `send`, `upload`, `getCustomers`), `app/admin/controller/marketing/message.php`
Tag: [legacy]

**40. Order history with per-zone filtering**
Paginated order history with sortable, filterable (zone, status) views.
Where: `app/shop/controller/account/history.php` (`zone()`), `app/shop/controller/account/order.php`
Tag: [legacy]

**41. Customer invoice access**
Account-side invoice view mirroring admin invoice formatting.
Where: `app/shop/controller/account/invoice.php`
Tag: [legacy]

**42. Downloads center**
Purchased downloadable products remain downloadable from the account (order_download table gates access).
Where: `app/shop/controller/account/download.php` (`download()`), tables `nts8sd4fd_download`, `order_download`, `product_to_download`
Tag: [legacy]

**43. Address book**
Multi-address book with default address, RIF/company fields (Venezuelan tax id) and formatted address templates.
Where: `app/shop/controller/account/address.php`, table `nts8sd4fd_address`
Tag: [legacy]

**44. Newsletter & birthday preferences**
Customer-managed newsletter subscription and birthday-congrats opt-in (`congrats` flag) feeding the birthday cron.
Where: `app/shop/controller/account/newsletter.php`, `nts8sd4fd_customer.newsletter/birthday/congrats`
Tag: [legacy]

**45. Personal review history**
Customers manage/read all their reviews from the account area.
Where: `app/shop/controller/account/review.php`
Tag: [legacy]

---

## D. CMS & Content

**46. Unified post/page engine with per-post template override**
Posts and pages share one engine (`post_type` discriminator) and each row carries its own `template` column for layout selection.
Where: `app/shop/controller/content/{post,page}.php`, table `nts8sd4fd_post` (post_type + template)
Tag: [legacy]

**47. Blog with post categories**
Category tree for editorial content (`content/category/all` = `/blog` alias) with category widgets (title/image/description/overview/list).
Where: `app/shop/controller/content/category.php`, `app/shop/controller/module/post_category_*.php`
Tag: [legacy]

**48. Scheduled publishing windows**
Posts/pages and banners publish between `date_publish_start` / `date_publish_end`.
Where: tables `nts8sd4fd_post`, `banner` (publish windows), `app/shop/model/content/banner.php:26-56` (storefront filter)
Tag: [legacy]

**49. Contact form widget with configurable recipients**
Drop-in contact form widget module with admin settings and per-store forms.
Where: `app/shop/controller/module/contact_form.php`, `app/admin/controller/module/contact_form/`, `necoyoad-next/app/View/Components/Widgets/ContactForm.php`
Tag: [both]

**50. HTML sitemap page**
`/sitemap` alias renders a full site map of catalog + content.
Where: `app/shop/controller/page/sitemap.php`, `app/shop/controller/common/seo_url.php:107`
Tag: [legacy]

**51. Facebook comments plugin**
Facebook comments widget module for posts/pages.
Where: `app/shop/controller/module/facebook_comments.php`
Tag: [legacy]

**52. Native comment system**
Threaded site comments module with moderation hooks.
Where: `app/shop/controller/module/comments.php`, `app/admin/controller/module/comments/`
Tag: [legacy]

**53. Embeddable pages & landing-page widget targeting**
`page_embed.tpl` for iframe/embed rendering; `widget_landing_page` maps widget instances to specific routes (landing pages) beyond global positions.
Where: `app/shop/view/theme/choroni/content/page_embed.tpl`, table `nts8sd4fd_widget_landing_page`, `landing_page` session key in every controller
Tag: [legacy]

**54. Featured-content block system**
Home/landing pages compose from `featuredContent` / `featuredFooter` widget regions distinct from main content.
Where: `app/shop/controller/common/home.php`, `system/helper/widgets.php` (position criteria)
Tag: [legacy]

---

## E. SEO

**55. SEO URL layer with keyword map**
All catalog/CMS entities resolve through `url_alias` (query→keyword); `Url::createUrl` rewrites links when `config_seo_url` is on.
Where: `app/shop/controller/common/seo_url.php`, `system/library/url.php`, table `nts8sd4fd_url_alias`
Tag: [legacy]

**56. Bilingual route aliases**
Every magic route has Spanish and English forms (`buscar|search`, `productos|products`, `categorias|categories`, `paginas|pages`, `articulos|posts`, `ofertas|special`, `fabricantes|manufacturers`).
Where: `app/shop/controller/common/seo_url.php:93-139`
Tag: [legacy]

**57. Customer vanity profile URLs**
SEO layer derives a slug from the logged-in customer's name and routes `/<nombre>/pedidos`, `/<nombre>/mensajes`, `/<nombre>/pagos`, `/<nombre>/comentarios`.
Where: `app/shop/controller/common/seo_url.php:23-38,123-136`
Tag: [legacy]

**58. Auto slug generator with uniqueness & reserved words**
Admin `slug()` endpoint (and the module scaffold) transliterates accents, avoids reserved words and loops until `url_alias` uniqueness per store is satisfied.
Where: `app/admin/controller/common/home.php` (`slug`), `app/modules/mymodule/app/shop/controller/home.php` (`slug()`)
Tag: [legacy]

**59. Per-language SEO metadata**
Title, SEO title (60), meta description (160) and meta keywords per language per entity via the polymorphic `description` DTO; the next stack reproduces these as Filament fields.
Where: table `nts8sd4fd_description` (seo_title/meta_description/meta_keywords), `necoyoad-next/app/Filament/Resources/NecoyoadResource.php` (SEO fields)
Tag: [both]

---

## F. Marketing & Campaigns

**60. Campaign composer with scheduling & repeat**
Campaigns bind a newsletter, sender identity, start/end dates and repeat interval; sending creates a cron task + per-contact queue entries.
Where: `app/admin/controller/marketing/campaign.php:771-841` (`send()`), tables `nts8sd4fd_campaign`, `task`, `task_queue`
Tag: [both]

**61. Per-contact personalized email bodies**
Template tokens `{%contact_id%}`, `{%campaign_id%}` are substituted per recipient and each personalized HTML body is cached individually.
Where: `app/admin/controller/marketing/campaign.php:807-836`; next: `necoyoad-next/app/Jobs/SendCampaignEmail.php` (`personalise()` with `{%fullname%}`, `{%email%}`, `{%store_name%}`…)
Tag: [both]

**62. Tracked campaign links with click stats**
Every campaign URL is registered in `campaign_link` with per-contact substitution; clicks land in `campaign_link_stat` (next: nonce-based `/track/click/{nonce}`).
Where: `app/admin/model/marketing/campaign.php` (`addLink`, `getLinks`), tables `campaign_link`, `campaign_link_stat`; next: `necoyoad-next/app/Http/Controllers/StorefrontController.php:253`
Tag: [both]

**63. Email open tracking**
`trace_email` flag embeds per-contact tracking (next: 1×1 PNG pixel route `/track/open/{campaign}/{contact}` writing `campaign_stats`).
Where: `app/admin/model/marketing/campaign.php:42-51`; next: `necoyoad-next/routes/web.php` (`marketing.track.open`), `StorefrontController::trackOpen`
Tag: [both]

**64. Multiple SMTP mail servers per campaign + connection tester**
Admin manages a pool of mail servers (host/user/password/port) and can test SMTP connectivity before sending; each campaign picks its server.
Where: `app/admin/controller/marketing/mailserver.php:298` (`testConnection()`), `system/library/email/` (PHPMailer fork + smtp/pop3)
Tag: [legacy]

**65. Contact lists & segmentation**
Named contact lists with m:n membership (`contact_to_list`) power campaign audiences.
Where: `app/admin/controller/marketing/list.php`, tables `nts8sd4fd_contact_list`, `contact_to_list`; next: `necoyoad-next/app/Filament/Resources/ContactListResource.php`
Tag: [both]

**66. Contact import wizard**
Multi-step CSV import wizard (upload → map → process) for contacts.
Where: `app/admin/controller/marketing/contact.php:648-787` (`import`, `importwizard`, `importprocess`)
Tag: [legacy]

**67. Contact & vCard export**
Export filtered contacts (email campaigns) and vCard files.
Where: `app/admin/controller/marketing/contact.php:88` (`exportThis`), `app/admin/controller/tool/vcard.php`
Tag: [legacy]

**68. Newsletter editor with premade templates**
Dual HTML/text bodies, copy/activate/deactivate actions and a library of premade templates read from disk.
Where: `app/admin/controller/marketing/newsletter.php:782` (`readPremadeTemplate`), tables `nts8sd4fd_newsletter`
Tag: [both]

**69. Drag-and-drop product picker for newsletters**
Campaign builder renders category products as draggable cards (jQuery UI `draggable()`) to drop into the newsletter canvas.
Where: `app/admin/controller/marketing/campaign.php:733-769` (`products()`), `app/admin/controller/marketing/newsletter.php:658` (`products()`)
Tag: [legacy]

**70. Automated birthday email campaigns**
Birthday customers (with `congrats` opt-in) get scheduled greeting emails — legacy via `CronBirthday` task type, next via daily 09:00 scheduler command + queue job.
Where: `system/cron/api/birthday.php`, `app/admin/controller/sale/cumpleanos.php`, `web/admin/email_templates/01_defaults/happy-birthday/`; next: `necoyoad-next/app/Console/Commands/SendBirthdayEmails.php`, `app/Jobs/SendBirthdayEmail.php`
Tag: [both]

**71. Promoter — visit-triggered follow-ups**
Cron `CronPromoter` prepares newsletter sends based on visitor activity (visit-based marketing automation).
Where: `system/cron/api/promoter.php`
Tag: [legacy]

**72. In-app notification system**
Per-store/per-customer/per-object notifications table for order/newsletter events.
Where: table `nts8sd4fd_notification`
Tag: [legacy]

**73. Bounce processing & auto-unsubscribe**
Hourly command marks bounced contacts inactive (structural stub wired to scheduler; legacy pop3 bounce rules ship in the PHPMailer fork).
Where: `necoyoad-next/app/Console/Commands/ProcessBounces.php`, `routes/console.php` (hourly), `system/library/email/` (bounce rules)
Tag: [both]

**74. CAN-SPAM/GDPR unsubscribe compliance**
Next stack: per-contact unsubscribe tokens (`/unsubscribe/{token}`), one-click `List-Unsubscribe` + `List-Unsubscribe-Post` SMTP headers.
Where: `necoyoad-next/app/Mail/CampaignEmail.php:41-47`, `app/Http/Controllers/StorefrontController.php:276` (`unsubscribe`)
Tag: [next]

---

## G. Design & Theming

**75. Visual theme editor (no-code CSS)**
Admin edits selector/property/value rows stored in `theme_style` and compiled into `custom-<id>-<tpl>.css` per template, with visual editors for colors, fonts, borders, radius, shadows, margins/paddings, dimensions and backgrounds.
Where: `app/admin/controller/style/theme.php:136` (`save()`), `app/shop/view/theme/choroni/common/admin/admin-theme-*.tpl`, table `nts8sd4fd_theme_style`
Tag: [legacy]

**76. Raw template file editor**
In-admin code editor reading/writing theme template files (with security caveats documented in prior research).
Where: `app/admin/controller/style/editor.php` (`file`, `save`), `web/admin/js/frontend/theme_editor.js`
Tag: [both]

**77. Per-entity default view overrides**
Configurable `default_view_*` settings choose the template per context (product, quickview, checkout/cart, account pages…) with per-entity EAV `property('style','view')` overriding further.
Where: `app/admin/controller/style/views.php`, e.g. `app/shop/controller/checkout/cart.php:44`
Tag: [both]

**78. Theme/template marketplace scaffolding**
Template catalog with install/uninstall/buy/download actions and a `template` table (version, colors, cols, scheme, for_nt_version compatibility).
Where: `app/admin/controller/style/template.php:51-55`, table `nts8sd4fd_template`
Tag: [legacy]

**79. Device-aware theme switching (mobile / tablet / Facebook)**
Browser detection swaps `config_template` for dedicated mobile/tablet/Facebook themes or auto-redirects to a separate mobile URL.
Where: `app/shop/map.php:199-233`, `system/library/browser.php` (`isMobile/isTablet/isFacebook`)
Tag: [legacy]

**80. Live theme preview via `?template=`**
Any installed theme can be previewed by switching the template on the fly.
Where: `app/shop/map.php:254-257`
Tag: [legacy]

**81. Scheduled & duplicated themes**
Theme instances carry publish windows, defaults, copy() duplication and sortable ordering for campaign skins.
Where: `app/admin/controller/style/theme.php` (`copy`, `sortable`, `activate`), table `nts8sd4fd_theme` (date_publish_start/end)
Tag: [legacy]

---

## H. Widgets & Layout

**82. Visual widget layout manager (rows/columns)**
Admin composes pages from widget rows and columns with AJAX save/sort endpoints (`saveRow`, `saveCol`, `sortable`, `sortrow`, `sortCol`, `deleteRow`, `deleteColumn`); rows/cols stored as EAV entries in legacy and real tables in next.
Where: `app/admin/controller/style/widget.php:203-346`; next: `necoyoad-next/app/Filament/Resources/WidgetRowResource.php`
Tag: [both]

**83. 67-widget module library**
69 module controllers ship, spanning catalog (product_*), blog (post_*), category_*, page_*, commerce (shopping_cart_box, shopping_cart_checkout), forms (login_form, register_form, contact_form), social (fblike, facebook_comments), media (image, lightbox, banner), utilities (search, links, redirect, separator, plaintext, richtext, google_maps, google_analytics, store_logo/phone/title, language/currency selectors, rooms).
Where: `app/shop/controller/module/*` (69 files)
Tag: [legacy]

**84. Seven widget positions**
header, main, featuredContent, featuredFooter, column_left, column_right, footer — identical in legacy and next (`widget_positions` config).
Where: `app/shop/controller/common/*.php` (`loadWidgets` calls), `necoyoad-next/config/necoyoad.php`
Tag: [both]

**85. Context-aware widget targeting**
Widget visibility filters by landing page/route, object type+id (page-specific widgets), device and store.
Where: `system/helper/widgets.php` (`NecoWidget` criteria engine), `session object_type/object_id/landing_page` set in every storefront controller
Tag: [both]

**86. Per-widget animation effects (animate.css catalog)**
Widget settings offer entrance transitions from an animate.css catalog (`transition_effects`).
Where: `app/admin/controller/module/widgetcontroller.php:307`, `app/admin/controller/module/widget_common.php:184`
Tag: [legacy]

**87. Async widget rendering**
Widgets render out-of-band: legacy `?cve` async JSON with widget-head contracts; next `GET /widget/async/{name}` with `X-Widget-Styles/Scripts` headers.
Where: `app/shop/controller/module/modulecontroller.php` (async mode), `necoyoad-next/app/Http/Controllers/WidgetController.php`
Tag: [both]

**88. Module install/uninstall lifecycle with settings forms**
Every widget module has admin `widget.php` (settings form), `install.php`, `uninstall.php` and some `config.php`/`plugin.php` — a full extension lifecycle.
Where: `app/admin/controller/module/<name>/*`, `app/admin/controller/extension/module.php`
Tag: [legacy]

**89. Module scaffold for custom extensions**
`app/modules/mymodule` demonstrates third-party apps with own controllers/views, JSON health check, admin auth/ACL hooks and SEO slug service, routed via `modules/<m>/...` action remapping.
Where: `app/modules/mymodule/**`, `system/engine/action.php` (module remap)
Tag: [legacy]

---

## I. Banners

**90. 33 banner slider templates (multi-engine)**
Nivo (2 versions), Slick, Camera (canvas), Elastic Image Slideshow, Slider Evolution, custom layer-slider, Parallax Content Slider, Slicebox 3D cuboids, Jssor vertical-thumb navigator, Owl Carousel, fancyBox gallery/grid, gridrotator, horizontal/horizontal-parallax/vertical CSS layouts and 15 hover-effect variants.
Where: `app/shop/view/theme/choroni/banner/*.tpl` (33 files), `web/assets/js/sliders/*` (14 JS engines)
Tag: [legacy]

**91. Per-slide widget composition on banners**
Banner slides can embed widget tokens (`{%widget%}`) — per-item widget trees resolved during render (horizontal.tpl, layer-slider).
Where: `app/shop/view/theme/choroni/banner/horizontal.tpl:28-31`, `layer-slider-v0.0.1.tpl:11-15`, `system/engine/controller.php:356-365`
Tag: [legacy]

**92. Banner scheduling & store scoping**
Banners publish between dates, per store, with per-item status/sort.
Where: table `nts8sd4fd_banner` (jquery_plugin, publish windows), `app/shop/model/content/banner.php`
Tag: [both]

**93. Visual slide composer (legacy)**
Admin slide editor with image manager dialogs, per-slide links and inline save/delete item endpoints (`saveItem`, `deleteItem`).
Where: `app/admin/controller/content/banner.php:238-252`, `app/admin/view/templates/default/content/banner_form.tpl`
Tag: [legacy]

**94. Modern drag-drop banner composer with XYZ layers**
Livewire `BannerComposer` edits slides and layer stacks (add/select/delete slides, typed layers) in the admin.
Where: `necoyoad-next/app/Livewire/Admin/BannerComposer.php`, `resources/views/livewire/admin/banner-composer.blade.php`
Tag: [next]

**95. 8 modern banner engines (WebGL/GSAP/Canvas/SVG)**
Swiper, GSAP 3D Cube, GSAP Coverflow, GSAP Flip, Three.js WebGL distortion, Canvas 2D particle dissolve, SVG path morph, Ken Burns cinematic — each a Blade template + JS engine module with EAV config (autoplay, parallax depth, ken-burns intensity).
Where: `necoyoad-next/app/Services/BannerRendererService.php` (ENGINES), `resources/views/components/banners/engines/*`, `resources/js/banners/engines/*`
Tag: [next]

**96. Event-driven banner render pipeline + interaction analytics**
`BannerRendering`/`BannerRendered` events bracket each render; `BannerSlideChanged`/`BannerInteraction` events feed an analytics endpoint (throttled 120/min) recording slide views, direction and link clicks.
Where: `necoyoad-next/app/Events/*`, `app/Services/BannerEventService.php`, `app/Http/Controllers/BannerEventController.php`, `resources/js/banners/banner-loader.js`
Tag: [next]

---

## J. Multi-store

**97. Store CRUD + per-store app generator**
Creating a store with `create_app` copies css/js/theme asset trees into `web/<folder>/`, generates the store's `config.php` from `system/config/config_shared.txt` placeholders and writes a mini front-controller from `index.txt`.
Where: `app/admin/controller/store/store.php:840-916` (`createStandardApp`, `createPath`, `copyFiles`), `system/config/config_shared.txt`, `system/config/index.txt`
Tag: [legacy]

**98. Subdomain/folder tenant resolution**
The web entry resolves the tenant by subdomain (`<store>.domain`) or first path segment against `store.folder`; the SEO layer strips store folders from routes.
Where: `web/index.php`, `app/shop/controller/common/seo_url.php:40-45`, table `nts8sd4fd_store.folder`
Tag: [both]

**99. Per-store settings & data scoping**
Every `config_*` setting is stored per store_id; products, categories, posts, banners, menus, widgets and themes scope through `object_to_store` (legacy) / `store_assignments` (next).
Where: table `nts8sd4fd_setting` (store_id), `nts8sd4fd_object_to_store`, `system/engine/model.php` (store API); next: `necoyoad-next/app/Traits/HasStoreAssignment.php`
Tag: [both]

**100. Store-assignment pickers for every entity**
Store edit screen exposes per-entity assignment browsers: products, categories, manufacturers, pages, posts, post categories, banners, downloads, coupons, bank accounts, customers, menus.
Where: `app/admin/controller/store/store.php:939-1473` (`products()`, `categories()`, … `menus()`)
Tag: [legacy]

**101. Dedicated mobile storefront app (`m.` namespace)**
`app/m` is a separate entry (store id 9, `m.<domain>` URLs) reusing shop controllers with a mobile theme — a native-like mobile mirror.
Where: `app/m/config.php`, `web/m/index.php`, `web/assets/theme/mobile/`
Tag: [legacy]

**102. 4-strategy store resolution in next stack**
Exact-domain match, `?store_id=`, subdomain match on `stores.folder`, path-segment match, then default-store fallback — cached per request.
Where: `necoyoad-next/app/Services/StoreContext.php:32-89`, `app/Http/Middleware/ResolveStoreContext.php`
Tag: [next]

**103. Multi-store admin (Filament)**
Stores managed as a first-class Filament resource with domain/folder/status editing for the Laravel rewrite.
Where: `necoyoad-next/app/Filament/Resources/StoreResource.php`
Tag: [next]

---

## K. Localisation

**104. Polymorphic multi-language content (description DTO)**
One `description` table serves every entity (product/category/post/page/banner/menu link…) with per-language title/rich body/SEO fields; queries LEFT-JOIN and merge.
Where: table `nts8sd4fd_description`, `system/engine/model.php` (description API); next: `necoyoad-next/app/Models/Description.php`, `app/Traits/HasDescriptions.php`
Tag: [both]

**105. Language & currency selector widgets**
Front-office switcher widgets for language and currency (session-driven conversion/formatting).
Where: `app/shop/controller/module/language_selector.php`, `app/shop/controller/module/currency_selector.php`, `system/library/currency.php`
Tag: [legacy]

**106. Auto-updating exchange rates**
Cron-pull of Yahoo Finance CSV updates all currency values when `config_currency_auto` is enabled.
Where: `app/admin/model/localisation/currency.php:54+` (`updateAll()` via `download.finance.yahoo.com`)
Tag: [legacy]

**107. Complete localisation registry**
Admin CRUD for languages, currencies, countries, zones, geo zones, tax classes, order statuses, payment statuses, stock statuses, weight classes, length classes (11 controllers).
Where: `app/admin/controller/localisation/*` (11 files), `app/shop/model/localisation/packaging.php`
Tag: [legacy]

**108. Spanish-first validation engine**
Built-in `Validar` validator with Spanish error messages and regex patterns for the storefront forms.
Where: `system/library/validar.php`
Tag: [legacy]

**109. Language context resolution (next)**
Middleware resolves request language (context cascade) and shares it with views/EAV in the rewrite.
Where: `necoyoad-next/app/Services/LanguageContext.php`, `app/Http/Middleware/ResolveLanguageContext.php`
Tag: [next]

---

## L. Admin Back-office

**110. Admin dashboard widget system**
Draggable dashboard widgets: order stats (last sales/orders/visits), server status (via cPanel), update checker — each with its own controller.
Where: `app/admin/controller/widgets/{order_stats,server_status,update,order}.php`, `app/admin/view/templates/default/widget/*`
Tag: [legacy]

**111. Highcharts analytics (product/order/customer charts)**
Dashboard charts for sales, product views and customer growth using Highcharts.
Where: `app/admin/controller/chart/{order,product,customer}.php`, `web/admin/js/vendor/highcharts/`
Tag: [legacy]

**112. Order management with invoice generation & history**
Full order lifecycle: edit, delete, per-order history with notify flag, invoice rendering with address-format templating, customer search helper.
Where: `app/admin/controller/sale/order.php` (`invoice`, `history`, `generate`, `searchCustomer`, `eliminar`)
Tag: [legacy]

**113. PDF engine (TCPDF fork)**
`ntsPDF` extends TCPDF for invoices/receipts/catalog PDFs with custom footers.
Where: `system/library/ntspdf.php`, `system/library/tcpdf/`
Tag: [legacy]

**114. Barcode generation — Code39 + multi-type QR**
Code39 PNG barcodes and QR codes for URLs, contacts (vCard), WiFi, geo, SMS, phone, email, bookmarks, text (via Google Chart API).
Where: `system/library/Barcode39.php`, `system/library/BarcodeQR.php:33-161`
Tag: [legacy]

**115. CSV & Excel import/export tools**
Bulk data import/export in CSV and Excel formats for catalog/entities.
Where: `app/admin/controller/tool/csv.php`, `app/admin/controller/tool/excel.php`
Tag: [legacy]

**116. Database backup & restore**
One-click DB dumps to `backups/` and restore from admin.
Where: `app/admin/controller/tool/{backup,restore}.php`, `system/library/backup.php`
Tag: [legacy]

**117. Platform self-updater**
Update library downloads and installs platform patches (xhttp + pclzip); admin update tool + update-check dashboard widget that can register its own cron.
Where: `app/admin/controller/tool/update.php`, `system/library/update.php` (`run`, `checkForUpdates`, `checkRequirements`), `app/admin/controller/widgets/update.php` (`addCron`)
Tag: [legacy]

**118. Error log viewer with clear**
Reads/clears the system log from admin.
Where: `app/admin/controller/tool/error_log.php`
Tag: [legacy]

**119. User groups with access/modify ACL**
Serialized per-group permission arrays gate every admin route via `hasPermission()`; dedicated permission editor.
Where: `app/admin/controller/user/{user,user_permission}.php`, `system/library/user.php`, table `nts8sd4fd_user_group.permission`
Tag: [legacy]

**120. User activity audit log**
Back-office actions recorded in `user_activity` (legacy) and an `AuditService` capturing queries/requests/model changes/exceptions in next.
Where: table `nts8sd4fd_user_activity`; next: `necoyoad-next/app/Services/AuditService.php` (5 log channels), `app/Models/UserActivity.php`, `app/Traits/Auditable.php`
Tag: [both]

**121. REST JSON API v1 (~40 entities, 83 endpoints)**
`api/v1` + `api/v1.0.0/` expose every domain object (products, orders, customers, campaigns, contacts, templates, widgets, settings, adminmenu…) as JSON with token validation and 404/503 handlers.
Where: `app/admin/controller/api/v1.php`, `app/admin/controller/api/v1.0.0/*` (83 files)
Tag: [legacy]

**122. Public/private key callback gateway**
`common/callback` authenticates external API consumers with public+private key pairs for remote object manipulation.
Where: `app/admin/controller/common/callback.php`
Tag: [legacy]

**123. CKEditor-integrated file manager**
Full web file manager (create/rename/move/copy/delete directories & files, upload, thumbnails) that plugs into CKEditor file browser dialogs.
Where: `app/admin/controller/common/filemanager.php` (index/image/directory/files/create/delete/move/copy/folders/rename/upload/uploader)
Tag: [both]

**124. Extension installer with licensing**
`extension` table tracks type/app/key/license/install/uninstall routes/version for modules, payments, shippings and totals.
Where: `app/admin/controller/extension/{module,payment,shipping,total}.php`, table `nts8sd4fd_extension`
Tag: [legacy]

**125. Menu builder (nestedSortable, 3 levels, icon picker)**
Drag-nested menu editor (max 3 levels) with FontAwesome icon picker, URL/tag/slug/CSS-class inputs, per-language CKEditor labels and page-select fed by the REST API.
Where: `app/admin/controller/content/menu.php`, `web/admin/js/frontend/contentmenu.js` (nestedSortable), `app/admin/model/content/menu.php`
Tag: [both]

**126. Cache manager**
Flush-all file cache endpoint that also clears per-store config session caches (`ntConfig_<id>`).
Where: `app/admin/controller/setting/cache.php` (`deletefilecache`)
Tag: [legacy]

**127. Grid views with batch operations & save patterns**
Entity grids support batch `copyAll`/`deleteAll`, column formatters and "Save & Keep" / "Save & New" submit targets.
Where: `app/admin/controller/content/banner.php:114-115`, `app/admin/controller/store/store.php:74-79`
Tag: [legacy]

**128. Transactional email template packs with previews**
Six default HTML email templates — new customer, new order, new payment, new reply, new comment, happy birthday — each with a `preview.gif`.
Where: `web/admin/email_templates/01_defaults/{cliente-nuevo,pedido-nuevo,pago-nuevo,replica-nueva,comentario-nuevo,happy-birthday}/`
Tag: [legacy]

**129. Vendor feedback form**
Admin support form mailing domain/IP/feedback + server vars to the platform vendor via SMTP.
Where: `app/admin/controller/support/feedback.php`
Tag: [legacy]

**130. Visits / search-term analytics report**
Report over the `stat` + `search` tables (page views, referrers, browsers, search terms).
Where: `app/admin/controller/report/visits.php`
Tag: [legacy]

---

## M. Platform & Engine

**131. WordPress-style hooks: filters + actions with priorities**
`Hooks` engine (`addFilter/applyFilters/addAction/run/removeFilter`, priority constants URGENT→LOWEST, short-circuit returns, `did()` introspection) — the entire admin CRUD and module pipeline is filterable.
Where: `system/library/automation/hooks.php`
Tag: [both]

**132. Static event bus (on/once/off/emit)**
`Events` pub/sub with `once` support and meta-emissions; registry updates dispatch events.
Where: `system/library/automation/events.php`, `system/engine/registry.php`
Tag: [both]

**133. Hookable PDO database layer**
The single driver `ntMySQLPdo` fires `db:query` action before execute and applies `db:escape` filter on escaping; returns row/rows/obj/num_rows result objects.
Where: `system/database/ntMySQLPdo.php`, `system/library/db.php`
Tag: [legacy]

**134. Generic EAV model base**
Abstract `Model` provides table/pkey/object_type metadata, generic CRUD (add/update/copy/delete/getAll/getAllTotal), SQL builder, plus property/description/store/category helper APIs every domain model inherits.
Where: `system/engine/model.php` (1,802 lines)
Tag: [both]

**135. Front controller with pre-action interception chain**
Ordered pre-actions run before dispatch: storefront (maintenance check, seo_url), admin (login, permission) — enabling middleware-like behavior.
Where: `system/engine/front.php`, `app/shop/map.php`, `web/admin/index.php`
Tag: [legacy]

**136. File cache with TTL-in-filename**
Cache class writes `*.cache` files with TTL encoded in names (default 60h), prefix support, used for settings, pages, search results, campaigns.
Where: `system/library/cache.php`, `system/temp/cache/`
Tag: [legacy]

**137. Maintenance mode**
Store-wide maintenance pre-action with its own template and admin toggle (`editMaintenance`).
Where: `app/shop/controller/common/maintenance.php`, `app/admin/controller/store/store.php:82-84`
Tag: [legacy]

**138. Gzip response compression**
Output pipeline gzips responses at configured compression levels (zlib).
Where: `system/library/response.php` (`compress`, `output`)
Tag: [legacy]

**139. GD image pipeline — resize, crop, watermark, rotate, letterbox**
`Image`/`NTImage` classes: proportional resize with background fill, crop, rotate, watermark positioning, static `resizeAndSave` used across filemanager/banners/widgets.
Where: `system/library/image.php` (`resize`, `crop`, `watermark`, `rotate`, `setBgColor`)
Tag: [both]

**140. blueimp jQuery-File-Upload server handler**
Robust upload class bound to the upload directory powering admin uploads.
Where: `system/library/upload.php`
Tag: [legacy]

**141. Math captcha + Google reCAPTCHA**
Two captcha options: built-in GD math captcha and full reCAPTCHA client (incl. mailhide email obfuscation).
Where: `system/library/captcha.php`, `system/library/recaptcha.php`
Tag: [legacy]

**142. Cron task scheduler with 6 task types + queue**
`system/cron/cron.php` reads `task`/`task_queue`, builds `Task` objects (intervals, run_once) and dispatches by type: `send`, `sale*`, `enquiry*`, `report*`, `backup*`, `maintenance*`; send + promoter + birthday processors active.
Where: `system/cron/cron.php:208-226`, `system/cron/api/{send,birthday,promoter}.php`, `system/library/task.php`, tables `task`, `task_queue`, `task_exec`
Tag: [both]

**143. Full-visit tracker with campaign attribution**
Every page view records customer, object, store, serialized `$_SERVER`/session/request, referrer, browser/version/OS, IP — auto-creating marketing contacts from referred visits.
Where: `system/library/tracker.php:28-60`, table `nts8sd4fd_stat` (17 cols)
Tag: [legacy]

**144. Browser/UA detection incl. Facebook in-app**
Chris-Schuld Browser class extended with `isFacebook()` for FB webview theming, plus mobile/tablet detection.
Where: `system/library/browser.php`, `system/config/config_browser.php`
Tag: [legacy]

**145. AJAX-aware pagination**
Pager renderer with an ajax mode (`ajaxTarget`) for partial list reloads.
Where: `system/library/pagination.php`
Tag: [legacy]

**146. JSON+CORS response helper**
`Json::encode` emits JSON content-type + CORS headers for the AJAX endpoints.
Where: `system/library/json.php`
Tag: [legacy]

**147. Source-code encoder for distribution**
Author's obfuscation tool to distribute licensed platform code.
Where: `system/library/encoder.php`
Tag: [legacy]

**148. xhttp cURL wrapper with plugin stack**
cURL abstraction with cookie/multi/oauth/rpc/profile plugins used by updater, QR, APIs.
Where: `system/library/xhttp/xhttp.php`
Tag: [legacy]

**149. PHPMailer fork with SMTP/POP3/bounce rules**
Campaign-grade mailer with SMTP auth, POP-before-SMTP, UTF-8 handling, vCard + spam-rule utilities.
Where: `system/library/email/mailer.php`, `system/library/email/{smtp,pop3,utf8,newsletter,vcard,spam_rules}.php` — wrapped by `system/library/ntsmailer.php`
Tag: [legacy]

**150. Server-side React/JSX experiment**
Bundled `reactjs.php` — a server-side React/JSX rendering experiment from the platform's R&D.
Where: `system/library/reactjs/reactjs.php`
Tag: [legacy]

---

## N. Security

**151. AES-256-CTR encrypted cookies**
All cookies are encrypted/decrypted with `CRYPT_KEY` (AES-256-CTR) and obfuscated cookie names (`md5(CRYPT_KEY.key)`).
Where: `system/library/request.php` (`encrypt_string`/`decrypt_string`)
Tag: [legacy]

**152. CSRF `fkey` token + session hardening**
Session-bound `fkey` CSRF token; sessions re-pointed to private `DIR_SESSION`, `nts_token` cookie scoped to the parent domain; admin token validation with an ignorable-route list (`config_token_ignore`).
Where: `system/library/session.php`, `app/admin/map.php` (login pre-action), `app/admin/controller/store/store.php:52-53`
Tag: [legacy]

**153. Login + ACL pre-action gate on every admin request**
Admin requests are pre-screened for a valid session (ukey signing) and per-route access/modify permissions before dispatch; permission-denied error controller.
Where: `app/admin/controller/common/home.php` (`login`, `permission`), `app/admin/controller/error/permission.php`
Tag: [legacy]

**154. Sandboxed file/theme editors (next)**
FileManagerService and ThemeEditorService enforce path-traversal guards, extension/mime whitelists (blade.php/css/js/scss/json), 1MB size caps, dedicated exception types and audit logging; routes gated by `can:file-manager` / `can:theme-edit` abilities with throttling.
Where: `necoyoad-next/app/Services/{FileManagerService,ThemeEditorService}.php`, `app/Http/Controllers/Admin/*`, `app/Exceptions/{UnsafeFileException,FileTooLargeException,InvalidFileTypeException,...}.php`
Tag: [next]

**155. SVG sanitization**
`enshrined/svg-sanitize` sanitizes uploaded SVGs in the modern stack.
Where: `necoyoad-next/composer.json`, `app/Services/FileManagerService.php`
Tag: [next]

**156. Rate-limited public endpoints (next)**
Banner events (120/min) and browser-audit API (60/min) are throttled; audit beacon route is CSRF-exempt by design.
Where: `necoyoad-next/routes/web.php`, `routes/api.php`
Tag: [next]

---

## O. Integrations

**157. Facebook SDK v5 (bundled)**
OAuth, Graph calls, publish/pages/messaging scopes used by login and social widgets.
Where: `system/library/facebook/`
Tag: [legacy]

**158. Google APIs client (bundled)**
OAuth + Contacts + userinfo/plus scopes for login and invites.
Where: `system/library/google/`
Tag: [legacy]

**159. PayU OpenPayU v2 SDK (bundled)**
Order/refund operations for the payment gateway.
Where: `system/library/payu/`
Tag: [legacy]

**160. MercadoLibre SDK (bundled)**
OAuth + API client for the Latin-American marketplace login/sync.
Where: `system/library/meli/meli.php`
Tag: [legacy]

**161. cPanel XML-API hosting integration**
cPanel API client powering the server-status dashboard widget (bandwidth, disk, IPs, PHP/Apache/MySQL versions, subdomains, email accounts) and hosting provisioning hooks.
Where: `system/library/cpxmlapi.php`, `app/admin/controller/widgets/server_status.php:33-36`
Tag: [legacy]

**162. Google Analytics & Google Maps widgets**
First-class GA tracking widget and Google Maps embed widget modules.
Where: `app/shop/controller/module/{google_analytics,google_maps}.php`
Tag: [legacy]

**163. Social sharing & like widgets**
Facebook like button (fblike) and share-oriented widget modules.
Where: `app/shop/controller/module/fblike.php`, `app/shop/language/spanish/module/share_buttons.php`
Tag: [legacy]

---

## P. necoyoad-next Modern Stack

**164. Laravel 11 + Filament 3 + Livewire 3 + Sanctum platform**
Modern rewrite on PHP 8.3 with Filament admin panel (Blue theme), Livewire 3 storefront, Sanctum API auth, Predis, Intervention Image.
Where: `necoyoad-next/composer.json`, `app/Providers/FilamentAdminPanelProvider.php`
Tag: [next]

**165. 16 Filament admin resources**
Product, Category, Post, Banner, Menu, Store, User, Language, Currency, Manufacturer, Campaign, Newsletter, Contact, ContactList, WidgetRow (+ abstract NecoyoadResource base).
Where: `necoyoad-next/app/Filament/Resources/*` (16 resources, each with List/Create/Edit pages)
Tag: [next]

**166. Shared multilingual + store-visibility + SEO form tabs**
Every resource inherits reusable tabs: per-language descriptions repeater (title/body/SEO fields), multi-store visibility select with helper text.
Where: `necoyoad-next/app/Filament/Resources/NecoyoadResource.php:27-75` (`sharedTabs`)
Tag: [next]

**167. Filament pages: Dashboard, ThemeEditor, FileManager + stats widget**
Custom admin pages for visual file management and theme editing, plus a `DashboardStats` overview widget.
Where: `necoyoad-next/app/Filament/Pages/{Dashboard,ThemeEditor,FileManager}.php`, `app/Filament/Widgets/DashboardStats.php`
Tag: [next]

**168. Livewire slide-out CartDrawer**
Reactive mini-cart drawer: add/update/remove/clear with live count, opens on add events, per-store pricing context.
Where: `necoyoad-next/app/Livewire/Storefront/CartDrawer.php`, `resources/views/livewire/storefront/cart-drawer.blade.php`
Tag: [next]

**169. Livewire ProductPage with quantity stepper**
Interactive product detail component (qty increment/decrement + add-to-cart dispatch).
Where: `necoyoad-next/app/Livewire/Storefront/ProductPage.php`
Tag: [next]

**170. Single-page Livewire checkout (4 steps)**
Shipping → Payment → Confirm → Success in one reactive component: validation per step, terms acceptance, order + items + totals snapshot creation, stock decrement, cart clearing.
Where: `necoyoad-next/app/Livewire/Storefront/CheckoutForm.php`
Tag: [next]

**171. Theme editor with sha256 versioning & one-click restore**
Every save snapshots content into `theme_file_versions` (sha256 hash, size); admin lists versions and restores any of them.
Where: `necoyoad-next/app/Services/ThemeEditorService.php` (`saveFile`, `getVersions`, `restoreVersion`), `app/Http/Controllers/Admin/ThemeEditorController.php`
Tag: [next]

**172. Intervention-powered ImageService with WebP**
Resize (fit modes), crop, watermark positioning and format conversion (WebP default at 80 quality) with GD/Imagick drivers and its own exception hierarchy.
Where: `necoyoad-next/app/Services/ImageService.php` (`getThumbnail`, `resize`, `crop`, `watermark`, `convert`), `config/necoyoad.php` (image block)
Tag: [next]

**173. EavService + enforced morph map**
Service-layer EAV over the `properties` table with store-scoped values and a strict morph map (`product, post, page, category, manufacturer, banner, banner_item, menu_link`).
Where: `necoyoad-next/app/Services/EavService.php`, `app/Providers/AppServiceProvider.php` (`Relation::enforceMorphMap`)
Tag: [next]

**174. TemplateResolver — 3-level template resolution**
Per-entity EAV `style.view` override → config default map (`config/necoyoad.php defaults`) → hard fallback, mirroring legacy `default_view_*`.
Where: `necoyoad-next/app/Services/TemplateResolver.php`, `app/Http/Controllers/StorefrontController.php`
Tag: [next]

**175. WidgetService with poison-proof cache keys**
Widget tree queries cached 300s under composite keys of store:position:language:route:objectType:objectId; async endpoint renders widgets standalone with style/script headers.
Where: `necoyoad-next/app/Services/WidgetService.php:145-152`, `app/Http/Controllers/WidgetController.php` (`async`)
Tag: [next]

**176. AssetManifest per-widget asset pipeline**
Vite-backed manifest registers per-widget CSS/JS bundles (rich-text, product-list, category-list, contact-form, search, banner) — the legacy `deps.php` equivalent.
Where: `necoyoad-next/app/Services/AssetManifest.php`, `app/Providers/NecoyoadServiceProvider.php` (`registerWidgetAssets`)
Tag: [next]

**177. Campaign queue jobs with token personalization**
`SendCampaignEmail` job per campaign+contact: personalizes tokens, rewrites links to click-tracked nonces, appends the open pixel, respects inactive contacts, fails gracefully.
Where: `necoyoad-next/app/Jobs/SendCampaignEmail.php`
Tag: [next]

**178. Scheduler-driven marketing automation**
`campaigns:send-due` every 15 min, `campaigns:process-bounces` hourly, `campaigns:send-birthdays` daily 09:00, `images:clean-cache` daily 03:00.
Where: `necoyoad-next/routes/console.php`, `app/Console/Commands/*`
Tag: [next]

**179. Browser audit beacon**
`resources/js/audit-logger.js` captures window.onerror, unhandled rejections, console.error and failed fetch/XHR (status outside 200-399), batches and ships them via `navigator.sendBeacon` to the throttled `/api/audit/browser` endpoint.
Where: `necoyoad-next/resources/js/audit-logger.js`, `app/Http/Controllers/AuditController.php`
Tag: [next]

**180. Query/request/exec/model/exception audit service**
`AuditService` logs slow queries (via `DB::listen`), HTTP responses outside 200-399 (`LogHttpResponse` middleware), exec calls, model changes and every uncaught Throwable.
Where: `necoyoad-next/app/Services/AuditService.php`, `app/Http/Middleware/LogHttpResponse.php`, `bootstrap/app.php`
Tag: [next]

**181. Demo login picker (one-click accounts)**
Dev-only page listing all admin/customer demo accounts with instant auto-login into either guard.
Where: `necoyoad-next/app/Http/Controllers/DemoLoginController.php`, `resources/views/auth/demo-login.blade.php`
Tag: [next]

**182. Docker + FrankenPHP + Caddy deployment**
FrankenPHP php8.3 worker image, Caddyfile (zstd/gzip, dotfile 404s, try_files→index.php), compose with MySQL 8 + Redis 7 (+ optional Meilisearch & Mailhog profiles), entrypoint auto-generates APP_KEY, waits for DB, migrates + seeds + publishes assets.
Where: `necoyoad-next/docker-compose.yml`, `docker/Dockerfile`, `Caddyfile`, `docker/entrypoint.sh`
Tag: [next]

**183. Rich demo seeder — 5 stores / 28 accounts**
Seeds 3 admin roles, 2 languages, 3 currencies, 5 tenant stores each with customers, categories, products, posts/pages, banners (3 slides), menus, widget trees, contact lists and newsletters.
Where: `necoyoad-next/database/seeders/DatabaseSeeder.php` (519 lines)
Tag: [next]

**184. Pest test suite**
Feature tests (home/search storefront) and unit tests (WidgetService, StoreContext, LanguageContext, AssetManifest instantiation).
Where: `necoyoad-next/tests/Feature/StorefrontTest.php`, `tests/Unit/WidgetEngineTest.php`, `phpunit.xml`
Tag: [next]

**185. Legacy Hooks port — FilterPipeline**
`app/Filters/*` reimplements the WordPress-style filter pipeline as a service (`'filter' => FilterPipeline`) for cross-cutting next-stack extension.
Where: `necoyoad-next/app/Filters/{Filter,FilterPipeline,FilterServiceProvider}.php`
Tag: [next]

**186. Health endpoint & storefront error page**
`/up` health route; dedicated `errors/storefront.blade.php` rendering for all StorefrontException subclasses (or JSON).
Where: `necoyoad-next/bootstrap/app.php` (health), `resources/views/errors/storefront.blade.php`, `app/Exceptions/*`
Tag: [next]

---

## TOTAL: 186 features

Group counts: A. Storefront Catalog 16 · B. Cart & Checkout 15 · C. Customer Account 14 · D. CMS & Content 9 · E. SEO 5 · F. Marketing & Campaigns 15 · G. Design & Theming 7 · H. Widgets & Layout 8 · I. Banners 7 · J. Multi-store 7 · K. Localisation 6 · L. Admin Back-office 21 · M. Platform & Engine 20 · N. Security 6 · O. Integrations 7 · P. necoyoad-next Modern Stack 23.

### Notes & caveats (verified during cataloging)
- No wishlist feature exists in the shipped storefront code (only product compare) — deliberately not cataloged.
- Guest checkout is only partially present (session `guest` clearing in success; confirm requires login) — not cataloged as full guest checkout.
- `setting/cache.php` cache manager is half-built (`echo 'build cache manager'` for index, but flush works).
- Several catalog widgets referenced in language packs (catalog2pdf, layer_slider, resellerclub, web_content_crawler, promoter, cpanel, twitter modules) have no shipped controller — language files only; not cataloged.
- Banner `params` column and `menu.position/route/default` columns are dead (per research 3a3/3a4) — features cataloged only where behaviorally real.
- The `rooms`/`rooms_admin_table` widgets and `mymodule` are scaffolds; the module scaffold itself is cataloged (#89).
