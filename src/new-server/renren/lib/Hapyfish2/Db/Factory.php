<?php

class Hapyfish2_Db_Factory
{
    protected static $_basicPool = array();
    protected static $_eventPool = array();

	protected static $_pool = array();

    protected static function getDBKey($uid = null)
    {
    	$id = $uid % DATABASE_NODE_NUM;
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

    public static function getDB($uid)
    {
    	$key = self::getDBKey($uid);
    	if (!isset(self::$_pool[$key])) {
    		include CONFIG_DIR . '/database.php';
    		$params = $DATABASE_LIST[$key];
    		$dbAdapter = self::buildAdapter($params);
    		self::$_pool[$key] = array('r' => $dbAdapter, 'w' => $dbAdapter);
    	}

        return self::$_pool[$key];
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


	public static function getEventDB($key)
    {
    	if (!isset(self::$_eventPool[$key])) {
    		include CONFIG_DIR . '/database-event.php';
    		$params = $DATABASE_EVENT_LIST[$key];
    		$dbAdapter = self::buildAdapter($params);
    		self::$_eventPool[$key] = array('r' => $dbAdapter, 'w' => $dbAdapter);
    	}

        return self::$_eventPool[$key];
    }

}