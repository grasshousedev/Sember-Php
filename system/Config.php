<?php

namespace Sember\System;

class Config
{
    public static function get(string $key, $default = null): mixed
    {
        $config = [];

        if (file_exists(SEMBER_ROOT . '/system/Config/config.php')) {
            $config = array_merge_recursive($config, require SEMBER_ROOT . '/system/Config/config.php');
        }

        if (file_exists(SEMBER_ROOT . '/app/config.php')) {
            $config = array_merge_recursive($config, require SEMBER_ROOT . '/app/config.php');
        }

        ray($config);

        return $config[$key] ?? $default;
    }

    public static function getBlock(string $key, $default = null): mixed
    {
       $blocks = self::getBlocks();

        return $blocks[$key] ?? $blocks[$default];
    }

    public static function getBlocks(): array
    {
        $blocks = [];

        if (file_exists(SEMBER_ROOT . '/system/Config/blocks.php')) {
            $blocks = [...$blocks, ...require SEMBER_ROOT . '/system/Config/blocks.php'];
        }

        if (file_exists(SEMBER_ROOT . '/app/blocks.php')) {
            $blocks = [...$blocks, ...require SEMBER_ROOT . '/app/blocks.php'];
        }

        return $blocks;
    }
}