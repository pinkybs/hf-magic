<?php

class Hapyfish2_Magic_HFC_Desk
{
	public static function getInScene($uid, $savehighcache = false)
    {
        $ids = Hapyfish2_Magic_Cache_Desk::getInSceneIds($uid);
        
        if (!$ids) {
        	return null;
        }
        
        $keys = array();
        foreach ($ids as $id) {
        	$keys[] = 'i:u:desk:' . $uid . ':' . $id;
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
        		Hapyfish2_Magic_Cache_Desk::reloadInSceneIds($uid);
        		
	            $dalDesk = Hapyfish2_Magic_Dal_Desk::getDefaultInstance();
	            $result = $dalDesk->getInScene($uid);
	            if ($result) {
	            	$data = array();
	            	foreach ($result as $item) {
	            		$key = 'i:u:desk:' . $uid . ':' . $item[0];
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
        
        $desks = array();
        $vaildIds = array();
        foreach ($data as $item) {
        	if ($item) {
        		$id = $item[0];
        		$vaildIds[] = $id;
	        	$desks[] = array(
		        	'id' => $id,
		        	'cid' => $item[1],
		        	'x' => $item[2],
		        	'y' => $item[3],
		        	'z' => $item[4],
		        	'mirro' => $item[5],
		        	'item_type' => $item[6],
	        		'status' => $item[7],
		        	'student_id' => $item[8],
	        		'magic_id' => $item[9],
		        	'coin' => $item[10],
	        		'end_time' => $item[11],
	        		'stone_time' => $item[12]
	        	);
        	}
        }
        
        $data = array('ids' => $vaildIds, 'desks' => $desks);
        
        if ($savehighcache) {
        	$key = 'magic:alldeskinscene:' . $uid;
			$hc = Hapyfish2_Cache_HighCache::getInstance();
			$hc->set($key, $data);
		}
		
		return $data;
    }
    
	public static function getInBag($uid)
    {
        $ids = Hapyfish2_Magic_Cache_Desk::getInBagIds($uid);
        
        if (!$ids) {
        	return null;
        }
        
        $keys = array();
        foreach ($ids as $id) {
        	$keys[] = 'i:u:desk:' . $uid . ':' . $id;
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
	            $dalDesk = Hapyfish2_Magic_Dal_Desk::getDefaultInstance();
	            $result = $dalDesk->getInBag($uid);
	            if ($result) {
	            	$data = array();
	            	foreach ($result as $item) {
	            		$key = 'i:u:desk:' . $uid . ':' . $item[0];
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
        
        $desks = array();
        foreach ($data as $item) {
        	if ($item) {
	        	$desks[$item[0]] = array(
		        	'id' => $item[0],
		        	'cid' => $item[1],
		        	'x' => $item[2],
		        	'y' => $item[3],
		        	'z' => $item[4],
		        	'mirro' => $item[5],
		        	'item_type' => $item[6],
	        		'status' => $item[7],
		        	'student_id' => $item[8],
	        		'magic_id' => $item[9],
		        	'coin' => $item[10],
	        		'end_time' => $item[11],
	        		'stone_time' => $item[12]
	        	);
        	}
        }
		
		return $desks;
    }
    
	public static function getAll($uid)
    {
        $ids = Hapyfish2_Magic_Cache_Desk::getAllIds($uid);
        
        if (!$ids) {
        	return null;
        }
        
        $keys = array();
        foreach ($ids as $id) {
        	$keys[] = 'i:u:desk:' . $uid . ':' . $id;
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
	            $dalDesk = Hapyfish2_Magic_Dal_Desk::getDefaultInstance();
	            $result = $dalDesk->getAll($uid);
	            if ($result) {
	            	$data = array();
	            	foreach ($result as $item) {
	            		$key = 'i:u:desk:' . $uid . ':' . $item[0];
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
        
        $desks = array();
        foreach ($data as $item) {
        	if ($item) {
	        	$desks[$item[0]] = array(
		        	'id' => $item[0],
		        	'cid' => $item[1],
		        	'x' => $item[2],
		        	'y' => $item[3],
		        	'z' => $item[4],
		        	'mirro' => $item[5],
		        	'item_type' => $item[6],
	        		'status' => $item[7],
		        	'student_id' => $item[8],
	        		'magic_id' => $item[9],
		        	'coin' => $item[10],
	        		'end_time' => $item[11],
	        		'stone_time' => $item[12]
	        	);
        	}
        }
		
		return $desks;
    }
    
    public static function getAllInSceneFromHighCache($uid)
    {
    	$key = 'magic:alldeskinscene:' . $uid;
    	$hc = Hapyfish2_Cache_HighCache::getInstance();
    	return $hc->get($key);
    }
    
    public static function getOne($uid, $id, $status = 1)
    {
    	$key = 'i:u:desk:' . $uid . ':' . $id;
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
    	$item = $cache->get($key);
    	
    	if ($item === false) {
    		try {
	    		$dalDesk = Hapyfish2_Magic_Dal_Desk::getDefaultInstance();
	    		$item = $dalDesk->getOne($uid, $id);
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
        	'student_id' => $item[8],
    		'magic_id' => $item[9],
        	'coin' => $item[10],
    		'end_time' => $item[11],
    		'stone_time' => $item[12]
        );
    }
    
    public static function loadOne($uid, $id)
    {
		try {
	    	$dalDesk = Hapyfish2_Magic_Dal_Desk::getDefaultInstance();
	    	$item = $dalDesk->getOne($uid, $id);
	    	if ($item) {
	    		$key = 'i:u:desk:' . $uid . ':' . $id;
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
    
    public static function updateFieldOfDesk($uid, $id, $fieldInfo)
    {
    	$desk = self::getOne($uid, $id);
    	if ($desk) {
    		foreach ($fieldInfo as $k => $v) {
    			if(isset($desk[$k])) {
    				$desk[$k] = $v;
    			}
    		}
			return self::updateOne($uid, $id, $desk);
    	}
    	
    	return false;
    }
    
    public static function saveOne($uid, $id, $desk)
    {
		try {
    		$info = array(
				'x' => $desk['x'], 
    			'y' => $desk['y'], 
    			'z' => $desk['z'], 
    			'mirro' => $desk['mirro'], 
    			'status' => $desk['status'],
				'student_id' => $desk['student_id'],
    			'magic_id' => $desk['magic_id'],
    			'coin' => $desk['coin'],
    			'end_time' => $desk['end_time'],
    			'stone_time' => $desk['stone_time']
    		);
	    			
    		$dalDesk = Hapyfish2_Magic_Dal_Desk::getDefaultInstance();
    		$dalDesk->update($uid, $id, $info);
    	} catch (Exception $e) {
    		err_log($e->getMessage());
    	}
    }
    
    public static function updateOne($uid, $id, $desk, $savedb = false)
    {
    	$key = 'i:u:desk:' . $uid . ':' . $id;
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
    	$data = array(
    		$desk['id'], $desk['cid'],$desk['x'], $desk['y'], $desk['z'], $desk['mirro'], 
    		$desk['item_type'], $desk['status'], 
    		$desk['student_id'], $desk['magic_id'], $desk['coin'], $desk['end_time'], $desk['stone_time']
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
						'x' => $desk['x'], 
	    				'y' => $desk['y'], 
	    				'z' => $desk['z'], 
	    				'mirro' => $desk['mirro'], 
	    				'status' => $desk['status'],
						'student_id' => $desk['student_id'],
	    				'magic_id' => $desk['magic_id'],
	    				'coin' => $desk['coin'],
	    				'end_time' => $desk['end_time'],
	    				'stone_time' => $desk['stone_time']
	    			);
	    			
	    			$dalDesk = Hapyfish2_Magic_Dal_Desk::getDefaultInstance();
	    			$dalDesk->update($uid, $id, $info);
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
		$key = 'i:u:desk:' . $uid . ':' . $id;
		$cache = Hapyfish2_Cache_Factory::getHFC($uid);
		return $cache->delete($key);
    }
    
    public static function getNewDeskId($uid)
    {
        try {
    		$dalUserSequence = Hapyfish2_Magic_Dal_UserSequence::getDefaultInstance();
    		return $dalUserSequence->get($uid, 'c', 1);
    	} catch (Exception $e) {
    	}
    	
    	return 0;
    }
    
    public static function addOne($uid, &$desk)
    {
    	$result = false;
    	try {
    		$id = self::getNewDeskId($uid);
    		if ($id > 0) {
    			$desk['id'] = $id;
	    		$dalDesk = Hapyfish2_Magic_Dal_Desk::getDefaultInstance();
	    		$dalDesk->insert($uid, $desk);
	    		
	    		self::loadOne($uid, $id);
				//Hapyfish2_Magic_Cache_Desk::reloadInSceneIds($uid);
				Hapyfish2_Magic_Cache_Desk::pushOneIdInAll($uid, $id);
				
				if ($desk['status'] == 1) {
					Hapyfish2_Magic_Cache_Desk::pushOneIdInScene($uid, $id);
				}
	    		
	    		$result = true;
    		}
    	} catch (Exception $e) {
    		
    	}
    	
        if ($result) {
    		$addDecorBag = array($id, 1, $desk['cid']);
    		Hapyfish2_Magic_Bll_UserResult::addField($uid, 'addDecorBag', array($addDecorBag));
    	}
    	
    	return $result;
    }
    
    public static function delOne($uid, $id, $status, $cid = 0)
    {
    	$result = false;
    	try {
    		$dalDesk = Hapyfish2_Magic_Dal_Desk::getDefaultInstance();
    		$dalDesk->delete($uid, $id);
    		
    		self::removeOne($uid, $id);
    		//Hapyfish2_Magic_Cache_Desk::reloadInSceneIds($uid);
    		Hapyfish2_Magic_Cache_Desk::popOneIdInAll($uid, $id);
    		
    		if ($status == 1) {
    			Hapyfish2_Magic_Cache_Desk::pushOneIdInScene($uid, $id);
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