<?php

class Hapyfish2_Magic_Cache_Wall
{
    public static function getInScene($uid)
    {
        $key = 'm:u:wall:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
        if ($data === false) {
    		try {
	    		$dalWall = Hapyfish2_Magic_Dal_Wall::getDefaultInstance();
	    		$data = $dalWall->get($uid);
	    		if ($data) {
	    			$cache->add($key, $data);
	    		} else {
	    			return null;
	    		}
    		}catch (Exception $e) {
    			return null;
    		}
    	}
    	
    	if (!empty($data)) {
    		return json_decode($data, true);
    	}
    	
    	return array();
    }
    
    public static function saveInScene($uid, $wallData)
    {
        try {
    		$dalWall = Hapyfish2_Magic_Dal_Wall::getDefaultInstance();
    		$dalWall->update($uid, $wallData);
    		return true;
   		}catch (Exception $e) {
   			return false;
   		}
    }
    
    public static function reloadInScene($uid)
    {
       	try {
    		$dalWall = Hapyfish2_Magic_Dal_Wall::getDefaultInstance();
    		$data = $dalWall->get($uid);
    		if ($data) {
         		$key = 'm:u:wall:' . $uid;
        		$cache = Hapyfish2_Cache_Factory::getMC($uid);
    			$cache->set($key, $data);
    			return true;
    		} else {
    			return false;
    		}
   		}catch (Exception $e) {
   			return false;
   		}
    }
    
    public static function updateInScene($uid, $wallData)
    {
		if (is_array($wallData)) {
			$wallData = json_encode($wallData);
		}
		
    	$ok = self::saveInScene($uid, $wallData);
		if ($ok) {
        	$key = 'm:u:wall:' . $uid;
       		$cache = Hapyfish2_Cache_Factory::getMC($uid);
   			$cache->set($key, $wallData);
		}
		
		return $ok;
    }
    
}