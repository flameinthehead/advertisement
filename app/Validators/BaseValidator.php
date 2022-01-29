<?php

namespace App\Validators;

abstract class BaseValidator
{
    const REQUIRED = 'required';
    const STRING = 'string';
    const NUMERIC = 'numeric';
    const URL = 'url';

    protected $request;
    protected $errors;
    protected $rules;

    public function getErrors()
    {
        return $this->errors;
    }

    protected function baseValidateValue($value)
    {
        $value = stripslashes($value);
        $value = htmlspecialchars($value);
        return trim($value);
    }

    protected function specifyFieldValidate($field, $value, $fieldRules)
    {
        $fieldRules = $fieldRules ? explode('|', $fieldRules) : null;

        if(empty($fieldRules)){
            return;
        }
        foreach($fieldRules as $rule){
            if($rule == self::REQUIRED){
                $this->required($field, $value);
            } elseif($rule == self::STRING) {
                $this->string($field, $value);
            } elseif($rule == self::NUMERIC) {
                $this->numeric($field, $value);
            }
        }
    }

    protected function required($field, $value)
    {
        if(empty($value)){
            $this->errors[$field][] = self::REQUIRED;
        }
    }

    protected function string($field, $value)
    {
        if(!is_string($value)){
            $this->errors[$field][] = self::STRING;
        }
    }

    protected function numeric($field, $value)
    {
        if(!is_numeric($value)){
            $this->errors[$field][] = self::NUMERIC;
        }
    }

    protected static function rules()
    {
        return [];
    }
}
