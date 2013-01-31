<?php

class Hapyfish2_Magic_Cache_Mooch
{
    public static function getMoochDeskList($uid, $ids)
    {
    	$keys = array();
    	foreach ($ids as $id) {
    		$keys[] = 'i:u:mooch:desk:' . $uid . ':' . $id;
    	}
    	$cache = Hapyfish2_Cache_Factory::getMC($uid);
    	$data = $cache->getMulti($keys);
    	
    	$list = array();
    	foreach ($ids as $id) {
    		$key = 'i:u:mooch:desk:' . $uid . ':' . $id;
    		$list[$id] = $data[$key];
    	}
    	
    	return $list;
    }
	
	public static function getMoochDesk($uid, $id)
    {
        $key = 'i:u:mooch:desk:' . $uid . ':' . $id;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		if ($data === false) {
			return array();
		}
		
		return $data;
    }
    
    public static function moochDesk($uid, $id, $data)
    {
        $key = 'i:u:mooch:desk:' . $uid . ':' . $id;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $data);
    }
    
    public static function clearMoochDesk($uid, $id)
    {
        $key = 'i:u:mooch:desk:' . $uid . ':' . $id;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $cache->delete($key);
    }

}