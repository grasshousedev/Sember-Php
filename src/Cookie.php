<?php

namespace Asko\Sember;

class Cookie
{
    public function set(string $name, string $value, int $expire = 0, string $path = '', string $domain = '', bool $secure = false, bool $httpOnly = false): void
    {
        setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
    }

    public function get(string $name): string
    {
        return $_COOKIE[$name] ?? '';
    }

    public function has(string $name): bool
    {
        return isset($_COOKIE[$name]);
    }

    public function remove(string $name, string $path = '', string $domain = ''): void
    {
        setcookie($name, '', time() - 3600, $path, $domain);
    }
}