<?php

namespace Asko\Sember;

class Session
{
    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public function get(string $key, $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function set(string $key, $value): mixed
    {
        $_SESSION[$key] = $value;

        return $value;
    }

    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }
}