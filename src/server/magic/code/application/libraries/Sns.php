<?php defined('SYSPATH') OR die('No direct access allowed.');
class Sns {
 
	//这里添加 constructor/methods/properties
	public function __construct()
	{
		
	}
	
	public static function factory($mode, $api_key, $secret, $app_id = null, $app_name = null, $extend_pramas = array())
	{
		//return new Scene($name, $data, $type);
		// Set driver name
		$driver = 'Sns_'.ucfirst($mode).'_Driver';
		// Load the driver
		if ( ! Kohana::auto_load($driver))
			throw new Kohana_Exception('core.driver_not_found', $driver, get_class('Sns'));
 
		// Initialize the driver
		return new $driver($api_key, $secret, $app_id, $app_name, $extend_pramas);
	}
}