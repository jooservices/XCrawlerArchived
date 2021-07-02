<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NowServices extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = [
        'id',
        'country_id',
        'name',
        'call_center',
        'code',
        'url',
    ];

    protected $casts = [
        'id' => 'integer',
        'country_id' => 'integer',
        'name' => 'string',
        'call_center' => 'string',
        'code' => 'string',
        'url' => 'string',
    ];
}
