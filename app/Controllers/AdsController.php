<?php

namespace App\Controllers;

use App\Response;
use App\Validators\AdsValidator;

// контроллер для работы с объявлениями

class AdsController
{
    private $validator;

    // простой вариант Dependency Injection - прокидывание валидаторов через конструктор
    public function __construct(AdsValidator $validator)
    {
        $this->validator = $validator;
    }

    // добавление нового
    public function add()
    {
        if($this->validator->validate()){

        } else {
            return new Response(Response::HTTP_OK, [
                // т.к. у нас по заданию должно быть одно сообщение, берём первое попавшееся
                'message' => $this->validator->getFirstErrorMessage(),
                'code' => Response::HTTP_BAD_REQUEST,
                'data' => new \stdClass(),
            ]);
        }
    }

    // открутка
    public function relevant()
    {

    }

    // редактирование
    public function edit()
    {

    }
}
