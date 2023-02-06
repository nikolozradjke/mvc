<?php

namespace app\middlewares;

use core\Application;
use core\middlewares\BaseMiddleware;
use core\Response;

class AuthMiddleware extends BaseMiddleware
{
    public array $actions;

    public function __construct(array $actions = []){
        $this->actions = $actions;
    }

    public function execute()
    {
        if(Application::isGuest()){
            if(empty($this->actions) || in_array(Application::$app->controller->action, $this->actions)){
                $response = new Response();
                echo $response->response(401, 'Unauthorized action!');
                exit;
            }
        }
    }
}