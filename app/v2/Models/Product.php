<?php

namespace App\v2\Models;

use App\AttributeProduct;
use App\Category;
use App\Manufacturer;
use App\Models\traits\HasOptCatalogLink;
use App\Price;
use App\ProductBatch;
use App\ProductImage;
use App\ProductQuantity;
use App\ProductThumb;
use App\Subcategory;
use App\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;


/**
 * App\Product
 *
 * @property int $id
 * @property string $product_name
 * @property string $product_name_web
 * @property string|null $product_description
 * @property int $product_price
 * @property string $product_barcode
 * @property int|null $group_id
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int $is_hit
 * @property int $is_site_visible
 * @property Carbon|null $deleted_at
 * @property-read Collection|AttributeProduct[] $attributes
 * @property-read int|null $attributes_count
 * @property-read Collection|Category $category
 * @property-read int|null $categories_count
 * @property-read Collection|Product[] $children
 * @property-read int|null $children_count
 * @property-read mixed $current_price
 * @property-read Collection|Manufacturer $manufacturer
 * @property-read int|null $manufacturer_count
 * @property-read Product|null $parent
 * @property-read Collection|Price[] $price
 * @property-read int|null $price_count
 * @property-read Collection|ProductImage[] $product_images
 * @property-read int|null $product_images_count
 * @property-read Collection|ProductQuantity[] $product_quantity
 * @property-read int|null $product_quantity_count
 * @property-read Collection|ProductThumb[] $product_thumbs
 * @property-read int|null $product_thumbs_count
 * @property-read Collection|ProductBatch[] $quantity
 * @property-read int|null $quantity_count
 * @property-read Collection|Subcategory $subcategory
 * @property-read int|null $subcategories_count
 * @property-read Collection|Tag[] $tags
 * @property-read int|null $tag_count
 * @method static Builder|Product inStock($store_id = 1)
 * @method static Builder|Product isHit($param = 'false')
 * @method static Builder|Product main()
 * @method static Builder|Product newModelQuery()
 * @method static Builder|Product newQuery()
 * @method static Builder|Product ofBrand($param)
 * @method static Builder|Product ofCategory($param)
 * @method static Builder|Product ofPrice($param)
 * @method static Builder|Product ofSearch($search)
 * @method static Builder|Product ofSubcategory($param)
 * @method static Builder|Product ofTag($search)
 * @method static \Illuminate\Database\Query\Builder|Product onlyTrashed()
 * @method static Builder|Product query()
 * @method static Builder|Product with($relations)()
 * @method static Builder|Product whereCreatedAt($value)
 * @method static Builder|Product whereDeletedAt($value)
 * @method static Builder|Product whereGroupId($value)
 * @method static Builder|Product whereId($value)
 * @method static Builder|Product whereIsHit($value)
 * @method static Builder|Product whereIsSiteVisible($value)
 * @method static Builder|Product whereProductBarcode($value)
 * @method static Builder|Product whereProductDescription($value)
 * @method static Builder|Product whereProductName($value)
 * @method static Builder|Product whereProductPrice($value)
 * @method static Builder|Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Product withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Product withoutTrashed()
 * @mixin \Eloquent
 * @property-read int|null $tags_count
 * @property int|null $category_id
 * @property int|null $manufacturer_id
 * @method static Builder|Product whereCategoryId($value)
 * @method static Builder|Product whereManufacturerId($value)
 * @property int $product_discount_price
 * @property int $grouping_attribute_id
 * @property int $subcategory_id
 * @property-read Collection|ProductSku[] $sku
 * @property-read int|null $sku_count
 * @method static Builder|Product whereGroupingAttributeId($value)
 * @method static Builder|Product whereProductDiscountPrice($value)
 * @method static Builder|Product whereSubcategoryId($value)
 * @property bool $is_kaspi_visible
 * @property int $kaspi_product_price
 * @property-read Collection|ProductBatch[] $batches
 * @property-read int|null $batches_count
 * @method static Builder|Product whereIsKaspiVisible($value)
 * @method static Builder|Product whereKaspiProductPrice($value)
 * @property int|null $supplier_id
 * @property string $meta_title
 * @property string|null $meta_description
 * @property-read \App\v2\Models\Supplier|null $supplier
 * @method static Builder|Product whereMetaDescription($value)
 * @method static Builder|Product whereMetaTitle($value)
 * @method static Builder|Product whereSupplierId($value)
 * @property-read \App\v2\Models\Favorite|null $favorite
 * @property-read Collection|ProductSaleEarning[] $seller_earning
 * @property-read int|null $seller_earning_count
 * @method static Builder|Product whereProductNameWeb($value)
 * @property-read Collection|Subcategory[] $additionalSubcategories
 * @property-read int|null $additional_subcategories_count
 * @property-read Collection|ProductComment[] $comments
 * @property-read int|null $comments_count
 * @property-read mixed $stock_price
 * @property-read Collection|StockProducts[] $stocks
 * @property-read int|null $stocks_count
 * @method static Builder|Product ofStocks()
 * @property bool $is_iherb
 * @method static Builder|Product whereIsIherb($value)
 * @property int|null $iherb_price
 * @method static Builder|Product whereIherbPrice($value)
 * @property int|null $product_price_rub
 * @property bool $is_iherb_hit
 * @property bool $is_dubai
 * @method static Builder|Product whereIsDubai($value)
 * @method static Builder|Product whereIsIherbHit($value)
 * @method static Builder|Product whereProductPriceRub($value)
 * @property string|null $product_description_kaz
 * @property-read Collection|\App\v2\Models\KaspiEntityProduct[] $kaspi_price
 * @property-read int|null $kaspi_price_count
 * @method static Builder|Product whereProductDescriptionKaz($value)
 * @property int $margin_type_id
 * @method static Builder|Product whereMarginTypeId($value)
 * @property-read Collection|\App\v2\Models\AttributeValue[] $filters
 * @property-read int|null $filters_count
 * @property bool $is_opt
 * @property-read Collection|\App\v2\Models\WholesalePrice[] $wholesale_prices
 * @property-read int|null $wholesale_prices_count
 * @method static Builder|Product whereIsOpt($value)
 * @method static Builder|Product optProducts()
 * @property-read Collection|\App\v2\Models\WholesaleFavorite[] $wholesaleFavorite
 * @property-read int|null $wholesale_favorite_count
 * @property-read Collection|\App\v2\Models\ProductAvailability[] $quantities
 * @property-read int|null $quantities_count
 * @property-read \App\v2\Models\OptDailyDealProduct|null $optDailyDeals
 * @method static Builder|Product withActiveDailyDeals()
 */
class Product extends Model
{
    use HasOptCatalogLink;
    use SoftDeletes;

    // product fields
    const PRODUCT_NAME = 'product_name';
    const PRODUCT_DESCRIPTION = 'product_description';
    const PRODUCT_PRICE = 'product_price';
    const PRODUCT_BARCODE = 'product_barcode';
    const IS_HIT = 'is_hit';
    const IS_SITE_VISIBLE = 'is_site_visible';
    const META_TITLE = 'meta_title';
    const META_DESCRIPTION = 'meta_description';

    // relationship fields
    const SKU = 'sku';
    const SKU_ATTRIBUTES = 'sku.attributes';
    const CATEGORY = 'category';
    const CATEGORY_ID = 'category_id';
    const MANUFACTURER = 'manufacturer';
    const MANUFACTURER_ID = 'manufacturer_id';
    const SUBCATEGORY = 'subcategory';
    const SUBCATEGORY_ID = 'subcategory_id';
    const ATTRIBUTES = 'attributes';
    const ATTRIBUTE_NAMES = 'attributes.attribute_name';
    const GROUPING_ATTRIBUTE_ID = 'grouping_attribute_id';
    const TAG = 'tags';
    const PRICE = 'price';
    const PRODUCT_IMAGES = 'product_images';
    const PRODUCT_THUMBS = 'product_thumbs';
    const KASPI_PRODUCT_PRICE = 'kaspi_product_price';
    const IS_KASPI_VISIBLE = 'is_kaspi_visible';
    const IS_IHERB = 'is_iherb';
    const SUPPLIER_ID = 'supplier_id';
    const PRODUCT_NAME_WEB = 'product_name_web';


    // filters constants
    const FILTER_CATEGORIES = 'category';
    const FILTER_SUBCATEGORIES = 'subcategory';
    const FILTER_BRANDS = 'brands';
    const FILTER_PRICES = 'prices';
    const FILTER_IS_HIT = 'is_hit';
    const FILTER_IS_IHERB_HIT = 'is_iherb_hit';
    const FILTER_SEARCH = 'search';
    const FILTER_STORE = 'store_id';
    const FILTER_FILTERS = 'filters';

    // cache constants

    const CACHE_PREFIX = 'PRODUCTS_CACHE';

    const DEFAULT_IMAGE = 'products/product_image_default.jpg';

    const PRODUCT_STORES_ID = [
        1, 2, 3, 4, 5, 6
    ];

	protected $guarded = ['id'];

    protected $hidden = ['pivot'];

    protected $casts = [
        'id' => 'integer',
        'product_price' => 'integer',
        'is_hit' => 'boolean',
        'is_site_visible' => 'boolean',
        'group_id' => 'integer',
        'kaspi_product_price' => 'integer',
        'is_kaspi_visible' => 'boolean',
        'category_id' => 'integer',
        'subcategory_id' => 'integer',
        'manufacturer_id' => 'integer',
        'is_iherb' => 'boolean',
        'is_iherb_hit' => 'boolean',
        'is_dubai' => 'boolean',
        'is_opt' => 'boolean',
        'extra' => 'json'
    ];

    public $timestamps = true;

    public function comments(): HasMany
    {
        return $this->hasMany('App\v2\Models\ProductComment')
            ->orderByDesc('created_at');
    }

    public function sku()
    {
        return $this->hasMany(ProductSku::class);
    }

    public function attributes() {
        return $this
            ->morphToMany(AttributeValue::class, 'attributable', 'attributable')
            ->where('filterable', false);
    }

    public function filters(): MorphToMany
    {
        return $this
            ->morphToMany(AttributeValue::class, 'attributable', 'attributable')
            ->where('filterable', true);
    }

    public function tags() {
        return $this->morphToMany('App\Tag', 'taggable');
    }


    public function category() {
        return $this->belongsTo('App\Category')
            ->select(['category_name', 'id'])
            ->withDefault([
                'category_name' => 'неизвестно',
                'id' => null
            ]);
    }

    public function subcategory() {
        return $this->belongsTo('App\Subcategory')
            ->select(['subcategory_name', 'id'])
            ->withDefault([
                'subcategory_name' => 'Неизвестно',
                'id' => -1
            ]);
    }

    public function additionalSubcategories(): BelongsToMany {
        return $this->belongsToMany(Subcategory::class, 'subcategory_product');
    }

    public function supplier() {
        return $this->belongsTo('App\v2\Models\Supplier');
    }

    public function manufacturer() {
        return $this->belongsTo('App\Manufacturer')
            ->select(['manufacturer_name', 'id'])
            ->withDefault([
                'manufacturer_name' => 'Неизвестно',
                'id' => -1
            ]);
    }

    public function batches() {
        return $this->hasManyThrough(
            'App\ProductBatch',
            'App\v2\Models\ProductSku',
            'product_id',
            'product_id',
            'id',
            'id'
        );
    }

    public function seller_earning() {
        return $this->hasMany('App\v2\Models\ProductSaleEarning');
    }

    public function price() {
        return $this->hasMany('App\Price', 'product_id');
    }


    public function product_images() {
        return $this->morphToMany('App\v2\Models\Image', 'imagable', 'imagable');
    }

    public function product_thumbs() {
        return $this->morphToMany('App\v2\Models\Thumb', 'thumbable', 'thumbable');
    }

    public function favorite() {
        return $this->hasOne('App\v2\Models\Favorite', 'product_id');
    }

    public function wholesale_prices(): HasMany
    {
        return $this->hasMany(WholesalePrice::class, 'product_id');
    }

    public function kaspi_price(): HasMany
    {
        return $this->hasMany(KaspiEntityProduct::class, 'product_id');
    }


    public function scopeMain(Builder $query)
    {
        return $query->whereHas('children');
    }


    public function scopeOfSearch($query, $search) {
        if (!$search) {
            return $query;
        }
        $query->where('product_name', 'like', $search);
    }



    public function scopeOfCategory($query, $categories) {
        return $query->whereIn('category_id', $categories);
    }

    public function scopeOfSubcategory($query, $subcategories) {
        return $query
            ->whereIn('subcategory_id', $subcategories)
            ->orWhereHas('additionalSubcategories', function ($query) use ($subcategories) {
                return $query->whereIn('id', $subcategories);
            });
    }

    public function scopeOfBrand($query, $brands) {
        return $query
            ->whereIn('manufacturer_id', $brands);
    }

    public function scopeOfPrice($query, $prices) {
        return $query->where('product_price', '>=', $prices[0])->where('product_price', '<=', $prices[1]);
    }

    public function scopeIsHit($query) {
        return $query->where('is_hit', true);
    }

    public function quantities(): HasMany
    {
        return $this->hasMany(ProductAvailability::class, 'product_id');
    }

    public function scopeInStock($query, $store_id) {
        return $query->whereHas('batches', function ($q) use ($store_id) {
            return $q->where('quantity', '>', 0)->where('store_id', $store_id);
        });
    }

    public function scopeOfTag($query, $tag) {
        return $query->whereHas('tags', function ($q) use ($tag) {
            return $q
                ->where('name', 'like', $tag)
                ->orWhere('name', 'like', _transliterate($tag));
        });
    }

    public function stocks() {
        return $this->hasMany('App\v2\Models\StockProducts')->with(['stock' => function ($q) {
            return $q->where('started_at', '<=', now())->where('finished_at', '>=', now());
        }]);
    }

    public function getStockPriceAttribute() {
        $stocks = $this->stocks;
        if (!$stocks || $stocks->count() === 0) {
            return $this->product_price;
        }

        $stocks = $this->stocks->filter(function ($item) {
            return $item['stock'];
        });

        if (!$stocks || $stocks->count() === 0) {
            return $this->product_price;
        }

        $discount = $stocks->first()['stock']['discount'] / 100;
        return ceil($this->product_price *  (1 - $discount));
    }

    public function scopeOfStocks($query) {
        //return $query->
    }

    public function retrieveProductThumb(): string
    {
        return url('/') . Storage::url($this->product_thumbs[0]->image ?? 'products/product_image_default.jpg');

    }

    public function scopeOptProducts($query)
    {
        return $query->where('is_opt', true);
    }

    public function scopeFreeEliteGift(Builder $query): void
    {
        $query->whereJsonContains('extra->is_free_elite_gift', true);
    }

    public function isFreeEliteGift()
    {
        return data_get($this->extra, 'is_free_elite_gift', false);
    }

    public function getPackaging($attributes)
    {
        $packagingIds = [2, 5, 6];
        $needle = collect($attributes)->whereIn('attribute_id', $packagingIds)->first();
        return optional($needle)['attribute_value'] ?? null;
    }

    public function wholesaleFavorite(): HasMany
    {
        return $this->hasMany(WholesaleFavorite::class, 'product_id');
    }

    protected static function boot() {
        parent::boot();
        static::creating(function ($query) {
            $query->meta_title = $query->meta_title ?? '';
            $query->product_name_web = $query->product_name_web ?? '';
        });
        static::updating(function ($query) {
            $query->meta_title = $query->meta_title ?? '';
            $query->product_name_web = $query->product_name_web ?? '';
        });
    }

    public function getWholesaleQuantityType($quantity): array
    {
        if ($quantity < 10) {
            return [
                'text' => 'Мало',
                'color' => '#E63534',
            ];
        }

        if ($quantity < 50) {
            return [
                'text' => 'Среднее кол-во',
                'color' => '#E3B511',
            ];
        }

        return [
            'text' => 'Много',
            'color' => '#2A972E',
        ];
    }

    public function getChips(): array
    {
        $chips = [];
        if (Carbon::parse($this->created_at)->gte(today()->subMonth())) {
            $chips[] = [
                'type' => 'new',
                'text' => 'New'
            ];
        }
        if ($this->manufacturer_id === 608) {
            $chips[] = [
                'type' => 'stock',
                'text' => '7+1'
            ];
        }
        return $chips;
    }

    public function optDailyDeals(): HasOne
    {
        return $this->hasOne(OptDailyDealProduct::class, 'product_id');
    }

    public function scopeWithActiveDailyDeals($query)
    {
        return $query->with(['optDailyDeals' => function ($query) {
            $query->whereHas('dailyDeal', function ($subQuery) {
                $subQuery->where('active_from', '<=', now())
                    ->where('active_to', '>=', now());
            });
        }]);
    }

    public function loadActiveDailyDeals(): void
    {
        $this->load(['optDailyDeals' => function ($query) {
            $query->whereHas('dailyDeal', function ($subQuery) {
                $subQuery->where('active_from', '<=', now())
                    ->where('active_to', '>=', now());
            });
        }]);
    }

    public function retrieveActiveDiscountPercent(): int
    {
        if ($this->optDailyDeals && $this->optDailyDeals->discount > 0) {
            return $this->optDailyDeals->discount;
        }
        return 0;
    }
}
