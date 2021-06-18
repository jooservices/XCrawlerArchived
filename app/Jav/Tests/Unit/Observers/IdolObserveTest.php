<?php

namespace App\Jav\Tests\Unit\Observers;

use App\Jav\Mail\WordPressIdolPost;
use App\Models\Idol;
use App\Models\WordPressPost;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class IdolObserveTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_created_observer()
    {
        Idol::factory()->create();
        Mail::assertQueued(WordPressIdolPost::class);
    }

    public function test_created_observer_already_posted()
    {
        $wordPressPost = WordPressPost::factory()->create();
        Idol::factory()->create(['name' => $wordPressPost->title]);
        Mail::assertNotQueued(WordPressIdolPost::class);
    }
}
