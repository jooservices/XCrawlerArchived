<?php

namespace App\Flickr\Tests\Unit\Services;

use App\Services\Client\FlickrClientResponse;
use Tests\TestCase;

class FlickrClientResponseTest extends TestCase
{
    public function test_succeed()
    {
        $response = new FlickrClientResponse();
        $response->endpoint = $this->faker->url;
        $response->request = [];
        $response->body = json_encode([
            'stat' => 'ok'
        ], true);
        $response->loadData();

        $this->assertTrue($response->isSuccessful());
    }

    public function test_failed()
    {
        $response = new FlickrClientResponse();
        $response->endpoint = $this->faker->url;
        $response->request = [];
        $response->body = json_encode([
            'stat' => 'fail'
        ], true);
        $response->loadData();

        $this->assertFalse($response->isSuccessful());
    }
}
