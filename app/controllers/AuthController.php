<?php

namespace app\controllers;

use app\models\Login;
use app\models\User;
use core\Application;
use core\Controller;
use core\Request;
use core\Response;

class AuthController extends Controller
{
    public function login(Request $request, Response $response){
        if(Application::$app->user){
            return $response->response('406', 'Already Logged in');
        }
        $validationRules = [
            'email' => [User::RULE_REQUIRED, User::RULE_EMAIL],
            'password' => [User::RULE_REQUIRED],
        ];
        $login = new User($validationRules);
        if($request->isPost()){
            $login->loadData($request->getBody());
            if(!$login->validate()){
                return $response->response('400 ', 'Bad Request', $login->errors);
            }
            if($res = $login->login()){
                return $response->response('201', 'Success', $res);
            }else{
                return $response->response('400 ', 'Bad Request', $login->errors);
            }
        }
    }

    public function register(Request $request, Response $response){
        if(Application::$app->user){
            return $response->response('406', 'Already Logged in');
        }
        $validationRules = [
            'firstname' => [User::RULE_REQUIRED],
            'lastname' => [User::RULE_REQUIRED],
            'email' => [User::RULE_REQUIRED, User::RULE_EMAIL, [
                User::RULE_UNIQUE, 'class' => User::class
            ]],
            'image' => [[User::RULE_MIMES, 'mimes' => ['jpeg', 'png', 'jpg']]],
            'password' => [User::RULE_REQUIRED, [User::RULE_MIN, 'min' => 8], [User::RULE_MAX, 'max' => 20]],
            'passwordConfirm' => [User::RULE_REQUIRED, [User::RULE_MATCH, 'match' => 'password']],
        ];
        $user = new User($validationRules);
        if($request->isPost() || $request->isPut()){
            $user->loadData($request->getBody());
            $user->rules();
            if(!$user->validate()){
                return $response->response(400, 'Bad Request', $user->errors);
            }
            if($res = $user->save()){
                return $response->response(201, 'Success', $res);
            }
        }
        return $response->response(500, 'Interval server error');
    }

    public function logout(){
        Application::$app->logout();

        return Application::$app->response->response('201', 'Success');
    }

}