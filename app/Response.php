<?php

namespace App;

// класс для работы с ответом (сделано только для json, без абстракций)
class Response
{
    // набор ответов, которые будут использоваться в приложении
    const HTTP_OK = 200;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_NOT_FOUND = 404;

    const JSON_CONTENT_TYPE = 'Content-Type: application/json; charset=utf-8';

    private $status;
    private $return;
    private $answerType;

    public function __construct($status = null, $return = null, $answerType = null)
    {
        $this->status = $status ?? self::HTTP_OK;
        $this->return = $return ?? [];
        $this->answerType = $answerType ?? self::JSON_CONTENT_TYPE;
    }

    public function sendJson()
    {
        http_response_code($this->status);
        header($this->answerType);
        echo json_encode($this->return);
    }
}
