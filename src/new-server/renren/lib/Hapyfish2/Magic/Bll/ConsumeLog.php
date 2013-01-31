<?php

class Hapyfish2_Magic_Bll_ConsumeLog
{
	public static function coin($uid, $cost, $summary, $time)
	{
		$info = array(
			'uid' => $uid,
			'cost' => $cost,
			'summary' => $summary,
			'create_time' => $time
		);
		
		$ok = false;
		try {
			$dalLog = Hapyfish2_Magic_Dal_ConsumeLog::getDefaultInstance();
			$dalLog->insert($uid, $info);
			$ok = true;
		} catch (Exception $e) {
			
		}
		
		return $ok;
	}
	
	public static function getCoin($uid, $year, $month, $limit = 50)
	{
		try {
			if ($month < 10) {
				$month = '0' . $month;
			}
			$yearmonth = $year . $month;
			$dalLog = Hapyfish2_Magic_Dal_ConsumeLog::getDefaultInstance();
			return $dalLog->getCoin($uid, $yearmonth, $limit);
		} catch (Exception $e) {
			
		}
		
		return null;
	}
	
	public static function getGold($uid, $year, $month, $limit = 50)
	{
		try {
			if ($month < 10) {
				$month = '0' . $month;
			}
			$yearmonth = $year . $month;
			$dalLog = Hapyfish2_Magic_Dal_ConsumeLog::getDefaultInstance();
			return $dalLog->getGold($uid, $yearmonth, $limit);
		} catch (Exception $e) {
			
		}
		
		return null;
	}
}