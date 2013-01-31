<?php

class Hapyfish2_Magic_Cache_TaskDaily
{
	public static function getIds($uid, $today = null)
    {
        if (!$today) {
        	$today = date('Ymd');
        }
    	
    	$key = 'm:u:alltaskdly:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);

        if ($data === false) {
        	try {
	            $dalTaskDaily = Hapyfish2_Magic_Dal_TaskDaily::getDefaultInstance();
	            $data = $dalTaskDaily->get($uid);
	            if ($data) {
	            	if ($data[0] < $today) {
	            		$data = array($today, '');
	            	}
	            } else {
	            	$data = array($today, '');
	            }
	            $cache->add($key, $data);
        	} catch (Exception $e) {
        		
        	}
        } else {
			if ($data[0] < $today) {
				$data = array($today, '');
				$cache->set($key, $data);
            }
        }
        
        if (empty($data[1])) {
        	return array();
        } else {
        	return explode(',', $data[1]);
        }
    }
    
    public static function clearAll($uid)
    {
        $today = date('Ymd');
        $data = array($today, '');
    	$key = 'm:u:alltaskdly:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $cache->set($key, $data);
    }
    
    public static function isCompletedTask($uid, $tid)
    {
    	$tids = self::getIds($uid);
    	if (is_array($tids) && in_array($tid, $tids)) {
    		return true;
    	}
    	return false;
    }
    
    public static function completeTask($uid, $tid, $time = null)
    {
    	if (!$time) {
    		$time = time();
    	}
    	
    	$today = date('Ymd', $time);
    	
    	$completed = false;
    	try {
    		$tids = self::getIds($uid, $today);
    		$tids[] = $tid;
    		$newTids = join(',', $tids);
    		
    		$dalTask = Hapyfish2_Magic_Dal_TaskDaily::getDefaultInstance();
    		$dalTask->insert($uid, $newTids, $today);
    		
    		$key = 'm:u:alltaskdly:' . $uid;
        	$cache = Hapyfish2_Cache_Factory::getMC($uid);
        	$cache->set($key, array($today, $newTids));
        	
    		$completed = true;
    	}catch (Exception $e) {
    		info_log($e->getMessage(), 'DB');
    	}
    	
    	return $completed;
    }
    
}