<?php

namespace Tests\Unit\Command\Jav;

use App\Jobs\Jav\OnejavFetchNewJob;
use App\Models\Movie;
use App\Models\Onejav;
use App\Models\TemporaryUrl;
use App\Services\Client\CrawlerClientResponse;
use App\Services\Client\Domain\ResponseInterface;
use App\Services\Client\XCrawlerClient;
use App\Services\Crawler\OnejavCrawler;
use App\Services\Jav\OnejavService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class OnejavNewTest extends TestCase
{
    use RefreshDatabase;

    private MockObject|XCrawlerClient $mocker;

    public function setUp(): void
    {
        parent::setUp();
        app()->bind(ResponseInterface::class, CrawlerClientResponse::class);
        $this->mocker = $this->getMockBuilder(XCrawlerClient::class)->getMock();
        $this->mocker->method('init')->willReturnSelf();
        $this->mocker->method('setHeaders')->willReturnSelf();
        $this->mocker->method('setContentType')->willReturnSelf();
        $this->fixtures = __DIR__ . '/../../../Fixtures/Onejav';
    }

    public function test_onejav_new_command()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('new.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);

        $this->artisan('jav:onejav-new');

        $this->assertDatabaseHas('temporary_urls', [
            'url' => Onejav::NEW_URL,
            'source' => OnejavService::SOURCE,
        ]);

        $temporaryUrl = TemporaryUrl::bySource(OnejavService::SOURCE)->byState(TemporaryUrl::STATE_INIT)->first();
        $this->assertEquals(2, $temporaryUrl->data['current_page']);

        // We dont need assert queue because we will check queue result

        $sampleItem = json_decode($this->getFixture('item.json'), true);
        $tags = $sampleItem['tags'];

        $actresses = $sampleItem['actresses'];
        unset($sampleItem['tags']);
        unset($sampleItem['actresses']);
        unset($sampleItem['date']);

        // Make sure we have created onejav record for this movie
        $this->assertDatabaseHas('onejav', $sampleItem);

        // Try to crawl directly to get items for comparing
        $items = app(OnejavCrawler::class)->getItems(Onejav::NEW_URL);

        // Make sure we have created enough records
        $this->assertEquals($items->count(), Onejav::count());

        // Make sure we have created movie record for this movie
        $this->assertDatabaseHas('movies', ['dvd_id' => $sampleItem['dvd_id']]);
        // Make sure we have created enough records
        $this->assertEquals($items->count(), Movie::count());

        // Movie id
        $movie = Movie::where(['dvd_id' => $items->first()->get('dvd_id')])->first();
        $this->assertNotNull($movie->id);

        $this->assertEquals($tags, $movie->tags->pluck('name')->toArray());
        $this->assertEquals($actresses, $movie->idols->pluck('name')->toArray());

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
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('new.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);

        $this->artisan('jav:onejav-daily');

        $this->assertDatabaseMissing('temporary_urls', [
            'url' => Onejav::NEW_URL,
            'source' => OnejavService::SOURCE,
        ]);

        // We dont need assert queue because we will check queue result

        $sampleItem = json_decode($this->getFixture('item.json'), true);
        $tags = $sampleItem['tags'];

        $actresses = $sampleItem['actresses'];
        unset($sampleItem['tags']);
        unset($sampleItem['actresses']);
        unset($sampleItem['date']);

        // Make sure we have created onejav record for this movie
        $this->assertDatabaseHas('onejav', $sampleItem);

        // Try to crawl directly to get items for comparing
        $items = app(OnejavCrawler::class)->getItems(Onejav::NEW_URL);

        // Make sure we have created enough records
        $this->assertEquals($items->count(), Onejav::count());

        // Make sure we have created movie record for this movie
        $this->assertDatabaseHas('movies', ['dvd_id' => $sampleItem['dvd_id']]);
        // Make sure we have created enough records
        $this->assertEquals($items->count(), Movie::count());

        // Movie id
        $movie = Movie::where(['dvd_id' => $items->first()->get('dvd_id')])->first();
        $this->assertNotNull($movie->id);

        $this->assertEquals($tags, $movie->tags->pluck('name')->toArray());
        $this->assertEquals($actresses, $movie->idols->pluck('name')->toArray());

        // Try to run again it'll not duplicate data
        $this->artisan('jav:onejav-daily');
        $this->artisan('jav:onejav-daily');
        $this->assertEquals($items->count(), Onejav::all()->count());
    }

    public function test_onejav_new_command_job()
    {
        Queue::fake();
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('new.html'));

        app()->instance(XCrawlerClient::class, $this->mocker);

        $this->artisan('jav:onejav-new');

        Queue::assertPushed(function (OnejavFetchNewJob $job) {
            return $job->url instanceof TemporaryUrl && $job->url->url === Onejav::NEW_URL;
        });
    }
}
