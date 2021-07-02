<?php

namespace App\Now\Tests\Unit\Models;

use App\Models\NowPromotion;
use App\Models\NowRestaurant;
use Tests\TestCase;

class TestPromotionModel extends TestCase
{
    public function test_promotion_relationship()
    {
        NowRestaurant::factory()->create();
        $restaurant = NowRestaurant::factory()->create();
        NowPromotion::factory()->create();
        $promotion = NowPromotion::factory()->create();

        $restaurant->promotions()->syncWithoutDetaching([$promotion->id]);

        $promotion->refresh();
        $this->assertEquals(1, $promotion->restaurants->count());
        $this->assertEquals($restaurant->id, $promotion->restaurants->first()->id);
    }
}
