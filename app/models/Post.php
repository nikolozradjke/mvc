<?php

namespace app\models;

use core\DBModel;

class Post extends DBModel
{
    public string $title = '';
    public string $description = '';
    public string $image = '';
    public string $user_id = '';
    public array $validationRules = [];

    public function tableName(): string
    {
        return 'posts';
    }

    public function attributes(): array
    {
        return ['title', 'description', 'image', 'user_id'];
    }

    public function primaryKey(): string
    {
        return 'id';
    }

    public function rules(): array
    {
        return $this->validationRules;
    }


}