<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NowDistrict extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = [
        'id',
        'city_id',
        'name',
        'url_rewrite_name',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'id' => 'integer',
        'city_id' => 'integer',
        'name' => 'string',
        'url_rewrite_name' => 'string',
        'latitude' => 'float',
        'longitude' => 'float',
    ];
}
