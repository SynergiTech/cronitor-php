<?php

namespace SynergiTech\Cronitor\Tests\Api;

use SynergiTech\Cronitor\Api\Monitor;
use SynergiTech\Cronitor\Tests\TestCase;

class MonitorTest extends TestCase
{
    public function testToArray(): void
    {
        $monitor = new Monitor('key', 'type');
        $monitor->setAssertions([
            'metric.duration < 5',
        ])
            ->setGraceSeconds(2)
            ->setGroup('group')
            ->setKey('key2')
            ->setMetadata([
                'key' => 'value',
            ])
            ->setName('name')
            ->setNote('note')
            ->setNotify(['default'])
            ->setPlatform('php')
            ->setRealertInterval('30')
            ->setRequest([
                'url' => 'https://google.com',
            ])
            ->setSchedule('0 0 * * *')
            ->setTags(['tag1', 'tag2'])
            ->setTimezone('Europe/London')
            ->setToleratedFailures(3)
            ->setType('job');

        $array = $monitor->toArray();

        $this->assertSame([
            'metric.duration < 5',
        ], $array['assertions']);
        $this->assertSame(2, $array['grace_seconds']);
        $this->assertSame('group', $array['group']);
        $this->assertSame('key2', $array['key']);
        $this->assertSame('{"key":"value"}', $array['metadata']);
        $this->assertSame('name', $array['name']);
        $this->assertSame('note', $array['note']);
        $this->assertSame(['default'], $array['notify']);
        $this->assertSame('php', $array['platform']);
        $this->assertSame('30', $array['realert_interval']);
        $this->assertSame([
            'url' => 'https://google.com',
        ], $array['request']);
        $this->assertSame('0 0 * * *', $array['schedule']);
        $this->assertSame(['tag1', 'tag2'], $array['tags']);
        $this->assertSame('Europe/London', $array['timezone']);
        $this->assertSame(3, $array['tolerated_failures']);
        $this->assertSame('job', $array['type']);
    }
}
