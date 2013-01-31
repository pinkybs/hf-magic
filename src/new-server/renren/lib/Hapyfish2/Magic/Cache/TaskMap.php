<?php

class Hapyfish2_Magic_Cache_TaskMap
{
	public static function getIdsInMap($uid, $pMapId)
    {
        $key = 'm:u:tskmapids:' . $uid . ':' .$pMapId;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $ids = $cache->get($key);

        if ($ids === false) {
        	try {
	            $dal = Hapyfish2_Magic_Dal_TaskMap::getDefaultInstance();
	            $ids = $dal->getTaskMapIds($uid, $pMapId);
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

    public static function reloadIdsInMap($uid, $pMapId)
    {
        try {
            $dal = Hapyfish2_Magic_Dal_TaskMap::getDefaultInstance();
            $ids = $dal->getTaskMapIds($uid, $pMapId);
            if ($ids) {
        		$key = 'm:u:tskmapids:' . $uid . ':' .$pMapId;
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

    public static function clearAllIds($uid, $pMapId)
    {
        $key = 'm:u:tskmapids:' . $uid . ':' .$pMapId;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        return $cache->delete($key);
    }

    public static function pushOneIdInMap($uid, $pMapId, $id)
    {
        $key = 'm:u:tskmapids:' . $uid . ':' .$pMapId;
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

    public static function popOneIdInMap($uid, $pMapId, $id)
    {
        $key = 'm:u:tskmapids:' . $uid . ':' .$pMapId;
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