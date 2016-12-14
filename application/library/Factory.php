<?php

class Factory
{
    public static $apps = [];
    public static $users = [];

    public static function getApp($app)
    {
        $app_key = is_array($app) ? $app['app_key'] : $app;
        if (!isset(static::$apps[$app_key])) {
            static::$apps[$app_key] = new Authority_Rule($app_key);
        }

        return static::$apps[$app_key];
    }

    public static function getUser($user)
    {
        $uid = is_array($user) ? $user['id'] : $user;
        if (!isset(static::$users[$uid])) {
            static::$users[$uid] = new Authority_User($user);
        }

        return static::$users[$uid];
    }
}
