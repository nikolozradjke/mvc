<?php

namespace core;

class Application
{
    public string $userClass;
    public static string $ROOT_DIR;
    public Router $router;
    public Request $request;
    public Response $response;
    public Session $session;
    public static Application $app;
    public Controller $controller;
    public Database $db;
    public $user = false;

    public function __construct($rootpath, array $config){
        $this->userClass = $config['userClass'];
        self::$ROOT_DIR = $rootpath;
        self::$app = $this;
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->router = new Router($this->request, $this->response);
        $this->db = new Database($config['db']);
        $primaryValue = $this->session->get('user');

        if($primaryValue){
            $this->user = $this->userClass::find('users', 'id', $primaryValue);
        }
    }

    public function run(){
        echo $this->router->resolve();
    }

    public function getController(): Controller
    {
        return $this->controller;
    }

    public function setController(Controller $controller): void
    {
        $this->controller = $controller;
    }

    public function login($user){
        $this->user = $user;
        $this->session->set('user', $user->id);
        return $user;
    }

    public function logout(){
        $this->user = null;
        $this->session->remove('user');
    }

    public static function isGuest(){
        return !self::$app->user;
    }

}