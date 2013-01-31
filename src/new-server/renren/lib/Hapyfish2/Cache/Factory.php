<?php

class Hapyfish2_Cache_Factory
{
    protected static $_BASIC_POOL = array();

    protected static $_POOL = array();

    protected static $_MC_POOL = array();

    public static function getBasicMC($key)
    {
    	if (!isset(self::$_BASIC_POOL[$key])) {
    		include CONFIG_DIR . '/memcached-basic.php';
    		$server = $MEMCACHED_BASIC_LIST[$key];
    		$mc = new Memcached();
    		$mc->addServer($server['host'], $server['port']);
    		self::$_BASIC_POOL[$key] = new Hapyfish2_Cache_Memcached($mc);
    	}

        return self::$_BASIC_POOL[$key];
    }

    public static function getMCKey($uid)
    {
    	$id = $uid % 10;
    	return 'mc_' . $id;
    }

    public static function getMemcached($key)
    {
    	if (!isset(self::$_MC_POOL[$key])) {
    		include CONFIG_DIR . '/memcached.php';
    		$server = $MEMCACHED_LIST[$key];
    		$mc = new Memcached();
    		$mc->addServer($server['host'], $server['port']);
    		self::$_MC_POOL[$key] = $mc;
    		return $mc;
    	}

        return self::$_MC_POOL[$key];
    }

    public static function getMC($uid)
    {
    	$key = self::getMCKey($uid);
    	$MCkey = 'MC_' . $key;
    	if (!isset(self::$_POOL[$MCkey])) {
			$mc = self::getMemcached($key);
    		self::$_POOL[$MCkey] = new Hapyfish2_Cache_Memcached($mc);
    	}

        return self::$_POOL[$MCkey];
    }

    /**
     * get feed cache object
     *
     * @param int $uid
     * @return Hapyfish2_Cache_Feed
     */
    public static function getFeed($uid)
    {
    	$key = self::getMCKey($uid);
    	$FEEDkey = 'FEED_' . $key;
    	if (!isset(self::$_POOL[$FEEDkey])) {
			$mc = self::getMemcached($key);
    		self::$_POOL[$FEEDkey] = new Hapyfish2_Cache_Feed($mc);
    	}

        return self::$_POOL[$FEEDkey];
    }

    /**
     * get remind cache object
     *
     * @param int $uid
     * @return Hapyfish2_Cache_Remind
     */
    public static function getRemind($uid)
    {
    	$key = self::getMCKey($uid);
    	$REMINDkey = 'FEED_' . $key;
    	if (!isset(self::$_POOL[$REMINDkey])) {
			$mc = self::getMemcached($key);
    		self::$_POOL[$REMINDkey] = new Hapyfish2_Cache_Remind($mc);
    	}

        return self::$_POOL[$REMINDkey];
    }

    /**
     * get HappyFish Cache Object
     *
     * @param int $uid
     * @return Hapyfish2_Cache_HFC
     */
    public static function getHFC($uid)
    {
    	$key = self::getMCKey($uid);
    	$HFCkey = 'HFC_' . $key;
    	if (!isset(self::$_POOL[$HFCkey])) {
			$mc = self::getMemcached($key);
    		self::$_POOL[$HFCkey] = new Hapyfish2_Cache_HFC($mc);
    	}

        return self::$_POOL[$HFCkey];
    }

    /**
     * get lock object
     *
     * @param int $uid
     * @return Hapyfish2_Cache_Lock
     */
    public static function getLock($uid)
    {
    	$key = self::getMCKey($uid);
    	$LOCKkey = 'LOCK_' . $key;
    	if (!isset(self::$_POOL[$LOCKkey])) {
			$mc = self::getMemcached($key);
    		self::$_POOL[$LOCKkey] = new Hapyfish2_Cache_Lock($mc);
    	}

        return self::$_POOL[$LOCKkey];
    }


    /**
     * get event feed cache object
     *
     * @param int $uid
     * @return Hapyfish2_Cache_EventFeed
     */
    public static function getEventFeed($uid = 0)
    {
    	$key = self::getMCKey($uid);
    	$FEEDkey = 'EVENTFEED_' . $key;
    	if (!isset(self::$_POOL[$FEEDkey])) {
			$mc = self::getMemcached($key);
    		self::$_POOL[$FEEDkey] = new Hapyfish2_Cache_EventFeed($mc);
    	}

        return self::$_POOL[$FEEDkey];
    }

	/**
     * get event rank cache object
     *
     * @param int $uid
     * @return Hapyfish2_Cache_EventRank
     */
    public static function getEventRank($uid = 3)
    {
    	$key = self::getMCKey($uid);
    	$rankkey = 'EVENTRANK_' . $key;
    	if (!isset(self::$_POOL[$rankkey])) {
			$mc = self::getMemcached($key);
    		self::$_POOL[$rankkey] = new Hapyfish2_Cache_EventRank($mc);
    	}

        return self::$_POOL[$rankkey];
    }
    
    /**
     * get gift log cache object
     *
     * @param int $uid
     * @return Hapyfish2_Cache_GiftPackage
     */
    public static function getLogData($uid)
    {
    	$key = self::getMCKey($uid);
    	$giftlogkey = 'GIFTLOG_' . $key;
    	if (!isset(self::$_POOL[$giftlogkey])) {
			$mc = self::getMemcached($key);
    		self::$_POOL[$giftlogkey] = new Hapyfish2_Cache_GiftPackage($mc);
    	}

        return self::$_POOL[$giftlogkey];
    }

}