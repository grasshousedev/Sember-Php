<?php

namespace Asko\Nth\Helpers;

use Ramsey\Uuid\Uuid;

class BlockHelper
{
    public static function new(string $type): array
    {
        return [
            'id' => Uuid::uuid4()->toString(),
            'type' => $type,
            'value' => '',
        ];
    }
}