<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $url
 * @property string $name
 * @property string $cover
 * @property integer $favorite
 * @property string $birthday
 * @property string $blood_type
 * @property string $city
 * @property integer $height
 * @property integer $breast
 * @property integer $waist
 * @property integer $hips
 * @property string $state_code
 * @package App\Models
 */
class XCityIdol extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const ENDPOINT_URL = 'https://xxx.xcity.jp';
    public const HOMEPAGE_URL = self::ENDPOINT_URL . '/idol/';
    public const PER_PAGE = 30;

    public const STATE_INIT = 'XCIN';
    public const STATE_PROCESSING = 'XCIP';
    public const STATE_COMPLETED = 'XCIC';

    protected $fillable = [
        'url',
        'name',
        'cover',
        'favorite',
        'birthday',
        'blood_type',
        'city',
        'height',
        'breast',
        'waist',
        'hips',
        'state_code'
    ];

    protected $casts = [
        'url' => 'string',
        'name' => 'string',
        'cover' => 'string',
        'favorite' => 'integer',
        'birthday' => 'datetime:Y-m-d',
        'blood_type' => 'string',
        'city' => 'string',
        'height' => 'string',
        'breast' => 'integer',
        'waist' => 'integer',
        'hips' => 'integer',
        'state_code' => 'string',
    ];

    public function scopeForState(Builder $builder, string $state)
    {
        return $builder->where(['state_code' => $state]);
    }
}
