<?php

class Hapyfish2_Magic_HFC_TaskMap
{

	public static function getAllInMap($uid, $pMapId)
    {
        $ids = Hapyfish2_Magic_Cache_TaskMap::getIdsInMap($uid, $pMapId);

        if (!$ids) {
        	return null;
        }

        $keys = array();
        foreach ($ids as $id) {
        	$keys[] = 'm:u:tskmap:' . $uid . ':' . $id;
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
	            $dal = Hapyfish2_Magic_Dal_TaskMap::getDefaultInstance();
	            $result = $dal->getAllTaskInMap($uid, $pMapId);
	            if ($result) {
	            	$data = array();
	            	foreach ($result as $item) {
	            		$key = 'm:u:tskmap:' . $uid . ':' . $item[0];
	            		$data[$key] = $item;
	            	}
	            	$cache->addMulti($data);
	            } else {
	            	return null;
	            }
        	} catch (Exception $e) {
        		return null;
        	}
        } else if (!empty($nocacheKeys)) {
        	foreach ($nocacheKeys as $key) {
        		$tmp = split(':', $key);
        		$data[$key] = self::loadOne($uid, $tmp[4]);
        	}
        }

        $task = array();
        foreach ($data as $item) {
        	if ($item) {
	        	$task[$item[0]] = array(
		        	'tid' => $item[0],
		        	'map_parent_id' => $item[1],
		        	'cur_num' => $item[2],
		        	'award_status' => $item[3],
		        	'begin_time' => $item[4],
		        	'end_time' => $item[5],
	        		'complete_count' => $item[6]
	        	);
        	}
        }

		return $task;
    }

    public static function clearAll($uid, $pMapId)
    {

        $ids = Hapyfish2_Magic_Cache_TaskMap::getIdsInMap($uid, $pMapId);
        $keys = array();
        if ($ids) {
            foreach ($ids as $id) {
            	$keys[] = 'm:u:tskmap:' . $uid . ':' . $id;
            }

            $cache = Hapyfish2_Cache_Factory::getHFC($uid);
            foreach ($keys as $key) {
                $cache->delete($key);
            }
        }

        return Hapyfish2_Magic_Cache_TaskMap::clearAllIds($uid, $pMapId);
    }

    public static function getOne($uid, $id)
    {
    	$key = 'm:u:tskmap:' . $uid . ':' . $id;
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
    	$item = $cache->get($key);

    	if ($item === false) {
    		try {
	    		$dal = Hapyfish2_Magic_Dal_TaskMap::getDefaultInstance();
	    		$item = $dal->getOne($uid, $id);
	    		if ($item) {
	    			$cache->add($key, $item);
	    		} else {
	    			return null;
	    		}
    		}catch (Exception $e) {
    			return null;
    		}
    	}

    	return array(
        	'tid' => $item[0],
        	'map_parent_id' => $item[1],
        	'cur_num' => $item[2],
        	'award_status' => $item[3],
        	'begin_time' => $item[4],
        	'end_time' => $item[5],
    		'complete_count' => $item[6]
        );
    }

    public static function loadOne($uid, $id)
    {
		try {
	    	$dal = Hapyfish2_Magic_Dal_TaskMap::getDefaultInstance();
	    	$item = $dal->getOne($uid, $id);
	    	if ($item) {
	    		$key = 'm:u:tskmap:' . $uid . ':' . $id;
	    		$cache = Hapyfish2_Cache_Factory::getHFC($uid);
	    		$cache->save($key, $item);
	    	} else {
	    		return null;
	    	}

	    	return $item;
		}catch (Exception $e) {
			err_log($e->getMessage());
			return null;
		}
    }

    public static function loadMultiInMap($uid, $ids)
    {
    	$items = array();
    	foreach ($ids as $id) {
    		$items[$id] = self::loadOne($uid, $id);
    	}

    	return $items;
    }

    public static function saveOne($uid, $id, $task)
    {
		try {
    		$info = array(
            	'cur_num' => $task['cur_num'],
            	'award_status' => $task['award_status'],
            	'begin_time' => $task['begin_time'],
            	'end_time' => $task['end_time'],
        		'complete_count' => $task['complete_count']
    		);

    		$dal = Hapyfish2_Magic_Dal_TaskMap::getDefaultInstance();
    		$dal->update($uid, $id, $info);
    	} catch (Exception $e) {
    		err_log($e->getMessage());
    	}
    }

    public static function updateOne($uid, $id, $task, $savedb = false)
    {
    	$key = 'm:u:tskmap:' . $uid . ':' . $id;
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
    	$data = array(
    		$task['tid'], $task['map_parent_id'], $task['cur_num'],
    		$task['award_status'], $task['begin_time'], $task['end_time'],
    		$task['complete_count']
    	);

    	if (!$savedb) {
    		$savedb = $cache->canSaveToDB($key, 900);
    	}

    	if ($savedb) {
    		$ok = $cache->save($key, $data);
    		if ($ok) {
	    		//save to db
	    		self::saveOne($uid, $id, $task);
    		}
    	} else {
    		$ok = $cache->update($key, $data);
    	}

    	return $ok;
    }

    public static function removeOne($uid, $id)
    {
		$key = 'm:u:tskmap:' . $uid . ':' . $id;
		$cache = Hapyfish2_Cache_Factory::getHFC($uid);
		return $cache->delete($key);
    }


    public static function initMapTask($uid, $pMapId, $basicMapTask, &$taskList)
    {
        try {
            $dal = Hapyfish2_Magic_Dal_TaskMap::getDefaultInstance();
            //init map task
            if (empty($taskList)) {
                $info = array();
                foreach ($basicMapTask as $tid=>$data) {
                    $tmp = array();
                    $tmp['tid'] = $data['id'];
                    $tmp['map_parent_id'] = $data['map_parent_id'];
                    $info[] = $tmp;
                }
                $ok = $dal->init($uid, $pMapId, $info);
                if ($ok) {
                    Hapyfish2_Magic_Cache_TaskMap::reloadIdsInMap($uid, $pMapId);
                    $taskList = self::getAllInMap($uid, $pMapId);
                }
            }
            //update map task if need
            else {
                $changed = false;
                //basic task added
                foreach ($basicMapTask as $tid=>$data) {
                    $row = $dal->getOne($uid, $tid);
                    if (!$row) {
                        $dal->insert($uid, array('uid'=>$uid, 'tid'=>$tid, 'map_parent_id'=>$pMapId));
                        $changed = true;
                    }
                }
                //basic task removed
                foreach ($taskList as $tid=>$data) {
                    if (!array_key_exists($tid, $basicMapTask)) {
                        $dal->delete($uid, $tid);
                        $changed = true;
                    }
                }
                if ($changed) {
                    Hapyfish2_Magic_Cache_TaskMap::reloadIdsInMap($uid, $pMapId);
                    $taskList = self::getAllInMap($uid, $pMapId);
                }
            }
        } catch (Exception $e) {
    		info_log('initMapTask:'.$uid.':'.$pMapId.':'.$e->getMessage(), 'err_HFC_TaskMap');
    	}
        return true;
    }

}