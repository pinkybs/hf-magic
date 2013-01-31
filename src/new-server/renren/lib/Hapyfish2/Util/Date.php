<?php

class Hapyfish2_Util_Date
{
	protected static $_instance;
	
	protected $_time;
	
	/**
	 * 
	 *
	 * @return Hapyfish2_Util_Date
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
		$this->_time = time();
	}
	
	public function refresh()
	{
		$this->_time = time();
	}
	
	public function getTime()
	{
		return $this->_time;
	}

}