<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NowPromotion extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = [
        'id',
        'discount_amount',
        'max_discount_amount' ,
        'min_order_amount' ,
        'discount_on_type' ,
        'discount_type' ,
        'discount_value_type',
        'expired',
        'home_title' ,
        'promotion_type' ,
    ];

    public function restaurants()
    {
        return $this->belongsToMany(NowRestaurant::class, 'now_promotion_restaurant', 'restaurant_id', 'promotion_id');
    }
}
