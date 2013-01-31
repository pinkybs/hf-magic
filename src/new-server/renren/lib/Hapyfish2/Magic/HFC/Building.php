<?php

class Hapyfish2_Magic_HFC_Building
{
	public static function getInScene($uid, $savehighcache = false)
    {
        $ids = Hapyfish2_Magic_Cache_Building::getInSceneIds($uid);
        
        if (!$ids) {
        	return null;
        }
        
        $keys = array();
        foreach ($ids as $id) {
        	$keys[] = 'm:u:bld:' . $uid . ':' . $id;
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
        		Hapyfish2_Magic_Cache_Building::reloadInSceneIds($uid);
        		
	            $dalBuilding = Hapyfish2_Magic_Dal_Building::getDefaultInstance();
	            $result = $dalBuilding->getInScene($uid);
	            if ($result) {
	            	$data = array();
	            	foreach ($result as $item) {
	            		$key = 'm:u:bld:' . $uid . ':' . $item[0];
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
        
        $buildings = array();
        foreach ($data as $item) {
        	if ($item) {
	        	$buildings[$item[0]] = array(
		        	'id' => $item[0],
		        	'cid' => $item[1],
		        	'x' => $item[2],
		        	'y' => $item[3],
		        	'z' => $item[4],
		        	'mirro' => $item[5],
	        		'item_type' => $item[6],
	        		'status' => $item[7]
	        	);
        	}
        }
		
		return $buildings;
    }
    
	public static function getInBag($uid)
    {
        $ids = Hapyfish2_Magic_Cache_Building::getInBagIds($uid);
		if (!$ids) {
			return null;
		}
    	
        $keys = array();
        foreach ($ids as $id) {
        	$keys[] = 'm:u:bld:' . $uid . ':' . $id;
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
	            $dalBuilding = Hapyfish2_Magic_Dal_Building::getDefaultInstance();
	            $result = $dalBuilding->getInBag($uid);
	            if ($result) {
	            	$data = array();
	            	foreach ($result as $item) {
	            		$key = 'm:u:bld:' . $uid . ':' . $item[0];
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
        
        $buildings = array();
        foreach ($data as $item) {
        	if ($item) {
	        	$buildings[$item[0]] = array(
		        	'id' => $item[0],
		        	'cid' => $item[1],
		        	'x' => $item[2],
		        	'y' => $item[3],
		        	'z' => $item[4],
		        	'mirro' => $item[5],
	        		'item_type' => $item[6],
	        		'status' => $item[7]
	        	);
        	}
        }
		
		return $buildings;
    }
    
    public static function getAll($uid)
    {
        $ids = Hapyfish2_Magic_Cache_Building::getAllIds($uid);
        
        if (!$ids) {
        	return null;
        }
        
        $keys = array();
        foreach ($ids as $id) {
        	$keys[] = 'm:u:bld:' . $uid . ':' . $id;
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
	            $dalBuilding = Hapyfish2_Magic_Dal_Building::getDefaultInstance();
	            $result = $dalBuilding->getAll($uid);
	            if ($result) {
	            	$data = array();
	            	foreach ($result as $item) {
	            		$key = 'm:u:bld:' . $uid . ':' . $item[0];
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
        
        $buildings = array();
        foreach ($data as $item) {
			if ($item) {
	        	$buildings[$item[0]] = array(
		        	'id' => $item[0],
		        	'cid' => $item[1],
		        	'x' => $item[2],
		        	'y' => $item[3],
		        	'z' => $item[4],
		        	'mirro' => $item[5],
	        		'item_type' => $item[6],
	        		'status' => $item[7]
	        	);
        	}
        }
		
		return $buildings;
    }
    
    public static function getOne($uid, $id, $status = 1)
    {
    	$key = 'm:u:bld:' . $uid . ':' . $id;
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
    	$item = $cache->get($key);
    	
    	if ($item === false) {
    		try {
	    		$dalBuilding = Hapyfish2_Magic_Dal_Building::getDefaultInstance();
	    		$item = $dalBuilding->getOne($uid, $id);
	    		if ($item) {
	    			$cache->add($key, $item);
	    		} else {
	    			return null;
	    		}
    		} catch (Exception $e) {
    			return null;
    		}
    	}
    	
    	if ($status == 1) {
	    	return array(
		        'id' => $item[0],
		        'cid' => $item[1],
		        'x' => $item[2],
		        'y' => $item[3],
		        'z' => $item[4],
		        'mirro' => $item[5],
		        'item_type' => $item[6],
	    		'status' => $item[7]
	        );
    	} else {
	    	return array(
		        'id' => $item[0],
		        'cid' => $item[1],
		        'item_type' => $item[6],
	    		'status' => $item[7]
	        );
    	}
    }
    
    public static function loadOne($uid, $id)
    {
		try {
	    	$dalBuilding = Hapyfish2_Magic_Dal_Building::getDefaultInstance();
	    	$item = $dalBuilding->getOne($uid, $id);
	    	if ($item) {
	    		$key = 'm:u:bld:' . $uid . ':' . $id;
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
    
    public static function loadMulti($uid, $ids)
    {
    	$items = array();
    	foreach ($ids as $id) {
    		$items[$id] = self::loadOne($uid, $id);
    	}
    	
    	return $items;
    }
    
    public static function updateFieldOfBuilding($uid, $id, $fieldInfo)
    {
    	$building = self::getOne($uid, $id);
    	if ($building) {
    		foreach ($fieldInfo as $k => $v) {
    			if(isset($building[$k])) {
    				$plant[$k] = $v;
    			}
    		}
			return self::updateOne($uid, $id, $building);
    	}
    	
    	return false;
    }
    
    public static function updateOne($uid, $id, $building, $savedb = false)
    {
    	$key = 'm:u:bld:' . $uid . ':' . $id;
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
    	$data = array(
    		$building['id'], $building['cid'], $building['x'], $building['y'], 
    		$building['z'], $building['mirro'], $building['item_type'], $building['status']
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
						'id' => $building['id'], 
	    				'cid' => $building['cid'], 
						'x' => $building['x'], 
	    				'y' => $building['y'], 
	    				'z' => $building['z'], 
	    				'mirro' => $building['mirro'], 
	    				'item_type' => $building['item_type'],
	    				'status' => $building['status']
	    			);
	    			
	    			$dalBuilding = Hapyfish2_Magic_Dal_Building::getDefaultInstance();
	    			$dalBuilding->update($uid, $id, $info);
	    		}catch (Exception $e) {
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
		$key = 'm:u:bld:' . $uid . ':' . $id;
		$cache = Hapyfish2_Cache_Factory::getHFC($uid);
		return $cache->delete($key);
    }
    
    public static function getNewBuildingId($uid)
    {
        try {
    		$dalUserSequence = Hapyfish2_Magic_Dal_UserSequence::getDefaultInstance();
    		return $dalUserSequence->get($uid, 'b', 1);
    	} catch (Exception $e) {
    	}
    	
    	return 0;
    }
    
    public static function addOne($uid, &$building)
    {
    	$result = false;
    	try {
    		$id = self::getNewBuildingId($uid);
    		if ($id > 0) {
    			$building['id'] = $id;
	    		$dalBuilding = Hapyfish2_Magic_Dal_Building::getDefaultInstance();
	    		$dalBuilding->insert($uid, $building);
	    		
	    		self::loadOne($uid, $id);
				//Hapyfish2_Magic_Cache_Building::reloadAllIds($uid);
				Hapyfish2_Magic_Cache_Building::pushOneIdInAll($uid, $id);
				if ($building['status'] == 1) {
					Hapyfish2_Magic_Cache_Building::pushOneIdOnMagic($uid, $id);
				}
	    		
	    		$result = true;
    		}
    	} catch (Exception $e) {
    		
    	}
    	
    	if ($result) {
    		$addDecorBag = array($id, 1, $building['cid']);
    		Hapyfish2_Magic_Bll_UserResult::addField($uid, 'addDecorBag', array($addDecorBag));
    	}
    	
    	return $result;
    }
    
    public static function delOne($uid, $id, $status, $cid = 0)
    {
    	$result = false;
    	try {
    		$dalBuilding = Hapyfish2_Magic_Dal_Building::getDefaultInstance();
    		$dalBuilding->delete($uid, $id);
    		
    		self::removeOne($uid, $id);
    		
    		//update all ids
			//Hapyfish2_Magic_Cache_Building::reloadAllIds($uid);
			Hapyfish2_Magic_Cache_Building::popOneIdInAll($uid, $id);
    		
    		//if in scene
    		//update in scene ids
    		if ($status == 1) {
    			Hapyfish2_Magic_Cache_Building::popOneIdOnMagic($uid, $id);
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