<?php

class Hapyfish2_Magic_Cache_Visit
{
    public static function dailyVisit($uid, $fid)
    {
    	if ($uid == $fid) {
			return;
		}

    	$key = 'm:u:dlyvisit:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		if ($data === false) {
			$data = array();
		}

		$num = count($data);

		if ($num > 10) {
			return;
		}

		if (in_array($fid, $data)) {
			return;
		}

		$data[] = (int)$fid;

		$t = time();
		$today = strtotime(date('Ymd', $t));
		$expire = $today + 86400 - $t;
		if ($expire < 10) {
			$expire = 86400;
		}

		$cache->set($key, $data, $expire);

		Hapyfish2_Magic_Bll_Task_Daily_Base::trigger($uid, 9004);
    }
}