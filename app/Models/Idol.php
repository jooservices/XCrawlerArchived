<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $name
 * @property array $alias
 * @property string $birthday
 * @property string $blood_type
 * @property string $city
 * @property integer $height
 * @property integer $breast
 * @property integer $waist
 * @property integer $hips
 * @property string $cover
 * @property integer $favorite
 * @package App\Models
 */
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

    protected $casts = [
        'name' => 'string',
        'alias' => 'array',
        'birthday' => 'datetime:Y-m-d',
        'blood_type' => 'string',
        'city' => 'string',
        'height' => 'integer',
        'breast' => 'integer',
        'waist' => 'integer',
        'hips' => 'integer',
        'cover' => 'string',
        'favorite' => 'integer',
    ];
}
