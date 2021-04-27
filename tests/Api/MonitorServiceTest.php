<?php

namespace SynergiTech\Cronitor\Tests\Api;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use SynergiTech\Cronitor\Api\Monitor;
use SynergiTech\Cronitor\Client;
use SynergiTech\Cronitor\Exception\Api\ValidationException;
use SynergiTech\Cronitor\Tests\TestCase;

class MonitorServiceTest extends TestCase
{
    public function testGet(): void
    {
        $container = [];
        $history = Middleware::history($container);

        $monitorResponse = [
            'assertions' => [],
            'created' => '2021-04-26T12:00:00Z',
            'disabled' => false,
            'grace_seconds' => 100,
            'group' => null,
            'initialized' => true,
            'key' => 'monitor-key',
            'metadata' => json_encode([]),
            'name' => 'Monitor Name',
            'note' => '',
            'passing' => true,
            'paused' => false,
            'platform' => 'platform',
            'realert_interval' => 3,
            'request' => [],
            'running' => true,
            'schedule' => 'every 5 minutes',
            'status' => 'Completed 15 minutes ago',
            'tags' => ['tag1', 'tag2'],
            'timezone' => null,
            'tolerated_failures' => 3,
            'type' => 'job',
        ];

        $mock = new MockHandler([
            new Response(200, [], json_encode($monitorResponse)),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);

        $guzzle = new GuzzleClient([
            'handler' => $handlerStack,
        ]);
        $client = new Client('', $guzzle);
        $monitor = $client->monitors()->get('monitor-key');

        $sentRequest = $container[0]['request'];

        $this->assertSame('/api/monitors/monitor-key', $sentRequest->getUri()->getPath());
        $this->assertInstanceOf(Monitor::class, $monitor);
        $this->assertSame(1619438400, $monitor->getCreated()->getTimestamp());
        $this->assertFalse($monitor->getDisabled());
        $this->assertTrue($monitor->getInitialized());
        $this->assertTrue($monitor->getPassing());
        $this->assertFalse($monitor->getPaused());
        $this->assertTrue($monitor->getRunning());
        $this->assertSame('Completed 15 minutes ago', $monitor->getStatus());
        $this->assertSame([], $monitor->getAssertions());
        $this->assertSame(100, $monitor->getGraceSeconds());
        $this->assertNull($monitor->getGroup());
        $this->assertSame('monitor-key', $monitor->getKey());
        $this->assertSame([], $monitor->getMetadata());
        $this->assertSame('Monitor Name', $monitor->getName());
        $this->assertSame('', $monitor->getNote());
        $this->assertSame([], $monitor->getNotify());
        $this->assertSame('platform', $monitor->getPlatform());
        $this->assertSame('3', $monitor->getRealertInterval());
        $this->assertSame([], $monitor->getRequest());
        $this->assertSame(['tag1', 'tag2'], $monitor->getTags());
        $this->assertNull($monitor->getTimezone());
        $this->assertSame(3, $monitor->getToleratedFailures());
        $this->assertSame('job', $monitor->getType());
        $this->assertSame('every 5 minutes', $monitor->getSchedule());
    }

    public function testList(): void
    {
        $monitorResponse = [
            'assertions' => [],
            'created' => '2021-04-26T12:00:00Z',
            'disabled' => false,
            'grace_seconds' => 100,
            'group' => null,
            'initialized' => true,
            'key' => 'monitor-key',
            'metadata' => json_encode([]),
            'name' => 'Monitor Name',
            'note' => '',
            'passing' => true,
            'paused' => false,
            'platform' => 'platform',
            'realert_interval' => 3,
            'request' => [],
            'running' => true,
            'schedule' => 'every 5 minutes',
            'status' => 'Completed 15 minutes ago',
            'tags' => ['tag1', 'tag2'],
            'timezone' => null,
            'tolerated_failures' => 3,
            'type' => 'job',
        ];

        $mock = new MockHandler([
            new Response(200, [], json_encode([$monitorResponse, $monitorResponse])),
        ]);

        $handlerStack = HandlerStack::create($mock);

        $guzzle = new GuzzleClient([
            'handler' => $handlerStack,
        ]);
        $client = new Client('', $guzzle);
        $monitors = $client->monitors()->list();

        $this->assertContainsOnlyInstancesOf(Monitor::class, $monitors);
    }

    public function testCreate(): void
    {
        $container = [];
        $history = Middleware::history($container);

        $mock = new MockHandler([
            new Response(200),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);

        $guzzle = new GuzzleClient([
            'handler' => $handlerStack,
        ]);
        $client = new Client('', $guzzle);

        $monitor = new Monitor('monitor-key', 'job');
        $client->monitors()->create($monitor);

        $sentRequest = $container[0]['request'];
        $this->assertSame('/api/monitors', $sentRequest->getUri()->getPath());
    }

    public function testCreateWithValidationException(): void
    {
        $validationErrors = [
            'errors' => 'unstructured-error-response',
        ];

        $mock = new MockHandler([
            new Response(400, [], json_encode($validationErrors)),
        ]);

        $handlerStack = HandlerStack::create($mock);

        $guzzle = new GuzzleClient([
            'handler' => $handlerStack,
        ]);
        $client = new Client('', $guzzle);

        $monitor = new Monitor('monitor-key', 'job');

        $caughtException = null;
        try {
            $client->monitors()->create($monitor);
        } catch (ValidationException $e) {
            $caughtException = $e;
        }

        $this->assertInstanceOf(ValidationException::class, $caughtException);
        $this->assertSame(
            [
                'errors' => 'unstructured-error-response',
            ],
            $caughtException->getValidationErrors()
        );
    }

    public function testCreateWithClientError(): void
    {
        $mock = new MockHandler([
            new Response(400, [], ''),
        ]);

        $handlerStack = HandlerStack::create($mock);

        $guzzle = new GuzzleClient([
            'handler' => $handlerStack,
        ]);
        $client = new Client('', $guzzle);

        $monitor = new Monitor('monitor-key', 'job');

        $this->expectException(ClientException::class);

        $client->monitors()->create($monitor);
    }

    public function testUpdate(): void
    {
        $container = [];
        $history = Middleware::history($container);

        $mock = new MockHandler([
            new Response(200),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);

        $guzzle = new GuzzleClient([
            'handler' => $handlerStack,
        ]);
        $client = new Client('', $guzzle);

        $monitor = new Monitor('monitor-key', 'job');

        $client->monitors()->update($monitor);

        $sentRequest = $container[0]['request'];
        $this->assertSame('/api/monitors', $sentRequest->getUri()->getPath());
    }

    public function testDelete(): void
    {
        $container = [];
        $history = Middleware::history($container);

        $mock = new MockHandler([
            new Response(200),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);

        $guzzle = new GuzzleClient([
            'handler' => $handlerStack,
        ]);
        $client = new Client('', $guzzle);

        $client->monitors()->delete('monitor-key');

        $sentRequest = $container[0]['request'];
        $this->assertSame('/api/monitors/monitor-key', $sentRequest->getUri()->getPath());
    }

    public function testPause(): void
    {
        $container = [];
        $history = Middleware::history($container);

        $mock = new MockHandler([
            new Response(200),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);

        $guzzle = new GuzzleClient([
            'handler' => $handlerStack,
        ]);
        $client = new Client('', $guzzle);

        $client->monitors()->pause('monitor-key');

        $sentRequest = $container[0]['request'];
        $this->assertSame('/api/monitors/monitor-key/pause', $sentRequest->getUri()->getPath());
    }

    public function testUnpause(): void
    {
        $container = [];
        $history = Middleware::history($container);

        $mock = new MockHandler([
            new Response(200),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);

        $guzzle = new GuzzleClient([
            'handler' => $handlerStack,
        ]);
        $client = new Client('', $guzzle);

        $client->monitors()->unpause('monitor-key');

        $sentRequest = $container[0]['request'];
        $this->assertSame('/api/monitors/monitor-key/pause/0', $sentRequest->getUri()->getPath());
    }
}
