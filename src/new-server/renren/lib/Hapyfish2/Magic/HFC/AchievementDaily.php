<?php

class Hapyfish2_Magic_HFC_AchievementDaily
{
	public static function getUserAchievementDaily($uid)
    {
		$today = date('Ymd');
    	$key = 'm:u:achdly:' . $uid;
        
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $data = $cache->get($key);

        if ($data === false) {
        	try {
	            $dalAchievementDaily = Hapyfish2_Magic_Dal_AchievementDaily::getDefaultInstance();
	            $data = $dalAchievementDaily->get($uid);
	            if ($data) {
	            	if ($data[0] < $today) {
	            		$data = array($today, 0, 0, 0, 0, 0, 0, 0, 0);
	            	}
	            } else {
	            	$data = array($today, 0, 0, 0, 0, 0, 0, 0, 0);
	            }
	            
	            $cache->add($key, $data);
        	} catch (Exception $e) {
        		return null;
        	}
        } else {
			if ($data[0] < $today) {
				$data = array($today, 0, 0, 0, 0, 0, 0, 0, 0);
			} else {
				if (!isset($data[9])) {
					$data[9] = 0;
				}
				if (!isset($data[10])) {
					$data[10] = 0;
				}
			}
        }
        
        $achievementdaily = array(
        		'today'	=> $data[0],
	        	'num_1' => $data[1],
	        	'num_2' => $data[2],
	        	'num_3' => $data[3],
	        	'num_4' => $data[4],
	        	'num_5' => $data[5],
	        	'num_6' => $data[6],
        		'num_7' => $data[7],
        		'num_8' => $data[8]
        );
        
        return $achievementdaily;
    }
    
    public static function updateUserAchievementDaily($uid, $info)
    {
    	$achievement = self::getUserAchievementDaily($uid);
        if ($achievement) {
        	foreach ($info as $k => $v) {
	    		if (isset($achievement[$k])) {
	    			$achievement[$k] = $v;
	    		}
        	}
    		return self::saveUserAchievementDaily($uid, $achievement);
    	}
    	
    	return false;
    }
    
    public static function updateUserAchievementDailyByField($uid, $field, $change)
    {
    	$achievement = self::getUserAchievementDaily($uid);
    	if ($achievement) {
    		if (isset($achievement[$field])) {
    			$achievement[$field] += $change;
    			return self::saveUserAchievementDaily($uid, $achievement);
    		}
    	}
    	
    	return false;
    }
    
    public static function updateUserAchievementDailyByMultiField($uid, $info)
    {
    	$achievement = self::getUserAchievementDaily($uid);
    	if ($achievement) {
    		foreach ($info as $k => $v) {
	    		if (isset($achievement[$k])) {
	    			$achievement[$k] += $v;
	    		}
    		}
    		return self::saveUserAchievementDaily($uid, $achievement);
    	}
    	
    	return false;
    }
    
    public static function saveUserAchievementDaily($uid, $achievement)
    {
    	$key = 'm:u:achdly:' . $uid;
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
    	$data = array(
    		$achievement['today'],
    		$achievement['num_1'],$achievement['num_2'],$achievement['num_3'],$achievement['num_4'],
    		$achievement['num_5'],$achievement['num_6'],$achievement['num_7'],$achievement['num_8']
    	);
    	
        $savedb = $cache->canSaveToDB($key, 900);
    	if ($savedb) {
    		$ok = $cache->save($key, $data);
    		if ($ok) {
				try {
					$info = array(
						'today' => $achievement['today'],
    					'num_1' => $achievement['num_1'], 'num_2' => $achievement['num_2'], 'num_3' => $achievement['num_3'],
						'num_4' => $achievement['num_4'], 'num_5' => $achievement['num_5'], 'num_6' => $achievement['num_6'],
    					'num_7' => $achievement['num_7'], 'num_8' => $achievement['num_8']
					);
	            	$dalAchievementDaily = Hapyfish2_Magic_Dal_AchievementDaily::getDefaultInstance();
	            	$dalAchievementDaily->update($uid, $info);
	        	} catch (Exception $e) {
	        	}
    		}
    	} else {
    		$ok = $cache->update($key, $data);
    	}
    	
    	return $ok;
    }
    
}