<?php

class Hapyfish2_Magic_HFC_WallBag
{
	public static function getUserWall($uid)
    {
        $key = 'm:u:wallbag:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);
        if ($data === false) {
    		try {
	    		$dalWallBag = Hapyfish2_Magic_Dal_WallBag::getDefaultInstance();
	    		$result = $dalWallBag->get($uid);
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
    			return null;
    		}
    	}
        
        $walls = array();
        foreach ($data as $cid => $wall) {
        	$walls[$cid] = array('count' => (int)$wall[0], 'update' => $wall[1]);
        }
        
        return $walls;
    }
    
    public static function updateUserWall($uid, $walls, $savedb = false)
    {
        $key = 'm:u:wallbag:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        
        if (!$savedb) {
        	$savedb = $cache->canSaveToDB($key, 900);
        }

        if ($savedb) {
            $data = array();
        	foreach ($walls as $cid => $wall) {
        		$data[$cid] = array($wall['count'], 0);
        	}
        	$ok = $cache->save($key, $data);
        	if ($ok) {
        		try {
	        		$dalWallBag = Hapyfish2_Magic_Dal_WallBag::getDefaultInstance();
	        		foreach ($walls as $cid => $wall) {
	        			if ($wall['update']) {
	        				$dalWallBag->update($uid, $cid, $wall['count']);
	        			}
	        		}
        		} catch (Exception $e) {
        			
        		}
        	}
        	
        	return $ok;
        } else {
            $data = array();
        	foreach ($walls as $cid => $wall) {
        		$data[$cid] = array($wall['count'], $wall['update']);
        	}
        	return $cache->update($key, $data);
        }
    }
    
    public static function addUserWall($uid, $cid, $count = 1, $walls = null)
    {
    	if (!$walls) {
	    	$walls = self::getUserWall($uid);
	    	if ($walls === null) {
	    		return false;
	    	}
    	}
    	
    	if (isset($walls[$cid])) {
    		$walls[$cid]['count'] += $count;
    		$walls[$cid]['update'] = 1;
    	} else {
    		$walls[$cid] = array('count' => $count, 'update' => 1);
    	}

    	$ok = self::updateUserWall($uid, $walls, true);
        if ($ok) {
    		$addDecorBag = array($cid, $count, $cid);
    		Hapyfish2_Magic_Bll_UserResult::addField($uid, 'addDecorBag', array($addDecorBag));
    	}
    	
    	return $ok;
    }
    
    public static function useUserWall($uid, $cid, $count = 1, $walls = null)
    {
        if (!$walls) {
	    	$walls = self::getUserWall($uid);
	    	if ($walls === null) {
	    		return false;
	    	}
    	}

        if (!isset($walls[$cid]) || $walls[$cid]['count'] < $count) {
    		return false;
    	} else {
    		$walls[$cid]['count'] -= $count;
    		$walls[$cid]['update'] = 1;
    		$ok = self::updateUserWall($uid, $walls);
    	    if ($ok) {
    			$removeDecorBag = array($cid, $count, $cid);
    			Hapyfish2_Magic_Bll_UserResult::addField($uid, 'removeDecorBag', array($removeDecorBag));
    		}
    		
    		return $ok;
    	}
    }
    
}