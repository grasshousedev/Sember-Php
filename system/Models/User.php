<?php

namespace Sember\System\Models;

use Sember\System\Database;
use Sember\System\Request;

class User extends Model
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->storage_name = 'users';
    }

    public static function current(): ?Model
    {
        $auth_token = (new Request())->cookie()->get('auth_token');

        if (empty($auth_token)) {
            return null;
        }

        return (new Database())->findOne(User::class, 'where auth_token = ?', [$auth_token]);
    }
}