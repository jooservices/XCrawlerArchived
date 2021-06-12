<?php

namespace Tests;

use App\Services\Client\Domain\ResponseInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Mail;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;
    use CreatesApplication;
    use WithFaker;
    use WithoutMiddleware;

    protected string $fixtures;
    protected bool $seed = true;

    public function setUp(): void
    {
        parent::setUp();

        Mail::fake();
    }

    /**
     * Get Successful Mocked External Service Response
     *
     * @param string $path
     *
     * @return ResponseInterface
     */
    protected function getSuccessfulMockedResponse(string $path): ResponseInterface
    {
        $clientResponse = app(ResponseInterface::class);
        $clientResponse->responseSuccess = true;
        $clientResponse->body = $this->getFixture($path);
        $clientResponse->loadData();

        return $clientResponse;
    }

    /**
     * Get Successful Mocked External Service Response
     *
     * @param string $path
     *
     * @return ResponseInterface
     */
    protected function getErrorMockedResponse(string $path): ResponseInterface
    {
        $clientResponse = app(ResponseInterface::class);
        $clientResponse->responseSuccess = false;
        $clientResponse->body = $this->getFixture($path);
        $clientResponse->loadData();

        return $clientResponse;
    }

    protected function getFixture(string $path): string
    {
        if (!file_exists($this->fixtures . '/' . $path)) {
            return '';
        }

        return file_get_contents($this->fixtures . '/' . $path);
    }
}
