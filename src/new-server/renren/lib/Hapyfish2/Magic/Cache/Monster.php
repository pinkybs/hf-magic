<?php

class Hapyfish2_Magic_Cache_Monster
{
    public static function getUser($uid, $sceneId)
    {
    	$key = 'm:u:monster:' . $uid . ':' . $sceneId;
    	$cache = Hapyfish2_Cache_Factory::getMC($uid);
    	$data = $cache->get($key);
    	if ($data === false) {
			$data = self::initUser($uid, $sceneId);
		} else {
			$t = time();
			$revive = false;
			foreach ($data as &$m) {
				if ($m[2] == 1) {
					if ($t - $m[3] > MONSTER_REVIVE_TIME) {
						$m[2] = 0;
						$revive = true;
					}
				}
			}
			if ($revive) {
				self::updateUser($uid, $sceneId, $data);
			}
		}
		
		return $data;
    }
	
	public static function updateUser($uid, $sceneId, $data)
    {
    	$key = 'm:u:monster:' . $uid . ':' . $sceneId;
    	$cache = Hapyfish2_Cache_Factory::getMC($uid);
		return $cache->set($key, $data);
    }
    
    public static function initUser($uid, $sceneId)
    {
		$data = array();
		$list = Hapyfish2_Magic_Cache_BasicInfo::getMonsterList();
		
		if ($list) {
			$i = 0;
			foreach ($list as $d) {
				if ($d['scene_id'] == $sceneId) {
					$num = $d['num'];
					for($j = 0; $j < $num; $j++) {
						$data[] = array(
							$i,
							$d['id'],
							0, //是否死亡
							0  //死亡时间
						);
						$i++;
					}
				}
			}
		}
    	
    	$key = 'm:u:monster:' . $uid . ':' . $sceneId;
    	$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $data);
		
		return $data;
    }

}