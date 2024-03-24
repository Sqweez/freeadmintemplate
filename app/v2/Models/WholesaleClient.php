<?php

namespace App\v2\Models;

use Eloquent;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\v2\Models\WholesaleClient
 *
 * @property int $id
 * @property string $iin
 * @property string $last_name
 * @property string $first_name
 * @property string|null $patronymic
 * @property int $country_id
 * @property int $city_id
 * @property string $phone
 * @property string $email
 * @property string $password
 * @property string|null $delivery_address
 * @property string|null $company_type
 * @property int $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $access_token
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleClient newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleClient newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleClient query()
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleClient whereAccessToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleClient whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleClient whereCompanyType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleClient whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleClient whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleClient whereDeliveryAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleClient whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleClient whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleClient whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleClient whereIin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleClient whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleClient whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleClient wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleClient wherePatronymic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleClient wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleClient whereUpdatedAt($value)
 * @mixin Eloquent
 */
class WholesaleClient extends Model implements Authenticatable
{
    protected $guarded = ['id'];

    public function getFullNameAttribute(): string
    {
        return trim(
            sprintf("%s %s %s", $this->first_name, $this->last_name, $this->patronymic)
        );
    }

    public function getAuthIdentifierName()
    {
        // TODO: Implement getAuthIdentifierName() method.
    }

    public function getAuthIdentifier()
    {
        // TODO: Implement getAuthIdentifier() method.
    }

    public function getAuthPassword()
    {
        // TODO: Implement getAuthPassword() method.
    }

    public function getRememberToken()
    {
        // TODO: Implement getRememberToken() method.
    }

    public function setRememberToken($value)
    {
        // TODO: Implement setRememberToken() method.
    }

    public function getRememberTokenName()
    {
        // TODO: Implement getRememberTokenName() method.
    }
}
