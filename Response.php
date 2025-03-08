<?php


namespace app\core;

/**
 * Class Response
 * @package app\core
 */
class Response
{
    public function setStatusCode(int $code): void
    {
        http_response_code($code);
    }

    public function redirect(string $url): void
    {
        header('Location: ' . $url);
    }
}