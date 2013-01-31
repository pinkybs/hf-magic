<?php

class Hapyfish2_Magic_Bll_Block
{
	public static function add($uid, $status, $type = 0)
	{
		$info = array(
			'uid' => $uid,
			'status' => $status,
			'type' => $type,
			'time' => time()
		);
		
		$ok = false;
		try {
			$ok = Hapyfish2_Platform_Cache_User::updateStatus($uid, $status, true);
			if ($ok) {
				$dalLog = Hapyfish2_Magic_Dal_BlockLog::getDefaultInstance();
				$dalLog->insert($uid, $info);
			}
		} catch (Exception $e) {
		}
		
		return $ok;
	}
	
	public static function getAll($uid)
	{
		try {
			$dalLog = Hapyfish2_Magic_Dal_BlockLog::getDefaultInstance();
			return $dalLog->getAll($uid);
		} catch (Exception $e) {
		}
		
		return null;
	}

}