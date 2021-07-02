<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NowCities extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'country_id',
        'name',
        'url_rewrite_name',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'id' => 'integer',
        'country_id' => 'integer',
        'name' => 'string',
        'url_rewrite_name' => 'string',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public $incrementing = false;
}
