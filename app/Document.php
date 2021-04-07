<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $guarded = [
        'id'
    ];

    const DOCUMENT_WAYBILL = 1;
    const DOCUMENT_INVOICE = 2;
    const DOCUMENT_INVOICE_PAYMENT = 3;

    const DOCUMENT_TYPES = [
        1 => 'Накладная',
        2 => 'Счет-фактура',
        3 => 'Счет на оплату'
    ];

}
