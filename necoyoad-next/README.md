# New Necoyoad

A digital web agency platform built on Laravel 11, preserving the widget engine
and key architectural patterns from the original Necoyoad.

## Architecture

This project is the modern rebuild of the original Necoyoad platform (PHP/MySQL,
2009-2023). The architecture is documented in 12 volumes of blueprints at
`../docs/architecture/`.

### Preserved patterns

1. **Widget Engine** — drag widgets into positions, configure via admin, see on page
2. **Declarative Admin CRUD** — Filament 3 Resources (replaces AdminController)
3. **Polymorphic Object Spine** — Eloquent morph traits (replaces 5 polymorphic tables)
4. **Dual Hooks/Events** — Laravel Events + custom Filter pipeline
5. **Multi-Store** — StoreContext middleware + tenancy scope
6. **Multi-Language** — LanguageContext middleware (6-level detection) + HasDescriptions
7. **Per-Entity Template Override** — TemplateResolver (entity → config → default)
8. **Banner jQuery Plugin Discriminator** — Dynamic Blade components
9. **Campaign Pipeline** — Queue jobs + Laravel Mail + tracking
10. **ntPlugins JS Registry** — Alpine.js store

### Key files

| File | Purpose |
|------|---------|
| `app/Services/WidgetService.php` | The widget data-access service (NecoWidget equivalent) |
| `app/View/Composers/WidgetComposer.php` | The loadWidgets() equivalent (View Composer) |
| `app/View/Components/WidgetComponent.php` | Base Blade component for all widgets (Module equivalent) |
| `app/Services/AssetManifest.php` | The deps.php manifest equivalent |
| `app/Services/StoreContext.php` | Multi-store resolver (3 strategies) |
| `app/Services/LanguageContext.php` | Multi-language resolver (6-level priority) |
| `app/Traits/HasDescriptions.php` | Polymorphic localised content |
| `app/Traits/HasProperties.php` | EAV key-value metadata |
| `app/Traits/HasStoreAssignment.php` | Multi-store scoping |
| `app/Traits/HasSeoUrl.php` | Per-language SEO slugs |
| `app/Traits/HasCategories.php` | Polymorphic category assignment |
| `app/Filters/Filter.php` | The Hooks system (pipeline-based) |
| `app/Providers/NecoyoadServiceProvider.php` | Registers all services + widget assets |

## Quick Start

```bash
# Install dependencies
composer install

# Configure environment
cp .env.example .env
php artisan key:generate

# Set up database (MySQL 8 InnoDB)
# Create database: necoyoad_next
# Update .env with your DB credentials

# Run migrations
php artisan migrate

# Seed demo data
php artisan db:seed

# Start development server
php artisan serve
```

## Creating a New Widget Module

1. **Create the component class** (`app/View/Components/Widgets/MyWidget.php`):
```php
class MyWidget extends WidgetComponent {
    public function data(): array {
        return ['items' => MyModel::all()];
    }
}
```

2. **Create the Blade template** (`resources/views/components/widgets/my-widget.blade.php`):
```blade
<li id="{{ $widgetName }}" class="widget my-widget nt-editable"
    data-widget="{{ $widgetName }}" data-position="{{ $position }}">
    <!-- Your widget HTML -->
</li>
```

3. **Register assets** (in `app/Providers/NecoyoadServiceProvider.php`):
```php
$manifest->registerWidget('my-widget', [
    'css' => ['css/widgets/my-widget.css'],
    'js' => ['js/widgets/my-widget.js'],
    'routes' => ['*'],
]);
```

4. **Create the admin settings form** (in Filament widget layout manager).

Done. The admin can now drag the widget into any position.

## License

MIT
