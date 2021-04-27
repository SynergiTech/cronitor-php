<?php

namespace SynergiTech\Cronitor\Telemetry;

class Event
{
    /**
     * @var string
     */
    private $state;

    /**
     * @var string|null
     */
    private $host = null;

    /**
     * @var string|null
     */
    private $message = null;

    /**
     * @var array<string, string>
     */
    private $metrics = [];

    /**
     * @var string|null
     */
    private $series = null;

    /**
     * @var int|null
     */
    private $statusCode = null;

    public function __construct(string $state)
    {
        $this->state = $state;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setHost(?string $host): self
    {
        $this->host = $host;
        return $this;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMetric(string $type, string $value): self
    {
        $this->metrics[$type] = $value;
        return $this;
    }

    public function clearMetrics(): self
    {
        $this->metrics = [];
        return $this;
    }

    /**
     * @return array<string, string>
     */
    public function getMetrics(): array
    {
        return $this->metrics;
    }

    public function setSeries(?string $series): self
    {
        $this->series = $series;
        return $this;
    }

    public function getSeries(): ?string
    {
        return $this->series;
    }

    public function setStatusCode(?int $statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    /**
     * @return array<array{string, string}>
     */
    public function toArray(): array
    {
        $array = [
            ['state', $this->state],
        ];

        if ($this->host !== null) {
            $array[] = ['host', $this->host];
        }

        if ($this->message !== null) {
            $array[] = ['message', $this->message];
        }

        if ($this->series !== null) {
            $array[] = ['series', $this->series];
        }

        if ($this->statusCode !== null) {
            $array[] = ['statusCode', (string) $this->statusCode];
        }

        foreach ($this->metrics as $metric => $value) {
            $array[] = ['metric', "{$metric}:{$value}"];
        }

        return $array;
    }
}
