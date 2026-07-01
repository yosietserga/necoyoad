<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Disable FK checks for the duration of this monolithic migration.
        // MySQL 8 validates foreign key references at ALTER TABLE time, so
        // tables that reference entities created later in this same migration
        // (e.g. categorizables -> categories, reviews -> customers) would fail
        // with error 1824 "Failed to open the referenced table". Disabling
        // checks here lets all 43 tables + their constraints be created in a
        // single pass regardless of declaration order.
        Schema::disableForeignKeyConstraints();

        // ============================================
        // MULTI-TENANCY & LOCALISATION
        // ============================================

        if (!Schema::hasTable('stores')) {
            Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('folder')->unique();
            $table->string('domain')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('status')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        }

        if (!Schema::hasTable('languages')) {
            Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64);
            $table->string('code', 5)->unique();
            $table->string('locale', 255);
            $table->string('directory', 32)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
        }

        if (!Schema::hasTable('store_languages')) {
            Schema::create('store_languages', function (Blueprint $table) {
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->foreignId('language_id')->constrained()->cascadeOnDelete();
            $table->primary(['store_id', 'language_id']);
        });
        }

        if (!Schema::hasTable('currencies')) {
            Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('symbol_left', 12)->nullable();
            $table->string('symbol_right', 12)->nullable();
            $table->char('decimal_place', 1)->default('2');
            $table->decimal('value', 15, 8)->default(1);
            $table->boolean('status')->default(true);
            $table->timestamp('date_modified')->useCurrent();
            $table->timestamps();
        });
        }

        if (!Schema::hasTable('countries')) {
            Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 128);
            $table->string('iso_code_2', 2);
            $table->string('iso_code_3', 3);
            $table->text('address_format')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
        }

        if (!Schema::hasTable('zones')) {
            Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->string('code', 32);
            $table->string('name', 128);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
        }

        if (!Schema::hasTable('geo_zones')) {
            Schema::create('geo_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name', 32);
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });
        }

        // ============================================
        // POLYMORPHIC SPINE
        // ============================================

        if (!Schema::hasTable('descriptions')) {
            Schema::create('descriptions', function (Blueprint $table) {
            $table->id();
            $table->morphs('describable');
            $table->foreignId('language_id')->constrained()->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->longText('description')->nullable();
            $table->string('seo_title', 60)->nullable();
            $table->string('meta_description', 160)->nullable();
            $table->string('meta_keywords', 255)->nullable();
            $table->json('params')->nullable();
            $table->timestamps();
            $table->unique(['describable_type', 'describable_id', 'language_id']);
        });
        }

        if (!Schema::hasTable('properties')) {
            Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->morphs('propertiable');
            $table->foreignId('store_id')->nullable()->constrained()->nullOnDelete();
            $table->string('group', 100);
            $table->string('key', 100);
            $table->text('value');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->index(['propertiable_type', 'propertiable_id', 'group', 'key']);
        });
        }

        if (!Schema::hasTable('url_aliases')) {
            Schema::create('url_aliases', function (Blueprint $table) {
            $table->id();
            $table->morphs('aliasable');
            $table->foreignId('language_id')->constrained()->cascadeOnDelete();
            $table->string('keyword', 255);
            $table->string('query', 255)->nullable();
            $table->timestamps();
            $table->unique(['keyword', 'language_id']);
            $table->unique(['aliasable_type', 'aliasable_id', 'language_id']);
        });
        }

        if (!Schema::hasTable('categorizables')) {
            Schema::create('categorizables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->morphs('categorizable');
            $table->unique(['category_id', 'categorizable_type', 'categorizable_id']);
        });
        }

        if (!Schema::hasTable('store_assignments')) {
            Schema::create('store_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->morphs('assignable');
            $table->unique(['store_id', 'assignable_type', 'assignable_id']);
        });
        }

        // ============================================
        // CATALOG
        // ============================================

        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable();
            $table->string('object_type', 50)->default('product');
            $table->string('image', 255)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index('parent_id');
        });
        }

        if (!Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 64)->unique();
            $table->string('model', 64)->nullable();
            $table->decimal('price', 15, 4)->default(0);
            $table->decimal('cost', 15, 4)->default(0);
            $table->integer('quantity')->default(0);
            $table->boolean('subtract')->default(true);
            $table->integer('minimum')->default(1);
            $table->string('image', 255)->nullable();
            $table->decimal('weight', 5, 2)->default(0);
            $table->foreignId('weight_class_id')->nullable();
            $table->decimal('length', 5, 2)->default(0);
            $table->decimal('width', 5, 2)->default(0);
            $table->decimal('height', 5, 2)->default(0);
            $table->foreignId('length_class_id')->nullable();
            $table->foreignId('manufacturer_id')->nullable();
            $table->boolean('shipping')->default(true);
            $table->boolean('featured')->default(false);
            $table->integer('viewed')->default(0);
            $table->integer('sort_order')->default(0);
            $table->boolean('status')->default(true);
            $table->date('date_available')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        }

        if (!Schema::hasTable('manufacturers')) {
            Schema::create('manufacturers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64);
            $table->string('image', 255)->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
        }

        if (!Schema::hasTable('product_images')) {
            Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('image', 255);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
        }

        if (!Schema::hasTable('reviews')) {
            Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->morphs('reviewable');
            $table->foreignId('customer_id')->nullable();
            $table->foreignId('parent_id')->nullable();
            $table->string('author', 64);
            $table->text('text');
            $table->tinyInteger('rating')->default(5);
            $table->boolean('status')->default(false);
            $table->timestamps();
        });
        }

        // ============================================
        // CMS
        // ============================================

        if (!Schema::hasTable('posts')) {
            Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50)->default('post'); // 'post' or 'page'
            $table->foreignId('parent_id')->nullable();
            $table->foreignId('author_id')->nullable();
            $table->string('image', 255)->nullable();
            $table->string('template', 100)->nullable();
            $table->boolean('publish')->default(true);
            $table->boolean('allow_reviews')->default(false);
            $table->integer('sort_order')->default(0);
            $table->boolean('status')->default(true);
            $table->datetime('date_publish_start')->nullable();
            $table->datetime('date_publish_end')->nullable();
            $table->integer('viewed')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index('type');
        });
        }

        if (!Schema::hasTable('menus')) {
            Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('position', 100)->nullable();
            $table->integer('sort_order')->default(0);
            $table->string('route', 150)->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
        }

        if (!Schema::hasTable('menu_links')) {
            Schema::create('menu_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable();
            $table->string('link', 250);
            $table->string('tag', 250);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->index('parent_id');
        });
        }

        if (!Schema::hasTable('banners')) {
            Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('name', 250);
            $table->string('jquery_plugin', 150)->default('nivo-slider');
            $table->json('params')->nullable();
            $table->date('publish_date_start')->nullable();
            $table->date('publish_date_end')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
        }

        if (!Schema::hasTable('banner_items')) {
            Schema::create('banner_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('banner_id')->constrained()->cascadeOnDelete();
            $table->string('image', 250);
            $table->string('link', 250)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
        }

        // ============================================
        // WIDGET ENGINE
        // ============================================

        if (!Schema::hasTable('widget_rows')) {
            Schema::create('widget_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->string('position', 50);
            $table->string('key', 100);
            $table->json('settings')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->index(['store_id', 'position']);
        });
        }

        if (!Schema::hasTable('widget_columns')) {
            Schema::create('widget_columns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('row_id')->constrained('widget_rows')->cascadeOnDelete();
            $table->string('key', 100);
            $table->json('settings')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
        }

        if (!Schema::hasTable('widgets')) {
            Schema::create('widgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('column_id')->constrained('widget_columns')->cascadeOnDelete();
            $table->string('name', 250);
            $table->string('module', 50);
            $table->foreignId('store_id')->nullable()->constrained()->nullOnDelete();
            $table->string('landing_page', 150)->default('all');
            $table->string('object_type', 50)->nullable();
            $table->foreignId('object_id')->nullable();
            $table->json('settings')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->index(['column_id', 'sort_order']);
            $table->index(['object_type', 'object_id']);
        });
        }

        // ============================================
        // COMMERCE
        // ============================================

        if (!Schema::hasTable('customer_groups')) {
            Schema::create('customer_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 32);
            $table->json('params')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
        }

        if (!Schema::hasTable('customers')) {
            Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_group_id')->nullable()->constrained()->nullOnDelete();
            $table->string('firstname', 32);
            $table->string('lastname', 32);
            $table->string('email', 96)->unique();
            $table->string('password');
            $table->string('telephone', 32)->nullable();
            $table->boolean('newsletter')->default(false);
            $table->boolean('status')->default(true);
            $table->boolean('approved')->default(false);
            $table->integer('visits')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
        }

        if (!Schema::hasTable('addresses')) {
            Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('firstname', 32);
            $table->string('lastname', 32);
            $table->string('company', 32)->nullable();
            $table->string('address_1', 128);
            $table->string('address_2', 128)->nullable();
            $table->string('city', 128);
            $table->string('postcode', 10);
            $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('zone_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
        }

        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_group_id')->nullable();
            $table->string('firstname', 32);
            $table->string('lastname', 32);
            $table->string('email', 96);
            $table->string('telephone', 32);
            $table->json('shipping_address')->nullable();
            $table->json('payment_address')->nullable();
            $table->string('shipping_method', 128)->nullable();
            $table->string('payment_method', 128)->nullable();
            $table->decimal('total', 15, 4)->default(0);
            $table->foreignId('order_status_id')->default(0);
            $table->foreignId('language_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('currency_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip', 45)->nullable();
            $table->timestamps();
        });
        }

        if (!Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable();
            $table->string('name', 255);
            $table->string('model', 64)->nullable();
            $table->decimal('price', 15, 4)->default(0);
            $table->decimal('total', 15, 4)->default(0);
            $table->decimal('tax', 15, 4)->default(0);
            $table->integer('quantity')->default(1);
            $table->timestamps();
        });
        }

        if (!Schema::hasTable('order_totals')) {
            Schema::create('order_totals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('title', 255);
            $table->string('text', 255)->nullable();
            $table->decimal('value', 15, 4)->default(0);
            $table->integer('sort_order')->default(0);
        });
        }

        if (!Schema::hasTable('coupons')) {
            Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 24)->unique();
            $table->char('type', 1)->default('F'); // F=fixed, P=percent
            $table->decimal('discount', 15, 4)->default(0);
            $table->boolean('logged')->default(false);
            $table->boolean('shipping')->default(false);
            $table->decimal('total', 15, 4)->default(0);
            $table->date('date_start')->nullable();
            $table->date('date_end')->nullable();
            $table->integer('uses_total')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
        }

        // ============================================
        // MARKETING
        // ============================================

        if (!Schema::hasTable('contacts')) {
            Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable();
            $table->string('name', 250);
            $table->string('email', 100);
            $table->string('telephone', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('date_deleted')->nullable();
            $table->timestamps();
        });
        }

        if (!Schema::hasTable('contact_lists')) {
            Schema::create('contact_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name', 250);
            $table->string('description', 250)->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
        }

        if (!Schema::hasTable('contact_list_subscriptions')) {
            Schema::create('contact_list_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_list_id')->constrained()->cascadeOnDelete();
            $table->timestamp('subscribed_at')->useCurrent();
            $table->unique(['contact_id', 'contact_list_id']);
        });
        }

        if (!Schema::hasTable('newsletters')) {
            Schema::create('newsletters', function (Blueprint $table) {
            $table->id();
            $table->string('name', 250);
            $table->text('textbody')->nullable();
            $table->longText('htmlbody')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
        }

        if (!Schema::hasTable('campaigns')) {
            Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('newsletter_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 250);
            $table->string('subject', 200);
            $table->string('from_name', 70);
            $table->string('from_email', 100);
            $table->string('replyto_email', 100)->nullable();
            $table->boolean('trace_email')->default(false);
            $table->boolean('trace_click')->default(false);
            $table->boolean('embed_image')->default(false);
            $table->string('repeat', 50)->nullable();
            $table->datetime('date_start')->nullable();
            $table->datetime('date_end')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
        }

        if (!Schema::hasTable('campaign_links')) {
            Schema::create('campaign_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->string('url', 250);
            $table->string('redirect', 250);
            $table->string('link', 250)->nullable();
            $table->timestamps();
        });
        }

        if (!Schema::hasTable('campaign_stats')) {
            Schema::create('campaign_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_id')->nullable();
            $table->foreignId('customer_id')->nullable();
            $table->string('store_url', 250)->nullable();
            $table->text('server')->nullable();
            $table->text('session')->nullable();
            $table->text('request')->nullable();
            $table->string('ref', 250)->nullable();
            $table->string('browser', 150)->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamp('date_added')->useCurrent();
        });
        }

        if (!Schema::hasTable('campaign_link_stats')) {
            Schema::create('campaign_link_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_id')->nullable();
            $table->foreignId('customer_id')->nullable();
            $table->string('link', 250);
            $table->string('store_url', 250)->nullable();
            $table->text('server')->nullable();
            $table->text('session')->nullable();
            $table->text('request')->nullable();
            $table->string('ref', 250)->nullable();
            $table->string('browser', 150)->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamp('date_added')->useCurrent();
        });
        }

        // ============================================
        // ADMIN
        // ============================================

        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 20)->unique();
            $table->string('password');
            $table->string('firstname', 32);
            $table->string('lastname', 32);
            $table->string('email', 96)->unique();
            $table->string('image', 255)->nullable();
            $table->boolean('status')->default(true);
            $table->string('ip', 45)->nullable();
            $table->timestamps();
        });
        }

        if (!Schema::hasTable('user_activity')) {
            Schema::create('user_activity', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->morphs('activitable');
            $table->string('event', 50);
            $table->string('action', 50);
            $table->text('description')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('browser', 50)->nullable();
            $table->timestamp('date_added')->useCurrent();
        });
        }

        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->default(0);
            $table->string('group', 32);
            $table->string('key', 64);
            $table->text('value')->nullable();
            $table->timestamps();
            $table->unique(['store_id', 'group', 'key']);
        });
        }

        // Re-enable FK checks now that all tables + constraints exist.
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // Disable FK checks during teardown so DROP TABLE doesn't fail on
        // dependent tables regardless of drop order.
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('settings');
        Schema::dropIfExists('user_activity');
        Schema::dropIfExists('users');
        Schema::dropIfExists('campaign_link_stats');
        Schema::dropIfExists('campaign_stats');
        Schema::dropIfExists('campaign_links');
        Schema::dropIfExists('campaigns');
        Schema::dropIfExists('newsletters');
        Schema::dropIfExists('contact_list_subscriptions');
        Schema::dropIfExists('contact_lists');
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('order_totals');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('addresses');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('customer_groups');
        Schema::dropIfExists('widgets');
        Schema::dropIfExists('widget_columns');
        Schema::dropIfExists('widget_rows');
        Schema::dropIfExists('banner_items');
        Schema::dropIfExists('banners');
        Schema::dropIfExists('menu_links');
        Schema::dropIfExists('menus');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('manufacturers');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('store_assignments');
        Schema::dropIfExists('categorizables');
        Schema::dropIfExists('url_aliases');
        Schema::dropIfExists('properties');
        Schema::dropIfExists('descriptions');
        Schema::dropIfExists('geo_zones');
        Schema::dropIfExists('zones');
        Schema::dropIfExists('countries');
        Schema::dropIfExists('currencies');
        Schema::dropIfExists('store_languages');
        Schema::dropIfExists('languages');
        Schema::dropIfExists('stores');

        Schema::enableForeignKeyConstraints();
    }
};
