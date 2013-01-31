<?php

class Hapyfish2_Cache_Remind
{
    protected static $_instance;

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

    public function delete($key)
    {
        return $this->_memcached->delete($key);
    }
    
    public function get($key)
    {
    	return $this->_memcached->get($key);
    }
    
    public function set($key, $data, $time = 604800)
    {
    	return $this->_memcached->set($key, $data, $time);
    }
    
    public function increment($key, $value)
    {
    	$this->_memcached->increment($key, $value);
    	if ($this->_memcached->getResultCode() == Memcached::RES_NOTSTORED) {
    		$this->_memcached->add($key, $value);
    	}
    }
	
    public function insert($key, $remind, $time = 604800)
    {
    	$try = 5;
    	$null = null;
    	$maxLen = 50;
    	$ok = false;
    	$first = false;
    	while($try > 0) {
    	    $data = $this->_memcached->get($key, $null, $token);
    	    if ($data === false) {
    	        if ($this->_memcached->getResultCode() == Memcached::RES_NOTFOUND) {
    				$data = array();
    				$first = true;
    			} else {
    				break;
    			}
    	    }

    		if (count($data) >= $maxLen) {
    			$data = array_splice($data, 0, $maxLen - 1);
    		}
    		
    		array_unshift($data, $remind);
    		
    		if ($first) {
    			$this->_memcached->add($key, $data, $time);
    		} else {
				$this->_memcached->cas($token, $key, $data, $time);
    		}

			if ($this->_memcached->getResultCode() == Memcached::RES_SUCCESS) {
				$ok = true;
				break;
			}
				
			$try--;

    	}
    	return $ok;
    }
    
}