<?php

namespace Asko\Nth;

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