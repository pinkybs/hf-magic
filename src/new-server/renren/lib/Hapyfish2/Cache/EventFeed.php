<?php

class Hapyfish2_Cache_EventFeed
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

    public function set($key, $data, $time = 0)
    {
    	return $this->_memcached->set($key, $data, $time);
    }

    public function insert($key, $feed, $time = 0, $maxLen = 100)
    {
    	$try = 5;
    	$null = null;
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

    		array_unshift($data, $feed);

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