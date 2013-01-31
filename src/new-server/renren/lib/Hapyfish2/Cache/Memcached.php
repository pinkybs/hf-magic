<?php

class Hapyfish2_Cache_Memcached
{
    protected static $_instance;

    const LIFE_TIME_ONE_MINUTE = 60;
    const LIFE_TIME_ONE_HOUR = 3600;
    const LIFE_TIME_ONE_DAY = 86400;
    const LIFE_TIME_ONE_WEEK = 604800;
    const LIFE_TIME_ONE_MONTH = 2592000;
    const LIFE_TIME_MAX = 0;


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

    public function add($id, $data, $time = 0)
    {
        return $this->_memcached->add($id, $data, $time);
    }

    public function addMulti($items, $time = 0)
    {
    	foreach ($items as $key => $value) {
    		$this->_memcached->add($key, $value, $time);
    	}
    }

    public function delete($id)
    {
        return $this->_memcached->delete($id);
    }

    public function get($id)
    {
    	return $this->_memcached->get($id);
    }

    /**
     * get multiple items at once
     *
     * @param array $ids
     * @return array
     */
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

    public function replaceMulti($items, $time = 0)
    {
        foreach ($items as $key => $value) {
    		$this->_memcached->replace($key, $value, $time);
    	}
    }

    public function increment($id, $value)
    {
    	$this->_memcached->increment($id, $value);
        if ($this->isNotFound()) {
    		$this->_memcached->add($id, $value);
    	}
    	return $this->getResultCode() == Memcached::RES_SUCCESS;
    }

    public function decrement($id, $value)
    {
    	return $this->_memcached->decrement($id, $value);
    	if ($this->isNotFound()) {
    		$this->_memcached->add($id, $value);
    	}
    	return $this->getResultCode() == Memcached::RES_SUCCESS;
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