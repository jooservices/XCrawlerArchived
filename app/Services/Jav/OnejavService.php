<?php

namespace App\Services\Jav;

use App\Jobs\Jav\OnejavFetchDailyJob;
use App\Jobs\Jav\OnejavFetchNewJob;
use App\Models\Onejav;
use App\Models\TemporaryUrl;

class OnejavService extends AbstractJavService
{
    public const SOURCE = 'onejav';

    public function daily()
    {
        OnejavFetchDailyJob::dispatch();
    }

    public function released()
    {
        if (!$temporary = TemporaryUrl::bySource(self::SOURCE)->byState(TemporaryUrl::STATE_INIT)->first()) {
            $temporary = TemporaryUrl::create([
                'url' => Onejav::NEW_URL,
                'source' => self::SOURCE, // For Onejav we only save 1 temporary
                'data' => ['current_page' => 1],
                'state_code' => TemporaryUrl::STATE_INIT
            ]);
        }

        OnejavFetchNewJob::dispatch($temporary);
    }
}
