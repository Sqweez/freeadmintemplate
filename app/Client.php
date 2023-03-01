<?php

namespace App;

use App\v2\Models\MailingRecepient;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Client
 *
 * @property int $id
 * @property string $client_name
 * @property string $client_phone
 * @property string $client_card
 * @property int $client_discount
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $password
 * @property string $address
 * @property string $user_token
 * @property string $email
 * @property int $is_partner
 * @property int $client_city
 * @property string|null $partner_expired_at
 * @property Carbon|null $deleted_at
 * @property-read \App\Store $city
 * @property-read mixed $balance
 * @property-read Collection|Order[] $orders
 * @property-read int|null $orders_count
 * @property-read Collection|Sale[] $partner_sales
 * @property-read int|null $partner_sales_count
 * @property-read Collection|Sale[] $purchases
 * @property-read int|null $purchases_count
 * @property-read Collection|ClientSale[] $sales
 * @property-read int|null $sales_count
 * @property-read Collection|ClientTransaction[] $transactions
 * @property-read int|null $transactions_count
 * @method static \Illuminate\Database\Eloquent\Builder|Client newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Client newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Client ofPhone($phone)
 * @method static \Illuminate\Database\Eloquent\Builder|Client ofToken($token)
 * @method static \Illuminate\Database\Query\Builder|Client onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Client partner()
 * @method static \Illuminate\Database\Eloquent\Builder|Client query()
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereClientCard($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereClientCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereClientDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereClientName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereClientPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereIsPartner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client wherePartnerExpiredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereUserToken($value)
 * @method static \Illuminate\Database\Query\Builder|Client withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Client withoutTrashed()
 * @mixin \Eloquent
 * @property-read Collection|\App\Promocode[] $promocodes
 * @property-read int|null $promocodes_count
 * @property int $loyalty_id
 * @property string $photo
 * @property string $job
 * @property string $instagram
 * @property-read \App\v2\Models\Loyalty $loyalty
 * @method static \Illuminate\Database\Eloquent\Builder|Client platinumClients()
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereInstagram($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereJob($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereLoyaltyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client wherePhoto($value)
 * @property string|null $birth_date
 * @property string $gender
 * @property-read mixed $current_month_sales_amount
 * @property-read mixed $total_sales_amount
 * @property-read Collection|\App\Sale[] $real_sales
 * @property-read int|null $real_sales_count
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereGender($value)
 * @property bool $is_wholesale_buyer
 * @property int|null $wholesale_type_id
 * @property-read mixed $wholesale_type
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereIsWholesaleBuyer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereWholesaleTypeId($value)
 * @property string $wholesale_status
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereWholesaleStatus($value)
 * @property-read string $wholesale_status_text
 * @property bool $is_kaspi
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereIsKaspi($value)
 * @property-read MailingRecepient|null $lastMailing
 * @property-read Collection|MailingRecepient[] $recipients
 * @property-read int|null $recipients_count
 * @property int $cached_balance
 * @property int $cached_total_sale_amount
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereCachedBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereCachedTotalSaleAmount($value)
 */
class Client extends Model
{

    use SoftDeletes;

    protected $casts = [
        'client_city' => 'integer',
        'is_wholesale_buyer' => 'boolean',
        'is_kaspi' => 'boolean'
    ];

    protected $guarded = [];

    const WHOLE_SALE_TYPES = [
        0 => 'Спортивный зал',
        1 => 'Магазин',
        2 => 'Кофейня',
        3 => 'Прочее',
    ];

    const WHOLE_SALE_STATUS = [
        0 => 'Сотрудничает',
        1 => 'Думает',
        2 => 'Не сотрудничает',
    ];

    const DISCOUNT = [
        [
            'amount' => 30000,
            'discount' => 10
        ],
        [
            'amount' => 15000,
            'discount' => 5
        ]
    ];

    const TOTAL_DISCOUNT = [
        [
            'amount' => 60000,
            'discount' => 10
        ],
        [
            'amount' => 30000,
            'discount' => 5
        ]
    ];

    const GENDERS = [
        'M' => 'Мужчина',
        'F' => 'Женщина',
        'U' => 'Не определен'
    ];

    const PLATINUM_CLIENT_MONTHLY_THRESHOLD = 50000;

    public function recipients(): HasMany {
        return $this->hasMany(MailingRecepient::class, 'client_id');
    }

    public function lastMailing(): \Illuminate\Database\Eloquent\Relations\HasOne {
        return $this
            ->hasOne(MailingRecepient::class, 'client_id')
            ->latest();
    }

    public function transactions() {
        return $this->hasMany('App\ClientTransaction', 'client_id');
    }

    public function sales() {
        return $this->hasMany('App\ClientSale', 'client_id');
    }

    public function real_sales() {
        return $this->hasMany('App\Sale')->orderByDesc('created_at');
    }

    public function promocodes() {
        return $this->hasMany('App\Promocode', 'client_id');
    }

    public function orders() {
        return $this->hasMany('App\Order', 'client_id');
    }

    public function city() {
        return $this->belongsTo('App\v2\Models\City', 'client_city')->withDefault([
            'name' => 'Город не указан'
        ]);
    }

    public function loyalty() {
        return $this->belongsTo('App\v2\Models\Loyalty', 'loyalty_id');
    }

    public function partner_sales() {
        return $this->hasMany('App\Sale', 'partner_id');
    }

    public function getBalanceAttribute() {
        return intval($this->transactions->sum('amount'));
    }

    public function getTotalSalesAmountAttribute() {
        return $this->sales->sum('amount');
    }

    public function getCurrentMonthSalesAmountAttribute() {
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();
        $sales = $this->sales->filter(function ($sale) use ($startDate, $endDate) {
           $date = Carbon::parse($sale['created_at']);
           return $date->lessThanOrEqualTo($endDate) && $date->greaterThanOrEqualTo($startDate);
        });
        return $sales->sum('amount');
    }

    public function purchases(): HasMany {
        return $this->hasMany('App\Sale', 'client_id');
    }

    public function scopeOfPhone($q, $phone) {
        $q->where('client_phone', $phone);
    }

    public function scopeOfToken($q, $token) {
        $q->where('user_token', $token);
    }

    public function scopePartner($query) {
        $query->where('is_partner', true);
    }

    public function calculateDiscountPercent() {
        $total = $this->cached_total_sale_amount;
        $discountByAmount = collect(self::TOTAL_DISCOUNT)->filter(function ($item) use ($total) {
            return $total >= $item['amount'];
        })->first()['discount'] ?? 0;
        return min(max($this->client_discount, $discountByAmount), 100);
    }

    public function scopePlatinumClients($query) {
        $query->whereHas('loyalty', function ($q) {
            return $q->whereId(2);
        });
    }

    public function getWholesaleTypeAttribute(): string {
        return !$this->is_wholesale_buyer
            ? 'Обычный покупатель'
            : (self::WHOLE_SALE_TYPES[$this->wholesale_type_id - 1] ?? 'Неизвестно'
            );
    }

    public function getWholesaleStatusTextAttribute(): string {
        return !$this->is_wholesale_buyer
            ? 'Обычный покупатель'
            : (self::WHOLE_SALE_STATUS[$this->wholesale_status - 1] ?? 'Неизвестно'
            );
    }

    public function setPhotoAttribute($value) {
        $this->attributes['photo'] = $value !== null ?  $value : '';
    }

    public function setJobAttribute($value) {
        $this->attributes['job'] = $value !== null ?  $value : '';
    }

    public function setInstagramAttribute($value) {
        $this->attributes['instagram'] = $value !== null ?  $value : '';
    }

    protected static function boot() {
        parent::boot();
        static::creating(function ($query) {
            $query->loyalty_id = $query->loyalty_id ?? 1;
        });
        static::updating(function ($query) {
            $query->loyalty_id = $query->loyalty_id ?? 1;
        });
    }
}
