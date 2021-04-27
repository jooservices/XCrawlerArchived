<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XCityIdol extends Model
{
    use HasFactory;

    public const HOMEPAGE_URL = 'https://xxx.xcity.jp/idol/';

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
        'waist',
        'hips',
        'state_code'
    ];

    protected $casts = [
        'url' => 'string',
        'name'=> 'string',
        'cover'=> 'string',
        'favorite'=> 'integer',
        'birthday' => 'datetime:Y-m-d',
        'blood_type'=> 'string',
        'city'=> 'string',
        'height'=> 'string',
        'waist'=> 'integer',
        'hips'=> 'integer',
        'state_code'=> 'string',
    ];

    public function scopeForState(Builder $builder, string $state)
    {
        return $builder->where(['state_code' => $state]);
    }
}
