<?php

class Hapyfish2_Platform_Cache_UidMap
{
    public static function getMC($puid)
    {
    	$id = strtolower(substr($puid, -1, 1));
    	$key = 'mc_' . $id;
    	
    	return Hapyfish2_Cache_Factory::getBasicMC($key);
    }
    
    public static function getUids($pids)
    {
    	$keysList = array();
    	foreach ($pids as $puid) {
    		$id = strtolower(substr($puid, -1, 1));
    		$key = 'mc_' . $id;
    		if (isset($keys[$id])) {
    			$keysList[$id][] = $key;
    		} else {
    			$keysList[$id] = array($key);
    		}
    	}
    	
    	$uids = array();
    	foreach ($keysList as $id => $keys) {
    		$uids += self::getSubUids($id, $keys);
    	}
    	
    	sort($uids);
    	
    	return $uids;
    }
    
    public static function getSubUids($id, $keys)
    {
    	$key = 'mc_' . $id;
    	$cache = Hapyfish2_Cache_Factory::getBasicMC($key);
    	$data = $cache->getMulti($keys);
        if ($data === false) {
        	return null;
        }
        
        //check all in memory
        $nocacheKeys = array();
        foreach ($data as $k => $item) {
        	if ($item == null) {
        		$nocacheKeys[] = $k;
        	}
        }
        
		if (!empty($nocacheKeys)) {
			foreach ($nocacheKeys as $key) {
				$tmp = split(':', $key);
				$data[$key] = self::loadOne($tmp[2]);
			}
		}
		
		$uids = array();
		foreach ($data as $item) {
			if ($item && $item[1] == 0) {
				$uids[] = $item[0];
			}
		}
		
		return $uids;
    }
    
    public static function loadOne($puid)
    {
		try {
			$dalUidMap = Hapyfish2_Platform_Dal_UidMap::getDefaultInstance();
			$data = $dalUidMap->getUser($puid);
			if ($data) {
				$cache->add($key, $data);
				return $data;
			} else {
				return null;
			}
		} catch (Exception $e) {
			return null;
		}
    }
	
	public static function getUser($puid)
    {
    	$cache = self::getMC($puid);
    	$key = 'm:id:' . $puid;
		$data = $cache->get($key);
        if ($data === false) {
        	if ($cache->isNotFound()) {
	        	try {
	            	$dalUidMap = Hapyfish2_Platform_Dal_UidMap::getDefaultInstance();
	            	$data = $dalUidMap->getUser($puid);
		            if ($data) {
		            	$cache->add($key, $data);
		            } else {
		            	return null;
		            }
	        	} catch (Exception $e) {
	        		throw new Exception('1002');
	        	}
        	} else {
        		throw new Exception('1001');
        	}
        }
        
        return array('puid' => $puid, 'uid' => $data[0], 'status' => $data[1]);
    }
    
    public static function newUser($puid)
    {
    	try {
    		$dalUidMap = Hapyfish2_Platform_Dal_UidMap::getDefaultInstance();
    		$uid = $dalUidMap->getSequence($puid);
    		$dalUidMap->newUser($uid, $puid, time());

    		$cache = self::getMC($puid);
    		$key = 'm:id:' . $puid;
    		$data = array($uid, 0);
    		$cache->set($key, $data);
    		
    		return array('puid' => $puid, 'uid' => $uid, 'status' => 0);
    	}catch (Exception $e) {
    		
    	}
    	
    	return null;
    }

}