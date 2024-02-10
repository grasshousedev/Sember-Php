<?php

declare(strict_types=1);

const NTH_ROOT = __DIR__ . '/..';

require_once __DIR__ . '/../vendor/autoload.php';

try {
    \Asko\Sember\Core::init();
} catch (Exception $e) {
    echo $e->getMessage();
}
