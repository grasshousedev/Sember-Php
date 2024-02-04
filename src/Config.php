<?php

namespace Asko\Nth;

class Config
{
    public static function get(string $key, $default = null): mixed
    {
        if (!file_exists(__DIR__ . '/config/config.php')) {
            return null;
        }

        $config = require __DIR__ . '/config/config.php';

        return $config[$key] ?? $default;
    }

    public static function getBlock(string $key, $default = null): mixed
    {
        if (!file_exists(__DIR__ . '/config/blocks.php')) {
            return null;
        }

        $config = require __DIR__ . '/config/blocks.php';

        return $config[$key] ?? $config[$default];
    }

    public static function getBlocks(): array
    {
        if (!file_exists(__DIR__ . '/config/blocks.php')) {
            return [];
        }

        return require __DIR__ . '/config/blocks.php';
    }
}