<?php

class Hapyfish2_Platform_Bll_Friend
{
    public static function getFriend($uid)
    {
        $hc = Hapyfish2_Cache_HighCache::getInstance();
        $key = 'p:f:' . $uid;
        $data = $hc->get($key);
        
        if (!$data) {
        	$data = Hapyfish2_Platform_Cache_Friend::getFriend($uid);
        	if ($data) {
        		$hc->set($key, $data);
        	}
        }
        
        return $data;
    }
    
    public static function updateFriend($uid, $fids, $highcache = false)
    {
        $count = count($fids);
        $fids = join(',', $fids);
    	$res = Hapyfish2_Platform_Cache_Friend::updateFriend($uid, $fids, $count);
        if ($res) {
        	$data = array(
	        	'uid' => $uid,
        		'fids' => $fids,
	        	'count' => $count
        	);
        	if ($highcache) {
	        	$hc = Hapyfish2_Cache_HighCache::getInstance();
	        	$key = 'p:f:' . $uid;
	        	$hc->set($key, $data);
        	}
        	
        	return $data;
        }
        
        return null;
    }
    
    public static function addFriend($uid, $fids, $highcache = false)
    {
        $count = count($fids);
        $fids = join(',', $fids);
    	$res = Hapyfish2_Platform_Cache_Friend::addFriend($uid, $fids, $count);
        if ($res) {
        	$data = array(
	        	'uid' => $uid,
        		'fids' => $fids,
	        	'count' => $count
        	);
        	if ($highcache) {
	        	$hc = Hapyfish2_Cache_HighCache::getInstance();
	        	$key = 'p:f:' . $uid;
	        	$hc->set($key, $data);
        	}
        	
        	return $data;
        }
        
        return null;
    }
    
    public static function getFriendIds($uid)
    {
    	$data = self::getFriend($uid);
    	if ($data) {
    		$fids = $data['fids'];
    		$fids = explode(',', $fids);
    		return $fids;
    	}
    	
    	return null;
    }
    
    public static function getFriendCount($uid)
    {
        $data = self::getFriend($uid);
        
        if (empty($data)) {
            return 0;
        }

        return $data['count'];
    }
    
    public static function isFriend($uid, $fid)
    {
        $fids = self::getFriendIds($uid);
        
        if (empty($fids)) {
            return false;
        }
                
        return in_array($fid, $fids);
    }

}