<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnalyticSearch extends Model
{
    protected $fillable = ['search', 'client_id'];

    public function client() {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
