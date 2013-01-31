<?php

class Hapyfish2_Platform_Cache_User
{
    public static function getUser($uid)
    {
        $key = 'p:u:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$result = $cache->get($key);
        if ($result === false) {
        	if ($cache->isNotFound()) {
        		try {
		            $dalUser = Hapyfish2_Platform_Dal_User::getDefaultInstance();
		            $result = $dalUser->getInfo($uid);
		            if ($result) {
		            	$cache->add($key, $result);
		            } else {
				        return array(
				        	'uid' => $uid,
				        	'puid' => '',
				        	'name' => '未知',
				        	'figureurl' => 'http://head.xiaonei.com/photos/0/0/men_head.gif',
				        	'gender' => -1,
				        	'create_time' => 0
				        );
		            }
        		}
	            catch (Exception $e) {
	            	return null;
	            }
        	} else {
        		return null;
        	}
        }
        
        if (!isset($result[5]) || !$result[5]) {
            try {
	            $dalUser = Hapyfish2_Platform_Dal_User::getDefaultInstance();
	            $result = $dalUser->getInfo($uid);
	            if ($result) {
	            	$cache->update($key, $result);
	            }
        	} catch (Exception $e) {
				return null;
			}
        }
        
        return array(
        	'uid' => $result[0],
        	'puid' => $result[1],
        	'name' => $result[2],
        	'figureurl' => $result[3],
        	'gender' => $result[4],
        	'create_time' => $result[5]
        );
    }
    
    public static function updateUser($uid, $user, $savedb = false)
    {
        $key = 'p:u:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $data = array($uid, $user['puid'], $user['name'], $user['figureurl'], $user['gender'], $user['create_time']);
        
        if (!$savedb) {
        	$savedb = $cache->canSaveToDB($key, 3600);
        }
        if ($savedb) {
        	$ok = $cache->save($key, $data);
        	if ($ok) {
	        	try {
	        		$info = array(
	        			'name' => $user['name'],
	        			'figureurl' => $user['figureurl'],
	        			'gender' => $user['gender']
	        		);
	        		$dalUser = Hapyfish2_Platform_Dal_User::getDefaultInstance();
	        		$r = $dalUser->update($uid, $info);
	        		if ($r == 0) {
	        			$info['uid'] = $uid;
	        			$info['puid'] = $user['puid'];
	        			$dalUser->add($info);
	        		}
	        	} catch (Exception $e) {
	        	}
        	} else {
        		$ok = $cache->update($key, $data);
        	}
        }
        
        return $ok;
    }
    
    public static function addUser($user)
    {
        $uid = $user['uid'];
        if (!isset($user['create_time'])) {
        	$user['create_time'] = time();
        }
    	$key = 'p:u:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $data = array($uid, $user['puid'], $user['name'], $user['figureurl'], $user['gender'], $user['create_time']);
        $ok = $cache->save($key, $data);
        if ($ok) {
        	try {
        		$dalUser = Hapyfish2_Platform_Dal_User::getDefaultInstance();
        		$dalUser->add($user);
        	}catch (Exception $e) {
        		
        	}
        }
        
        return $ok;
    }
    
    public static function getStatus($uid)
    {
    	$statusInfo = self::getStatus2($uid);
        
        return $statusInfo['status'];
    }
    
    public static function getStatus2($uid)
    {
        $key = 'p:u:s2:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
        if ($data === false) {
        	if ($cache->isNotFound()) {
        		try {
		            $dalUser = Hapyfish2_Platform_Dal_User::getDefaultInstance();
		            $data = $dalUser->getStatus2($uid);
		            if ($data) {
		            	$cache->add($key, $data);
		            } else {
		            	$data = array(-1, 0);
		            }
        		} catch (Exception $e) {
        			$data = array(-1, 0);
        		}
        	} else {
				$data = array(-1, 0);
        	}
        }
        
        return array('status' => $data[0], 'status_update_time' => $data[1]);
    }
    
    public static function updateStatus($uid, $status, $savedb = true)
    {
        $key = 'p:u:s2:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $t = time();
        $data = array($status, $t);
        $result = $cache->set($key, $data);
        
        if ($savedb && $result) {
			try {
        		$dalUser = Hapyfish2_Platform_Dal_User::getDefaultInstance();
        		$dalUser->updateStatus($uid, $status, $t);
        	} catch (Exception $e) {
        	}
        }
        
        return $result;
    }
    
    public static function getVUID($uid)
    {
        $key = 'p:u:vuid:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
        if ($data === false) {
        	if ($cache->isNotFound()) {
        		try {
		            $dalUser = Hapyfish2_Platform_Dal_User::getDefaultInstance();
		            $data = $dalUser->getVUID($uid);
		            if ($data) {
		            	$cache->add($key, $data);
		            } else {
		            	$data = '';
		            }
        		} catch (Exception $e) {
        			$data = '';
        		}
        	} else {
				$data = '';
        	}
        }
        
        return $data;
    }
    
    public static function updateVUID($uid, $vuid)
    {
        $key = 'p:u:vuid:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $result = $cache->set($key, $vuid);
        
		try {
        	$dalUser = Hapyfish2_Platform_Dal_User::getDefaultInstance();
        	$dalUser->update($uid, array('vuid' => $vuid));
        } catch (Exception $e) {
        }
        
        return $result;
    }
}