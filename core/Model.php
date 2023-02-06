<?php

namespace core;

 use function app\Helper;

 abstract class Model
{

    public const RULE_REQUIRED = 'required';
    public const RULE_EMAIL = 'email';
    public const RULE_MIN = 'min';
    public const RULE_MAX = 'max';
    public const RULE_MATCH = 'match';
    public const RULE_UNIQUE = 'unique';
    public const RULE_MIMES = 'mimes';

    public array $errors = [];

    public function loadData($data){
        if($data){
            foreach($data as $key => $value){
                if(property_exists($this, $key)){
                    $this->{$key} = $value;
                }
            }
        }
    }

    abstract public function rules() : array;

    public function validate(){
        foreach($this->rules() as $attribute => $rules){
            $value = $this->{$attribute};
            foreach($rules as $rule){
                $ruleName = $rule;
                if(!is_string($ruleName)){
                    $ruleName = $rule[0];
                }
                if($ruleName === self::RULE_REQUIRED && !$value){
                    $this->addErrorForRule(self::RULE_REQUIRED, ['field' => $attribute]);
                }
                if($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)){
                    $this->addErrorForRule(self::RULE_EMAIL, ['field' => $attribute]);
                }
                if($ruleName === self::RULE_MIN && strlen($value) < $rule['min']){
                    $this->addErrorForRule(self::RULE_MIN, $rule);
                }
                if($ruleName === self::RULE_MAX && strlen($value) > $rule['max']){
                    $this->addErrorForRule(self::RULE_MAX, $rule);
                }
                if($ruleName === self::RULE_MATCH && $value !== $this->{$rule['match']}){
                    $this->addErrorForRule(self::RULE_MATCH, ['field' => $attribute, 'match' => $rule['match']]);
                }
                if($ruleName === self::RULE_UNIQUE){
                    $className = $rule['class'];
                    $attribute = $rule['attribute'] ?? $attribute;
                    $tableName = $className::tableName();
                    $record = Application::$app->db->query->table($tableName)->select('email')->where('email', $value)->get();
                    if($record){
                        $this->addErrorForRule(self::RULE_UNIQUE, ['field' => $attribute]);
                    }
                }
                if($ruleName == self::RULE_MIMES){
                    $mimes = $rule['mimes'];
                    if($_FILES && $_FILES[$attribute]){
                        $fileNameArr = explode('.',$_FILES[$attribute]['name']);
                        $file_ext = strtolower(end($fileNameArr));
                        if(!in_array($file_ext, $mimes)){
                            $this->addErrorForRule(self::RULE_MIMES, ['field' => $attribute]);
                        }
                    }
                }
            }
        }

        return empty($this->errors);
    }

    private function addErrorForRule(string $rule, $params = [], $attribute = null){
        $message = $this->errorMessages()[$rule] ?? '';
        foreach($params as $key => $value){
            $message = str_replace("{{$key}}", $value, $message);
        }
        $this->errors[] = $message;
    }

    public function addError(string $attribute, string $message){
        $this->errors[$attribute][] = $message;
    }

    public function errorMessages(){
        return [
            self::RULE_REQUIRED => '{field} is required',
            self::RULE_EMAIL => '{field} Must be a valid',
            self::RULE_MIN => 'Min length: {min}',
            self::RULE_MAX => 'Max length: {max}',
            self::RULE_MATCH => '{field} must be the same as {match}',
            self::RULE_UNIQUE => 'Record with this {field} already exists',
            self::RULE_MIMES => 'Cant upload this type of {field}'
        ];
    }
}