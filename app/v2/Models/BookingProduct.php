<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\v2\Models\BookingProduct
 *
 * @property int $id
 * @property int $product_id
 * @property int $booking_id
 * @property int $arrival_product_id
 * @property int $product_price
 * @property int $count
 * @property-read \App\v2\Models\Booking $booking
 * @method static \Illuminate\Database\Eloquent\Builder|BookingProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BookingProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BookingProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|BookingProduct whereArrivalProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookingProduct whereBookingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookingProduct whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookingProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookingProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookingProduct whereProductPrice($value)
 * @mixin \Eloquent
 * @property int $purchase_price
 * @property-read \App\v2\Models\ProductSku $product
 * @method static \Illuminate\Database\Eloquent\Builder|BookingProduct wherePurchasePrice($value)
 */
class BookingProduct extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];
    protected $casts = [
        'product_id' => 'integer',
        'count' => 'integer',
        'booking_id' => 'integer',
        'product_price' => 'integer'
    ];

    public function product() {
        return $this->belongsTo('App\v2\Models\ProductSku', 'product_id');
    }

    public function booking() {
        return $this->belongsTo('App\v2\Models\Booking');
    }


}
