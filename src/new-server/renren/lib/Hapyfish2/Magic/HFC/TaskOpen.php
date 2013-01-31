<?php

class Hapyfish2_Magic_HFC_TaskOpen
{
	public static function getInfo($uid)
    {
		$key = 'm:u:taskopen:' . $uid;

        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $data = $cache->get($key);

        if ($data === false) {
        	try {
            	$dalTaskOpen = Hapyfish2_Magic_Dal_TaskOpen::getDefaultInstance();
            	$data = $dalTaskOpen->get($uid);
	            if ($data) {
	            	$cache->add($key, $data);
	            } else {
	            	return null;
	            }
        	} catch (Exception $e) {
        		return null;
        	}
        }

        $info =  array(
        	'trunk' => $data[0],
        	'trunk_track_num' => $data[1],
        	'trunk_start' => $data[2],
        	'branch' => json_decode($data[3], true)
        );

        return $info;
    }

    public static function update($uid, $info)
    {
    	$taskOpenInfo = self::getInfo($uid);
        if ($taskOpenInfo) {
        	foreach ($info as $k => $v) {
        		if (isset($taskOpenInfo[$k])) {
    				$taskOpenInfo[$k] = $v;
    			}
        	}

    		return self::save($uid, $taskOpenInfo);
    	}
    }

    public static function save($uid, $taskOpenInfo, $savedb = false)
    {
    	$key = 'm:u:taskopen:' . $uid;
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
    	if (is_array($taskOpenInfo['branch'])) {
    		$taskOpenInfo['branch'] = json_encode($taskOpenInfo['branch']);
    	}

    	$data = array(
    		$taskOpenInfo['trunk'], $taskOpenInfo['trunk_track_num'], $taskOpenInfo['trunk_start'], $taskOpenInfo['branch']
    	);

    	if (!$savedb) {
    		$savedb = $cache->canSaveToDB($key, 900);
    	}

    	if ($savedb) {
    		$ok = $cache->save($key, $data);
    		if ($ok) {
				try {
					$info = array(
    					'trunk' => $taskOpenInfo['trunk'],
    					'trunk_track_num' => $taskOpenInfo['trunk_track_num'],
						'trunk_start' => $taskOpenInfo['trunk_start'],
    					'branch' => $taskOpenInfo['branch']
					);
	            	$dalTaskOpen = Hapyfish2_Magic_Dal_TaskOpen::getDefaultInstance();
	            	$dalTaskOpen->update($uid, $info);
	        	} catch (Exception $e) {
	        		info_log($e->getMessage(), 'err.db');
	        	}
    		}
    	} else {
    		$ok = $cache->update($key, $data);
    	}

    	return $ok;
    }

}