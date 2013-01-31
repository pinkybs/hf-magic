<?php

class MyLib_Redis_Queue
{
    protected $_name;
    
    protected $_host;
    
    protected $_port;
    
    protected $_redis;
    
    protected $_connected = false;
    
    public static $_instance = null;
    
    public function __construct($name, $host = '127.0.0.1', $port = 6379)
    {
        $this->_name = $name;
        $this->_host = $host;
        $this->_port = $port;
        $this->_redis = new Redis();
    }
    
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
    
    protected function _connect()
    {
        $this->_connected = $this->_redis->connect($this->_host, $this->_port);
        
        if(!$this->_connected) {
            throw new Exception('Can not connecte to redis!');
        }
    }
    
    public function push($object)
    {
        $this->_connect();
        $this->_redis->lPush($this->_name, json_encode($object));
    }
    
    public function pop()
    {
        $this->_connect();
        $object = $this->_redis->rPop($this->_name);
        if ($object) {
            return json_decode($object);
        }
        
        return null;
    }
}