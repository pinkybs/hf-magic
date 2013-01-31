<?php

class Hapyfish2_Magic_Bll_InviteLog
{
	public static function add($uid, $fid, $t = null)
	{
		$ok = false;
		if (!$t) {
			$t = time();
		}
		$info = array(
			'uid' => $uid,
			'fid' => $fid,
			'time' => $t
		);

		try {
			$dalLog = Hapyfish2_Magic_Dal_InviteLog::getDefaultInstance();
			$dalLog->insert($uid, $info);

			$dalUser = Hapyfish2_Magic_Dal_User::getDefaultInstance();
			$dalUser->update($fid, array('inviter' => $uid));

			$ok = true;
		} catch (Exception $e) {

		}

		return $ok;
	}

	public static function getAll($uid)
	{
		try {
			$dalLog = Hapyfish2_Magic_Dal_InviteLog::getDefaultInstance();
			return $dalLog->getAll($uid);
		} catch (Exception $e) {
		}

		return null;
	}

	public static function getAllOfFlow($uid)
	{
		//'2011-09-30'  开始 1317312000
		$time = 1317312000;
		try {
			$dalLog = Hapyfish2_Magic_Dal_InviteLog::getDefaultInstance();
			return $dalLog->getAllByTime($uid, $time);
		} catch (Exception $e) {
		}

		return null;
	}
}