<?php

declare(strict_types=1);

const SEMBER_ROOT = __DIR__ . '/..';

require_once __DIR__ . '/../vendor/autoload.php';

try {
    \Sember\System\Core::init();
    return;
} catch (Exception $e) {
    echo $e->getMessage();
}
