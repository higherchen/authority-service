<?php

class Common
{

    public static function jsonReturn($data, $exit = false)
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        $exit && exit();

        return true;
    }
}
