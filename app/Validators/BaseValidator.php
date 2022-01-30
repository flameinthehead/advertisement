<?php

namespace App\Validators;

abstract class BaseValidator
{
    const REQUIRED = 'required';
    const STRING = 'string';
    const NUMERIC = 'numeric';
    const URL = 'url';
    const ERROR_MESSAGES = [
        self::REQUIRED => 'Field %s is required',
        self::STRING => 'Field %s is not string',
        self::NUMERIC => 'Field %s is not numeric',
        self::URL => 'Field %s is not url',
    ];

    protected $request;
    protected $errors;
    protected $rules;
    protected $validated;

    public function getErrors()
    {
        return $this->errors;
    }

    public function getFirstErrorMessage()
    {
        foreach($this->errors as $fieldName => $rules){
            return $this->getErrorMessage($fieldName, reset($rules));
        }
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
            } elseif($rule == self::URL) {
                $this->url($field, $value);
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

    protected function url($field, $value)
    {
        if(!filter_var($value, FILTER_VALIDATE_URL)){
            $this->errors[$field][] = self::URL;
        }
    }

    protected static function rules()
    {
        return [];
    }

    protected static function mapErrorMessage($rule)
    {
        return self::ERROR_MESSAGES[$rule] ?? null;
    }
}
