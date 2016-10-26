<?php

class Model
{
    protected $_database = 'default';

    // pdo instance for model
    protected $_db;

    // pdo prepare statements
    protected $_prepared = [];

    public function __construct($database = 'default')
    {
        $this->_database = $database;
        $this->connect();
        $this->ping();
    }

    protected function connect()
    {
        $db = include ROOT.'/config/database.php';
        $config = $db[$this->_database];
        $this->_db = new PDO(
            $config['connection_string'],
            $config['username'],
            $config['password'],
            $config['driver_options']
        );
    }

    public function getStatement($sql)
    {
        $mark = md5($sql);
        if (!isset($this->_prepared[$mark])) {
            $this->_prepared[$mark] = $this->prepare($sql);
        }

        return $this->_prepared[$mark];
    }

    public function __call($method, $arguments)
    {
        if ($this->_db && method_exists($this->_db, $method)) {
            return call_user_func_array([$this->_db, $method], $arguments);
        }

        return false;
    }

    protected function ping()
    {
        try {
            $this->_db->getAttribute(PDO::ATTR_SERVER_INFO);
        } catch (Exception $e) {
            if ($e->getCode() == 'HY000') {
                $this->_db = null;
                $this->_prepared = [];
                $this->connect();
            } else {
                throw $e;
            }
        }
    }
}
