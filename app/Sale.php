<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use function foo\func;

/**
 * App\Sale
 *
 * @property int $id
 * @property int $client_id
 * @property int $store_id
 * @property int $user_id
 * @property int $discount
 * @property int $kaspi_red
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $balance
 * @property int|null $partner_id
 * @property int $payment_type
 * @property-read \App\Client $client
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\SaleProduct[] $products
 * @property-read int|null $products_count
 * @property-read \App\Store $store
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Sale byDate($date)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Sale newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Sale query()
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereKaspiRed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale wherePartnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale wherePaymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereUserId($value)
 * @mixin \Eloquent
 * @property-read mixed $product_price
 * @property-read mixed $purchase_price
 * @property-read mixed $date
 * @method static \Illuminate\Database\Eloquent\Builder|Sale report()
 * @property-read mixed $discount_percent
 * @property-read mixed $final_price
 * @property-read mixed $margin
 * @method static \Illuminate\Database\Eloquent\Builder|Sale reportDate($dates)
 */
class Sale extends Model
{
    protected $guarded = [];

    protected $casts = [
        'id' => 'integer',
        'discount' => 'integer',
        'payment_type' => 'integer',
        'user_id' => 'integer',
        'store_id' => 'integer',
        'client_id' => 'integer',
        'balance' => 'integer',
        'kaspi_red' => 'boolean',
        'partner_id' => 'integer',
    ];

    const CLIENT_CASHBACK_PERCENT = 0.01;
    const PARTNER_CASHBACK_PERCENT = 0.05;
    const KASPI_RED_PERCENT = 0.11;

    const PAYMENT_TYPES = [
        0 => [
            'name' => 'Наличные'
        ],
        1 => [
            'name' => 'Безналичная оплата'
        ],
        2 => [
            'name' => 'Kaspi RED/PayDa!'
        ],
        3 => [
            'name' => 'Перевод на карту'
        ]
    ];

    public function client() {
        return $this->belongsTo('App\Client', 'client_id')->withDefault([
            'client_name' => 'Гость',
            'id' => -1
        ])->withTrashed();
    }

    public function store() {
        return $this->belongsTo('App\Store', 'store_id')->withTrashed();
    }

    public function products() {
        return $this->hasMany('App\SaleProduct', 'sale_id');
    }


    public function product_count() {
        return $this->hasMany('App\SaleProduct', 'sale_id')->groupBy(['product_id', 'sale_id'])->count();
    }

    public function user() {
        return $this->belongsTo('App\User', 'user_id')->withDefault([
            'name' => 'Неизвестно',
            'id' => -1
        ])->withTrashed();
    }

    public function scopeByDate($q, $date) {
        $q->where('created_at', $date);
    }

    public function scopeReportDate($q, $dates) {
        return $q->whereDate('created_at', '>=', $dates[0])->whereDate('created_at', '<=', $dates[1])->orderByDesc('created_at');
    }

    public function scopeReport($q) {
        return $q->with(['client', 'user', 'store','products.product', 'products'])
            ->with(['products.product.product:id,product_name,manufacturer_id'])
            ->with(['products.product.product.manufacturer', 'products.product.product.attributes', 'products.product.attributes'])
            /*->with(['products' => function ($query) {
                return $query->groupBy(['product_id', 'sale_id', 'discount'])->addSelect(\DB::raw('*, count(*) as product_count'));
            }])*/;
    }

    public function getPurchasePriceAttribute() {
        return intval($this->products->reduce(function ($a, $c) {
            return $a + $c->purchase_price;
        }, 0));
    }

    public function getProductPriceAttribute() {
        return intval($this->products->reduce(function ($a, $c) {
            return $a + $c->product_price;
        }, 0));
    }

    public function getDiscountPercentAttribute() {
        return $this->discount / 100;
    }

    public function getFinalPriceAttribute() {
        $price = intval($this->products->reduce(function ($a, $c) {
            return $a + $c->final_price;
        }, 0));;
        if ($this->kaspi_red) {
            $price -= $price * self::KASPI_RED_PERCENT;
        }
        return ceil($price - $this->balance);
    }

    public function getMarginAttribute() {
        return intval($this->products->reduce(function ($a, $c) {
            return $a + $c->margin;
        }, 0));
    }

    public function getDateAttribute() {
        return Carbon::parse($this->created_at)->format('d.m.Y H:i:s');
    }

    protected static function boot() {
        parent::boot();
        static::creating(function ($query) {
            $query->client_id = $query->client_id ?? -1;
        });
        static::updating(function ($query) {
            $query->client_id = $query->client_id ?? -1;
        });
    }
}
