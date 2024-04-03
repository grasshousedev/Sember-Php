<?php

namespace Sember\System\Models;

class Meta extends Model
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->storage_name = 'meta';
    }

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $meta = self::findOne("where meta_name = ?", [$key]);

        return $meta ? $meta->get('meta_value') : $default;
    }
}