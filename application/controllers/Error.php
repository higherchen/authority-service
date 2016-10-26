<?php

class ErrorController extends Yaf_Controller_Abstract
{

    public function errorAction($exception)
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(
            [
                'code' => $exception->getCode(),
                'msg' => $exception->getMessage(),
            ]
        );
        exit;
    }
}
