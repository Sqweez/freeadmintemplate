<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Seller
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\SellerRating[] $rating
 * @property-read int|null $rating_count
 * @method static \Illuminate\Database\Eloquent\Builder|Seller newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Seller newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Seller query()
 * @method static \Illuminate\Database\Eloquent\Builder|Seller whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Seller whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Seller whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Seller whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Seller whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Seller whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Seller extends Model
{
    protected $guarded = [];

    public function rating() {
        return $this->hasMany('App\SellerRating');
    }

    public function avg_rating() {
        $this->rating()->average('rating');
    }
}
