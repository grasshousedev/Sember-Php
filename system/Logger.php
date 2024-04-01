<?php

namespace Sember\System;

class Logger
{
    public static function log(string $key, string $message): void
    {
        file_put_contents(SEMBER_ROOT . '/storage/info.log', "[$key] => $message\n", FILE_APPEND);
    }
}