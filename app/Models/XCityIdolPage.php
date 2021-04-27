<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XCityIdolPage extends Model
{
    use HasFactory;

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
