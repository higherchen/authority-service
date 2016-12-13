<?php

class Common
{
    public static function jsonReturn($data, $exit = false)
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        $exit && exit;

        return true;
    }

    public static function abort($error_code, $error_message = '')
    {
        switch ($error_code) {
            case 404:
                header("HTTP/1.0 404 Not Found");
                echo $error_message ?: '404 Page not found';
                break;

            default:
                # code...
                break;
        }
        exit;
    }
}
