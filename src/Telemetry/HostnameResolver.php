<?php

namespace SynergiTech\Cronitor\Telemetry;

class HostnameResolver implements HostnameResolverInterface
{
    public function getHostname(): ?string
    {
        $hostname = gethostname();

        return $hostname === false ? null : $hostname;
    }
}
