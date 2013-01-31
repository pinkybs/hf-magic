<?php

/**
 * logic's Operation
 *
 * @package    Happyfish_Magic_Bll
 * @copyright  Copyright (c) 
 * @create      2010/11/12    zhangxin
 */
class Happyfish_Magic_Bll_TaskDaily
{
	//const 
	const OUTPUT_ERRCODE = -1;
	const DAILY_TASK_COUNT = 5;
	const PREFIX_NAME = '_Happyfish_Magic_Bll_TaskDaily_';
	
	/**
	 * get today's task
	 *
	 * @param integer $uid
	 * @return mixed array 
	 */
	public static function getTodayTask($uid)
	{
	
		if (empty($uid)) {
			return null;
		}
		try {
			$today = date('Ymd');
			$key = SNS_PLATFORM . self::PREFIX_NAME . 'getTodayTask_' . $uid;
	        $cache = Happyfish_Cache_Memcached::getInstance();
	        $aryTodayTask = $cache->get($key);
	        $isNew = $aryTodayTask ? false : true;
	        //init today's task
	        if (empty($aryTodayTask) || $aryTodayTask['t_date'] != $today) {
	        	$aryTodayTask = array();
	        	$aryTodayTask['t_date'] = $today;
	        	$lstNbTask = Happyfish_Magic_Bll_Cache_NbBasicInfo::listNbTaskDaily();
	        	$rndTaskIds = array_rand($lstNbTask, self::DAILY_TASK_COUNT);
	        	foreach ($rndTaskIds as $idx=>$data) {
	        		$aryTodayTask[$data] = 0;
	        	}
	        	if ($isNew) {
	        		$cache->add($key, $aryTodayTask, Happyfish_Cache_Memcached::LIFE_TIME_ONE_DAY);
	        	}
	        	else {
	        		$cache->replace($key, $aryTodayTask, Happyfish_Cache_Memcached::LIFE_TIME_ONE_DAY);
	        	}
	        }
		}
		catch (Exception $e) {
            info_log($uid.'[Happyfish_Magic_Bll_TaskDaily]-[getTodayTask]:'.$e->getMessage(), 'err-TaskDaily-catched');
            info_log($uid.'[Happyfish_Magic_Bll_TaskDaily]-[getTodayTask]:'.$e->getTraceAsString(), 'err-TaskDaily-catched');
            return null;
		}
		
		return $aryTodayTask;
	}
	
	/**
	 * update today's task
	 *
	 * @param integer $uid
	 * @param integer $taskId
	 * @return mixed array 
	 */
	public static function updateTodayTask($uid, $taskId)
	{
	
		if (empty($uid) || empty($taskId)) {
			return false;
		}
		try {
			$aryTodayTask = self::getTodayTask($uid);
			if (isset($aryTodayTask[$taskId]) && $aryTodayTask[$taskId]>=0) {
				$aryTodayTask[$taskId] += 1;
				$key = SNS_PLATFORM . self::PREFIX_NAME . 'getTodayTask_' . $uid;
		        $cache = Happyfish_Cache_Memcached::getInstance();
		        $cache->replace($key, $aryTodayTask, Happyfish_Cache_Memcached::LIFE_TIME_ONE_DAY);
			}
		}
		catch (Exception $e) {
            info_log($uid.'[Happyfish_Magic_Bll_TaskDaily]-[updateTodayTask]:'.$e->getMessage(), 'err-TaskDaily-catched');
            info_log($uid.'[Happyfish_Magic_Bll_TaskDaily]-[updateTodayTask]:'.$e->getTraceAsString(), 'err-TaskDaily-catched');
            return false;
		}
		
		return true;
	}
	
	
	/**
	 * update today's task
	 *
	 * @param integer $uid
	 * @param integer $taskId
	 * @return mixed array 
	 */
	public static function achieveTodayTask($uid, $taskId)
	{
		if (empty($uid) || empty($taskId)) {
			return null;
		}
		try {
			$aryTodayTask = self::getTodayTask($uid);
			if (isset($aryTodayTask[$taskId])) {
				//daily task acheved
				$lstNbTask = Happyfish_Magic_Bll_Cache_NbBasicInfo::listNbTaskDaily();
				$rowNbTask = $lstNbTask[$taskId];
				if ($aryTodayTask[$taskId] >= $rowNbTask['need_count']) {
					$aryTodayTask[$taskId] = -1;
					$key = SNS_PLATFORM . self::PREFIX_NAME . 'getTodayTask_' . $uid;
			        $cache = Happyfish_Cache_Memcached::getInstance();
			        $cache->replace($key, $aryTodayTask, Happyfish_Cache_Memcached::LIFE_TIME_ONE_DAY);
			        
			        //rewards to user
			        $rowUser = Happyfish_Magic_Bll_User::getUserGameInfo($uid);
					$crystalType = '';
					if (1 == $rowUser['major_magic']) {
						$crystalType = 'red';
					}
					else if (2 == $rowUser['major_magic']) {
						$crystalType = 'blue';
					}
					else {
						$crystalType = 'green';
					}
			        $dalUser = Happyfish_Magic_Dal_User::getDefaultInstance();
			        $aryParam['exp'] = $rowNbTask['gain_exp'];
			        $aryParam[$crystalType] = $rowNbTask['gain_crystal'];
			        $dalUser->updateUserByMultipleField($uid, $aryParam);
					//clear cache
					Happyfish_Magic_Bll_Cache_User::clearAppUser($uid);
			
					//reward card
					/*if ($nbInfo['levup_card'] && $nbInfo['levup_card_count']) {
						$dalCard = Happyfish_Magic_Dal_Card::getDefaultInstance();
						$rowCard = $dalCard->getUserCard($uid, $nbInfo['levup_card']);
						if (empty($rowCard)) {
							$dalCard->insert(array('uid'=>$uid, 'cid'=>$nbInfo['levup_card'], 'card_count'=>$nbInfo['levup_card_count']));
						}
						else {
							$dalCard->updateUserCardByField($uid, $nbInfo['levup_card'], 'card_count', $nbInfo['levup_card_count']);
						}
					}*/
					
					//is level up 
					$levelUp = Happyfish_Magic_Bll_User::levelUp($uid);
					if ($levelUp) {
						$info = $levelUp;
						$info['levelUp'] = true;
					}
					else {
						$info = array();
						$info['levelUp'] = false;
					}
					$info['exp'] = $rowNbTask['gain_exp'];
					$resultVo = Happyfish_Magic_Bll_FormatVo::resultVo($info);
				}
			}
		}
		catch (Exception $e) {
            info_log($uid.'[Happyfish_Magic_Bll_TaskDaily]-[achieveTodayTask]:'.$e->getMessage(), 'err-TaskDaily-catched');
            info_log($uid.'[Happyfish_Magic_Bll_TaskDaily]-[achieveTodayTask]:'.$e->getTraceAsString(), 'err-TaskDaily-catched');
            return false;
		}
		
		return true;
	}
}