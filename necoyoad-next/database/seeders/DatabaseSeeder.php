<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\BannerItem;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Language;
use App\Models\Menu;
use App\Models\MenuLink;
use App\Models\Post;
use App\Models\Product;
use App\Models\Store;
use App\Models\Widget;
use App\Models\WidgetColumn;
use App\Models\WidgetRow;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================
        // STORES
        // ============================================
        $store = Store::firstOrCreate(
            ['folder' => 'default'],
            [
                'name' => 'Necoyoad Demo',
                'is_default' => true,
                'status' => true,
                'settings' => [
                    'config_template' => 'choroni',
                    'config_language' => 'en',
                    'config_currency' => 'USD',
                    'config_title' => 'Necoyoad Demo Store',
                ],
            ]
        );

        // ============================================
        // LANGUAGES
        // ============================================
        $en = Language::firstOrCreate(['code' => 'en'], ['name' => 'English', 'locale' => 'en_US', 'sort_order' => 1]);
        $es = Language::firstOrCreate(['code' => 'es'], ['name' => 'Español', 'locale' => 'es_VE', 'sort_order' => 2]);

        $store->languages()->syncWithoutDetaching([$en->id, $es->id]);

        // ============================================
        // CURRENCY
        // ============================================
        Currency::firstOrCreate(['code' => 'USD'], ['symbol_left' => '$', 'decimal_place' => '2', 'value' => 1, 'status' => true]);
        Currency::firstOrCreate(['code' => 'VES'], ['symbol_right' => ' Bs', 'decimal_place' => '2', 'value' => 36.5, 'status' => true]);

        // ============================================
        // CATEGORIES
        // ============================================
        $electronics = $this->firstOrCreateCategory('product', 1, [
            $en->id => ['title' => 'Electronics', 'description' => 'Electronic products'],
            $es->id => ['title' => 'Electrónicos', 'description' => 'Productos electrónicos'],
        ]);
        $electronics->stores()->syncWithoutDetaching($store->id);

        $clothing = $this->firstOrCreateCategory('product', 2, [
            $en->id => ['title' => 'Clothing', 'description' => 'Clothing and apparel'],
            $es->id => ['title' => 'Ropa', 'description' => 'Ropa y vestimenta'],
        ]);
        $clothing->stores()->syncWithoutDetaching($store->id);

        // ============================================
        // PRODUCTS
        // ============================================
        $product1 = $this->firstOrCreateProduct('P001', [
            'model' => 'PHONE-001', 'price' => 599.99, 'cost' => 350,
            'quantity' => 100, 'featured' => true, 'status' => true,
        ], [
            $en->id => ['title' => 'Smartphone Pro', 'description' => 'Latest smartphone with advanced features'],
            $es->id => ['title' => 'Smartphone Pro', 'description' => 'Último smartphone con características avanzadas'],
        ]);
        $product1->categories()->syncWithoutDetaching($electronics->id);
        $product1->stores()->syncWithoutDetaching($store->id);

        $product2 = $this->firstOrCreateProduct('P002', [
            'model' => 'LAPTOP-001', 'price' => 1299.99, 'cost' => 800,
            'quantity' => 50, 'featured' => true, 'status' => true,
        ], [
            $en->id => ['title' => 'Laptop Ultra', 'description' => 'Powerful laptop for professionals'],
            $es->id => ['title' => 'Laptop Ultra', 'description' => 'Laptop potente para profesionales'],
        ]);
        $product2->categories()->syncWithoutDetaching($electronics->id);
        $product2->stores()->syncWithoutDetaching($store->id);

        // ============================================
        // CMS PAGES
        // ============================================
        $page = Post::firstOrCreate(
            ['type' => 'page', 'sort_order' => 1],
            ['publish' => true, 'status' => true]
        );
        $this->syncDescriptions($page, [
            $en->id => ['title' => 'About Us', 'description' => '<p>Welcome to Necoyoad!</p>'],
            $es->id => ['title' => 'Sobre Nosotros', 'description' => '<p>Bienvenido a Necoyoad!</p>'],
        ]);
        $page->stores()->syncWithoutDetaching($store->id);

        // ============================================
        // BANNER
        // ============================================
        $banner = Banner::firstOrCreate(
            ['name' => 'Home Hero'],
            ['jquery_plugin' => 'nivo-slider', 'publish_date_start' => now(), 'status' => true]
        );
        $banner->stores()->syncWithoutDetaching($store->id);

        BannerItem::firstOrCreate(
            ['banner_id' => $banner->id, 'image' => 'banners/slide1.jpg'],
            ['link' => '/', 'sort_order' => 1, 'status' => true]
        );
        BannerItem::firstOrCreate(
            ['banner_id' => $banner->id, 'image' => 'banners/slide2.jpg'],
            ['link' => '/products', 'sort_order' => 2, 'status' => true]
        );

        // ============================================
        // MENU
        // ============================================
        $menu = Menu::firstOrCreate(
            ['store_id' => $store->id, 'name' => 'Main Menu', 'position' => 'header'],
            ['sort_order' => 1, 'is_default' => true, 'status' => true]
        );

        $this->firstOrCreateMenuLink($menu->id, '/', 'Home', 1);
        $this->firstOrCreateMenuLink($menu->id, '/products', 'Products', 2);
        $aboutLink = $this->firstOrCreateMenuLink($menu->id, '/page/1', 'About', 3);
        $this->firstOrCreateMenuLink($menu->id, '/page/2', 'Team', 1, $aboutLink->id);

        // ============================================
        // WIDGET TREE (home page layout)
        // ============================================
        $row = WidgetRow::firstOrCreate(
            ['store_id' => $store->id, 'position' => 'main', 'key' => 'home_main_1'],
            ['settings' => ['classnames' => ''], 'sort_order' => 1, 'status' => true]
        );

        $col = WidgetColumn::firstOrCreate(
            ['row_id' => $row->id, 'key' => 'home_main_1_col_1'],
            ['settings' => ['grid_large' => 12, 'grid_medium' => 12, 'grid_small' => 12], 'sort_order' => 1]
        );

        // Banner widget
        Widget::firstOrCreate(
            ['column_id' => $col->id, 'name' => 'hero_banner'],
            [
                'module' => 'banner', 'store_id' => $store->id, 'landing_page' => 'all',
                'settings' => ['banner_id' => $banner->id, 'title' => 'Featured Products'],
                'sort_order' => 1, 'status' => true,
            ]
        );

        // ProductList widget
        Widget::firstOrCreate(
            ['column_id' => $col->id, 'name' => 'featured_products'],
            [
                'module' => 'product-list', 'store_id' => $store->id, 'landing_page' => 'all',
                'settings' => ['featured' => true, 'limit' => 4, 'title' => 'Featured Products'],
                'sort_order' => 2, 'status' => true,
            ]
        );

        // RichText widget
        Widget::firstOrCreate(
            ['column_id' => $col->id, 'name' => 'welcome_text'],
            [
                'module' => 'rich-text', 'store_id' => $store->id, 'landing_page' => 'all',
                'settings' => ['content' => '<p>Welcome to our store!</p>', 'title' => 'Welcome'],
                'sort_order' => 3, 'status' => true,
            ]
        );
    }

    /**
     * Idempotently create a category + sync its descriptions.
     */
    private function firstOrCreateCategory(string $objectType, int $sortOrder, array $descriptionsByLang): Category
    {
        $category = Category::firstOrCreate(
            ['object_type' => $objectType, 'sort_order' => $sortOrder, 'parent_id' => null],
            ['status' => true]
        );
        $this->syncDescriptions($category, $descriptionsByLang);

        return $category;
    }

    /**
     * Idempotently create a product + sync its descriptions.
     */
    private function firstOrCreateProduct(string $sku, array $attributes, array $descriptionsByLang): Product
    {
        $product = Product::firstOrCreate(['sku' => $sku], $attributes);
        $this->syncDescriptions($product, $descriptionsByLang);

        return $product;
    }

    /**
     * Idempotently create a menu link.
     */
    private function firstOrCreateMenuLink(int $menuId, string $link, string $tag, int $sortOrder, ?int $parentId = null): MenuLink
    {
        return MenuLink::firstOrCreate(
            ['menu_id' => $menuId, 'link' => $link, 'tag' => $tag],
            ['parent_id' => $parentId, 'sort_order' => $sortOrder]
        );
    }

    /**
     * Sync descriptions for a morph model — updateOrCreate per language so
     * re-running the seeder doesn't trip the (describable_type, describable_id,
     * language_id) unique constraint.
     */
    private function syncDescriptions($model, array $descriptionsByLang): void
    {
        foreach ($descriptionsByLang as $languageId => $attrs) {
            $model->descriptions()->updateOrCreate(
                ['language_id' => $languageId],
                $attrs
            );
        }
    }
}
