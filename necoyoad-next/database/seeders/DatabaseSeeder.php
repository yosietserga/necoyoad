<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Store;
use App\Models\Language;
use App\Models\Currency;
use App\Models\Category;
use App\Models\Product;
use App\Models\Post;
use App\Models\Banner;
use App\Models\BannerItem;
use App\Models\Menu;
use App\Models\MenuLink;
use App\Models\WidgetRow;
use App\Models\WidgetColumn;
use App\Models\Widget;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================
        // STORES
        // ============================================
        $store = Store::create([
            'name' => 'Necoyoad Demo',
            'folder' => 'default',
            'is_default' => true,
            'status' => true,
            'settings' => [
                'config_template' => 'choroni',
                'config_language' => 'en',
                'config_currency' => 'USD',
                'config_title' => 'Necoyoad Demo Store',
            ],
        ]);

        // ============================================
        // LANGUAGES
        // ============================================
        $en = Language::create(['name' => 'English', 'code' => 'en', 'locale' => 'en_US', 'sort_order' => 1]);
        $es = Language::create(['name' => 'Español', 'code' => 'es', 'locale' => 'es_VE', 'sort_order' => 2]);

        $store->languages()->attach([$en->id, $es->id]);

        // ============================================
        // CURRENCY
        // ============================================
        Currency::create(['code' => 'USD', 'symbol_left' => '$', 'decimal_place' => '2', 'value' => 1, 'status' => true]);
        Currency::create(['code' => 'VES', 'symbol_right' => ' Bs', 'decimal_place' => '2', 'value' => 36.5, 'status' => true]);

        // ============================================
        // CATEGORIES
        // ============================================
        $electronics = Category::create(['parent_id' => null, 'object_type' => 'product', 'sort_order' => 1, 'status' => true]);
        $electronics->descriptions()->createMany([
            ['language_id' => $en->id, 'title' => 'Electronics', 'description' => 'Electronic products'],
            ['language_id' => $es->id, 'title' => 'Electrónicos', 'description' => 'Productos electrónicos'],
        ]);
        $electronics->stores()->attach($store->id);

        $clothing = Category::create(['parent_id' => null, 'object_type' => 'product', 'sort_order' => 2, 'status' => true]);
        $clothing->descriptions()->createMany([
            ['language_id' => $en->id, 'title' => 'Clothing', 'description' => 'Clothing and apparel'],
            ['language_id' => $es->id, 'title' => 'Ropa', 'description' => 'Ropa y vestimenta'],
        ]);
        $clothing->stores()->attach($store->id);

        // ============================================
        // PRODUCTS
        // ============================================
        $product1 = Product::create([
            'sku' => 'P001', 'model' => 'PHONE-001', 'price' => 599.99, 'cost' => 350,
            'quantity' => 100, 'featured' => true, 'status' => true,
        ]);
        $product1->descriptions()->createMany([
            ['language_id' => $en->id, 'title' => 'Smartphone Pro', 'description' => 'Latest smartphone with advanced features'],
            ['language_id' => $es->id, 'title' => 'Smartphone Pro', 'description' => 'Último smartphone con características avanzadas'],
        ]);
        $product1->categories()->attach($electronics->id);
        $product1->stores()->attach($store->id);

        $product2 = Product::create([
            'sku' => 'P002', 'model' => 'LAPTOP-001', 'price' => 1299.99, 'cost' => 800,
            'quantity' => 50, 'featured' => true, 'status' => true,
        ]);
        $product2->descriptions()->createMany([
            ['language_id' => $en->id, 'title' => 'Laptop Ultra', 'description' => 'Powerful laptop for professionals'],
            ['language_id' => $es->id, 'title' => 'Laptop Ultra', 'description' => 'Laptop potente para profesionales'],
        ]);
        $product2->categories()->attach($electronics->id);
        $product2->stores()->attach($store->id);

        // ============================================
        // CMS PAGES
        // ============================================
        $page = Post::create([
            'type' => 'page', 'publish' => true, 'status' => true, 'sort_order' => 1,
        ]);
        $page->descriptions()->createMany([
            ['language_id' => $en->id, 'title' => 'About Us', 'description' => '<p>Welcome to Necoyoad!</p>'],
            ['language_id' => $es->id, 'title' => 'Sobre Nosotros', 'description' => '<p>Bienvenido a Necoyoad!</p>'],
        ]);
        $page->stores()->attach($store->id);

        // ============================================
        // BANNER
        // ============================================
        $banner = Banner::create([
            'name' => 'Home Hero', 'jquery_plugin' => 'nivo-slider',
            'publish_date_start' => now(), 'status' => true,
        ]);
        $banner->stores()->attach($store->id);

        BannerItem::create(['banner_id' => $banner->id, 'image' => 'banners/slide1.jpg', 'link' => '/', 'sort_order' => 1, 'status' => true]);
        BannerItem::create(['banner_id' => $banner->id, 'image' => 'banners/slide2.jpg', 'link' => '/products', 'sort_order' => 2, 'status' => true]);

        // ============================================
        // MENU
        // ============================================
        $menu = Menu::create([
            'store_id' => $store->id, 'name' => 'Main Menu', 'position' => 'header',
            'sort_order' => 1, 'is_default' => true, 'status' => true,
        ]);

        MenuLink::create(['menu_id' => $menu->id, 'parent_id' => null, 'link' => '/', 'tag' => 'Home', 'sort_order' => 1]);
        MenuLink::create(['menu_id' => $menu->id, 'parent_id' => null, 'link' => '/products', 'tag' => 'Products', 'sort_order' => 2]);
        $aboutLink = MenuLink::create(['menu_id' => $menu->id, 'parent_id' => null, 'link' => '/page/1', 'tag' => 'About', 'sort_order' => 3]);
        MenuLink::create(['menu_id' => $menu->id, 'parent_id' => $aboutLink->id, 'link' => '/page/2', 'tag' => 'Team', 'sort_order' => 1]);

        // ============================================
        // WIDGET TREE (home page layout)
        // ============================================
        $row = WidgetRow::create([
            'store_id' => $store->id, 'position' => 'main', 'key' => 'home_main_1',
            'settings' => ['classnames' => ''], 'sort_order' => 1, 'status' => true,
        ]);

        $col = WidgetColumn::create([
            'row_id' => $row->id, 'key' => 'home_main_1_col_1',
            'settings' => ['grid_large' => 12, 'grid_medium' => 12, 'grid_small' => 12],
            'sort_order' => 1,
        ]);

        // Banner widget
        Widget::create([
            'column_id' => $col->id, 'name' => 'hero_banner', 'module' => 'banner',
            'store_id' => $store->id, 'landing_page' => 'all',
            'settings' => ['banner_id' => $banner->id, 'title' => 'Featured Products'],
            'sort_order' => 1, 'status' => true,
        ]);

        // ProductList widget
        Widget::create([
            'column_id' => $col->id, 'name' => 'featured_products', 'module' => 'product-list',
            'store_id' => $store->id, 'landing_page' => 'all',
            'settings' => ['featured' => true, 'limit' => 4, 'title' => 'Featured Products'],
            'sort_order' => 2, 'status' => true,
        ]);

        // RichText widget
        Widget::create([
            'column_id' => $col->id, 'name' => 'welcome_text', 'module' => 'rich-text',
            'store_id' => $store->id, 'landing_page' => 'all',
            'settings' => ['content' => '<p>Welcome to our store!</p>', 'title' => 'Welcome'],
            'sort_order' => 3, 'status' => true,
        ]);
    }
}
