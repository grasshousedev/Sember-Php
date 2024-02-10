<?php

namespace Asko\Sember;

/**
 * @package Asko\Nth
 * @since 0.1.0
 */
class Response
{
    private string $response;

    /**
     * Redirect to a URL or relative path.
     *
     * @param string $to
     * @param int $status
     * @return $this
     */
    public function redirect(string $to, int $status = 302): Response
    {
        header("Location: $to", true, $status);

        return $this;
    }

    /**
     * Return a JSON response.
     *
     * @param array $data
     * @param int $status
     * @return $this
     */
    public function json(array $data, int $status = 200): Response
    {
        header('Content-Type: application/json');
        http_response_code($status);

        $this->response = json_encode($data);

        return $this;
    }

    /**
     * Return a plain text response.
     *
     * @param string $content
     * @param int $status
     * @return $this
     */
    public function make(string $content, int $status = 200): Response
    {
        http_response_code($status);

        $this->response = $content;

        return $this;
    }

    /**
     * Render a view with Twig.
     *
     * @param string $view
     * @param array $data
     * @param int $status
     * @return $this
     */
    public function view(string $view, array $data = [], int $status = 200): Response
    {
        http_response_code($status);

        $this->response = View::render($view, $data);

        return $this;
    }

    /**
     * Flash a message to the session.
     *
     * @param string $key
     * @param $value
     * @return $this
     */
    public function flash(string $key, $value): Response
    {
        $_SESSION['flash'][$key] = $value;

        return $this;
    }

    /**
     * Send the response.
     *
     * @return string
     */
    public function send(): string
    {
        return $this->response ?? '';
    }
}