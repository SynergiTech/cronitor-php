<?php

namespace SynergiTech\Cronitor\Api;

use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;
use SynergiTech\Cronitor\Client;
use SynergiTech\Cronitor\Exception\Api\ValidationException;

abstract class BaseApiService
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return mixed
     */
    protected function httpGet(string $path)
    {
        $response = $this->httpRequest(
            'GET',
            $this->client->getApiBaseUrl() . $path
        );

        return json_decode($response->getBody(), true);
    }

    /**
     * @param array<mixed, mixed> $data
     * @return mixed
     */
    protected function httpPut(string $path, array $data)
    {
        $response = $this->httpRequest(
            'PUT',
            $this->client->getApiBaseUrl() . $path,
            [
                'json' => $data,
            ]
        );

        return json_decode($response->getBody(), true);
    }

    /**
     * @return mixed
     */
    protected function httpDelete(string $path)
    {
        $response = $this->httpRequest(
            'DELETE',
            $this->client->getApiBaseUrl() . $path
        );

        return json_decode($response->getBody(), true);
    }

    /**
     * @param array<string, mixed> $opts
     */
    protected function httpRequest(string $method, string $url, array $opts = []): ResponseInterface
    {
        $opts['auth'] = [$this->client->getApiKey(), ''];

        try {
            return $this->client->getHttpClient()->request($method, $url, $opts);
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $body = (string) $response->getBody();

            $json = json_decode($body, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw $e;
            }

            $validationException = new ValidationException('Validation failed', 0, $e);
            $validationException->setValidationErrors($json);
            throw $validationException;
        }
    }
}
