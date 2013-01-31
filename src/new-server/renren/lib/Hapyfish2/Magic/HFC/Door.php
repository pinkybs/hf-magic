<?php

class Hapyfish2_Magic_HFC_Door
{
	public static function getInScene($uid, $savehighcache = false)
    {
        $ids = Hapyfish2_Magic_Cache_Door::getInSceneIds($uid);
        
        if (!$ids) {
        	return null;
        }
        
        $keys = array();
        foreach ($ids as $id) {
        	$keys[] = 'i:u:door:' . $uid . ':' . $id;
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
        		Hapyfish2_Magic_Cache_Door::reloadInSceneIds($uid);
        		
	            $dalDoor = Hapyfish2_Magic_Dal_Door::getDefaultInstance();
	            $result = $dalDoor->getInScene($uid);
	            if ($result) {
	            	$data = array();
	            	foreach ($result as $item) {
	            		$key = 'i:u:door:' . $uid . ':' . $item[0];
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
        
        $doors = array();
        $vaildIds = array();
        foreach ($data as $item) {
        	if ($item) {
        		$id = $item[0];
        		$vaildIds[] = $id;
	        	$doors[] = array(
		        	'id' => $id,
		        	'cid' => $item[1],
		        	'x' => $item[2],
		        	'y' => $item[3],
		        	'z' => $item[4],
		        	'mirro' => $item[5],
		        	'item_type' => $item[6],
	        		'status' => $item[7],
		        	'left_student_num' => $item[8],
		        	'start_time' => $item[9],
	        		'end_time' => $item[10]
	        	);
        	}
        }
        
        $data = array('ids' => $vaildIds, 'doors' => $doors);
        
        if ($savehighcache) {
        	$key = 'magic:alldoorinscene:' . $uid;
			$hc = Hapyfish2_Cache_HighCache::getInstance();
			$hc->set($key, $data);
		}
		
		return $data;
    }
    
	public static function getInBag($uid)
    {
        $ids = Hapyfish2_Magic_Cache_Door::getInBagIds($uid);
        
        if (!$ids) {
        	return null;
        }
        
        $keys = array();
        foreach ($ids as $id) {
        	$keys[] = 'i:u:door:' . $uid . ':' . $id;
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
	            $dalDoor = Hapyfish2_Magic_Dal_Door::getDefaultInstance();
	            $result = $dalDoor->getInBag($uid);
	            if ($result) {
	            	$data = array();
	            	foreach ($result as $item) {
	            		$key = 'i:u:door:' . $uid . ':' . $item[0];
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
        
        $doors = array();
        foreach ($data as $item) {
        	if ($item) {
	        	$doors[$item[0]] = array(
		        	'id' => $item[0],
		        	'cid' => $item[1],
		        	'x' => $item[2],
		        	'y' => $item[3],
		        	'z' => $item[4],
		        	'mirro' => $item[5],
		        	'item_type' => $item[6],
	        		'status' => $item[7],
		        	'left_student_num' => $item[8],
		        	'start_time' => $item[9],
	        		'end_time' => $item[10]
	        	);
        	}
        }
		
		return $doors;
    }
    
	public static function getAll($uid)
    {
        $ids = Hapyfish2_Magic_Cache_Door::getAllIds($uid);
        
        if (!$ids) {
        	return null;
        }
        
        $keys = array();
        foreach ($ids as $id) {
        	$keys[] = 'i:u:door:' . $uid . ':' . $id;
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
	            $dalDoor = Hapyfish2_Magic_Dal_Door::getDefaultInstance();
	            $result = $dalDoor->getAll($uid);
	            if ($result) {
	            	$data = array();
	            	foreach ($result as $item) {
	            		$key = 'i:u:door:' . $uid . ':' . $item[0];
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
        
        $doors = array();
        foreach ($data as $item) {
        	if ($item) {
	        	$doors[$item[0]] = array(
		        	'id' => $item[0],
		        	'cid' => $item[1],
		        	'x' => $item[2],
		        	'y' => $item[3],
		        	'z' => $item[4],
		        	'mirro' => $item[5],
		        	'item_type' => $item[6],
	        		'status' => $item[7],
		        	'left_student_num' => $item[8],
		        	'start_time' => $item[9],
	        		'end_time' => $item[10]
	        	);
        	}
        }
		
		return $doors;
    }
    
    public static function getAllInSceneFromHighCache($uid)
    {
    	$key = 'magic:alldoorinscene:' . $uid;
    	$hc = Hapyfish2_Cache_HighCache::getInstance();
    	return $hc->get($key);
    }
    
    public static function getOne($uid, $id, $status = 1)
    {
    	$key = 'i:u:door:' . $uid . ':' . $id;
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
    	$item = $cache->get($key);
    	
    	if ($item === false) {
    		try {
	    		$dalDoor = Hapyfish2_Magic_Dal_Door::getDefaultInstance();
	    		$item = $dalDoor->getOne($uid, $id);
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
        	'id' => $item[0],
        	'cid' => $item[1],
        	'x' => $item[2],
        	'y' => $item[3],
        	'z' => $item[4],
        	'mirro' => $item[5],
        	'item_type' => $item[6],
        	'status' => $item[7],
        	'left_student_num' => $item[8],
        	'start_time' => $item[9],
    		'end_time' => $item[10]
        );
    }
    
    public static function loadOne($uid, $id)
    {
		try {
	    	$dalDoor = Hapyfish2_Magic_Dal_Door::getDefaultInstance();
	    	$item = $dalDoor->getOne($uid, $id);
	    	if ($item) {
	    		$key = 'i:u:door:' . $uid . ':' . $id;
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
    
    public static function loadMultiInScene($uid, $ids)
    {
    	$items = array();
    	foreach ($ids as $id) {
    		$items[$id] = self::loadOne($uid, $id);
    	}
    	
    	return $items;
    }
    
    public static function updateFieldOfDoor($uid, $id, $fieldInfo)
    {
    	$door = self::getOne($uid, $id);
    	if ($door) {
    		foreach ($fieldInfo as $k => $v) {
    			if(isset($door[$k])) {
    				$door[$k] = $v;
    			}
    		}
			return self::updateOne($uid, $id, $door);
    	}
    	
    	return false;
    }
    
    public static function saveOne($uid, $id, $door)
    {
		try {
    		$info = array(
				'x' => $door['x'], 
    			'y' => $door['y'], 
    			'z' => $door['z'], 
    			'mirro' => $door['mirro'], 
    			'status' => $door['status'],
				'left_student_num' => $door['left_student_num'], 
    			'start_time' => $door['start_time'],
    			'end_time' => $door['end_time']
    		);
	    			
    		$dalDoor = Hapyfish2_Magic_Dal_Door::getDefaultInstance();
    		$dalDoor->update($uid, $id, $info);
    	} catch (Exception $e) {
    		err_log($e->getMessage());
    	}
    }
    
    public static function updateOne($uid, $id, $door, $savedb = false)
    {
    	$key = 'i:u:door:' . $uid . ':' . $id;
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
    	$data = array(
    		$door['id'], $door['cid'],$door['x'], $door['y'], $door['z'], $door['mirro'], 
    		$door['item_type'], $door['status'], 
    		$door['left_student_num'], $door['start_time'], $door['end_time']
    	);
    	
    	if (!$savedb) {
    		$savedb = $cache->canSaveToDB($key, 900);
    	}
    	
    	if ($savedb) {
    		$ok = $cache->save($key, $data);
    		if ($ok) {
	    		//save to db
	    		try {
	    			$info = array(
						'x' => $door['x'], 
	    				'y' => $door['y'], 
	    				'z' => $door['z'], 
	    				'mirro' => $door['mirro'], 
	    				'status' => $door['status'],
	    				'left_student_num' => $door['left_student_num'], 
	    				'start_time' => $door['start_time'], 
	    				'end_time' => $door['end_time']
	    			);
	    			
	    			$dalDoor = Hapyfish2_Magic_Dal_Door::getDefaultInstance();
	    			$dalDoor->update($uid, $id, $info);
	    		} catch (Exception $e) {
	    			err_log($e->getMessage());
	    		}
    		}
    	} else {
    		$ok = $cache->update($key, $data);
    	}
    	
    	return $ok;
    }
    
    public static function removeOne($uid, $id)
    {
		$key = 'i:u:door:' . $uid . ':' . $id;
		$cache = Hapyfish2_Cache_Factory::getHFC($uid);
		return $cache->delete($key);
    }
    
    public static function getNewDoorId($uid)
    {
        try {
    		$dalUserSequence = Hapyfish2_Magic_Dal_UserSequence::getDefaultInstance();
    		return $dalUserSequence->get($uid, 'c', 1);
    	} catch (Exception $e) {
    	}
    	
    	return 0;
    }
    
    public static function addOne($uid, &$door)
    {
    	$result = false;
    	try {
    		$id = self::getNewDoorId($uid);
    		if ($id > 0) {
    			$door['id'] = $id;
	    		$dalDoor = Hapyfish2_Magic_Dal_Door::getDefaultInstance();
	    		$dalDoor->insert($uid, $door);
	    		
	    		self::loadOne($uid, $id);
				//Hapyfish2_Magic_Cache_Door::reloadInSceneIds($uid);
				Hapyfish2_Magic_Cache_Door::pushOneIdInAll($uid, $id);
				
				if ($door['status'] == 1) {
					Hapyfish2_Magic_Cache_Door::pushOneIdInScene($uid, $id);
				}
	    		
	    		$result = true;
    		}
    	} catch (Exception $e) {
    		info_log($e->getMessage(), 'db.err');
    	}
    	
        if ($result) {
    		$addDecorBag = array($id, 1, $door['cid']);
    		Hapyfish2_Magic_Bll_UserResult::addField($uid, 'addDecorBag', array($addDecorBag));
    	}
    	
    	return $result;
    }
    
    public static function delOne($uid, $id, $status, $cid = 0)
    {
    	$result = false;
    	try {
    		$dalDoor = Hapyfish2_Magic_Dal_Door::getDefaultInstance();
    		$dalDoor->delete($uid, $id);
    		
    		self::removeOne($uid, $id);
    		//Hapyfish2_Magic_Cache_Door::reloadInSceneIds($uid);
    		Hapyfish2_Magic_Cache_Door::popOneIdInAll($uid, $id);
    		
    		if ($status == 1) {
    			Hapyfish2_Magic_Cache_Door::pushOneIdInScene($uid, $id);
    		}
    		
    		$result = true;
    	} catch (Exception $e) {

    	}
    	
        if ($result) {
    		$removeDecorBag = array($id, 1, $cid);
    		Hapyfish2_Magic_Bll_UserResult::addField($uid, 'removeDecorBag', array($removeDecorBag));
    	}
    	
    	return $result;
    }
    
}