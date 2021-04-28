<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $url
 * @property integer $pages
 * @property integer $current
 * @package App\Models
 */
class XCityIdolPage extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'url',
        'pages',
        'current'
    ];

    protected $casts = [
        'url' => 'string',
        'pages' => 'integer',
        'current' => 'integer'
    ];
}
