<?php

class Hapyfish2_Magic_HFC_Item
{
	public static function getUserItem($uid)
    {
        $key = 'm:u:item:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);
        if ($data === false) {
        	try {
	            $dalItem = Hapyfish2_Magic_Dal_Item::getDefaultInstance();
	            $result = $dalItem->get($uid);
	            if ($result) {
	            	$data = array();
	            	foreach ($result as $cid => $count) {
	            		$data[$cid] = array($count, 0);
	            	}
	            	$cache->add($key, $data);
	            } else {
	            	return null;
	            }
        	} catch (Exception $e) {
        		
        	}
        }
        
        $items = array();
        foreach ($data as $cid => $item) {
        	$items[$cid] = array('count' => (int)$item[0], 'update' => $item[1]);
        }
        
        return $items;
    }
    
    public static function updateUserItem($uid, $items, $savedb = false)
    {
        $key = 'm:u:item:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        
        if (!$savedb) {
        	$savedb = $cache->canSaveToDB($key, 900);
        }

        if ($savedb) {
            $data = array();
        	foreach ($items as $cid => $item) {
        		$data[$cid] = array($item['count'], 0);
        	}
        	$ok = $cache->save($key, $data);
        	if ($ok) {
        		try {
	        		$dalItem = Hapyfish2_Magic_Dal_Item::getDefaultInstance();
	        		foreach ($items as $cid => $item) {
	        			if ($item['update']) {
	        				$dalItem->update($uid, $cid, $item['count']);
	        			}
	        		}
        		} catch (Exception $e) {
        			
        		}
        	}
        	
        	return $ok;
        } else {
            $data = array();
        	foreach ($items as $cid => $item) {
        		$data[$cid] = array($item['count'], $item['update']);
        	}
        	return $cache->update($key, $data);
        }
    }
    
    public static function addUserItem($uid, $cid, $count = 1, $items = null)
    {
    	if (!$items) {
	    	$items = self::getUserItem($uid);
	    	if (!$items) {
	    		return false;
	    	}
    	}
    	
    	if (isset($items[$cid])) {
    		$items[$cid]['count'] += $count;
    		$items[$cid]['update'] = 1;
    	} else {
    		$items[$cid] = array('count' => $count, 'update' => 1);
    	}

    	$ok = self::updateUserItem($uid, $items);
    	if ($ok) {
    		$addItem = array($cid, $count, $cid);
    		Hapyfish2_Magic_Bll_UserResult::addField($uid, 'addItem', array($addItem));
    	}
    	
    	return $ok;
    }
    
    public static function useUserItem($uid, $cid, $count = 1, $items = null)
    {
        if (!$items) {
	    	$items = self::getUserItem($uid);
	    	if (!$items) {
	    		return false;
	    	}
    	}

        if (!isset($items[$cid]) || $items[$cid]['count'] < $count) {
    		return false;
    	} else {
    		$items[$cid]['count'] -= $count;
    		$items[$cid]['update'] = 1;
    		$ok = self::updateUserItem($uid, $items);
    		if ($ok) {
    			$removeItems = array($cid, $count, $cid);
    			Hapyfish2_Magic_Bll_UserResult::addField($uid, 'removeItems', array($removeItems));
    		}
    		
    		return $ok;
    	}
    }
    
    public static function getUserItemCount($uid, $cid)
    {
    	$userItemList = self::getUserItem($uid);
    	if (empty($userItemList)) {
    		return 0;
    	}
    	
    	if (!isset($userItemList[$cid])) {
    		return 0;
    	}
    	
    	return $userItemList[$cid]['count'];
    }
    
}