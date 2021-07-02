<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NowSortType extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable=[
        'id',
        'code',
        'constant_id',
        'display_order',
        'is_required_location',
        'name',
        'status',
        'type',
    ];
}
