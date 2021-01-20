<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
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
 */
class Client extends Model
{

    use SoftDeletes;

    protected $casts = [
        'client_city' => 'integer'
    ];

    protected $guarded = [];

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

    public function transactions() {
        return $this->hasMany('App\ClientTransaction', 'client_id');
    }

    public function sales() {
        return $this->hasMany('App\ClientSale', 'client_id');
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

    public function partner_sales() {
        return $this->hasMany('App\Sale', 'partner_id');
    }

    public function getBalanceAttribute() {
        return intval($this->transactions()->sum('amount'));
    }

    public function purchases() {
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
        $total = $this->sales->sum('amount');
        $discountByAmount = collect(self::DISCOUNT)->search(function ($item) use ($total) {
            return $total >= $item['amount'];
        }) ?? 0;
        return min(max($this->client_discount, $discountByAmount), 100);
    }
}
