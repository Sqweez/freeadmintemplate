<?php

namespace App;

use App\Models\FitClient;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
 * @property bool $is_confirmed
 * @property-read int $kaspi_red_commission
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereIsConfirmed($value)
 * @property int|null $promocode_id
 * @property int $promocode_fixed_amount
 * @property-read \App\Promocode|null $promocode
 * @method static \Illuminate\Database\Eloquent\Builder|Sale wherePromocodeFixedAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale wherePromocodeId($value)
 * @property int $paid_by_barter_balance
 * @method static \Illuminate\Database\Eloquent\Builder|Sale wherePaidByBarterBalance($value)
 * @property int|null $fit_client_id
 * @property-read FitClient|null $fit_client
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereFitClientId($value)
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
        'is_opt' => 'boolean',
        'is_confirmed' => 'boolean'
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
            'name' => 'Безналичная оплата Kaspi'
        ],
        2 => [
            'name' => 'Kaspi RED/PayDa!'
        ],
        3 => [
            'name' => 'Перевод на карту'
        ],
        4 => [
            'name' => 'Kaspi Магазин (ИП Соловьев)'
        ],
        5 => [
            'name' => 'Раздельная оплата'
        ],
        6 => [
            'name' => 'Онлайн-оплата'
        ],
        7 => [
            'name' => 'Почта'
        ],
        8 => [
            'name' => 'Kaspi Магазин (ИП Соловьева)'
        ],
        9 => [
            'name' => 'Безналичная оплата Jysan'
        ]
    ];

    public function client() {
        return $this->belongsTo('App\Client', 'client_id')->withDefault([
            'client_name' => 'Гость',
            'id' => -1
        ])->withTrashed();
    }

    public function fit_client(): BelongsTo
    {
        return $this->belongsTo(FitClient::class, 'fit_client_id');
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

    public function certificate(): HasOne {
        return $this->hasOne('App\v2\Models\Certificate', 'sale_id')->withDefault([
            'amount' => 0
        ]);
    }

    public function promocode(): BelongsTo {
        return $this->belongsTo(Promocode::class)->select(['id', 'promocode', 'client_id', 'title'])->withDefault([
            'promocode' => '-'
        ]);
    }

    public function used_certificate(): HasOne {
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

    public function products(): HasMany {
        return $this->hasMany(SaleProduct::class, 'sale_id');
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

    public function scopeReportDate($q, array $dates) {
        return $q->whereBetween('created_at', $dates)
            ->orderByDesc('created_at');
    }

    public function scopeReport($q) {
        return $q->with(['client:id,client_name,is_wholesale_buyer', 'fit_client:id,name','user:id,name,store_id', 'store:id,name,type_id,city_id', 'store.city_name', 'products.product', 'products'])
            ->with(['products.product.product:id,product_name,manufacturer_id'])
            ->with(['certificate', 'preorder'])
            ->with('promocode')
            ->with('used_certificate')
            ->with(['products.product.product.manufacturer', 'products.product.product.attributes', 'products.product.attributes', 'promocode.partner:id,client_name']);
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

    public function getPurchasePriceAttribute(): int
    {
        return intval($this->products->reduce(function ($a, $c) {
            return $a + $c->purchase_price;
        }, 0));
    }

    public function getProductPriceAttribute(): int
    {
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

        $price += $this->certificate->final_amount;
        if ($this->booking) {
            $price -= $this->booking->paid_sum;
        }

        $price -= $this->promocode_fixed_amount;

        $price -= $this->paid_by_barter_balance;


        return ($price - $this->balance - optional($this->preorder)->amount);
    }

    public function getKaspiRedCommissionAttribute(): int {
        return $this->kaspi_red ? ($this->getFinalPriceAttribute() * self::KASPI_RED_PERCENT) : 0;
    }

    public function getFinalPriceWithoutRedAttribute() {
        $price = ($this->products->reduce(function ($a, $c) {
            return $a + $c->final_price;
        }, 0));

        $price += $this->certificate->final_amount;
        if ($this->booking) {
            $price -= $this->booking->paid_sum;
        }

        return ($price - $this->balance);
    }

    public function getMarginAttribute() {
        $productsMargin = intval($this->products->reduce(function ($a, $c) {
            return $a + $c->margin;
        }, 0));
        return $productsMargin + $this->certificate->final_amount + $this->certificate_margin - $this->getKaspiRedCommissionAttribute();
    }

    public function getDateAttribute(): string
    {
        return Carbon::parse($this->created_at)->format('d.m.Y H:i:s');
    }

    public function getCertificateMarginAttribute() {
        return max(0, $this->used_certificate ? $this->used_certificate->amount - $this->final_price : 0);
    }

    public function image() {
        return $this->morphToMany('App\v2\Models\Image', 'imagable', 'imagable');
    }

    public function getReportURL(): string {
        return sprintf('%s/reports/%s', config('app.url'), $this->id);
    }

    public function getCancelURL(): string {
        return sprintf('%s/api/sales/%s/cancel/full', config('app.url'), $this->id);
    }

    public function getConfirmationURL(): string {
        return sprintf('%s/api/sales/%s/confirm', config('app.url'), $this->id);
    }

    public function getRealPrice()
    {
        return $this->product_price + optional($this->certificate)->final_amount;
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
