<?php

namespace core;

use core\middlewares\BaseMiddlewarre;

class Controller
{
    public string $layout = 'client';
    protected array $middlewares = [];
    public string $action = '';

    public function setLayout($layout){
        $this->layout = $layout;
    }

    public function render($view, $params = []){
        return Application::$app->router->renderView($view, $params);
    }

    public function registerMiddleware($middleware){
        $this->middlewares[] = $middleware;
    }

    public function getMiddlewares(){
        return $this->middlewares;
    }

}