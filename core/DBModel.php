<?php

namespace core;

use function app\Helper;

abstract class DBModel extends Model
{

    abstract public function tableName(): string;

    abstract public function attributes(): array;

    abstract public function primaryKey(): string;

    public function save(){
        $tableName = $this->tableName();
        $attributes = $this->attributes();
        $data = [];

        foreach($attributes as $attribute){
            if(isset($this->{$attribute})){
                $data[$attribute] = $this->{$attribute};
            }
        }
        if($_FILES){
            foreach($_FILES as $key => $file){
                $file_tmp = $file['tmp_name'];
                $fileName = \app\Helper::generateRandomString(30);
                $fileNameArr = explode('.',$file['name']);
                $file_ext = strtolower(end($fileNameArr));
                $fileName .= '.'.$file_ext;
                $initializationName = substr(strrchr(get_class($this), "\\"), 1);
                $uploaddir = 'uploads/'.$initializationName.'/';
                if (!is_dir($uploaddir) && !mkdir($uploaddir)){
                    die("Error creating folder $uploaddir");
                }

                $fileName = $uploaddir.$fileName;
                move_uploaded_file($file_tmp, $fileName);

                $data[$key] = $fileName;
            }
        }

        if($id = $this->queryBuilder()->table($tableName)->insert($data)->execute()){
            return $id;
        }


    }

    public static function find($tableName, $column, $value){
        return (object)self::queryBuilder()->table($tableName)->select()->where($column, $value)->limit(1)->get();
    }

    public function update($id, $data){
        return self::queryBuilder()->table($this->tableName())->update()->set($data)->where($this->primaryKey(), $id)->execute();
    }

    public function delete($column, $id){
        return self::queryBuilder()->table($this->tableName())->delete()->where($column, $id)->execute();
    }


    public static function queryBuilder(){
        return Application::$app->db->query;
    }
}