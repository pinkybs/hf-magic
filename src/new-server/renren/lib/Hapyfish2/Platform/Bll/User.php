<?php

class Hapyfish2_Platform_Bll_User
{
    public static function getUser($uid)
    {
        $hc = Hapyfish2_Cache_HighCache::getInstance();
        $key = 'p:u:' . $uid;
        $data = $hc->get($key);

        if (!$data) {
        	$data = Hapyfish2_Platform_Cache_User::getUser($uid);
        	if ($data) {
        		$hc->set($key, $data);
        	}
        }

        return $data;
    }

    public static function getMultiUser($fids)
    {
    	$info = array();
    	foreach ($fids as $fid) {
    		$user = Hapyfish2_Platform_Cache_User::getUser($fid);
    		if ($user && $user['puid']) {
    			$info[] = array('uid' => $user['uid'], 'name' => $user['name'], 'face' => $user['figureurl']);
    		}
    	}

    	return $info;
    }

    public static function updateUser($uid, $user, $savedb = false)
    {
        //get
    	$old = Hapyfish2_Platform_Cache_User::getUser($uid);
    	if ($old) {
    		if ($old['name'] == $user['name'] && $old['figureurl'] == $user['figureurl'] && $old['gender'] == $user['gender']) {
    			return false;
    		}
    		$user['create_time'] = $old['create_time'];
    	} else {
    		$data = self::addUser($user);
    		if ($data) {
    			return true;
    		} else {
    			return false;
    		}
    	}
    	$res = Hapyfish2_Platform_Cache_User::updateUser($uid, $user, $savedb);
        if ($res) {
        	$hc = Hapyfish2_Cache_HighCache::getInstance();
        	$key = 'p:u:' . $uid;
        	$data = array(
	        	'uid' => $uid,
        		'puid' => $user['puid'],
	        	'name' => $user['name'],
	        	'figureurl' => $user['figureurl'],
	        	'gender' => $user['gender'],
        		'create_time' => $user['create_time']
        	);
        	$hc->set($key, $data);
        }

        return $res;
    }

    public static function addUser($user)
    {
        $res = Hapyfish2_Platform_Cache_User::addUser($user);
        if ($res) {
        	$hc = Hapyfish2_Cache_HighCache::getInstance();
        	$uid = $user['uid'];
        	$key = 'p:u:' . $uid;
        	$data = array(
	        	'uid' => $uid,
        		'puid' => $user['puid'],
	        	'name' => $user['name'],
	        	'figureurl' => $user['figureurl'],
	        	'gender' => $user['gender']
        	);
        	$hc->set($key, $data);

        	return $data;
        }

        return null;
    }

    public static function getUids($pids)
    {
    	$uids = array();
    	foreach ($pids as $puid) {
    		try {
    			$user = Hapyfish2_Platform_Cache_UidMap::getUser($puid);
    			if ($user && $user['status'] == 0) {
    				$uids[] = $user['uid'];
    			}
    		} catch (Exception $e) {

    		}
    	}

    	sort($uids);

    	return $uids;
    }

}