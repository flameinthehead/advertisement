<?php

namespace App;

class Response
{
    const HTTP_OK = 200;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_NOT_FOUND = 404;

    private $status;
    private $return;

    public function __construct($status = '', $return = null)
    {
        $this->status = $status;
        $this->return = $return;
    }
}
