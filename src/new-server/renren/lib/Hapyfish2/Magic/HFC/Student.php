<?php

class Hapyfish2_Magic_HFC_Student
{
    public static function getAll($uid)
    {
        $ids = Hapyfish2_Magic_Cache_Student::getUnlockStudentIds($uid);

    	if (!$ids) {
        	return null;
        }

        $keys = array();
        foreach ($ids as $id) {
        	$keys[] = 'm:u:student:' . $uid . ':' . $id;
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
	            $dalStudent = Hapyfish2_Magic_Dal_Student::getDefaultInstance();
	            $result = $dalStudent->getAll($uid);
	            if ($result) {
	            	$data = array();
	            	foreach ($result as $item) {
	            		$key = 'm:u:student:' . $uid . ':' . $item[0];
	            		$data[$key] = $item;
	            	}
	            	$cache->addMulti($data);
	            } else {
	            	return null;
	            }
        	} catch (Exception $e) {
        		info_log($e->getMessage(), 'err.db');
        		return null;
        	}
        } else if (!empty($nocacheKeys)) {
        	foreach ($nocacheKeys as $key) {
        		$tmp = split(':', $key);
        		$data[$key] = self::loadOne($uid, $tmp[4]);
        	}
        }

        $students = array();
        foreach ($data as $item) {
        	if ($item) {
	        	$students[$item[0]] = array(
		    		'sid' => $item[0],
		    		'exp' => $item[1],
		    		'level' => $item[2],
		    		'award_flg' => $item[3],
		    		'state' => $item[4],
		    		'desk_id' => $item[5],
		    		'start_time' => $item[6],
		    		'end_time' => $item[7],
		    		'spend_time' => $item[8],
		    		'event' => $item[9],
		    		'event_time' => $item[10],
		    		'magic_id' => $item[11],
		    		'coin' => $item[12],
		    		'stone_time' => $item[13]
	        	);
        	}
        }

		return $students;
    }

    public static function loadOne($uid, $sid)
    {
		try {
	    	$dalStudent = Hapyfish2_Magic_Dal_Student::getDefaultInstance();
	    	$data = $dalStudent->get($uid, $sid);
	    	if ($data) {
	    		$key = 'm:u:student:' . $uid . ':' . $sid;
	    		$cache = Hapyfish2_Cache_Factory::getHFC($uid);
	    		$cache->save($key, $data);
	    	} else {
	    		return null;
	    	}

	    	return $data;
		}catch (Exception $e) {
			err_log($e->getMessage());
			return null;
		}
    }

	public static function getOne($uid, $sid)
    {
    	$key = 'm:u:student:' . $uid . ':' . $sid;
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
    	$data = $cache->get($key);

    	if ($data === false) {
    		try {
	    		$dalStudent = Hapyfish2_Magic_Dal_Student::getDefaultInstance();
	    		$data = $dalStudent->get($uid, $sid);
	    		if ($data) {
	    			$cache->add($key, $data);
	    		} else {
	    			return null;
	    		}
    		} catch (Exception $e) {
    			return null;
    		}
    	}

    	return array(
    		'sid' => $data[0],
    		'exp' => $data[1],
    		'level' => $data[2],
    		'award_flg' => $data[3],
    		'state' => $data[4],
    		'desk_id' => $data[5],
    		'start_time' => $data[6],
    		'end_time' => $data[7],
    		'spend_time' => $data[8],
    		'event' => $data[9],
    		'event_time' => $data[10],
    		'magic_id' => $data[11],
    		'coin' => $data[12],
    		'stone_time' => $data[13]
    	);
    }

    public static function updateOne($uid, $sid, $studentInfo, $savedb = false)
    {
    	$key = 'm:u:student:' . $uid . ':' . $sid;
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
    	$data = array(
    		$studentInfo['sid'], $studentInfo['exp'], $studentInfo['level'], $studentInfo['award_flg'], $studentInfo['state'],
    		$studentInfo['desk_id'], $studentInfo['start_time'], $studentInfo['end_time'], $studentInfo['spend_time'],
    		$studentInfo['event'], $studentInfo['event_time'], $studentInfo['magic_id'], $studentInfo['coin'], $studentInfo['stone_time']
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
						'exp' => $studentInfo['exp'],
	    				'level' => $studentInfo['level'],
	    				'award_flg' => $studentInfo['award_flg'],
	    				'state' => $studentInfo['state'],
	    				'desk_id' => $studentInfo['desk_id'],
	    				'start_time' => $studentInfo['start_time'],
	    				'end_time' => $studentInfo['end_time'],
	    				'spend_time' => $studentInfo['spend_time'],
	    				'event' => $studentInfo['event'],
	    				'event_time' => $studentInfo['event_time'],
	    				'magic_id' => $studentInfo['magic_id'],
	    				'coin' => $studentInfo['coin'],
	    				'stone_time' => $studentInfo['stone_time']
	    			);

	    			$dalStudent = Hapyfish2_Magic_Dal_Student::getDefaultInstance();
	    			$dalStudent->update($uid, $sid, $info);
	    		}catch (Exception $e) {

	    		}
    		}
    	} else {
    		$ok = $cache->update($key, $data);
    	}

    	return $ok;
    }

    public static function addOne($uid, $sid)
    {
    	$result = false;
    	try {
    		$data = array('uid' => $uid, 'sid' => $sid);
    		$dalStudent = Hapyfish2_Magic_Dal_Student::getDefaultInstance();
    		$dalStudent->insert($uid, $data);

    		Hapyfish2_Magic_Cache_Student::reloadStudentIds($uid);

    		self::loadOne($uid, $id);

    		$result = true;
    	} catch (Exception $e) {

    	}

    	return $result;
    }

}