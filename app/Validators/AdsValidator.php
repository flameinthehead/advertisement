<?php

namespace App\Validators;

// валидатор полей объявления
class AdsValidator extends BaseValidator
{
    public function __construct($request)
    {
        $this->request = $request;
    }

    public function validate()
    {
        foreach(self::rules() as $fieldName => $rules){
            $value = $this->request[$fieldName] ?? null;
            $value = $this->baseValidateValue($value);
            $this->specifyFieldValidate($fieldName, $value, $rules);
        }

        return empty($this->errors);
    }

    public static function rules()
    {
        return [
            'text' => self::REQUIRED.'|'.self::STRING,
            'price' => self::REQUIRED.'|'.self::NUMERIC,
            'limit' => self::REQUIRED.'|'.self::NUMERIC,
            'banner' => self::REQUIRED.'|'.self::STRING.'|'.self::URL,
        ];
    }
}
