<?php

class MyLib_Cache_Memcached 
{
    protected static $_instance;
    
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
    protected $_memcache = null;
    
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
                    'port' => 11211, 
                    'persistent' => true)
            );
        }
        
        $this->_memcache = new Memcache;
        
        foreach ($MemcacheOptions['server'] as $server ) {
            $this->_memcache->addServer($server['host'], $server['port'], $server['persistent']);
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
    public function add($id, $data, $time)
    {
        return $this->_memcache->add($id, $data, 0, $time);
    }

    /**
     * delete memcache 
     *
     * @param string $parms
     * @return boolean
     */
    public function delete($id)
    {
        return $this->_memcache->delete($id);
    }
    
}