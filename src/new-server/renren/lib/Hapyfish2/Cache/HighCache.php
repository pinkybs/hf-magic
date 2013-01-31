<?php

class Hapyfish2_Cache_HighCache 
{
    protected static $_instance;
    
    protected $_cache = null;
    
    /**
     * Single Instance
     *
     * @return Hapyfish2_Cache_HighCache
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
	public function __construct()
	{
		$this->_cache = array();
	}
    
    public function set($key, $data)
    {
        $this->_cache[$key] = $data;
    }
    
    public function setMulti($items, $time = 0)
    {
    	foreach ($items as $key => $value) {
    		$this->_cache[$key] = $value;
    	}
    }

    public function delete($key)
    {
        unset($this->_cache[$key]);
    }
    
    public function get($key)
    {
    	if (isset($this->_cache[$key])) {
    		return $this->_cache[$key];
    	}
    	
    	return null;
    }
    
    public function getMulti($keys)
    {
		$data = array();
    	foreach ($keys as $key) {
    		if (isset($this->_cache[$key])) {
    			$data[$key] = $this->_cache[$key];
    		} else {
    			$data[$key] = null;
    		}
    	}
    	
    	return $data;
    }
    
    public function flush()
    {
    	$this->_cache = array();
    }
}