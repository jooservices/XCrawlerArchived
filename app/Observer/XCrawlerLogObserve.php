<?php

namespace App\Observer;

use App\Models\XCrawlerLog;
use App\Notifications\CrawlingFailed;

class XCrawlerLogObserve
{
    /**
     * Handle the XCrawlerLog "created" event.
     *
     * @param \App\Models\XCrawlerLog $XCrawlerLog
     * @return void
     */
    public function created(XCrawlerLog $XCrawlerLog)
    {
        if (!$XCrawlerLog->succeed) {
            $XCrawlerLog->notify(new CrawlingFailed());
        }
    }
}
