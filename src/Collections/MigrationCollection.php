<?php

namespace Asko\Sember\Collections;

use Asko\Sember\Collection;
use Asko\Sember\Models\Migration;

/**
 * @extends Collection<Migration>
 */
class MigrationCollection extends Collection
{
    public function __construct(Migration ...$items)
    {
        parent::__construct($items);
    }
}
