<?php

namespace SynergiTech\Cronitor\Exception\Api;

class ValidationException extends \Exception
{
    /**
     * @var array<mixed, mixed>
     */
    private $errors;

    /**
     * @param array<mixed, mixed> $errors
     */
    public function setValidationErrors(array $errors): void
    {
        $this->errors = $errors;
    }

    /**
     * @return array<mixed, mixed>
     */
    public function getValidationErrors(): array
    {
        return $this->errors;
    }
}
