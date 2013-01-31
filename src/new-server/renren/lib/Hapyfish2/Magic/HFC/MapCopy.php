<?php

class Hapyfish2_Magic_HFC_MapCopy
{

	public static function getAll($uid)
    {
        $ids = Hapyfish2_Magic_Cache_MapCopy::getAllIds($uid);

        if (!$ids) {
        	return null;
        }

        $keys = array();
        foreach ($ids as $id) {
        	$keys[] = 'm:u:mapmst:' . $uid . ':' . $id;
        }

        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $data = $cache->getMulti($keys);

        if ($data === false) {
        	return null;
        }

        //check all in memory
        $nocacheKeys = array();
        $empty = true;
        foreach ($data as $k => $item) {
        	if ($item == null) {
        		$nocacheKeys[] = $k;
        	} else {
        		$empty = false;
        	}
        }

        if ($empty) {
        	try {
	            $dal = Hapyfish2_Magic_Dal_MapCopyMonster::getDefaultInstance();
	            $result = $dal->getAll($uid);
	            if ($result) {
	            	$data = array();
	            	foreach ($result as $item) {
	            		$key = 'm:u:mapmst:' . $uid . ':' . $item[0];
	            		$data[$key] = $item;
	            	}
	            	$cache->addMulti($data);
	            } else {
	            	return null;
	            }
        	} catch (Exception $e) {
        	    info_log('getAll:'.$e->getMessage(), 'err_HFC_MapCopy');
        		return null;
        	}
        } else if (!empty($nocacheKeys)) {
        	foreach ($nocacheKeys as $key) {
        		$tmp = split(':', $key);
        		$data[$key] = self::loadOne($uid, $tmp[4]);
        	}
        }

        $mapMonster = array();
        foreach ($data as $item) {
        	$mapMonster[$item[0]] = array(
        	    'map_id' => $item[0],
	        	'data' => $item[1]
        	);
        }

		return $mapMonster;
    }

    public static function clearAll($uid)
    {

        $ids = Hapyfish2_Magic_Cache_MapCopy::getAllIds($uid);
        $keys = array();
        if ($ids) {
            foreach ($ids as $id) {
            	$keys[] = 'm:u:mapmst:' . $uid . ':' . $id;
            }

            $cache = Hapyfish2_Cache_Factory::getHFC($uid);
            foreach ($keys as $key) {
                $cache->delete($key);
            }
        }

        return Hapyfish2_Magic_Cache_MapCopy::clearAllIds($uid);
    }

    public static function getOne($uid, $mapId)
    {
    	$key = 'm:u:mapmst:' . $uid . ':' . $mapId;
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
    	$item = $cache->get($key);

    	if ($item === false) {
    		try {
	    		$dal = Hapyfish2_Magic_Dal_MapCopyMonster::getDefaultInstance();
	    		$item = $dal->getOne($uid, $mapId);
	    		if ($item) {
	    			$cache->add($key, $item);
	    		} else {
	    			return null;
	    		}
    		}catch (Exception $e) {
    		    info_log('getOne:'.$e->getMessage(), 'err_HFC_MapCopy');
    			return null;
    		}
    	}

    	return array(
        	'map_id' => $item[0],
        	'data' => $item[1]
        );
    }

    public static function loadOne($uid, $mapId)
    {
		try {
	    	$dal = Hapyfish2_Magic_Dal_MapCopyMonster::getDefaultInstance();
	    	$item = $dal->getOne($uid, $mapId);
	    	if ($item) {
	    		$key = 'm:u:mapmst:' . $uid . ':' . $mapId;
	    		$cache = Hapyfish2_Cache_Factory::getHFC($uid);
	    		$cache->save($key, $item);
	    	} else {
	    		return null;
	    	}
		}catch (Exception $e) {
		    info_log('loadOne:'.$e->getMessage(), 'err_HFC_MapCopy');
			return null;
		}

    	return array(
        	'map_id' => $item[0],
        	'data' => $item[1]
        );
    }

    public static function saveOne($uid, $mapId, $monster)
    {
		try {
    		$info = array(
    			'data' => $monster['data']
    		);

    		$dal = Hapyfish2_Magic_Dal_MapCopyMonster::getDefaultInstance();
    		$dal->update($uid, $mapId, $info);
    	} catch (Exception $e) {
    	    info_log('saveOne:'.$e->getMessage(), 'err_HFC_MapCopy');
    	}
    }

    public static function updateOne($uid, $mapId, $monster, $savedb = false)
    {
    	$key = 'm:u:mapmst:' . $uid . ':' . $mapId;
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
    	$data = array(
    		$monster['map_id'], $monster['data']
    	);

    	if (!$savedb) {
    		$savedb = $cache->canSaveToDB($key, 900);
    	}

    	if ($savedb) {
    		$ok = $cache->save($key, $data);
    		if ($ok) {
	    		//save to db
	    		self::saveOne($uid, $mapId, $monster);
    		}
    	} else {
    		$ok = $cache->update($key, $data);
    	}

    	return $ok;
    }

    public static function removeOne($uid, $mapId)
    {
		$key = 'm:u:mapmst:' . $uid . ':' . $mapId;
		$cache = Hapyfish2_Cache_Factory::getHFC($uid);
		return $cache->delete($key);
    }


    public static function getUserMapCopyInfo($uid)
    {
        $key = 'm:u:mapcopy:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);
        if ($data === false) {
        	try {
	            $dal = Hapyfish2_Magic_Dal_MapCopy::getDefaultInstance();
	            $data = $dal->getInfo($uid);
	            if ($data) {
	            	$cache->add($key, $data);
	            } else {
	            	return null;
	            }
        	} catch (Exception $e) {
        	    info_log('getUserMapCopyInfo:'.$e->getMessage(), 'err_HFC_MapCopy');
        		return null;
        	}
        }

        return $data;
    }

    public static function addUserMapCopyInfo($uid, $info, $savedb = false)
    {

    	$key = 'm:u:mapcopy:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);

        if (!$savedb) {
        	$savedb = $cache->canSaveToDB($key);
        }

        if ($savedb) {
        	$ok = $cache->save($key, $info);
        	if ($ok) {
        		try {
        			$dal = Hapyfish2_Magic_Dal_MapCopy::getDefaultInstance();
        			$dal->add($uid, $info);
        		} catch (Exception $e) {
        		    info_log('updateUserMapCopyInfo:'.$e->getMessage(), 'err_HFC_MapCopy');
        		}
        	}

        	return $ok;
        } else {
        	return $cache->update($key, $info);
        }
    }

}