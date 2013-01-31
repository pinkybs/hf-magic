<?php

class Hapyfish2_Magic_HFC_FloorBag
{
	public static function getUserFloor($uid)
    {
        $key = 'm:u:floorbag:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);
        if ($data === false) {
    		try {
	    		$dalFloorBag = Hapyfish2_Magic_Dal_FloorBag::getDefaultInstance();
	    		$result = $dalFloorBag->get($uid);
    			if ($result) {
	            	$data = array();
	            	foreach ($result as $cid => $count) {
	            		$data[$cid] = array($count, 0);
	            	}
	            	$cache->add($key, $data);
	            } else {
	            	return array();
	            }
    		} catch (Exception $e) {
    			info_log($e->getMessage(), 'err.db');
    			return null;
    		}
    	}
        
        $floors = array();
        foreach ($data as $cid => $floor) {
        	$floors[$cid] = array('count' => (int)$floor[0], 'update' => $floor[1]);
        }
        
        return $floors;
    }
    
    public static function updateUserFloor($uid, $floors, $savedb = false)
    {
        $key = 'm:u:floorbag:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        
        if (!$savedb) {
        	$savedb = $cache->canSaveToDB($key, 900);
        }

        if ($savedb) {
            $data = array();
        	foreach ($floors as $cid => $floor) {
        		$data[$cid] = array($floor['count'], 0);
        	}
        	$ok = $cache->save($key, $data);
        	if ($ok) {
        		try {
	        		$dalFloorBag = Hapyfish2_Magic_Dal_FloorBag::getDefaultInstance();
	        		foreach ($floors as $cid => $floor) {
	        			if ($floor['update']) {
	        				$dalFloorBag->update($uid, $cid, $floor['count']);
	        			}
	        		}
        		} catch (Exception $e) {
        			
        		}
        	}
        	
        	return $ok;
        } else {
            $data = array();
        	foreach ($floors as $cid => $floor) {
        		$data[$cid] = array($floor['count'], $floor['update']);
        	}
        	return $cache->update($key, $data);
        }
    }
    
    public static function addUserFloor($uid, $cid, $count = 1, $floors = null)
    {
    	if (!$floors) {
	    	$floors = self::getUserFloor($uid);
	    	if ($floors === null) {
	    		return false;
	    	}
    	}
    	
    	if (isset($floors[$cid])) {
    		$floors[$cid]['count'] += $count;
    		$floors[$cid]['update'] = 1;
    	} else {
    		$floors[$cid] = array('count' => $count, 'update' => 1);
    	}

    	$ok = self::updateUserFloor($uid, $floors, true);
    	if ($ok) {
    		$addDecorBag = array($cid, $count, $cid);
    		Hapyfish2_Magic_Bll_UserResult::addField($uid, 'addDecorBag', array($addDecorBag));
    	}
    	
    	return $ok;
    }
    
    public static function useUserFloor($uid, $cid, $count = 1, $floors = null)
    {
        if (!$floors) {
	    	$floors = self::getUserFloor($uid);
	    	if ($floors === null) {
	    		return false;
	    	}
    	}

        if (!isset($floors[$cid]) || $floors[$cid]['count'] < $count) {
    		return false;
    	} else {
    		$floors[$cid]['count'] -= $count;
    		$floors[$cid]['update'] = 1;
    		$ok = self::updateUserFloor($uid, $floors);
    	    if ($ok) {
    			$removeDecorBag = array($cid, $count, $cid);
    			Hapyfish2_Magic_Bll_UserResult::addField($uid, 'removeDecorBag', array($removeDecorBag));
    		}
    		
    		return $ok;
    	}
    }
    
}