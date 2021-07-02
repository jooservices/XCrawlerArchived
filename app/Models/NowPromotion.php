<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NowPromotion extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = [
        'id'
    ];

    public function restaurants()
    {
        return $this->belongsToMany(NowRestaurant::class, 'now_promotion_restaurant', 'restaurant_id', 'promotion_id');
    }
}
