<?php

class Hapyfish2_Magic_Bll_Gold
{
	public static function get($uid, $isVip = false, $cache = false)
	{
		$gold = 0;
		if ($cache) {
			$gold = Hapyfish2_Magic_HFC_User::getUserGold($uid);
		} else {
			try {
		    	$dalUser = Hapyfish2_Magic_Dal_User::getDefaultInstance();
            	$gold = $dalUser->getGold($uid);
			} catch (Exception $e) {
			}
		}

		if ($isVip) {
			return array('balance' => $gold, 'is_vip' => 0);
		} else {
			return $gold;
		}
	}

	public static function addConsumeGoldLog($uid, $info)
	{
		try {
			$dalLog = Hapyfish2_Magic_Dal_ConsumeLog::getDefaultInstance();
			$dalLog->insertGold($uid, $info);
		} catch (Exception $e) {

		}
	}

	public static function consume($uid, $goldInfo)
	{
        $gold = $goldInfo['cost'];

		try {
			$dalUser = Hapyfish2_Magic_Dal_User::getDefaultInstance();
			$rst = $dalUser->decGold($uid, $gold);
			if ($rst->rowCount() == 0) {
			    info_log('update gold failed', 'gold-consume-err');
			    return false;
			}

			Hapyfish2_Magic_HFC_User::reloadUserGold($uid);
		} catch (Exception $e) {
			$msg = json_encode($goldInfo);
			info_log($msg, 'gold-consume-err');
			return false;
		}

		$goldInfo['create_time'] = time();
        self::addConsumeGoldLog($uid, $goldInfo);

        Hapyfish2_Magic_Bll_UserResult::mergeGold($uid, -$gold);

        return true;
	}

	public static function add($uid, $goldInfo)
	{
		$gold = $goldInfo['gold'];

		if ($gold <=0) {
			return false;
		}

		$ok = false;
		try {
			$dalUser = Hapyfish2_Magic_Dal_User::getDefaultInstance();
			$dalUser->incGold($uid, $gold);
			Hapyfish2_Magic_HFC_User::reloadUserGold($uid);
			$ok = true;
		} catch (Exception $e) {
			info_log($e->getMessage(), 'gold-add');
		}

		if ($ok) {
			if (isset($goldInfo['type'])) {
				$type = $goldInfo['type'];
			} else {
				$type = 0;
			}

			if (isset($goldInfo['time'])) {
				$time = $goldInfo['time'];
			} else {
				$time = time();
			}

			$info = array('uid' => $uid, 'gold' => $gold, 'type' => $type, 'create_time' => $time);
			self::addGoldLog($uid, $info);

			Hapyfish2_Magic_Bll_UserResult::mergeGold($uid, $gold);
		}

		return $ok;
	}

	//宝石异动 $type 说明
	//99-系统发放  98-管理工具发放 1-连续登陆奖励  2-房间升级奖励  3-发feed分享奖励  4-完成新手引导  5-打副本奖励 6-剧情任务奖励 7-升级奖励 8-副本任务奖励
	public static function addGoldLog($uid, $info)
	{
		try {
			$dalLog = Hapyfish2_Magic_Dal_AddGoldLog::getDefaultInstance();
			$dalLog->insert($uid, $info);
		} catch (Exception $e) {

		}
	}
}