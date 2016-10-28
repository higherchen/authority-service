<?php

class Factory
{
    public static $rules = [];
    public static $users = [];

    public static function getRule($rule_name)
    {
        if (!isset(static::$rules[$rule_name])) {
            static::$rules[$rule_name] = new Authority_Rule($rule_name);
        }

        return static::$rules[$rule_name];
    }

    public static function getUser($uid)
    {
        if (!isset(static::$users[$uid])) {
            static::$users[$uid] = new Authority_User($uid);
        }

        return static::$users[$uid];
    }
}
