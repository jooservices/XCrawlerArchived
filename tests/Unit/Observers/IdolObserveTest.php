<?php

namespace Tests\Unit\Observers;

use App\Mail\WordPressIdolPost;
use App\Models\Idol;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class IdolObserveTest extends TestCase
{
    public function test_created_observer()
    {
        Mail::fake();
        Idol::factory()->create();
        Mail::assertQueued(WordPressIdolPost::class);
    }
}
