<?php

class Common
{
    public static function verifySession($username, $session_id)
    {
        $dashboard = new DashboardAPI();
        return $dashboard->verifySession($username, $session_id);
    }

    public static function jsonReturn($data)
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);

        return true;
    }
}
