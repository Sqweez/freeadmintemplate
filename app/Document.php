<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Document
 *
 * @property int $id
 * @property string $document
 * @property int $document_type
 * @property int $document_number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Document newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Document newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Document query()
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereDocument($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereDocumentNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereDocumentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Document whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read mixed $date
 * @property-read mixed $type
 */
class Document extends Model
{
    protected $guarded = [
        'id'
    ];

    protected $appends = [
        'date', 'type'
    ];

    const DOCUMENT_WAYBILL = 1;
    const DOCUMENT_INVOICE = 2;
    const DOCUMENT_INVOICE_PAYMENT = 3;
    const DOCUMENT_PRODUCT_CHECK = 4;

    const DOCUMENT_TYPES = [
        1 => 'Накладная',
        2 => 'Счет-фактура',
        3 => 'Счет на оплату',
        4 => 'Товарный чек'
    ];

    public function getDateAttribute() {
        return Carbon::parse($this->attributes['created_at'])->format('d.m.Y H:i:s');
    }

    public function getTypeAttribute() {
        return self::DOCUMENT_TYPES[$this->attributes['document_type']];
    }

}
