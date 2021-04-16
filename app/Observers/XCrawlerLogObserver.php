<?php

namespace App\Observers;

use App\Models\XCrawlerLog;
use App\Notifications\CrawlingFailed;

class XCrawlerLogObserver
{
    /**
     * Handle the XCrawlerLog "created" event.
     *
     * @param  \App\Models\XCrawlerLog  $XCrawlerLog
     * @return void
     */
    public function created(XCrawlerLog $XCrawlerLog)
    {
        if (!$XCrawlerLog->succeed) {
            $XCrawlerLog->notify(new CrawlingFailed());
        }
    }

    /**
     * Handle the XCrawlerLog "updated" event.
     *
     * @param  \App\Models\XCrawlerLog  $XCrawlerLog
     * @return void
     */
    public function updated(XCrawlerLog $XCrawlerLog)
    {
        //
    }

    /**
     * Handle the XCrawlerLog "deleted" event.
     *
     * @param  \App\Models\XCrawlerLog  $XCrawlerLog
     * @return void
     */
    public function deleted(XCrawlerLog $XCrawlerLog)
    {
        //
    }

    /**
     * Handle the XCrawlerLog "restored" event.
     *
     * @param  \App\Models\XCrawlerLog  $XCrawlerLog
     * @return void
     */
    public function restored(XCrawlerLog $XCrawlerLog)
    {
        //
    }

    /**
     * Handle the XCrawlerLog "force deleted" event.
     *
     * @param  \App\Models\XCrawlerLog  $XCrawlerLog
     * @return void
     */
    public function forceDeleted(XCrawlerLog $XCrawlerLog)
    {
        //
    }
}
