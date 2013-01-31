<?php

class Hapyfish2_Util_Context
{
	protected static $_instance;
	
	protected $_data;
	
	/**
	 * 
	 *
	 * @return Hapyfish2_Util_Context
	 */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
	public function __construct()
	{
		$this->_data = array();
	}
	
	public function get($key)
	{
		return $this->_data[$key];
	}
	
	public function set($key, $data)
	{
		$this->_data[$key] = $data;
	}
	
	public function setData($data)
	{
		foreach ($data as $k => $v) {
			$this->_data[$k] = $v;
		}
	}

}