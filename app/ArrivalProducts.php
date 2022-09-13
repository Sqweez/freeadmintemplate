<?php

namespace App;

use App\v2\Models\BookingProduct;
use App\v2\Models\ProductSku;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * App\ArrivalProducts
 *
 * @property int $id
 * @property int $product_id
 * @property int $arrival_id
 * @property int $count
 * @property int $purchase_price
 * @property-read ProductSku $product
 * @method static \Illuminate\Database\Eloquent\Builder|ArrivalProducts newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArrivalProducts newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArrivalProducts query()
 * @method static \Illuminate\Database\Eloquent\Builder|ArrivalProducts whereArrivalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArrivalProducts whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArrivalProducts whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArrivalProducts whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArrivalProducts wherePurchasePrice($value)
 * @mixin Eloquent
 * @property-read Arrival $arrival
 * @property-read Collection|BookingProduct[] $bookingProducts
 * @property-read int|null $booking_products_count
 * @property-read mixed $available_booking_count
 * @property-read mixed $booking_count
 * @property-read mixed $is_new_product
 */
class ArrivalProducts extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    public function product() {
        return $this->belongsTo(ProductSku::class, 'product_id')->withTrashed();
    }

    public function arrival(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
        return $this->belongsTo('App\Arrival');
    }

    public function bookingProducts() {
        return $this->hasMany('App\v2\Models\BookingProduct', 'arrival_product_id');
    }

    public function getIsNewProductAttribute() {
        $selfDate = Carbon::parse($this->product->created_at)->startOfDay();
        $arrivalDate = Carbon::parse($this->arrival->created_at)->startOfDay();
        return $selfDate->eq($arrivalDate);
    }

    public function getAvailableBookingCountAttribute() {
        return $this->attributes['count'] - $this->bookingProducts->reduce(function ($a, $c) {
                return $a + $c['count'];
            }, 0);
    }

    public function getBookingCountAttribute() {
        return $this->bookingProducts->reduce(function ($a, $c) {
            return $a + $c['count'];
        }, 0);
    }

}
