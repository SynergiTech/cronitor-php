<?php

namespace SynergiTech\Cronitor\Tests\Telemetry;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use SynergiTech\Cronitor\Client;
use SynergiTech\Cronitor\Telemetry\Event;
use SynergiTech\Cronitor\Telemetry\ExceptionHandlerInterface;
use SynergiTech\Cronitor\Telemetry\HostnameResolverInterface;
use SynergiTech\Cronitor\Telemetry\Monitor;
use SynergiTech\Cronitor\Tests\TestCase;

class TelemetryServiceTest extends TestCase
{
    public function testWithHostnameResolver(): void
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
        $client->telemetry()->withHostnameResolver(new class() implements HostnameResolverInterface {
            public function getHostname(): string
            {
                return 'test-hostname';
            }
        });

        $client->telemetry()->sendEvent('', new Event('run'));

        parse_str($container[0]['request']->getUri()->getQuery(), $queryParams);

        $this->assertSame('test-hostname', $queryParams['host']);
    }

    public function testWithDefaultHostnameResolver(): void
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
        $client->telemetry()->sendEvent('', new Event('run'));

        parse_str($container[0]['request']->getUri()->getQuery(), $queryParams);

        $this->assertSame(gethostname(), $queryParams['host']);
    }

    public function testWithoutHostnameResolver(): void
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
        $client->telemetry()->withoutHostnameResolver()
            ->sendEvent('', new Event('run'));

        parse_str($container[0]['request']->getUri()->getQuery(), $queryParams);

        $this->assertArrayNotHasKey('host', $queryParams);
    }

    public function testWithExceptionHandler(): void
    {
        $container = [];
        $history = Middleware::history($container);

        $mock = new MockHandler([
            new RequestException('test-error', new Request('GET', '')),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);

        $guzzle = new GuzzleClient([
            'handler' => $handlerStack,
        ]);
        $client = new Client('', $guzzle);

        $testExceptionHandler = new class() implements ExceptionHandlerInterface {
            public $exceptionReported = false;

            public function report(\Throwable $e): void
            {
                $this->exceptionReported = true;
            }
        };

        $client->telemetry()->withExceptionHandler($testExceptionHandler);
        $client->telemetry()->sendEvent('', new Event('run'));

        $this->assertTrue($testExceptionHandler->exceptionReported);
    }

    public function testWithoutExceptionHandler(): void
    {
        $container = [];
        $history = Middleware::history($container);

        $mock = new MockHandler([
            new RequestException('test-error', new Request('GET', '')),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);

        $guzzle = new GuzzleClient([
            'handler' => $handlerStack,
        ]);
        $client = new Client('', $guzzle);

        $client->telemetry()->withoutExceptionHandler();

        $this->expectException(RequestException::class);
        $this->expectExceptionMessage('test-error');

        $client->telemetry()->sendEvent('', new Event('run'));
    }

    public function testMonitor(): void
    {
        $client = new Client('');
        $monitor = $client->telemetry()->monitor('my-monitor-key');

        $this->assertInstanceOf(Monitor::class, $monitor);
        $this->assertSame('my-monitor-key', $monitor->getKey());
    }

    public function testSendEvent(): void
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
        $client = new Client('my-api-key', $guzzle);

        $event = new Event('complete');
        $event->setSeries('my-series-key');
        $event->setHost('my-hostname');
        $event->setMessage('my-message');
        $event->setMetric('count', '20');
        $event->setMetric('error_count', '1');
        $event->setMetric('duration', '5');
        $event->setStatusCode(2);

        $client->telemetry()->sendEvent('my-monitor-key', $event);

        $sentRequest = $container[0]['request'];
        $this->assertSame('/p/my-api-key/my-monitor-key', $sentRequest->getUri()->getPath());

        $queryString = $sentRequest->getUri()->getQuery();
        $queryParams = array_map(function ($param) {
            [$key, $val] = explode('=', $param);
            return [urldecode($key), urldecode($val)];
        }, explode('&', $queryString));

        $this->assertContains(
            [
                'state',
                'complete',
            ],
            $queryParams
        );
        $this->assertContains(
            [
                'host',
                'my-hostname',
            ],
            $queryParams
        );
        $this->assertContains(
            [
                'message',
                'my-message',
            ],
            $queryParams
        );
        $this->assertContains(
            [
                'series',
                'my-series-key',
            ],
            $queryParams
        );
        $this->assertContains(
            [
                'statusCode',
                '2',
            ],
            $queryParams
        );
        $this->assertContains(
            [
                'metric',
                'count:20',
            ],
            $queryParams
        );
        $this->assertContains(
            [
                'metric',
                'error_count:1',
            ],
            $queryParams
        );
        $this->assertContains(
            [
                'metric',
                'duration:5',
            ],
            $queryParams
        );
    }
}
