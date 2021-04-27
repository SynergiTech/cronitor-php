<?php

namespace SynergiTech\Cronitor\Tests\Telemetry;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use SynergiTech\Cronitor\Client;
use SynergiTech\Cronitor\Telemetry\EchoExceptionHandler;
use SynergiTech\Cronitor\Telemetry\Event;
use SynergiTech\Cronitor\Tests\TestCase;

class EchoExceptionHandlerTest extends TestCase
{
    public function testWithExceptionHandler(): void
    {
        $mock = new MockHandler([
            new RequestException('test-error', new Request('GET', '')),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $guzzle = new GuzzleClient([
            'handler' => $handlerStack,
        ]);

        $client = new Client('', $guzzle);
        $client->telemetry()->withExceptionHandler(new EchoExceptionHandler());

        $this->expectOutputString('GuzzleHttp\Exception\RequestException: test-error');

        $client->telemetry()->sendEvent('', new Event('run'));
    }
}
