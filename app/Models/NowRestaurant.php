<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NowRestaurant extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;

    protected $fillable = [
        'id',
        'address',
        'name',
        'name_en',
        'parent_category_id',
        'price_from',
        'price_to',
        'rating',
        'total_review',
        'restaurant_url',
        'city_id',
        'district_id',
        'delivery_id',
    ];

    public function promotions()
    {
        return $this->belongsToMany(NowPromotion::class, 'now_promotion_restaurant', 'promotion_id', 'restaurant_id');
    }
}
