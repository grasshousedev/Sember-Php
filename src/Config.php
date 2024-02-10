<?php

namespace Asko\Sember;

class Config
{
    public static function get(string $key, $default = null): mixed
    {
        if (!file_exists(NTH_ROOT . '/../src/Config/config.php')) {
            return null;
        }

        $config = require NTH_ROOT . '/../src/Config/config.php';

        return $config[$key] ?? $default;
    }

    public static function getBlock(string $key, $default = null): mixed
    {
        if (!file_exists(NTH_ROOT . '/../src/Config/blocks.php')) {
            return null;
        }

        $config = require NTH_ROOT . '/../src/Config/blocks.php';

        return $config[$key] ?? $config[$default];
    }

    public static function getBlocks(): array
    {
        if (!file_exists(NTH_ROOT . '/../src/Config/blocks.php')) {
            return [];
        }

        return require NTH_ROOT . '/../src/Config/blocks.php';
    }
}