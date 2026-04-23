<?php

declare(strict_types=1);

namespace SefinSdk\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use SefinSdk\Config\CertificateConfig;
use SefinSdk\Config\Environment;
use SefinSdk\Exception\ApiException;
use SefinSdk\Exception\TransportException;

class SefinBaseClient
{
    protected Client $httpClient;

    public function __construct(
        private readonly Environment $environment,
        private readonly CertificateConfig $certificateConfig,
        ?Client $httpClient = null
    ) {
        $this->httpClient = $httpClient ?? new Client([
            'base_uri' => $this->environment->getApiUrl(),
            'timeout' => 30,
            'http_errors' => false,
            ...$this->certificateConfig->toGuzzleOptions(),
        ]);
    }

    /**
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    protected function request(string $method, string $path, array $options = [], int $expectedStatusCode = 200): array
    {
        try {
            $response = $this->httpClient->request($method, ltrim($path, '/'), [
                'headers' => ['Accept' => 'application/json'],
                ...$options,
            ]);
        } catch (GuzzleException $exception) {
            throw TransportException::fromThrowable($exception);
        }

        $statusCode = $response->getStatusCode();
        $body = (string) $response->getBody();
        $decoded = $body !== '' ? json_decode($body, true) : [];
        $payload = is_array($decoded) ? $decoded : [];

        if ($statusCode !== $expectedStatusCode) {
            throw ApiException::fromResponse($statusCode, $payload);
        }

        return $payload;
    }

    protected function requestHead(string $path): bool
    {
        try {
            $response = $this->httpClient->request('HEAD', ltrim($path, '/'), [
                'headers' => ['Accept' => 'application/json'],
            ]);
        } catch (GuzzleException $exception) {
            throw TransportException::fromThrowable($exception);
        }

        $statusCode = $response->getStatusCode();

        if ($statusCode === 200) {
            return true;
        }

        if ($statusCode === 404) {
            return false;
        }

        throw ApiException::fromResponse($statusCode, []);
    }

    public function getEnvironment(): Environment
    {
        return $this->environment;
    }
}
