<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;

class RecordedEventTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $user = User::factory()->create();

        $this->assertDatabaseHas('events', [
            'model_id' => $user->id,
            'model_type' => User::class,
            'event' => 'UserCreated',
        ]);
    }
}
