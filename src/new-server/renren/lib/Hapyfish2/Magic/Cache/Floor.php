<?php

class Hapyfish2_Magic_Cache_Floor
{
    public static function getInScene($uid)
    {
        $key = 'm:u:floor:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
        if ($data === false) {
    		try {
	    		$dalFloor = Hapyfish2_Magic_Dal_Floor::getDefaultInstance();
	    		$data = $dalFloor->get($uid);
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
    
    public static function saveInScene($uid, $floorData)
    {
        try {
    		$dalFloor = Hapyfish2_Magic_Dal_Floor::getDefaultInstance();
    		$dalFloor->update($uid, $floorData);
    		return true;
   		}catch (Exception $e) {
   			return false;
   		}
    }
    
    public static function reloadInScene($uid)
    {
       	try {
    		$dalFloor = Hapyfish2_Magic_Dal_Floor::getDefaultInstance();
    		$data = $dalFloor->get($uid);
    		if ($data) {
         		$key = 'm:u:floor:' . $uid;
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
    
    public static function updateInScene($uid, $floorData)
    {
		if (is_array($floorData)) {
			$floorData = json_encode($floorData);
		}
		
    	$ok = self::saveInScene($uid, $floorData);
		if ($ok) {
        	$key = 'm:u:floor:' . $uid;
       		$cache = Hapyfish2_Cache_Factory::getMC($uid);
   			$cache->set($key, $floorData);
		}
		
		return $ok;
    }
    
}