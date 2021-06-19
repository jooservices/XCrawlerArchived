<?php

namespace App\Jav\Tests\Unit\Observers;

use App\Models\XCrawlerLog;
use App\Notifications\CrawlingFailed;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class XCrawlerLogObserverTest extends TestCase
{
    public function test_failed_will_trigger_notification()
    {
        Notification::fake();
        $log = XCrawlerLog::factory()->create([
            'succeed' => false
        ]);

        Notification::assertSentToTimes($log, CrawlingFailed::class);
    }
}
