<?php defined ( 'SYSPATH' ) or die ( 'No direct access allowed.' );
class SingleBase {
	protected $role_id;
	private static $instance;
	
	public function __construct($role_id)
	{
		$this->role_id = $role_id;
	}
	
	public static function instance($role_id)
	{
		$class_name = get_called_class();
		if (!isset(self::$instance[$class_name.'_'.$role_id]))
		{
			// Create a new instance
			self::$instance[$class_name.'_'.$role_id] = new $class_name($role_id);
		}

		return self::$instance[$class_name.'_'.$role_id];
	}
}