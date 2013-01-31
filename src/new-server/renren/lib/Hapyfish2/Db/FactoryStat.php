<?php

class Hapyfish2_Db_FactoryStat
{
	protected static $_log = null;
    
    public static function getStatLogDB()
    {
    	if (self::$_log === null) {
    		include CONFIG_DIR . '/database-stat.php';
    		$params = $DATABASE_STAT;
    		$dbAdapter = self::buildAdapter($params);
    		self::$_log = array('r' => $dbAdapter, 'w' => $dbAdapter);
    	}
    	
    	return self::$_log;
    }
    
    public static function buildAdapter($params)
	{
	    $params['driver_options'] = array(
	        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
	        PDO::ATTR_TIMEOUT => 4
	    );
	    
	    $dbAdapter =  Zend_Db::factory('PDO_MYSQL', $params);
	    $dbAdapter->query("SET NAMES utf8");
	
	    return $dbAdapter;
	}
}