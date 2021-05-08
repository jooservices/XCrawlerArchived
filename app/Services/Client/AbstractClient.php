<?php

namespace App\Services\Client;

use App\Services\Client\Domain\ResponseInterface;
use Carbon\CarbonImmutable;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\MessageFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

abstract class AbstractClient implements Domain\ClientInterface
{
    protected Logger $logger;
    protected array $headers = [];
    protected string $contentType = 'json';
    protected ClientInterface $client;
    protected ResponseInterface $response;

    public function init(
        string $name = null,
        array $options = [],
        int $maxRetries = 3,
        int $delayInSec = 1,
        int $minErrorCode = 500,
        string $loggingFormat = MessageFormatter::CLF
    ): Domain\ClientInterface
    {
        $serviceName = $name ?? 'xclient';
        $this->logger = new Logger($serviceName);
        $logPath = storage_path('logs/' . strtolower($serviceName) . '/' . CarbonImmutable::now()->format('Y-m-d') . '.log');
        $this->logger->pushHandler(
            new StreamHandler($logPath)
        );

        $factory = new Factory($this->logger);
        $this->client = $factory
            ->enableRetries($maxRetries, $delayInSec, $minErrorCode)
            ->enableLogging($loggingFormat)
            ->withOptions($options)
            ->enableCache()
            ->make();

        return $this;
    }

    /**
     * Get the Response
     *
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * Set the headers
     *
     * @param array $headers
     *
     * @return $this
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * Set the content type
     *
     * @param string $contentType
     *
     * @return $this
     */
    public function setContentType(string $contentType = 'json'): self
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * GET Request
     *
     * @param string $endpoint
     * @param array $payload
     * @return ResponseInterface
     */
    public function get(string $endpoint, array $payload = []): ResponseInterface
    {
        return $this->request($endpoint, $payload, 'GET');
    }

    /**
     * POST Request
     *
     * @param string $endpoint
     * @param array $payload
     * @return ResponseInterface
     */
    public function post(string $endpoint, array $payload = []): ResponseInterface
    {
        return $this->request($endpoint, $payload, 'POST');
    }

    /**
     * PUT Request
     *
     * @param string $endpoint
     * @param array $payload
     * @return ResponseInterface
     */
    public function put(string $endpoint, array $payload = []): ResponseInterface
    {
        return $this->request($endpoint, $payload, 'PUT');
    }

    /**
     * PATCH Request
     *
     * @param string $endpoint
     * @param array $payload
     * @return ResponseInterface
     */
    public function patch(string $endpoint, array $payload = []): ResponseInterface
    {
        return $this->request($endpoint, $payload, 'PATCH');
    }

    /**
     * DELETE Request
     *
     * @param string $endpoint
     * @param array $payload
     * @return ResponseInterface
     */
    public function delete(string $endpoint, array $payload = []): ResponseInterface
    {
        return $this->request($endpoint, $payload, 'DELETE');
    }

    /**
     * Perform the request
     *
     * @param string $endpoint
     * @param array $payload
     * @param string $method
     * @return ResponseInterface
     */
    protected function request(string $endpoint, array $payload = [], string $method = 'GET')
    {
        $options = [
            'headers' => $this->headers,
        ];

        if (isset($this->headers['auth'])) {
            $options['auth'] = $this->headers['auth'];
        }
        $payload = $this->convertToUTF8($payload);

        if ($method == 'GET') {
            $options['query'] = $payload;
        } else {
            switch ($this->contentType) {
                case 'application/x-www-form-urlencoded':
                    $options['form_params'] = $payload;
                    break;
                default:
                case 'json':
                    $options['json'] = $payload;
                    break;
            }
        }

        $this->response = app(ResponseInterface::class);
        $this->response->endpoint = $endpoint;
        $this->response->request = $payload;

        try {
            $data = $this->client->request($method, $endpoint, $options)->getBody();
            $this->response->body = (string)$data;
            $this->response->loadData();
        } catch (GuzzleException | ClientException $e) {
            $this->logger->error($e->getMessage());
            $this->response->responseSuccess = false;
            $this->response->responseCode = $e->getCode();
            $this->response->responseMessage = $e->getMessage();
            $this->response->body = $e->getResponse()->getBody()->getContents();
        } finally {
            return $this->response;
        }
    }

    /**
     * Sanitize payload to UTF-8
     *
     * @param array $array
     *
     * @return array
     */
    private function convertToUTF8(array $array): array
    {
        array_walk_recursive($array, function (&$item) {
            if (!mb_detect_encoding($item, 'utf-8', true)) {
                $item = utf8_encode($item);
            }
        });

        return $array;
    }
}
