<?php

class Happyfish_Cache_Memcached_real
{
    protected static $_instance;
    
    const LIFE_TIME_ONE_MINUTE = 60;
    const LIFE_TIME_ONE_HOUR = 3600;
    const LIFE_TIME_ONE_DAY = 86400;
    const LIFE_TIME_ONE_WEEK = 604800;
    const LIFE_TIME_ONE_MONTH = 2592000;
    const LIFE_TIME_MAX = 0;
    
    /**
     * Single Instance
     *
     * @return Hapyfish_Cache_Memcached
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Memcache object
     *
     * @var mixed memcache object
     */
    protected $_memcached = null;
    
    /**
     * Constructor
     *
     * @param array $options associative array of options
     * @throws Zend_Cache_Exception
     * @return void
     */
    public function __construct()
    {
        if (Zend_Registry::isRegistered('MemcacheOptions')) {
            $MemcacheOptions = Zend_Registry::get('MemcacheOptions');
        }
        else {
            $MemcacheOptions = array(
                'server' => array(
                    'host' => '127.0.0.1', 
                    'port' => 11211)
            );
        }
        
        $this->_memcached = new Memcached();
        
        foreach ($MemcacheOptions['server'] as $server ) {
            $this->_memcached->addServer($server['host'], $server['port']);
        }
    }
    
    public function lock($key, $time = 5)
    {
    	return $this->add($key, 1, $time);
    }
    
    public function unlock($key)
    {
        return $this->delete($key);
    }
    

    /**
     * add memcache 
     *
     * @param string $parms
     * @return boolean
     */
    public function add($id, $data, $time = 0)
    {
        return $this->_memcached->add($id, $data, $time);
    }

    /**
     * delete memcache 
     *
     * @param string $parms
     * @return boolean
     */
    public function delete($id)
    {
        return $this->_memcached->delete($id);
    }
    
    public function get($id)
    {
    	return $this->_memcached->get($id);
    }
    
    public function getMulti($ids)
    {
    	$null = null;
    	return $this->_memcached->getMulti($ids, $null, Memcached::GET_PRESERVE_ORDER);
    }
    
    public function setMulti($items, $time = 0)
    {
    	return $this->_memcached->setMulti($items, $time);
    }
    
    public function set($id, $data, $time = 0)
    {
    	return $this->_memcached->set($id, $data, $time);
    }
    
    public function replace($id, $data, $time = 0)
    {
    	return $this->_memcached->replace($id, $data, $time);
    }
    
    public function increment($id, $value)
    {
    	return $this->_memcached->increment($id, $value);
    }
    
    public function decrement($id, $value)
    {
    	return $this->_memcached->decrement($id, $value);
    }  
}