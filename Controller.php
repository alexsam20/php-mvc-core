<?php

namespace app\core;

use app\core\middlewares\BaseMiddleware;

/**
 * Class Controller
 * @package app\core
 */
class Controller
{
    public string $layout = 'main';
    public string $action = '';
    /**
     * @var \app\core\middlewares\BaseMiddleware[]
     */
    protected array $middlewares =[];

    public function setLayout($layout): void
    {
        $this->layout = $layout;
    }

    public function render($view, $params = []): string
    {
        return Application::$app->view->renderView($view, $params);
    }

    public function registerMiddleware(BaseMiddleware $middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    /**
     * @return BaseMiddleware[]
     */
    public function getMiddleware(): array
    {
        return  $this->middlewares;
    }
}