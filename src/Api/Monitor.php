<?php

namespace SynergiTech\Cronitor\Api;

class Monitor
{
    /**
     * @var array<mixed, mixed>|null
     */
    private $request = null;

    /**
     * @var bool|null
     */
    private $disabled = null;

    /**
     * @var bool|null
     */
    private $initialized = null;

    /**
     * @var bool|null
     */
    private $passing = null;

    /**
     * @var bool|null
     */
    private $paused = null;

    /**
     * @var bool|null
     */
    private $running = null;

    /**
     * @var ?int
     */
    private $graceSeconds = null;

    /**
     * @var ?int
     */
    private $toleratedFailures = null;

    /**
     * @var ?string
     */
    private $group = null;

    /**
     * @var string
     */
    private $key;

    /**
     * @var ?string
     */
    private $name = null;

    /**
     * @var ?string
     */
    private $note = null;

    /**
     * @var ?string
     */
    private $platform = null;

    /**
     * @var ?string
     */
    private $realertInterval = null;

    /**
     * @var ?string
     */
    private $schedule = null;

    /**
     * @var ?string
     */
    private $status = null;

    /**
     * @var ?string
     */
    private $timezone = null;

    /**
     * @var ?\DateTime
     */
    private $created = null;

    /**
     * @var array<string>
     */
    private $assertions = [];

    /**
     * @var array<mixed, mixed>
     */
    private $metadata = [];

    /**
     * @var array<mixed, mixed>
     */
    private $notify = [];

    /**
     * @var array<string>
     */
    private $tags = [];

    /**
     * @var string
     */
    private $type;

    public function __construct(string $key, string $type)
    {
        $this->key = $key;
        $this->name = $key;
        $this->type = $type;
    }

    /**
     * @param array<string, mixed> $properties
     */
    public function fromApiResponse(array $properties): self
    {
        $this->assertions = $properties['assertions'];
        $this->created = new \DateTime($properties['created']);
        $this->disabled = $properties['disabled'];
        $this->graceSeconds = $properties['grace_seconds'];
        $this->group = $properties['group'];
        $this->initialized = $properties['initialized'];
        $this->name = $properties['name'];
        $this->note = $properties['note'];
        $this->notify = $properties['notify'] ?? [];
        $this->passing = $properties['passing'];
        $this->paused = $properties['paused'];
        $this->platform = $properties['platform'];
        $this->realertInterval = $properties['realert_interval'];
        $this->request = $properties['request'];
        $this->running = $properties['running'];
        $this->schedule = $properties['schedule'];
        $this->status = $properties['status'];
        $this->tags = $properties['tags'];
        $this->timezone = $properties['timezone'];
        $this->toleratedFailures = $properties['tolerated_failures'];

        if ($properties['metadata'] !== null) {
            $this->metadata = json_decode($properties['metadata'], true);
        }

        return $this;
    }

    public function getCreated(): ?\DateTime
    {
        return $this->created;
    }

    public function getDisabled(): ?bool
    {
        return $this->disabled;
    }

    public function getInitialized(): ?bool
    {
        return $this->initialized;
    }

    public function getPassing(): ?bool
    {
        return $this->passing;
    }

    public function getPaused(): ?bool
    {
        return $this->paused;
    }

    public function getRunning(): ?bool
    {
        return $this->running;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param array<string> $assertions
     */
    public function setAssertions(array $assertions): self
    {
        $this->assertions = $assertions;
        return $this;
    }

    /**
     * @return array<string>
     */
    public function getAssertions(): array
    {
        return $this->assertions;
    }

    public function setGraceSeconds(int $graceSeconds): self
    {
        $this->graceSeconds = $graceSeconds;
        return $this;
    }

    public function getGraceSeconds(): ?int
    {
        return $this->graceSeconds;
    }

    public function setGroup(?string $group): self
    {
        $this->group = $group;
        return $this;
    }

    public function getGroup(): ?string
    {
        return $this->group;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;
        return $this;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param array<mixed, mixed> $metadata
     */
    public function setMetadata(array $metadata): self
    {
        $this->metadata = $metadata;
        return $this;
    }

    /**
     * @return array<mixed, mixed>
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;
        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    /**
     * @param array<mixed, mixed> $notify
     */
    public function setNotify(array $notify): self
    {
        $this->notify = $notify;
        return $this;
    }

    /**
     * @return array<mixed, mixed>
     */
    public function getNotify(): array
    {
        return $this->notify;
    }

    public function setPlatform(?string $platform): self
    {
        $this->platform = $platform;
        return $this;
    }

    public function getPlatform(): ?string
    {
        return $this->platform;
    }

    public function setRealertInterval(?string $realertInterval): self
    {
        $this->realertInterval = $realertInterval;
        return $this;
    }

    public function getRealertInterval(): ?string
    {
        return $this->realertInterval;
    }

    /**
     * @param ?array<mixed, mixed> $request
     */
    public function setRequest(?array $request): self
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @return ?array<mixed, mixed>
     */
    public function getRequest(): ?array
    {
        return $this->request;
    }

    public function setSchedule(?string $schedule): self
    {
        $this->schedule = $schedule;
        return $this;
    }

    public function getSchedule(): ?string
    {
        return $this->schedule;
    }

    /**
     * @param array<string> $tags
     */
    public function setTags(array $tags): self
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * @return array<string>
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    public function setTimezone(?string $timezone): self
    {
        $this->timezone = $timezone;
        return $this;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setToleratedFailures(?int $toleratedFailures): self
    {
        $this->toleratedFailures = $toleratedFailures;
        return $this;
    }

    public function getToleratedFailures(): ?int
    {
        return $this->toleratedFailures;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $array = [
            'assertions' => $this->assertions,
            'key' => $this->key,
            'metadata' => json_encode($this->metadata),
            'name' => $this->name,
            'note' => $this->note,
            'notify' => $this->notify,
            'platform' => $this->platform,
            'request' => $this->request,
            'schedule' => $this->schedule,
            'tags' => $this->tags,
            'timezone' => $this->timezone,
            'type' => $this->type,
        ];
        if ($this->toleratedFailures !== null) {
            $array['tolerated_failures'] = $this->toleratedFailures;
        }
        if ($this->graceSeconds !== null) {
            $array['grace_seconds'] = $this->graceSeconds;
        }
        if ($this->group !== null) {
            $array['group'] = $this->group;
        }
        if ($this->realertInterval !== null) {
            $array['realert_interval'] = $this->realertInterval;
        }

        return $array;
    }
}
