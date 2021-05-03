<?php

namespace App\Services;

use App\Jobs\OnejavFetchDailyJob;
use App\Jobs\OnejavFetchNewJob;
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
        if (!$temporary = TemporaryUrl::forSource(self::SOURCE)->forState(TemporaryUrl::STATE_INIT)->first()) {
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
