<?php

namespace SynergiTech\Cronitor\Telemetry;

class Monitor
{
    /**
     * @var TelemetryService
     */
    private $service;

    /**
     * @var string
     */
    private $monitorKey;

    /**
     * @var string|null
     */
    private $series;

    public function __construct(TelemetryService $service, string $monitorKey)
    {
        $this->service = $service;
        $this->monitorKey = $monitorKey;
        $this->series = bin2hex(random_bytes(24));
    }

    public function getKey(): string
    {
        return $this->monitorKey;
    }

    public function setSeries(string $series = null): self
    {
        $this->series = $series;
        return $this;
    }

    public function getSeries(): ?string
    {
        return $this->series;
    }

    /**
     * @return mixed
     */
    public function job(callable $func)
    {
        $this->run();

        $returnValue = null;

        try {
            $returnValue = $func();
        } catch (\Throwable $e) {
            $this->fail(get_class($e) . ': ' . $e->getMessage());
            throw $e;
        }

        $this->complete();

        return $returnValue;
    }

    public function run(): bool
    {
        $event = new Event('run');
        return $this->sendEvent($event);
    }

    public function complete(): bool
    {
        $event = new Event('complete');
        return $this->sendEvent($event);
    }

    public function fail(?string $message): bool
    {
        $event = new Event('fail');
        $event->setMessage($message);
        return $this->sendEvent($event);
    }

    public function ok(): bool
    {
        $event = new Event('ok');
        return $this->sendEvent($event);
    }

    public function sendEvent(Event $event): bool
    {
        if ($this->series !== null and $event->getSeries() === null) {
            $event->setSeries($this->series);
        }

        return $this->service->sendEvent($this->monitorKey, $event);
    }
}
