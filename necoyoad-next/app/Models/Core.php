<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Store extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'folder', 'domain', 'is_default', 'status', 'settings'];

    protected $casts = [
        'settings' => 'array',
        'is_default' => 'boolean',
        'status' => 'boolean',
    ];

    public function languages()
    {
        return $this->belongsToMany(Language::class, 'store_languages');
    }
}

class Language extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'locale', 'directory', 'sort_order', 'status'];

    public function stores()
    {
        return $this->belongsToMany(Store::class, 'store_languages');
    }
}

class Currency extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'symbol_left', 'symbol_right', 'decimal_place', 'value', 'status'];

    protected $casts = ['value' => 'float'];
}

class Description extends Model
{
    use HasFactory;

    protected $fillable = ['describable_type', 'describable_id', 'language_id', 'title', 'description', 'seo_title', 'meta_description', 'meta_keywords', 'params'];

    protected $casts = ['params' => 'array'];

    public function describable()
    {
        return $this->morphTo();
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}

class Property extends Model
{
    use HasFactory;

    protected $fillable = ['propertiable_type', 'propertiable_id', 'store_id', 'group', 'key', 'value'];

    public function propertiable()
    {
        return $this->morphTo();
    }

    public function getDecodedValue(): mixed
    {
        $value = $this->value;

        // Try JSON decode first
        $json = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $json;
        }

        return $value;
    }
}

class UrlAlias extends Model
{
    use HasFactory;

    protected $fillable = ['aliasable_type', 'aliasable_id', 'language_id', 'keyword', 'query'];

    public function aliasable()
    {
        return $this->morphTo();
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}

class Category extends Model
{
    use HasFactory, HasDescriptions, HasProperties, HasStoreAssignment, HasSeoUrl;

    protected $fillable = ['parent_id', 'object_type', 'image', 'sort_order', 'status'];

    public function products()
    {
        return $this->morphedByMany(Product::class, 'categorizable', 'categorizables');
    }
}

class WidgetRow extends Model
{
    use HasFactory;

    protected $table = 'widget_rows';

    protected $fillable = ['store_id', 'position', 'key', 'sort_order', 'status', 'settings'];

    protected $casts = ['settings' => 'array'];

    public function columns()
    {
        return $this->hasMany(WidgetColumn::class, 'row_id')->orderBy('sort_order');
    }
}

class WidgetColumn extends Model
{
    use HasFactory;

    protected $table = 'widget_columns';

    protected $fillable = ['row_id', 'key', 'sort_order', 'settings'];

    protected $casts = ['settings' => 'array'];

    public function widgets()
    {
        return $this->hasMany(Widget::class, 'column_id')->orderBy('sort_order');
    }
}

class Widget extends Model
{
    use HasFactory;

    protected $fillable = ['column_id', 'name', 'module', 'store_id', 'landing_page', 'object_type', 'object_id', 'settings', 'sort_order', 'status'];

    protected $casts = ['settings' => 'array'];

    public function column()
    {
        return $this->belongsTo(WidgetColumn::class);
    }

    public function getComponentNameAttribute(): string
    {
        return "widgets.{$this->module}";
    }
}

class Product extends Model
{
    use HasFactory, HasDescriptions, HasProperties, HasStoreAssignment, HasSeoUrl, HasCategories;

    protected $fillable = ['sku', 'model', 'price', 'cost', 'quantity', 'sort_order', 'status', 'weight', 'length', 'width', 'height', 'image', 'featured'];

    protected $casts = ['price' => 'decimal:4', 'cost' => 'decimal:4'];
}

class Post extends Model
{
    use HasFactory, HasDescriptions, HasProperties, HasStoreAssignment, HasSeoUrl;

    protected $fillable = ['type', 'parent_id', 'author_id', 'image', 'sort_order', 'status', 'publish', 'allow_reviews', 'template', 'date_publish_start', 'date_publish_end'];

    protected $casts = ['publish' => 'boolean', 'allow_reviews' => 'boolean'];

    public function scopePages($query) { return $query->where('type', 'page'); }
    public function scopePosts($query) { return $query->where('type', 'post'); }
}

class Banner extends Model
{
    use HasFactory, HasDescriptions, HasProperties, HasStoreAssignment;

    protected $fillable = ['name', 'jquery_plugin', 'settings', 'publish_date_start', 'publish_date_end', 'status'];

    protected $casts = ['settings' => 'array'];

    public function items()
    {
        return $this->hasMany(BannerItem::class)->orderBy('sort_order');
    }
}

class BannerItem extends Model
{
    use HasFactory, HasDescriptions, HasProperties;

    protected $fillable = ['banner_id', 'image', 'link', 'sort_order', 'status'];

    public function banner()
    {
        return $this->belongsTo(Banner::class);
    }
}

class Menu extends Model
{
    use HasFactory, HasDescriptions, HasProperties, HasStoreAssignment;

    protected $fillable = ['store_id', 'name', 'position', 'sort_order', 'route', 'status', 'is_default'];

    public function links()
    {
        return $this->hasMany(MenuLink::class)->whereNull('parent_id')->orderBy('sort_order');
    }
}

class MenuLink extends Model
{
    use HasFactory, HasDescriptions, HasProperties;

    protected $fillable = ['menu_id', 'parent_id', 'link', 'tag', 'sort_order'];

    public function children()
    {
        return $this->hasMany(MenuLink::class, 'parent_id')->orderBy('sort_order');
    }
}
