<?php

namespace App\Controllers;

use App\Response;
use App\Validators\AdsValidator;
use App\Services\AdsService;

// контроллер для работы с объявлениями

class AdsController
{
    private $validator;
    private $service;

    // простой вариант Dependency Injection - прокидывание валидаторов через конструктор
    public function __construct(AdsValidator $validator, AdsService $service)
    {
        $this->validator = $validator;
        $this->service = $service;
    }

    // добавление нового
    public function add()
    {
        if(
            $this->validator->validate()
            && ($ads = $this->service->add($this->validator->getValidated()))
        ){
            return [
                'message' => 'OK',
                'code' => Response::HTTP_OK,
                'data' => $ads,
            ];
        } else {
            return [
                // т.к. у нас по заданию должно быть одно сообщение, берём первое попавшееся
                'message' => $this->validator->getFirstErrorMessage(),
                'code' => Response::HTTP_BAD_REQUEST,
                'data' => new \stdClass(),
            ];
        }
    }

    // открутка
    public function relevant()
    {
        return [
            'message' => 'OK',
            'code' => Response::HTTP_OK,
            'data' => $this->service->relevant(),
        ];
    }

    // редактирование
    public function edit()
    {

    }
}
