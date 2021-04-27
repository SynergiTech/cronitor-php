<?php

namespace SynergiTech\Cronitor\Telemetry;

use GuzzleHttp\Exception\TransferException;
use SynergiTech\Cronitor\Client;

class TelemetryService
{
    /**
     * @var string
     */
    public static $telemetryBaseUrl = 'https://cronitor.link/p/';

    /**
     * @var Client
     */
    private $client;

    /**
     * @var HostnameResolverInterface|null
     */
    private $hostnameResolver;

    /**
     * @var ExceptionHandlerInterface|null
     */
    private $exceptionHandler;

    public function __construct(
        Client $client,
        ?HostnameResolverInterface $hostnameResolver = null,
        ?ExceptionHandlerInterface $exceptionHandler = null
    ) {
        $this->client = $client;
        $this->withHostnameResolver($hostnameResolver);
        $this->exceptionHandler = $exceptionHandler;
    }

    public function withHostnameResolver(?HostnameResolverInterface $hostnameResolver = null): self
    {
        $this->hostnameResolver = $hostnameResolver ?? new HostnameResolver();
        return $this;
    }

    public function withoutHostnameResolver(): self
    {
        $this->hostnameResolver = null;
        return $this;
    }

    public function withExceptionHandler(ExceptionHandlerInterface $exceptionHandler): self
    {
        $this->exceptionHandler = $exceptionHandler;
        return $this;
    }

    public function withoutExceptionHandler(): self
    {
        $this->exceptionHandler = null;
        return $this;
    }

    public function monitor(string $monitorKey): Monitor
    {
        return new Monitor($this, $monitorKey);
    }

    public function sendEvent(string $monitorKey, Event $event): bool
    {
        if (
            $this->hostnameResolver !== null
            && $event->getHost() === null
        ) {
            $event->setHost($this->hostnameResolver->getHostname());
        }

        $url = self::$telemetryBaseUrl . $this->client->getApiKey() . '/' . $monitorKey;
        $queryString = $this->buildQueryString($event->toArray());

        try {
            $this->client->getHttpClient()->get(
                $url,
                [
                    'query' => $queryString,
                ]
            );
        } catch (TransferException $e) {
            if ($this->exceptionHandler === null) {
                throw $e;
            }

            $this->exceptionHandler->report($e);
            return false;
        }

        return true;
    }

    /**
     * @param array<array{string, string}> $queryParams
     */
    protected function buildQueryString(array $queryParams): string
    {
        $pieces = [];
        foreach ($queryParams as $paramParcel) {
            $pieces[] = urlencode($paramParcel[0]) . '=' . urlencode($paramParcel[1]);
        }

        return implode('&', $pieces);
    }
}
