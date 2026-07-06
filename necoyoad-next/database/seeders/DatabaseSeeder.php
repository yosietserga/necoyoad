<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\BannerItem;
use App\Models\Category;
use App\Models\Contact;
use App\Models\ContactList;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\Language;
use App\Models\Menu;
use App\Models\MenuLink;
use App\Models\Newsletter;
use App\Models\Post;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Models\Widget;
use App\Models\WidgetColumn;
use App\Models\WidgetRow;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * DatabaseSeeder — comprehensive demo data for 5 stores (tenants).
 *
 * Creates:
 *   - 3 admin users (superadmin, editor, manager)
 *   - 5 stores with unique domains + folders
 *   - 2 languages (EN, ES) shared across all stores
 *   - 2 currencies (USD, VES)
 *   - 2 customer groups (Retail, Wholesale)
 *   - 5 customers per store (demo login accounts)
 *   - 3 categories per store (Electronics, Clothing, Home)
 *   - 5 products per store
 *   - 1 page + 2 blog posts per store
 *   - 1 banner with 3 slides per store
 *   - 1 menu with 4 links per store
 *   - Widget tree per store (banner + featured products + welcome text)
 *   - 1 contact list + 3 contacts per store
 *   - 1 newsletter per store
 *
 * All passwords are 'password' for easy demo login.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================
        // ADMIN USERS (Filament login)
        // ============================================
        $this->createAdminUsers();

        // ============================================
        // LANGUAGES + CURRENCIES (shared)
        // ============================================
        $en = Language::firstOrCreate(['code' => 'en'], ['name' => 'English', 'locale' => 'en_US', 'sort_order' => 1]);
        $es = Language::firstOrCreate(['code' => 'es'], ['name' => 'Español', 'locale' => 'es_VE', 'sort_order' => 2]);

        Currency::firstOrCreate(['code' => 'USD'], ['symbol_left' => '$', 'decimal_place' => '2', 'value' => 1, 'status' => true]);
        Currency::firstOrCreate(['code' => 'VES'], ['symbol_right' => ' Bs', 'decimal_place' => '2', 'value' => 36.5, 'status' => true]);
        Currency::firstOrCreate(['code' => 'EUR'], ['symbol_left' => '€', 'decimal_place' => '2', 'value' => 0.92, 'status' => true]);

        // ============================================
        // CUSTOMER GROUPS (shared)
        // ============================================
        $retailGroup = CustomerGroup::firstOrCreate(['name' => 'Retail'], ['description' => 'Standard retail customers', 'sort_order' => 1, 'status' => true]);
        $wholesaleGroup = CustomerGroup::firstOrCreate(['name' => 'Wholesale'], ['description' => 'Wholesale bulk buyers', 'sort_order' => 2, 'status' => true]);

        // ============================================
        // 5 STORES (TENANTS)
        // ============================================
        $storesData = [
            ['name' => 'Necoyoad Demo', 'folder' => 'default', 'domain' => null, 'is_default' => true, 'currency' => 'USD', 'lang' => 'en'],
            ['name' => 'TechWorld', 'folder' => 'techworld', 'domain' => 'techworld.local', 'is_default' => false, 'currency' => 'USD', 'lang' => 'en'],
            ['name' => 'Moda Latina', 'folder' => 'moda', 'domain' => 'moda.local', 'is_default' => false, 'currency' => 'VES', 'lang' => 'es'],
            ['name' => 'Home & Garden', 'folder' => 'home', 'domain' => 'home.local', 'is_default' => false, 'currency' => 'EUR', 'lang' => 'en'],
            ['name' => 'Gadgets Pro', 'folder' => 'gadgets', 'domain' => 'gadgets.local', 'is_default' => false, 'currency' => 'USD', 'lang' => 'en'],
        ];

        foreach ($storesData as $index => $storeData) {
            $store = $this->createStore($storeData, $en, $es);
            $this->seedStoreData($store, $en, $es, $retailGroup, $wholesaleGroup, $index);
        }
    }

    /**
     * Create 3 admin users with different roles.
     */
    private function createAdminUsers(): void
    {
        $admins = [
            ['username' => 'admin', 'firstname' => 'Super', 'lastname' => 'Admin', 'email' => 'admin@necoyoad.com'],
            ['username' => 'editor', 'firstname' => 'Content', 'lastname' => 'Editor', 'email' => 'editor@necoyoad.com'],
            ['username' => 'manager', 'firstname' => 'Store', 'lastname' => 'Manager', 'email' => 'manager@necoyoad.com'],
        ];

        foreach ($admins as $admin) {
            User::firstOrCreate(
                ['username' => $admin['username']],
                array_merge($admin, [
                    'password' => Hash::make('password'),
                    'status' => true,
                    'ip' => '127.0.0.1',
                ])
            );
        }
    }

    /**
     * Create a store.
     */
    private function createStore(array $data, Language $en, Language $es): Store
    {
        $store = Store::firstOrCreate(
            ['folder' => $data['folder']],
            [
                'name' => $data['name'],
                'domain' => $data['domain'],
                'is_default' => $data['is_default'],
                'status' => true,
                'settings' => [
                    'config_template' => 'choroni',
                    'config_language' => $data['lang'],
                    'config_currency' => $data['currency'],
                    'config_title' => $data['name'],
                ],
            ]
        );

        $store->languages()->syncWithoutDetaching([$en->id, $es->id]);

        return $store;
    }

    /**
     * Seed all demo data for a single store.
     */
    private function seedStoreData(Store $store, Language $en, Language $es, CustomerGroup $retailGroup, CustomerGroup $wholesaleGroup, int $storeIndex): void
    {
        // --- Customers (5 per store) ---
        $this->createCustomers($store, $retailGroup, $wholesaleGroup, $storeIndex);

        // --- Categories (3 per store) ---
        $categories = $this->createCategories($store, $en, $es, $storeIndex);

        // --- Products (5 per store) ---
        $this->createProducts($store, $en, $es, $categories, $storeIndex);

        // --- CMS: 1 page + 2 blog posts ---
        $this->createCmsContent($store, $en, $es, $storeIndex);

        // --- Banner with 3 slides ---
        $banner = $this->createBanner($store, $storeIndex);

        // --- Menu ---
        $this->createMenu($store);

        // --- Widget tree ---
        $this->createWidgetTree($store, $banner);

        // --- Contact list + contacts ---
        $this->createContacts($store, $storeIndex);

        // --- Newsletter ---
        $this->createNewsletter($store, $storeIndex);
    }

    /**
     * Create 5 demo customer accounts per store.
     * Password for all: 'password'
     */
    private function createCustomers(Store $store, CustomerGroup $retailGroup, CustomerGroup $wholesaleGroup, int $storeIndex): void
    {
        $storeSlug = $store->folder;
        $customers = [
            ['firstname' => 'John', 'lastname' => 'Doe', 'email' => "john@{$storeSlug}.demo", 'group' => $retailGroup, 'birthday' => '1990-05-15'],
            ['firstname' => 'Jane', 'lastname' => 'Smith', 'email' => "jane@{$storeSlug}.demo", 'group' => $retailGroup, 'birthday' => '1985-11-22'],
            ['firstname' => 'Carlos', 'lastname' => 'Pérez', 'email' => "carlos@{$storeSlug}.demo", 'group' => $wholesaleGroup, 'birthday' => '1978-03-10'],
            ['firstname' => 'María', 'lastname' => 'González', 'email' => "maria@{$storeSlug}.demo", 'group' => $retailGroup, 'birthday' => '1992-08-05'],
            ['firstname' => 'Bob', 'lastname' => 'Wilson', 'email' => "bob@{$storeSlug}.demo", 'group' => $wholesaleGroup, 'birthday' => '1980-12-01'],
        ];

        foreach ($customers as $cust) {
            Customer::firstOrCreate(
                ['email' => $cust['email']],
                [
                    'store_id' => $store->id,
                    'customer_group_id' => $cust['group']->id,
                    'firstname' => $cust['firstname'],
                    'lastname' => $cust['lastname'],
                    'password' => Hash::make('password'),
                    'telephone' => '+1-555-010' . rand(0, 9),
                    'birthday' => $cust['birthday'],
                    'newsletter' => (bool) rand(0, 1),
                    'status' => true,
                    'approved' => true,
                    'visits' => rand(1, 50),
                ]
            );
        }
    }

    /**
     * Create 3 categories per store.
     */
    private function createCategories(Store $store, Language $en, Language $es, int $storeIndex): array
    {
        $categorySets = [
            // Store 0: Necoyoad Demo
            [
                ['Electronics', 'Electronic products', 'Electrónicos', 'Productos electrónicos'],
                ['Clothing', 'Clothing and apparel', 'Ropa', 'Ropa y vestimenta'],
                ['Home', 'Home and garden', 'Hogar', 'Hogar y jardín'],
            ],
            // Store 1: TechWorld
            [
                ['Phones', 'Smartphones and accessories', 'Teléfonos', 'Smartphones y accesorios'],
                ['Laptops', 'Laptops and notebooks', 'Portátiles', 'Portátiles y notebooks'],
                ['Accessories', 'Tech accessories', 'Accesorios', 'Accesorios tecnológicos'],
            ],
            // Store 2: Moda Latina
            [
                ['Women', 'Women\'s fashion', 'Mujer', 'Moda femenina'],
                ['Men', 'Men\'s fashion', 'Hombre', 'Moda masculina'],
                ['Kids', 'Kids clothing', 'Niños', 'Ropa infantil'],
            ],
            // Store 3: Home & Garden
            [
                ['Furniture', 'Indoor and outdoor furniture', 'Muebles', 'Muebles de interior y exterior'],
                ['Garden', 'Garden tools and plants', 'Jardín', 'Herramientas y plantas'],
                ['Decor', 'Home decoration', 'Decoración', 'Decoración del hogar'],
            ],
            // Store 4: Gadgets Pro
            [
                ['Smart Home', 'Smart home devices', 'Hogar Inteligente', 'Dispositivos para hogar inteligente'],
                ['Wearables', 'Smartwatches and fitness', 'Wearables', 'Smartwatches y fitness'],
                ['Audio', 'Headphones and speakers', 'Audio', 'Auriculares y altavoces'],
            ],
        ];

        $catSet = $categorySets[$storeIndex] ?? $categorySets[0];
        $categories = [];

        foreach ($catSet as $i => $cat) {
            $category = Category::firstOrCreate(
                ['object_type' => 'product', 'sort_order' => $i + 1 + ($storeIndex * 10), 'parent_id' => null],
                ['status' => true]
            );
            $this->syncDescriptions($category, [
                $en->id => ['title' => $cat[0], 'description' => $cat[1]],
                $es->id => ['title' => $cat[2], 'description' => $cat[3]],
            ]);
            $category->stores()->syncWithoutDetaching($store->id);
            $categories[] = $category;
        }

        return $categories;
    }

    /**
     * Create 5 products per store.
     */
    private function createProducts(Store $store, Language $en, Language $es, array $categories, int $storeIndex): void
    {
        $productSets = [
            // Store 0: Necoyoad Demo
            [
                ['P001', 'PHONE-001', 599.99, 350, 'Smartphone Pro', 'Latest smartphone with advanced features'],
                ['P002', 'LAPTOP-001', 1299.99, 800, 'Laptop Ultra', 'Powerful laptop for professionals'],
                ['P003', 'WATCH-001', 249.99, 120, 'Smart Watch', 'Fitness tracking and notifications'],
                ['P004', 'BUDS-001', 149.99, 60, 'Wireless Buds', 'Noise-cancelling earbuds'],
                ['P005', 'TAB-001', 449.99, 250, 'Tablet Pro', '10-inch tablet with stylus'],
            ],
            // Store 1: TechWorld
            [
                ['TW001', 'IPHONE-15', 1099.00, 700, 'iPhone 15 Pro', 'Latest Apple smartphone with A17 chip'],
                ['TW002', 'MBP-14', 1999.00, 1400, 'MacBook Pro 14', 'M3 Pro chip, 16GB RAM'],
                ['TW003', 'IPAD-AIR', 599.00, 400, 'iPad Air', '10.9-inch M2 chip tablet'],
                ['TW004', 'AIRPODS-PRO', 249.00, 150, 'AirPods Pro 2', 'Active noise cancellation'],
                ['TW005', 'MAGIC-KB', 99.00, 50, 'Magic Keyboard', 'Wireless bluetooth keyboard'],
            ],
            // Store 2: Moda Latina
            [
                ['ML001', 'DRESS-001', 79.99, 25, 'Summer Dress', 'Floral print summer dress'],
                ['ML002', 'JEANS-001', 59.99, 20, 'Slim Jeans', 'Premium denim slim fit'],
                ['ML003', 'SHIRT-001', 39.99, 12, 'Cotton Shirt', '100% cotton casual shirt'],
                ['ML004', 'JACKET-001', 129.99, 50, 'Leather Jacket', 'Genuine leather biker jacket'],
                ['ML005', 'SHOES-001', 89.99, 30, 'Sneakers', 'Comfortable everyday sneakers'],
            ],
            // Store 3: Home & Garden
            [
                ['HG001', 'SOFA-001', 899.00, 500, '3-Seat Sofa', 'Modern fabric sofa'],
                ['HG002', 'TABLE-001', 349.00, 180, 'Dining Table', 'Oak wood dining table'],
                ['HG003', 'LAMP-001', 49.99, 15, 'Floor Lamp', 'LED floor lamp with dimmer'],
                ['HG004', 'PLANT-001', 29.99, 8, 'Monstera Plant', 'Live Monstera deliciosa'],
                ['HG005', 'RUG-001', 199.00, 80, 'Area Rug', 'Hand-woven wool rug 5x7'],
            ],
            // Store 4: Gadgets Pro
            [
                ['GP001', 'SMART-LIGHT', 79.99, 35, 'Smart Bulb Kit', 'Color-changing WiFi smart bulbs (4-pack)'],
                ['GP002', 'FITBIT-9', 169.99, 90, 'Fitness Watch 9', 'Heart rate + GPS + sleep tracking'],
                ['GP003', 'SPEAKER-BT', 129.99, 55, 'Bluetooth Speaker', 'Waterproof 360° sound'],
                ['GP004', 'DOORBELL', 199.99, 100, 'Video Doorbell', '1080p camera + motion detection'],
                ['GP005', 'PLUG-SMART', 24.99, 8, 'Smart Plug', 'WiFi smart plug with energy monitoring'],
            ],
        ];

        $prodSet = $productSets[$storeIndex] ?? $productSets[0];

        foreach ($prodSet as $i => $prod) {
            $sku = $prod[0] . '-' . $storeIndex;
            $product = Product::firstOrCreate(
                ['sku' => $sku],
                [
                    'model' => $prod[1],
                    'price' => $prod[2],
                    'cost' => $prod[3],
                    'quantity' => rand(10, 200),
                    'featured' => $i < 2,
                    'status' => true,
                ]
            );
            $this->syncDescriptions($product, [
                $en->id => ['title' => $prod[4], 'description' => $prod[5]],
                $es->id => ['title' => $prod[4], 'description' => $prod[5]],
            ]);
            $product->categories()->syncWithoutDetaching($categories[$i % count($categories)]->id);
            $product->stores()->syncWithoutDetaching($store->id);
        }
    }

    /**
     * Create CMS content: 1 page + 2 blog posts per store.
     */
    private function createCmsContent(Store $store, Language $en, Language $es, int $storeIndex): void
    {
        $storeName = $store->name;

        // Page
        $page = Post::firstOrCreate(
            ['type' => 'page', 'sort_order' => $storeIndex + 1],
            ['publish' => true, 'status' => true]
        );
        $this->syncDescriptions($page, [
            $en->id => ['title' => "About {$storeName}", 'description' => "<p>Welcome to {$storeName}! We offer the best products and services.</p>"],
            $es->id => ['title' => "Sobre {$storeName}", 'description' => "<p>¡Bienvenido a {$storeName}! Ofrecemos los mejores productos y servicios.</p>"],
        ]);
        $page->stores()->syncWithoutDetaching($store->id);

        // Blog posts
        $posts = [
            ['title' => "Welcome to {$storeName}", 'desc_en' => "<p>Discover our latest products and offers at {$storeName}.</p>", 'desc_es' => "<p>Descubre nuestros últimos productos y ofertas en {$storeName}.</p>"],
            ['title' => "New Arrivals at {$storeName}", 'desc_en' => "<p>Check out our newest arrivals across all categories.</p>", 'desc_es' => "<p>Echa un vistazo a nuestras novedades en todas las categorías.</p>"],
        ];

        foreach ($posts as $i => $postData) {
            $post = Post::firstOrCreate(
                ['type' => 'post', 'sort_order' => $storeIndex * 10 + $i + 1],
                ['publish' => true, 'status' => true, 'date_publish_start' => now()->subDays($i * 3)]
            );
            $this->syncDescriptions($post, [
                $en->id => ['title' => $postData['title'], 'description' => $postData['desc_en']],
                $es->id => ['title' => $postData['title'], 'description' => $postData['desc_es']],
            ]);
            $post->stores()->syncWithoutDetaching($store->id);
        }
    }

    /**
     * Create a banner with 3 slides per store.
     */
    private function createBanner(Store $store, int $storeIndex): Banner
    {
        $banner = Banner::firstOrCreate(
            ['name' => $store->name . ' Hero'],
            ['jquery_plugin' => 'nivo-slider', 'publish_date_start' => now(), 'status' => true]
        );
        $banner->stores()->syncWithoutDetaching($store->id);

        for ($i = 1; $i <= 3; $i++) {
            BannerItem::firstOrCreate(
                ['banner_id' => $banner->id, 'sort_order' => $i],
                ['image' => "banners/{$store->folder}/slide{$i}.jpg", 'link' => $i === 1 ? '/' : '/products', 'status' => true]
            );
        }

        return $banner;
    }

    /**
     * Create a menu with links per store.
     */
    private function createMenu(Store $store): void
    {
        $menu = Menu::firstOrCreate(
            ['store_id' => $store->id, 'name' => 'Main Menu', 'position' => 'header'],
            ['sort_order' => 1, 'is_default' => true, 'status' => true]
        );

        $links = [
            ['/', 'Home', 1],
            ['/products', 'Products', 2],
            ['/posts', 'Blog', 3],
            ['/page/1', 'About', 4],
        ];

        foreach ($links as [$link, $tag, $sort]) {
            MenuLink::firstOrCreate(
                ['menu_id' => $menu->id, 'link' => $link, 'tag' => $tag],
                ['parent_id' => null, 'sort_order' => $sort, 'status' => true]
            );
        }
    }

    /**
     * Create widget tree per store.
     */
    private function createWidgetTree(Store $store, Banner $banner): void
    {
        $rowKey = "{$store->folder}_main_1";
        $row = WidgetRow::firstOrCreate(
            ['store_id' => $store->id, 'position' => 'main', 'key' => $rowKey],
            ['settings' => ['classnames' => ''], 'sort_order' => 1, 'status' => true]
        );

        $col = WidgetColumn::firstOrCreate(
            ['row_id' => $row->id, 'key' => $rowKey . '_col_1'],
            ['settings' => ['grid_large' => 12, 'grid_medium' => 12, 'grid_small' => 12], 'sort_order' => 1]
        );

        // Banner widget
        Widget::firstOrCreate(
            ['column_id' => $col->id, 'name' => $store->folder . '_hero'],
            [
                'module' => 'banner', 'store_id' => $store->id, 'landing_page' => 'all',
                'settings' => ['banner_id' => $banner->id, 'title' => $store->name . ' Featured'],
                'sort_order' => 1, 'status' => true,
            ]
        );

        // Featured products widget
        Widget::firstOrCreate(
            ['column_id' => $col->id, 'name' => $store->folder . '_featured'],
            [
                'module' => 'product-list', 'store_id' => $store->id, 'landing_page' => 'all',
                'settings' => ['featured' => true, 'limit' => 4, 'title' => 'Featured Products'],
                'sort_order' => 2, 'status' => true,
            ]
        );

        // Welcome text widget
        Widget::firstOrCreate(
            ['column_id' => $col->id, 'name' => $store->folder . '_welcome'],
            [
                'module' => 'rich-text', 'store_id' => $store->id, 'landing_page' => 'all',
                'settings' => ['content' => "<p>Welcome to {$store->name}!</p>", 'title' => 'Welcome'],
                'sort_order' => 3, 'status' => true,
            ]
        );
    }

    /**
     * Create contact list + 3 contacts per store.
     */
    private function createContacts(Store $store, int $storeIndex): void
    {
        $list = ContactList::firstOrCreate(
            ['name' => $store->name . ' Subscribers'],
            ['description' => 'Newsletter subscribers for ' . $store->name, 'status' => true]
        );

        $contactNames = ['Alice Johnson', 'Bob Brown', 'Carol Davis'];
        foreach ($contactNames as $i => $name) {
            $email = strtolower(str_replace(' ', '.', $name)) . "@{$store->folder}.demo";
            $contact = Contact::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'is_active' => true,
                ]
            );
            $contact->contactLists()->syncWithoutDetaching($list->id);
        }
    }

    /**
     * Create a newsletter per store.
     */
    private function createNewsletter(Store $store, int $storeIndex): void
    {
        Newsletter::firstOrCreate(
            ['name' => $store->name . ' Monthly Newsletter'],
            [
                'textbody' => "Check out our latest deals at {$store->name}!",
                'htmlbody' => "<h1>{$store->name} Newsletter</h1><p>Check out our latest deals!</p>",
                'status' => true,
            ]
        );
    }

    /**
     * Sync descriptions for a morph model.
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
