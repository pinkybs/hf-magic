<?php

class Hapyfish2_Magic_Cache_Desk
{
	public static function getAllIds($uid)
    {
        $key = 'm:u:dkids:all:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $ids = $cache->get($key);

        if ($ids === false) {
        	try {
	            $dalDesk = Hapyfish2_Magic_Dal_Desk::getDefaultInstance();
	            $ids = $dalDesk->getAllIds($uid);
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
	
	public static function getInSceneIds($uid)
    {
        $key = 'm:u:dkids:inscene:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $ids = $cache->get($key);

        if ($ids === false) {
        	try {
	            $dalDesk = Hapyfish2_Magic_Dal_Desk::getDefaultInstance();
	            $ids = $dalDesk->getInSceneIds($uid);
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
    
	public static function getInBagIds($uid)
    {
        $allIds = self::getAllIds($uid);
    	if (!$allIds) {
    		return null;
    	}
    	
    	$inSceneIds = self::getInSceneIds($uid);
    	if ($inSceneIds) {
    		$ids = array_diff($allIds, $inSceneIds);
    	} else {
    		$ids = $allIds;
    	}
        
        return $ids;
    }
    
    public static function reloadAllIds($uid)
    {
        try {
            $dalDesk = Hapyfish2_Magic_Dal_Desk::getDefaultInstance();
            $ids = $dalDesk->getAllIds($uid);
            if ($ids) {
        		$key = 'm:u:dkids:all:' . $uid;
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
    
    public static function reloadInSceneIds($uid)
    {
		try {
	    	$dalDesk = Hapyfish2_Magic_Dal_Desk::getDefaultInstance();
			$ids = $dalDesk->getInSceneIds($uid);
	            
			if ($ids) {
				$key = 'm:u:dkids:inscene:' . $uid;
				$cache = Hapyfish2_Cache_Factory::getMC($uid);
				$cache->set($key, $ids);
			} else {
				return null;
			}
			
			return $ids;
		}catch (Exception $e) {
			return null;
		}
    }

    public static function popOneIdInScene($uid, $id)
    {
        $key = 'm:u:dkids:inscene:' . $uid;
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
    
    public static function pushOneIdInScene($uid, $id)
    {
        $key = 'm:u:dkids:inscene:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $ids = $cache->get($key);

        if ($ids === false) {
        	return null;
        } else {
        	$contain = false;
        	if (empty($ids)) {
        		$ids = array($id);
        	} else {
				foreach ($ids as $v) {
        			if ($v == $id) {
        				$contain = true;
        				break;
        			}
        		}
				if (!$contain) {
					$ids[] = $id;
        		}
        	}
        	if(!$contain) {
				$cache->set($key, $ids);
        	}
			return $ids;
        }
    }
    
    public static function pushOneIdInAll($uid, $id)
    {
        $key = 'm:u:dkids:all:' . $uid;
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
        $key = 'm:u:dkids:all:' . $uid;
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