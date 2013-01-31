<?php defined ( 'SYSPATH' ) or die ( 'No direct access allowed.' );
class trans {
	public static function transTime($time)
	{
		$role_id = Role::getOwnRoleId();
		
		$timestamp = PEAR::getStaticProperty('_APP', 'timestamp');
		
		if ($time < $timestamp) {
			return 0;
		}
		
		return $time - $timestamp;
	}
}