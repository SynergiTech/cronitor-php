<?php

namespace SynergiTech\Cronitor\Tests\Telemetry;

use SynergiTech\Cronitor\Telemetry\Event;
use SynergiTech\Cronitor\Tests\TestCase;

class EventTest extends TestCase
{
    public function testState(): void
    {
        $event = new Event('test');
        $this->assertSame('test', $event->getState());
    }

    public function testHost(): void
    {
        $event = (new Event(''))->setHost('my-hostname');
        $this->assertSame('my-hostname', $event->getHost());
    }

    public function testMessage(): void
    {
        $event = (new Event(''))->setMessage('my-message');
        $this->assertSame('my-message', $event->getMessage());
    }

    public function testMetrics(): void
    {
        $event = (new Event(''))->setMetric('metric', 'val');
        $this->assertSame(
            [
                'metric' => 'val',
            ],
            $event->getMetrics()
        );

        $event->clearMetrics();
        $this->assertEmpty($event->getMetrics());
    }

    public function testSeries(): void
    {
        $event = (new Event(''))->setSeries('my-series');
        $this->assertSame('my-series', $event->getSeries());
    }

    public function testStatusCode(): void
    {
        $event = (new Event(''))->setStatusCode(123);
        $this->assertSame(123, $event->getStatusCode());
    }

    public function testToArray(): void
    {
        $event = (new Event('complete'))
            ->setHost('my-hostname')
            ->setMessage('my-message')
            ->setMetric('metric1', 'val1')
            ->setMetric('metric2', 'val2')
            ->setSeries('my-series')
            ->setStatusCode(123);

        $actual = $event->toArray();
        $expected = [
            ['state', 'complete'],
            ['host', 'my-hostname'],
            ['message', 'my-message'],
            ['series', 'my-series'],
            ['statusCode', '123'],
            ['metric', 'metric1:val1'],
            ['metric', 'metric2:val2'],
        ];

        $this->assertSame($expected, $actual);
    }
}
