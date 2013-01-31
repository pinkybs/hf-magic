<?php

class Hapyfish2_Cache_Lock
{
    /**
     * Memcached object
     *
     * @var mixed memcached object
     */
    protected $_memcached = null;
    
    public function __construct($mc)
    {
        $this->_memcached = $mc;
    }
    
    public function lock($key, $time = 5)
    {
    	return $this->_memcached->add('lock:' . $key, 1, $time);
    }
    
    public function unlock($key)
    {
        return $this->_memcached->delete('lock:' . $key);
    }
}