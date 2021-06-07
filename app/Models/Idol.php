<?php

namespace App\Models;

use App\Models\Traits\HasName;
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
    use HasName;

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

    public function favorite()
    {
        return $this->morphOne(Favorite::class, 'model');
    }
}
