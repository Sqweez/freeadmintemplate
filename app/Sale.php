<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Sale
 *
 * @property int $id
 * @property int $client_id
 * @property int $store_id
 * @property int $user_id
 * @property int $discount
 * @property int $kaspi_red
 * @property string $kaspi_transaction_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $balance
 * @property int|null $partner_id
 * @property int $payment_type
 * @property boolean $is_delivery
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
 * @property array|null $split_payment
 * @property-read \App\v2\Models\Certificate $certificate
 * @property-read mixed $certificate_margin
 * @property-read \App\v2\Models\Certificate|null $used_certificate
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereSplitPayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale reportSupplier($supplierProducts)
 * @property string $comment
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\Image[] $image
 * @property-read int|null $image_count
 * @property-read \App\v2\Models\Preorder $preorder
 * @method static \Illuminate\Database\Eloquent\Builder|Sale physicalSales()
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereComment($value)
 * @property int|null $order_id
 * @property int|null $booking_id
 * @property-read \App\v2\Models\Booking|null $booking
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereBookingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereIsDelivery($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereKaspiTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereOrderId($value)
 * @property bool $is_paid
 * @property bool $is_opt
 * @property-read mixed $final_price_without_red
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereIsOpt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereIsPaid($value)
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
        'split_payment' => 'array',
        'kaspi_transaction_id' => 'array',
        'is_delivery' => 'boolean',
        'is_paid' => 'boolean',
        'is_opt' => 'boolean'
    ];

    protected $appends = [
        'final_price',
        'final_price_without_red'
    ];

    const CLIENT_CASHBACK_PERCENT = 0.01;
    const PARTNER_CASHBACK_PERCENT = 0.05;
    const KASPI_RED_PERCENT = 0.11;
    const KASPI_PAYMENT_TYPE = 4;
    const INTERNET_USER_ID = 2;

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
        ],
        4 => [
            'name' => 'Kaspi Магазин'
        ],
        5 => [
            'name' => 'Раздельная оплата'
        ],
        6 => [
            'name' => 'Онлайн-оплата'
        ],
        7 => [
            'name' => 'Почта'
        ]
    ];

    public function client() {
        return $this->belongsTo('App\Client', 'client_id')->withDefault([
            'client_name' => 'Гость',
            'id' => -1
        ])->withTrashed();
    }

    public function booking() {
        return $this->belongsTo('App\v2\Models\Booking');
    }

    public function preorder() {
        return $this->hasOne('App\v2\Models\Preorder')->withDefault([
            'id' => -1,
            'amount' => 0
        ]);
    }

    public function certificate() {
        return $this->hasOne('App\v2\Models\Certificate', 'sale_id')->withDefault([
            'amount' => 0
        ]);
    }

    public function used_certificate() {
        return $this->hasOne('App\v2\Models\Certificate', 'used_sale_id');
    }

    public function store() {
        return $this->belongsTo('App\Store', 'store_id')
            ->withDefault([
                'name' => 'Iron Addicts - Казахстан',
                'id' => -1,
            ])
            ->withTrashed();
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
        return $q->whereDate('created_at', '>=', $dates[0])
            ->whereDate('created_at', '<=', $dates[1])
            ->orderByDesc('created_at');
    }

    public function scopeReport($q) {
        return $q->with(['client', 'user', 'store','products.product', 'products'])
            ->with(['products.product.product:id,product_name,manufacturer_id'])
            ->with(['certificate', 'preorder'])
            ->with(['products.product.product.manufacturer', 'products.product.product.attributes', 'products.product.attributes']);
    }

    public function scopeReportSupplier($q, $supplierProducts) {
        return $q
            ->whereHas('products.product', function ($query) use ($supplierProducts) {
                return $query->whereIn('product_id', $supplierProducts);
            })
            ->with(['client', 'user', 'store'])
            ->with(['products', 'products.product'])
            ->with(['products.product.product:id,product_name,manufacturer_id'])
            ->with(['certificate'])
            ->with(['products.product.product.manufacturer', 'products.product.product.attributes', 'products.product.attributes']);
    }

    public function scopePhysicalSales($query) {
        return $query->where('user_id', '!=', 2)
            ->where('payment_type', '!=', 4);
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
        $price = ($this->products->reduce(function ($a, $c) {
            return $a + $c->final_price;
        }, 0));
        if ($this->kaspi_red) {
            $price -= $price * self::KASPI_RED_PERCENT;
        }

        $price += $this->certificate->final_amount;
        if ($this->booking) {
            $price -= $this->booking->paid_sum;
        }

        return ceil($price - $this->balance);
    }

    public function getFinalPriceWithoutRedAttribute() {
        $price = ($this->products->reduce(function ($a, $c) {
            return $a + $c->final_price;
        }, 0));

        $price += $this->certificate->final_amount;
        if ($this->booking) {
            $price -= $this->booking->paid_sum;
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

    public function getCertificateMarginAttribute() {
        return max(0, $this->used_certificate ? $this->used_certificate->amount - $this->final_price : 0);
    }

    public function image() {
        return $this->morphToMany('App\v2\Models\Image', 'imagable', 'imagable');
    }

    /* public function setCommentAttribute($value) {
         $this->attributes['comment'] = '';//$value === null ? '' : $value;
     }*/

    protected static function boot() {
        parent::boot();
        static::creating(function ($query) {
            $query->client_id = $query->client_id ?? -1;
            $query->comment = strlen($query->comment) === 0 ? '' : $query->comment;
        });
        static::updating(function ($query) {
            $query->client_id = $query->client_id ?? -1;
        });
    }
}
