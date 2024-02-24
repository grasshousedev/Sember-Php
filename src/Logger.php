<?php

namespace Asko\Sember;

class Logger
{
    public static function log(string $key, string $message): void
    {
        file_put_contents(NTH_ROOT . '/storage/info.log', "[$key] => $message\n", FILE_APPEND);
    }
}