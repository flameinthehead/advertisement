<?php

namespace App\Controllers;

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
