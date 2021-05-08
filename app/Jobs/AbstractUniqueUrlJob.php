<?php

namespace App\Jobs;

use App\Models\TemporaryUrl;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class AbstractUniqueUrlJob  implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public int $uniqueFor = 900;

    /**
     * @var TemporaryUrl|string $url
     */
    protected $url;

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId(): string
    {
        if ($this->url instanceof TemporaryUrl)
        {
            return md5(serialize([$this->url->url, $this->url->source, $this->url->data, app()->environment('testing') ? Carbon::now() : null]));
        }

        return $this->url;
    }
}
