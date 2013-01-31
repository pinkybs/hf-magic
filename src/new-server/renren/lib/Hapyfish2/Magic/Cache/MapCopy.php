<?php

class Hapyfish2_Magic_Cache_MapCopy
{
	public static function getAllIds($uid)
    {
        $key = 'm:u:mapmst:all:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $ids = $cache->get($key);

        if ($ids === false) {
        	try {
	            $dal = Hapyfish2_Magic_Dal_MapCopyMonster::getDefaultInstance();
	            $ids = $dal->getAllIds($uid);
	            if ($ids) {
	            	$cache->add($key, $ids);
	            } else {
	            	return null;
	            }
        	} catch (Exception $e) {
        		return null;
        	}
        }

        return $ids;
    }

    public static function reloadAllIds($uid)
    {
        try {
            $dal = Hapyfish2_Magic_Dal_MapCopyMonster::getDefaultInstance();
            $ids = $dal->getAllIds($uid);
            if ($ids) {
        		$key = 'm:u:mapmst:all:' . $uid;
        		$cache = Hapyfish2_Cache_Factory::getMC($uid);
            	$cache->set($key, $ids);
            } else {
            	return null;
            }

            return $ids;
        } catch (Exception $e) {
        	return null;
        }
    }

    public static function clearAllIds($uid)
    {
        $key = 'm:u:mapmst:all:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        return $cache->delete($key);
    }

    public static function pushOneIdInAll($uid, $id)
    {
        $key = 'm:u:mapmst:all:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $ids = $cache->get($key);

        if ($ids === false) {
			return null;
        } else {
        	if (empty($ids)) {
        		$ids = array($id);
        	} else {
        		$ids[] = $id;
        	}
        	$cache->set($key, $ids);
        	return $ids;
        }
    }

    public static function popOneIdInAll($uid, $id)
    {
        $key = 'm:u:mapmst:all:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $ids = $cache->get($key);

        if ($ids === false) {
			return null;
        } else {
        	if (empty($ids)) {
        		return null;
        	} else {
	    		$newIds = array();
	    		foreach ($ids as $v) {
	    			if ($v != $id) {
	    				$newIds[] = $v;
	    			}
	    		}
	    		$cache->set($key, $newIds);
	    		return $newIds;
        	}
        }
    }

}