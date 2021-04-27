<?php

namespace SynergiTech\Cronitor\Tests;

use GuzzleHttp\Client as GuzzleClient;
use SynergiTech\Cronitor\Api\MonitorService;
use SynergiTech\Cronitor\Client;
use SynergiTech\Cronitor\Telemetry\TelemetryService;

class ClientTest extends TestCase
{
    public function testGetApiBaseUrl(): void
    {
        $client = new Client('');

        $this->assertSame('https://cronitor.io/api/', $client->getApiBaseUrl());
    }

    public function testGetHttpClient(): void
    {
        $guzzle = new GuzzleClient();
        $client = new Client('', $guzzle);

        $this->assertSame($guzzle, $client->getHttpClient());
    }

    public function testGetApiKey(): void
    {
        $client = new Client('test-api-key');

        $this->assertSame('test-api-key', $client->getApiKey());
    }

    public function testGetTelemetry(): void
    {
        $client = new Client('');

        $this->assertInstanceOf(TelemetryService::class, $client->telemetry());
    }

    public function testGetMonitors(): void
    {
        $client = new Client('');

        $this->assertInstanceOf(MonitorService::class, $client->monitors());
    }
}
