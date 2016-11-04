<?php

class Factory
{
    public static $rules = [];
    public static $users = [];

    public static function getRule($rule)
    {
        $rule_name = is_array($rule) ? $rule['name'] : $rule;
        if (!isset(static::$rules[$rule_name])) {
            static::$rules[$rule_name] = new Authority_Rule($rule);
        }

        return static::$rules[$rule_name];
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
