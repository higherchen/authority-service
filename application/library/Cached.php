<?php

class Cached
{
    protected static $_instance;

    public static function getMemcached()
    {
        if (static::$_instance === null) {
            $config = Yaf_Registry::get('config')->memcached->toArray();
            $memObj = new Memcached();
            $memObj->addServer($config['ip'], $config['port']);
            static::$_instance = $memObj;
        }

        return static::$_instance;
    }
}
