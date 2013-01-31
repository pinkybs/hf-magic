<?php

class Hapyfish2_Magic_Bll_LevelUpLog
{
	public static function add($uid, $from_level, $to_level)
	{
		$time = time();
		self::check($uid, $from_level, $time);
		
		$info = array(
			'uid' => $uid,
			'from_level' => $from_level,
			'to_level' => $to_level,
			'create_time' => $time
		);
		
		try {
			$dalLog = Hapyfish2_Magic_Dal_LevelUpLog::getDefaultInstance();
			$dalLog->insert($uid, $info);
		} catch (Exception $e) {
		}
	}
	
	public static function getAll($uid)
	{
		try {
			$dalLog = Hapyfish2_Magic_Dal_LevelUpLog::getDefaultInstance();
			return $dalLog->getAll($uid);
		} catch (Exception $e) {
		}
		
		return null;
	}
	
	public static function check($uid, $level, $time)
	{
		if ($level > 10) {
			$all = self::getAll($uid);
			if ($all) {
				$valid = true;
				$t = $time;
				$upHourNum = 0;
				$upDayNum = 0;
				foreach ($all as $item) {
					if ($t - $item['create_time'] < 300) {
						$valid = false;
						break;
					}
					if ($t - $item['create_time'] < 3600) {
						$upHourNum++;
					}
					if ($t - $item['create_time'] < 86400) {
						$upDayNum++;
					}
				}
				
				if ($valid && $level > 10 && $upHourNum > 3) {
					$valid = false;
				}
				
				if ($valid) {
					if (($level > 15 && $upDayNum > 10) || ($level > 20 && $upDayNum > 5)) {
						$valid = false;
					}
				}
				
				if (!$valid) {
					Hapyfish2_Magic_Bll_Block::add($uid, 2, 2);
				}
			}
		}
	}
}