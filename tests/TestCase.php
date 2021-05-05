<?php

namespace Tests;

use App\Services\Client\Domain\ResponseInterface;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use WithFaker;
    use WithoutMiddleware;

    protected string $fixtures;

    public function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware();
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
