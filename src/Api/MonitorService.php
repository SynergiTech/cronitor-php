<?php

namespace SynergiTech\Cronitor\Api;

class MonitorService extends BaseApiService
{
    public function create(Monitor $monitor): void
    {
        $this->createMany([$monitor]);
    }

    /**
     * @param array<Monitor> $monitors
     */
    public function createMany(array $monitors): void
    {
        $this->updateMany($monitors);
    }

    public function get(string $monitorKey): Monitor
    {
        $monitorProperties = $this->httpGet("monitors/{$monitorKey}");
        return (new Monitor($monitorProperties['key'], $monitorProperties['type']))
            ->fromApiResponse($monitorProperties);
    }

    /**
     * @return array<Monitor>
     */
    public function list(): array
    {
        $monitors = $this->httpGet('monitors');

        return array_map(function ($monitorProperties) {
            return (new Monitor($monitorProperties['key'], $monitorProperties['type']))
                ->fromApiResponse($monitorProperties);
        }, $monitors);
    }

    public function update(Monitor $monitor): void
    {
        $this->updateMany([$monitor]);
    }

    /**
     * @param array<Monitor> $monitors
     */
    public function updateMany(array $monitors): void
    {
        $payload = [
            'monitors' => array_map(function (Monitor $monitor) {
                return $monitor->toArray();
            }, $monitors),
        ];

        $this->httpPut('monitors', $payload);
    }

    public function delete(string $monitorKey): void
    {
        $this->httpDelete("monitors/{$monitorKey}");
    }

    public function pause(string $monitorKey, ?int $pauseTime = null): void
    {
        $url = "monitors/{$monitorKey}/pause";
        if ($pauseTime !== null) {
            $url .= "/{$pauseTime}";
        }

        $this->httpGet($url);
    }

    public function unpause(string $monitorKey): void
    {
        $this->pause($monitorKey, 0);
    }
}
