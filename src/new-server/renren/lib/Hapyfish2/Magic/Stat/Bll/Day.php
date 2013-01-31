<?php

class Hapyfish2_Magic_Stat_Bll_Day
{
	public static function getMain($day)
	{
		$data = null;
		try {
			$dalMain = Hapyfish2_Magic_Stat_Dal_Main::getDefaultInstance();
			$data = $dalMain->getDay($day); 
		} catch (Exception $e) {
			
		}
		
		return $data;
	}
	
	public static function getRetention($day)
	{
		$data = null;
		try {
			$dalRetention = Hapyfish2_Magic_Stat_Dal_Retention::getDefaultInstance();
			$data = $dalRetention->getRetention($day); 
		} catch (Exception $e) {

		}
		
		return $data;
	}
	
	public static function getActiveUserLevel($day)
	{
		$data = null;
		try {
			$dalLevel = Hapyfish2_Magic_Stat_Dal_ActiveUserLevel::getDefaultInstance();
			$data = $dalLevel->getDay($day); 
		} catch (Exception $e) {

		}
		
		return $data;
	}
	
	public static function getPayment($day)
	{
		$data = null;
		try {
			$dal = Hapyfish2_Magic_Stat_Dal_Payment::getDefaultInstance();
			$data = $dal->getPayment($day); 
		} catch (Exception $e) {

		}
		
		return $data;
	}
}