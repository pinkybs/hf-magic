<?php

class Hapyfish2_Platform_Cache_Friend
{
    public static function getFriend($uid)
    {
        $key = 'p:f:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
		$result = $cache->get($key);
        if ($result === false) {
        	try {
            	$dalFriend = Hapyfish2_Platform_Dal_Friend::getDefaultInstance();
            	$result = $dalFriend->getFriend($uid);
	            if ($result) {
	            	$cache->add($key, $result);
	            } else {
	            	return null;
	            }
        	} catch (Exception $e) {
        		return null;
        	}
        }
        
        return array(
        	'uid' => $result[0],
        	'fids' => $result[1],
        	'count' => $result[2]
        );
    }
    
    public static function updateFriend($uid, $fids, $count)
    {
        $key = 'p:f:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = array($uid, $fids, $count);
        $result = $cache->set($key, $data);
        if ($result) {
        	if ($count < 1000) {
	        	try {
	        		$dalFriend = Hapyfish2_Platform_Dal_Friend::getDefaultInstance();
	        		$r = $dalFriend->update($uid, $fids, $count);
	        		if ($r == 0) {
	        			$dalFriend->add($uid, $fids, $count);
	        		}
	        	} catch (Exception $e) {
	        		info_log($e->getMessage(), 'updateF');
	        	}
        	}
        	return true;
        }
        
        return false;
    }
    
    public static function addFriend($uid, $fids, $count)
    {
        $key = 'p:f:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = array($uid, $fids, $count);
        $result = $cache->set($key, $data);
        if ($result) {
        	try {
        		$dalFriend = Hapyfish2_Platform_Dal_Friend::getDefaultInstance();
        		$dalFriend->add($uid, $fids, $count);
        	} catch (Exception $e) {
        		info_log($e->getMessage(), 'addF');
        	}
        	
        	return true;
        }
        
        return false;
    }
}