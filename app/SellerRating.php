<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\SellerRating
 *
 * @property int $id
 * @property int $seller_id
 * @property int $criteria_id
 * @property int $rating
 * @property string $user_token
 * @property-read \App\RatingCriteria $criteria
 * @method static \Illuminate\Database\Eloquent\Builder|SellerRating newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SellerRating newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SellerRating query()
 * @method static \Illuminate\Database\Eloquent\Builder|SellerRating whereCriteriaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SellerRating whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SellerRating whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SellerRating whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SellerRating whereUserToken($value)
 * @mixin \Eloquent
 */
class SellerRating extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    public function criteria() {
        return $this->belongsTo('App\RatingCriteria');
    }

    public function criteria_name() {
        $this->criteria()->criteria;
    }
}
