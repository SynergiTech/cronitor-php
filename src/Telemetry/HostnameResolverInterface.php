<?php

namespace SynergiTech\Cronitor\Telemetry;

interface HostnameResolverInterface
{
    public function getHostname(): ?string;
}
