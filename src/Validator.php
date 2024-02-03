<?php

namespace Asko\Nth;

use Asko\Hird\Hird;
use Asko\Nth\Validators\SameValidator;

class Validator extends Hird
{
    private array $fieldNames = [
        'email' => 'E-mail',
        'name' => 'Name',
        'password' => 'Password',
        'password_confirm' => 'Repeated password',
    ];

    public function __construct(
        array $fields,
        array $rules,
    ) {
        parent::__construct($fields, $rules);

        // Set field names
        $this->setFieldNames($this->fieldNames);

        // Register validators
        $this->registerValidator("same", SameValidator::class);
    }
}