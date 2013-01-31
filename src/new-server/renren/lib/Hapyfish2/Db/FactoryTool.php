<?php

class Hapyfish2_Db_FactoryTool
{
    protected static $_basicPool = array();
	
	protected static $_pool = array();
	
	protected static $_log = null;
    
    protected static function getDBKey($id)
    {
    	return 'db_' . $id;
    }
    
    public static function getBasicDB($key)
    {
    	if (!isset(self::$_basicPool[$key])) {
    		include CONFIG_DIR . '/database-basic.php';
    		$params = $DATABASE_BASIC_LIST[$key];
    		$dbAdapter = self::buildAdapter($params);
    		self::$_basicPool[$key] = array('r' => $dbAdapter, 'w' => $dbAdapter);
    	}
    	
        return self::$_basicPool[$key];
    }
    
    public static function getDB($id)
    {
    	$key = self::getDBKey($id);
    	if (!isset(self::$_pool[$key])) {
    		include CONFIG_DIR . '/database.php';
    		$params = $DATABASE_LIST[$key];
    		$dbAdapter = self::buildAdapter($params);
    		self::$_pool[$key] = array('r' => $dbAdapter, 'w' => $dbAdapter);
    	}
    	
        return self::$_pool[$key];
    }
    
    public static function getLogDB()
    {
    	if (self::$_log === null) {
    		include CONFIG_DIR . '/database-log.php';
    		$params = $DATABASE_LOG;
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