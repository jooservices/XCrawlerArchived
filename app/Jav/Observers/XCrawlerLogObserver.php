<?php

namespace App\Jav\Observers;

use App\Models\XCrawlerLog;
use App\Notifications\CrawlingFailed;

class XCrawlerLogObserver
{
    /**
     * Handle the XCrawlerLog "created" event.
     *
     * @param XCrawlerLog $XCrawlerLog
     * @return void
     */
    public function created(XCrawlerLog $XCrawlerLog)
    {
        if (!$XCrawlerLog->succeed) {
            $XCrawlerLog->notify(new CrawlingFailed());
        }
    }
}
