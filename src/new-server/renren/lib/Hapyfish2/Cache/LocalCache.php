<?php

class Hapyfish2_Cache_LocalCache
{
    protected static $_instance;

    protected $_cache = null;

    protected $_prefix = SNS_ID;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Cache_LocalCache
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

    public function set($key, $data, $varcache = true, $ttl = 0)
    {
        $tkey = $this->_prefix . $key;
    	apc_store($tkey, $data, $ttl);
        if ($varcache) {
    		$this->_cache[$tkey] = $data;
        }
    }

    public function delete($key, $varcache = true)
    {
        $tkey = $this->_prefix . $key;
    	apc_delete($tkey);
        if ($varcache) {
    		unset($this->_cache[$tkey]);
        }
    }

    public function get($key, $varcache = true)
    {
    	$tkey = $this->_prefix . $key;
    	if ($varcache && isset($this->_cache[$tkey])) {
    		return $this->_cache[$tkey];
    	}

    	$data = apc_fetch($tkey);
    	$this->_cache[$tkey] = $data;

    	return $data;
    }

    public function flush()
    {
    	$this->_cache = array();
    	apc_clear_cache('user');
    }
}