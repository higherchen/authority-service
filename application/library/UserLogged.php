<?php

/**
 * 自系统登陆用户权限
 *
 * @access public
 *
 */

class UserLogged
{
    const AUTH_RULE = 1;

    protected static $current_user;
    protected static $items;

    public static function getUser()
    {
        if (static::$current_user === null) {
            static::$current_user = static::getUserById($_SESSION['uid']);
        }

        return static::$current_user;
    }

    public static function getUserById($uid)
    {
        $cached = Cached::getMemcached();
        $key = "user:auth:{$uid}";
        $user_auth = json_decode($cached->get($key), true);
        if (!$user_auth) {
            $user = Factory::getUser($uid);
            $rule = Factory::getRule('authority');

            $user_auth = $user->getAuth($rule);
            $user_auth['user'] = $user->getUser();
            $user_auth['assignable'] = $user->getAssignableGroup($rule);

            $cached->set($key, json_encode($user_auth), 0);
            $user_auth = json_decode(json_encode($user_auth), true);

            // 保存的user_ids
            $user_ids = $cached->get('user_ids') ? : [];
            $user_ids[] = $uid;
            array_unique($user_ids);
            $cached->set('user_ids', $user_ids, 0);
        }

        return $user_auth;
    }

    public static function flushCache()
    {
        $cached = Cached::getMemcached();
        $keys = [];
        foreach ($cached->get('user_ids') as $user_id) {
            $keys[] = "user:auth:{$user_id}";
        }
        $cached->deleteMulti($keys);
    }
}
