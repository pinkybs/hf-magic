<?php

class Hapyfish2_Platform_Bll_UidMap
{
    public static function getUser($puid)
    {
        $hc = Hapyfish2_Cache_HighCache::getInstance();
        $key = 'm:id:' . $puid;
        $data = $hc->get($key);
        
        if (!$data) {
        	$data = Hapyfish2_Platform_Cache_UidMap::getUser($puid);
        	if ($data) {
        		$hc->set($key, $data);
        	}
        }
        
        return $data;
    }
    
    public static function newUser($puid)
    {
    	try {
			$data = Hapyfish2_Platform_Cache_UidMap::newUser($puid);
			if ($data) {
        		$hc = Hapyfish2_Cache_HighCache::getInstance();
        		$key = 'm:id:' . $puid;
        		$hc->set($key, $data);
        		
        		return $data;
			}
    	}catch (Exception $e) {
    		
    	}
    	
    	return null;
    }

}