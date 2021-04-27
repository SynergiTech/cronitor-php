<?php

namespace SynergiTech\Cronitor\Telemetry;

interface ExceptionHandlerInterface
{
    public function report(\Throwable $e): void;
}
