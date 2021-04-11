<?php

namespace Tests\Unit\Command;

use App\Jobs\OnejavFetchJob;
use App\Models\Idol;
use App\Models\Movie;
use App\Models\Onejav;
use App\Models\Tag;
use App\Services\Client\CrawlerClientResponse;
use App\Services\Client\Domain\ResponseInterface;
use App\Services\Client\XCrawlerClient;
use App\Services\Crawler\OnejavCrawler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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
        $this->fixtures = __DIR__ . '/../../Fixtures/Onejav';
    }

    public function test_onejav_new_command()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('onejav_new.html'));

        app()->instance(XCrawlerClient::class, $this->mocker);
        $crawler = app(OnejavCrawler::class);
        $items = $crawler->getItems(Onejav::NEW_URL, ['page' => 7]);

        $this->artisan('jav:onejav-new');

        $data = json_decode($this->getFixture('onejav_item.json'), true);
        $tags = $data['tags'];
        $actresses = $data['actresses'];

        unset($data['tags']);
        unset($data['actresses']);
        unset($data['date']);

        $this->assertDatabaseHas('onejav', $data);
        $this->assertEquals($items->count(), Onejav::count());
        $this->assertDatabaseHas('movies', [
            'dvd_id' => $data['dvd_id']
        ]);
        $this->assertEquals($items->count(), Movie::count());

        foreach ($tags as $tag) {
            $this->assertDatabaseHas('tags', ['name' => $tag]);
        }

        foreach ($actresses as $actress) {
            $this->assertDatabaseHas('idols', ['name' => $actress]);
        }

        // Movie id
        $movieId = Movie::where(['dvd_id' => $items->first()->get('dvd_id')])->value('id');
        $this->assertNotNull($movieId);
        // Get tag ids
        $ids = DB::table('tags')->whereIn('name', $tags)->pluck('id');
        $this->assertEquals(count($tags), count($ids));
        foreach ($ids as $id) {
            $this->assertDatabaseHas('movie_attributes', [
                'movie_id' => $movieId,
                'model_id' => $id,
                'model_type' => Tag::class
            ]);
        }
        // Get idol ids
        $ids = DB::table('idols')->whereIn('name', $actresses)->pluck('id');
        foreach ($ids as $id) {
            $this->assertDatabaseHas('movie_attributes', [
                'movie_id' => $movieId,
                'model_id' => $id,
                'model_type' => Idol::class
            ]);
        }
        $this->assertEquals(count($actresses), count($ids));

        // No duplicate
        $this->artisan('jav:onejav-new');
        $this->assertEquals($items->count(), Onejav::all()->count());
    }

    public function test_onejav_new_command_job()
    {
        Queue::fake();
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('onejav_new.html'));

        app()->instance(XCrawlerClient::class, $this->mocker);

        $this->artisan('jav:onejav-new');

        Queue::assertPushed(function (OnejavFetchJob $job) {
            return $job->page === 1 && $job->url === Onejav::NEW_URL;
        });
    }
}
