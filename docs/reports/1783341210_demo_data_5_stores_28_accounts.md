# Demo Data Seeder — 5 Stores + 28 Demo Accounts

**Report ID:** `1783341210_demo_data_5_stores_28_accounts`
**Date:** 2026-07-04
**Commit:** `fb3382` (pushed to `origin/main`)

---

## Summary

Complete rewrite of `DatabaseSeeder.php` with comprehensive multi-tenant demo data: 5 stores, 3 admin accounts, 25 customer accounts, full product catalogs, CMS content, banners, menus, widgets, contacts, and newsletters per store.

---

## Demo Login Credentials

### Admin Panel (`/admin/login`)

| Username | Password | Role |
|----------|----------|------|
| `admin` | `password` | Super Admin |
| `editor` | `password` | Content Editor |
| `manager` | `password` | Store Manager |

### Customer Accounts (`/login`)

Each store has 5 customer accounts. Email format: `{firstname}@{store-folder}.demo`

| Store | Customer Emails | Password |
|-------|----------------|----------|
| Necoyoad Demo (default) | john@default.demo, jane@default.demo, carlos@default.demo, maria@default.demo, bob@default.demo | `password` |
| TechWorld | john@techworld.demo, jane@techworld.demo, carlos@techworld.demo, maria@techworld.demo, bob@techworld.demo | `password` |
| Moda Latina | john@moda.demo, jane@moda.demo, carlos@moda.demo, maria@moda.demo, bob@moda.demo | `password` |
| Home & Garden | john@home.demo, jane@home.demo, carlos@home.demo, maria@home.demo, bob@home.demo | `password` |
| Gadgets Pro | john@gadgets.demo, jane@gadgets.demo, carlos@gadgets.demo, maria@gadgets.demo, bob@gadgets.demo | `password` |

---

## 5 Stores

| # | Store Name | Folder | Domain | Currency | Language |
|---|-----------|--------|--------|----------|----------|
| 1 | Necoyoad Demo | default | (none — default) | USD | EN |
| 2 | TechWorld | techworld | techworld.local | USD | EN |
| 3 | Moda Latina | moda | moda.local | VES | ES |
| 4 | Home & Garden | home | home.local | EUR | EN |
| 5 | Gadgets Pro | gadgets | gadgets.local | USD | EN |

---

## Per-Store Data (×5 stores)

| Entity | Count per store | Total |
|--------|----------------|-------|
| Customers | 5 | 25 |
| Categories | 3 | 15 |
| Products | 5 | 25 |
| CMS Pages | 1 | 5 |
| Blog Posts | 2 | 10 |
| Banners | 1 (with 3 slides) | 5 |
| Menus | 1 (with 4 links) | 5 |
| Widget Trees | 1 (3 widgets) | 5 |
| Contact Lists | 1 (with 3 contacts) | 5 |
| Newsletters | 1 | 5 |

---

## Shared Data

- 2 languages: English (en_US), Español (es_VE)
- 3 currencies: USD ($), VES ( Bs), EUR (€)
- 2 customer groups: Retail, Wholesale

---

## Store-Specific Product Catalogs

### Store 1: Necoyoad Demo
- Smartphone Pro ($599.99), Laptop Ultra ($1299.99), Smart Watch ($249.99), Wireless Buds ($149.99), Tablet Pro ($449.99)

### Store 2: TechWorld
- iPhone 15 Pro ($1099), MacBook Pro 14 ($1999), iPad Air ($599), AirPods Pro 2 ($249), Magic Keyboard ($99)

### Store 3: Moda Latina
- Summer Dress ($79.99), Slim Jeans ($59.99), Cotton Shirt ($39.99), Leather Jacket ($129.99), Sneakers ($89.99)

### Store 4: Home & Garden
- 3-Seat Sofa ($899), Dining Table ($349), Floor Lamp ($49.99), Monstera Plant ($29.99), Area Rug ($199)

### Store 5: Gadgets Pro
- Smart Bulb Kit ($79.99), Fitness Watch 9 ($169.99), Bluetooth Speaker ($129.99), Video Doorbell ($199.99), Smart Plug ($24.99)

---

## Usage

```powershell
docker compose exec app php artisan migrate:fresh --seed
```

Then:
- Admin panel: `http://localhost:8080/admin` → login with `admin` / `password`
- Storefront: `http://localhost:8080/` → shows default store
- Customer login: `http://localhost:8080/login` → use any customer email + `password`
- Switch stores: `http://localhost:8080/?store_id=2` (TechWorld), `?store_id=3` (Moda Latina), etc.

---

## Prompt Engineering Best Practices

- **Idempotent seeder** — all creates use `firstOrCreate` so re-running is safe
- **Realistic data** — store-specific product names, prices, and descriptions (not generic "Product 1")
- **Multi-language** — all content has EN + ES descriptions
- **Multi-currency** — each store uses a different currency (USD, VES, EUR)
- **Multi-tenant** — 5 stores with unique folders + domains for testing multi-store detection
- **Comprehensive** — covers all entities: products, categories, CMS, banners, menus, widgets, contacts, newsletters
