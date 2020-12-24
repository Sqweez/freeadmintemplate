<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\AnalyticSearch
 *
 * @property int $id
 * @property string|null $search
 * @property int $client_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Client $client
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticSearch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticSearch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticSearch query()
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticSearch whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticSearch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticSearch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticSearch whereSearch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticSearch whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AnalyticSearch extends Model
{
    protected $fillable = ['search', 'client_id'];

    public function client() {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
