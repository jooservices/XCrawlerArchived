<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class XCityVideo
 * @package App\Models
 */
class XCityVideo extends AbstractJavMovie
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'cover',
        'sales_date',
        'release_date',
        'item_number',
        'dvd_id',
        'description',
        'time',
        'director',
        'studio',
        'marker',
        'label',
        'channel',
        'series',
        'gallery',
        'sample',
        'tags',
        'actresses',
        'favorite',
    ];

    protected $casts = [
        'name' => 'string',
        'cover' => 'string',
        'sales_date' => 'datetime:Y-m-d',
        'release_date' => 'datetime:Y-m-d',
        'item_number' => 'string',
        'dvd_id' => 'string',
        'description' => 'string',
        'label' => 'string',
        'channel' => 'string',
        'marker' => 'string',
        'tags' => 'array',
        'actresses' => 'array',
        'gallery' => 'array',
        'favorite' => 'integer'
    ];
}
