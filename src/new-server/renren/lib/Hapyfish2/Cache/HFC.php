<?php

class Hapyfish2_Cache_HFC
{
    const LIFE_TIME_ONE_MONTH = 2592000;
    const LIFE_TIME_MAX = 0;

    protected $_status;

    /**
     * Memcached object
     *
     * @var mixed memcached object
     */
    protected $_memcached = null;

    public function __construct($mc)
    {
        $this->_memcached = $mc;
        $this->_status = array();
    }

    public function getStatus($key)
    {
    	if (isset($this->_status[$key])) {
    		return $this->_status[$key];
    	}

    	return false;
    }

    public function canSaveToDB($key, $checktime = 900)
    {
        $savedb = false;
    	$status = $this->getStatus($key);
    	if ($status && $status['changed']) {
    		$passTime = time() - $status['time'];
    		if ($passTime > $checktime) {
    			$savedb = true;
    		}
    	}

    	if (defined('APP_SERVER_TYPE') && APP_SERVER_TYPE>1) {
    	    return true;
    	}
    	else {
    	    return $savedb;
    	}
    }

    public function add($key, $data, $time = 0)
    {
        $wt = time();
        $this->_status[$key] = array('changed' => 0, 'time' => $wt);
    	return $this->_memcached->add($key, array($data, 0, $wt), $time);
    }

    public function update($key, $data, $time = 0)
    {
    	$wt = time();
    	$this->_status[$key] = array('changed' => 1, 'time' => $wt);
    	return $this->_memcached->set($key, array($data, 1, $wt), $time);
    }

    public function save($key, $data, $time = 0)
    {
    	$wt = time();
    	$this->_status[$key] = array('changed' => 0, 'time' => $wt);
    	return $this->_memcached->set($key, array($data, 0, $wt), $time);
    }

    public function delete($key)
    {
        return $this->_memcached->delete($key);
    }

    public function get($key)
    {
    	$tmp = $this->_memcached->get($key);
        if (is_array($tmp)) {
        	$this->_status[$key] = array('changed' => $tmp[1], 'time' => $tmp[2]);
            return $tmp[0];
        }

        return false;
    }

    public function getMulti($keys)
    {
    	$null = null;
    	$tmp = $this->_memcached->getMulti($keys, $null, Memcached::GET_PRESERVE_ORDER);
        if (is_array($tmp)) {
        	$data = array();
            foreach ($tmp as $key => $item) {
            	if ($item !== null) {
            		$this->_status[$key] = array('changed' => $item[1], 'time' => $item[2]);
            		$data[$key] = $item[0];
            	} else {
            		$data[$key] = null;
            	}
    		}

    		return $data;
        }

        return false;
    }

    public function addMulti($items, $time = 0)
    {
    	$wt = time();
    	foreach ($items as $key => $value) {
    		$this->_status[$key] = array('changed' => 0, 'time' => $wt);
    		$this->_memcached->add($key, array($value, 0, $wt), $time);
    	}
    }

    public function getResultCode()
    {
    	return $this->_memcached->getResultCode();
    }

    public function isNotFound()
    {
    	return $this->_memcached->getResultCode() == Memcached::RES_NOTFOUND;
    }
}