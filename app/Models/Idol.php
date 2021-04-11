<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Idol extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'alias',
        'birthday',
        'blood_type',
        'city',
        'height',
        'breast',
        'waist',
        'hips',
        'cover',
        'favorite',
    ];
}
