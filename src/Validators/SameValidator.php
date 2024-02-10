<?php

namespace Asko\Sember\Validators;

use Asko\Hird\Validators\Validator;

readonly class SameValidator implements Validator
{
    public function __construct(
        private array $fields,
        private array $fieldNames,
    ) {}

    public function validate(string $field, mixed $value, mixed $modifier = null): bool
    {
        return $value === $this->fields[$modifier];
    }

    public function composeError(string $field, mixed $modifier = null): string
    {
        return "{$this->fieldNames[$field]} is not equal to {$modifier}.";
    }
}