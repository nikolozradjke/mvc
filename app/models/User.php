<?php

namespace app\models;

use core\Application;
use core\DBModel;
use core\Model;

class User extends DBModel
{
    public function attributes(): array
    {
        return ['email', 'password', 'firstname', 'lastname', 'role', 'image'];
    }
    public string $firstname = '';
    public string $lastname = '';
    public string $email = '';
    public string $password = '';
    public string $passwordConfirm = '';
    public int $role = 1;
    public string $image = '';
    public array $validationRules = [];

    public function __construct($rules = []){
        $this->validationRules = $rules;
    }

    public function tableName(): string
    {
        return 'users';
    }

    public function primaryKey(): string
    {
        return 'id';
    }

    public function rules(): array
    {
        return $this->validationRules;
    }

    public function save(){
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        $id = parent::save();
        $user = self::find($this->tableName(), $this->primaryKey(), $id);
        Application::$app->login($user);
        $response = [
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'email' => $user->email,
            'role' => $user->role
        ];
        return $response;
    }

    public function login(){
        $user = User::find($this->tableName(), 'email', $this->email);

        if(!$user){
            $this->addError('email', 'User does not exists with this email');
            return false;
        }
        if(!password_verify($this->password, $user->password)){
            $this->addError('password', 'Password is incorect');
            return false;
        }

        $response = [
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'email' => $user->email,
            'role' => $user->role
        ];

        Application::$app->login($user);

        return $response;
    }

}