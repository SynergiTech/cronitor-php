<?php

namespace SynergiTech\Cronitor\Tests\Telemetry;

use SynergiTech\Cronitor\Telemetry\Monitor;
use SynergiTech\Cronitor\Telemetry\TelemetryService;
use SynergiTech\Cronitor\Tests\TestCase;

class MonitorTest extends TestCase
{
    public function testJob(): void
    {
        $mockTelemetryService = $this->createMock(TelemetryService::class);
        $mockTelemetryService->expects($this->exactly(2))
            ->method('sendEvent')
            ->withConsecutive(
                [
                    'test-key',
                    $this->callback(function ($event) {
                        return $event->getState() === 'run';
                    }),
                ],
                [
                    'test-key',
                    $this->callback(function ($event) {
                        return $event->getState() === 'complete';
                    }),
                ],
            );

        $monitor = new Monitor($mockTelemetryService, 'test-key');
        $wasJobExecuted = false;

        $monitor->job(function () use (&$wasJobExecuted) {
            $wasJobExecuted = true;
        });

        $this->assertTrue($wasJobExecuted);
    }

    public function testJobThatThrows(): void
    {
        $mockTelemetryService = $this->createMock(TelemetryService::class);
        $mockTelemetryService->expects($this->exactly(2))
            ->method('sendEvent')
            ->withConsecutive(
                [
                    'test-key',
                    $this->callback(function ($event) {
                        return $event->getState() === 'run';
                    }),
                ],
                [
                    'test-key',
                    $this->callback(function ($event) {
                        return $event->getState() === 'fail'
                            and $event->getMessage() === 'RuntimeException: my-exception-message';
                    }),
                ],
            );

        $monitor = new Monitor($mockTelemetryService, 'test-key');
        $wasJobExecuted = false;
        $wasExceptionCaught = false;

        try {
            $monitor->job(function () use (&$wasJobExecuted) {
                $wasJobExecuted = true;

                throw new \RuntimeException('my-exception-message');
            });
        } catch (\RuntimeException $e) {
            $wasExceptionCaught = true;
        }

        $this->assertTrue($wasJobExecuted);
        $this->assertTrue($wasExceptionCaught);
    }

    public function testSetSeries(): void
    {
        $mockTelemetryService = $this->createMock(TelemetryService::class);
        $mockTelemetryService->expects($this->once())
            ->method('sendEvent')
            ->withConsecutive(
                [
                    'test-key',
                    $this->callback(function ($event) {
                        return $event->getSeries() === 'my-series-key';
                    }),
                ],
            );

        $monitor = new Monitor($mockTelemetryService, 'test-key');
        $monitor->setSeries('my-series-key');

        $this->assertSame('my-series-key', $monitor->getSeries());

        $monitor->ok();
    }
}
