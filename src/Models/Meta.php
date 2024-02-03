<?php

namespace Asko\Nth\Models;

class Meta extends Model
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->storage_name = 'meta';
    }
}