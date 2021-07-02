<?php

namespace App\Now\Tests\Unit\Models;

use App\Models\NowPromotion;
use App\Models\NowRestaurant;
use Tests\TestCase;

class TestRestaurantModel extends TestCase
{
    public function test_promotion_relationship()
    {
        $restaurant = NowRestaurant::factory()->create();
        NowPromotion::factory()->create();
        $promotion = NowPromotion::factory()->create();
        $restaurant->promotions()->syncWithoutDetaching([$promotion->id]);

        $this->assertEquals(1, $restaurant->promotions->count());
        $this->assertEquals($promotion->id, $restaurant->promotions->first()->id);
    }
}
