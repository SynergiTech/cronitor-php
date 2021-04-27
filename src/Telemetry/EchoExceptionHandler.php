<?php

namespace SynergiTech\Cronitor\Telemetry;

class EchoExceptionHandler implements ExceptionHandlerInterface
{
    public function report(\Throwable $e): void
    {
        echo get_class($e) . ': ' . $e->getMessage();
    }
}
