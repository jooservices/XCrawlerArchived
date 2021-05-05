<?php

namespace Tests\Unit\Notifications;

use App\Models\Favorite;
use App\Models\Idol;
use App\Models\Movie;
use App\Models\Tag;
use App\Notifications\FavoritedMovie;
use App\Services\Client\CrawlerClientResponse;
use App\Services\Client\Domain\ResponseInterface;
use App\Services\Client\XCrawlerClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class TestFavoriteNotifications extends TestCase
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

        Notification::fake();
    }

    public function test_favorite_movie_send_notfication_tag()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('onejav_new.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);

        $sampleItem = json_decode($this->getFixture('onejav_item.json'), true);

        $tag = Tag::factory()->create([
            'name' => $sampleItem['tags'][0]
        ]);

        Favorite::factory()->create([
            'model_id' => $tag->id,
            'model_type' => Tag::class
        ]);

        // Execute new command
        $this->artisan('jav:onejav-new');
        $this->assertDatabaseHas('movies', ['dvd_id' => $sampleItem['dvd_id']]);
        $movie = Movie::where(['dvd_id' => $sampleItem['dvd_id']])->first();
        Notification::assertSentToTimes($movie, FavoritedMovie::class);
    }

    public function test_favorite_movie_send_notfication_idol()
    {
        $this->mocker->method('get')->willReturn($this->getSuccessfulMockedResponse('onejav_new.html'));
        app()->instance(XCrawlerClient::class, $this->mocker);

        $sampleItem = json_decode($this->getFixture('onejav_item.json'), true);

        $idol = Idol::factory()->create([
            'name' => $sampleItem['actresses'][0]
        ]);

        Favorite::factory()->create([
            'model_id' => $idol->id,
            'model_type' => Idol::class
        ]);

        // Execute new command
        $this->artisan('jav:onejav-new');
        $this->assertDatabaseHas('movies', ['dvd_id' => $sampleItem['dvd_id']]);
        $movie = Movie::where(['dvd_id' => $sampleItem['dvd_id']])->first();
        Notification::assertSentToTimes($movie, FavoritedMovie::class);
    }
}
