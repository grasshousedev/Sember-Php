<?php

namespace Asko\Sember\Collections;

use Asko\Sember\Collection;
use Asko\Sember\Models\Post;

class PostCollection extends Collection
{
    public function __construct(Post ...$items)
    {
        parent::__construct($items);
    }
}
