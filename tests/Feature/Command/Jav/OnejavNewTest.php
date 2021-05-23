<?php

namespace Tests\Feature\Command\Jav;

use App\Jobs\Jav\OnejavFetchDailyJob;
use App\Jobs\Jav\OnejavFetchNewJob;
use App\Models\Movie;
use App\Models\Onejav;
use App\Models\TemporaryUrl;
use App\Services\Client\XCrawlerClient;
use App\Services\Crawler\OnejavCrawler;
use App\Services\Jav\OnejavService;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Tests\AbstractCrawlingTest;

class OnejavNewTest extends AbstractCrawlingTest
{
    private array $sampleItem;
    private array $tags;
    private array $actresses;

    public function setUp(): void
    {
        parent::setUp();

        $this->fixtures = __DIR__ . '/../../../Fixtures/Onejav';
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('new.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);

        $this->sampleItem = json_decode($this->getFixture('item.json'), true);
        $this->tags = $this->sampleItem['tags'];
        $this->actresses = $this->sampleItem['actresses'];
        unset($this->sampleItem['tags']);
        unset($this->sampleItem['actresses']);
        unset($this->sampleItem['date']);
    }

    public function test_onejav_new_command()
    {
        $this->artisan('jav:onejav-new');

        $this->assertDatabaseHas('temporary_urls', [
            'url' => Onejav::NEW_URL,
            'source' => OnejavService::SOURCE,
        ]);

        $temporaryUrl = TemporaryUrl::bySource(OnejavService::SOURCE)->byState(TemporaryUrl::STATE_INIT)->first();
        $this->assertEquals(2, $temporaryUrl->data['current_page']);

        // Make sure we have created onejav record for this movie
        $this->assertDatabaseHas('onejav', $this->sampleItem);

        // Try to crawl directly to get items for comparing
        $items = app(OnejavCrawler::class)->getItems(Onejav::NEW_URL);

        // Make sure we have created enough records
        $this->assertEquals($items->count(), Onejav::count());

        // Make sure we have created movie record for this movie
        $this->assertDatabaseHas('movies', ['dvd_id' => $this->sampleItem['dvd_id']]);
        // Make sure we have created enough records
        $this->assertEquals($items->count(), Movie::count());

        // Movie id
        $movie = Movie::where(['dvd_id' => $items->first()->get('dvd_id')])->first();
        $this->assertNotNull($movie->id);

        $this->assertEquals($this->tags, $movie->tags->pluck('name')->toArray());
        $this->assertEquals($this->actresses, $movie->idols->pluck('name')->toArray());

        // Try to run again it'll not duplicate data
        $this->artisan('jav:onejav-new');
        $this->assertEquals($items->count(), Onejav::count());
        $this->assertEquals(Movie::count(), Onejav::count());

        // Test when reached end of page
        $temporaryUrl->update(['data' => ['current_page' => config('services.onejav.pages_count')]]);

        $this->artisan('jav:onejav-new');

        $temporaryUrl->refresh();
        // This new already completed
        $this->assertEquals(TemporaryUrl::STATE_COMPLETED, $temporaryUrl->state_code);

        // Call again will create new TemporaryUrl
        $this->artisan('jav:onejav-new');
        $temporaryUrl = TemporaryUrl::bySource(OnejavService::SOURCE)->byState(TemporaryUrl::STATE_INIT)->first();
        $this->assertEquals(2, $temporaryUrl->data['current_page']);
    }

    public function test_onejav_daily_command()
    {
        Bus::fake();
        $this->artisan('jav:onejav-daily');

        Bus::assertDispatched(OnejavFetchDailyJob::class);
    }

    public function test_onejav_new_command_job()
    {
        Queue::fake();

        $this->artisan('jav:onejav-new');

        Queue::assertPushed(function (OnejavFetchNewJob $job) {
            return $job->url instanceof TemporaryUrl && $job->url->url === Onejav::NEW_URL;
        });
    }
}
