<?php

namespace SynergiTech\Cronitor;

use GuzzleHttp\Client as GuzzleClient;
use SynergiTech\Cronitor\Api\MonitorService;
use SynergiTech\Cronitor\Telemetry\TelemetryService;

class Client
{
    /**
     * @var GuzzleClient
     */
    private $httpClient;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var TelemetryService
     */
    private $telemetryService;

    /**
     * @var MonitorService
     */
    private $monitorService;

    public function __construct(string $apiKey, GuzzleClient $httpClient = null)
    {
        $this->httpClient = $httpClient ?? new GuzzleClient();
        $this->apiKey = $apiKey;
        $this->telemetryService = new TelemetryService($this);
        $this->monitorService = new MonitorService($this);
    }

    public function telemetry(): TelemetryService
    {
        return $this->telemetryService;
    }

    public function monitors(): MonitorService
    {
        return $this->monitorService;
    }

    public function getHttpClient(): GuzzleClient
    {
        return $this->httpClient;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getApiBaseUrl(): string
    {
        return 'https://cronitor.io/api/';
    }
}
