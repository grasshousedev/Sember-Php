<?php

namespace Asko\Sember;

class Request
{
    private string $method;
    private string $uri;

    public function __construct()
    {
        $this->method = strtolower($_SERVER['REQUEST_METHOD']);
        $this->uri = $_SERVER['REQUEST_URI'];
    }

    public function isGet(): bool
    {
        return $this->method === 'get';
    }

    public function isPost(): bool
    {
        return $this->method === 'post';
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function isSecure(): bool
    {
        if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) === 'on') {
            return true;
        }

        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') {
            return true;
        }

        if (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower($_SERVER['HTTP_X_FORWARDED_SSL']) === 'on') {
            return true;
        }

        if (isset($_SERVER['HTTP_X_FORWARDED_PORT']) && $_SERVER['HTTP_X_FORWARDED_PORT'] === '443') {
            return true;
        }

        if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] === '443') {
            return true;
        }

        return false;
    }

    public function protocol(): string
    {
        return $this->isSecure() ? 'https' : 'http';
    }

    public function hostname(): string
    {
        return $_SERVER['HTTP_HOST'];
    }

    public function flash(string $key, $default = null): string|array|null
    {
        if (isset($_SESSION['flash'][$key])) {
            return $_SESSION['flash'][$key];
        }

        if ($default) {
            return $default;
        }

        return null;
    }

    public function input(string $key, $default = null)
    {
        return $_REQUEST[$key] ?? $default;
    }

    public function all(): array
    {
        return $_REQUEST;
    }

    public function session(): Session
    {
        return new Session();
    }
}
