<?php defined ( 'SYSPATH' ) or die ( 'No direct access allowed.' );
/*
 * Created on 2009-3-19
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class event_Core {
	public static function eventLoad($name, $required = true)
	{
		if (!strstr($name, '.')) {
			$name = &$name;
			$path = 'config';
		} else {
			// Convert dot-noted key string to an array
			$keys = explode('.', $name, 2);
			$name = $keys[1];
			$path = 'config'.'/'.$keys[0];
		}
		
		if ($file = Kohana::find_file($path, $name, $required))
		{
			require $file;
		}
	}
}
?>
