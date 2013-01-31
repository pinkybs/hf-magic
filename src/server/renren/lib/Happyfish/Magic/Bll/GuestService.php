<?php

/**
 * logic's Operation
 *
 * @package    Happyfish_Magic_Bll
 * @copyright  Copyright (c) 
 * @create      2010/08/17    zhangxin
 */
class Happyfish_Magic_Bll_GuestService
{   

	const BE_STONE_TIME = 300;//172800;//48 hours
	const STEAL_CRYSTAL_NEED_MP = 1;
	const RESCUE_BREAK_NEED_MP = 6;
	const RESCUE_BREAK_GAIN_EXP = 5;
	const OPEN_DOOR_GAIN_EXP = 3;
	const OUTPUT_ERRCODE = -1;
	
	/**
	 * desk info
	 *
	 * @param integer $uid
	 * @return array
	 */
	public static function getDeskInfo($uid)
	{
		$dalMgDesk = Happyfish_Magic_Dal_Mongo_UserDesk::getDefaultInstance();
		$lstDesk = $dalMgDesk->lstDesk($uid, 1, 100);
		return $lstDesk;
	}
	
	/**
	 * calculate door guest queue
	 *
	 * @param integer $uid
	 * @return array
	 */
	public static function getDoorInfo($uid)
	{
		$dalMgDoor = Happyfish_Magic_Dal_Mongo_UserDoor::getDefaultInstance();
		$lstDoor = $dalMgDoor->lstDoor($uid, 1, 100);
		$now = time();
		foreach ($lstDoor as $key=>$vdata) {
			if (empty($vdata['wait_guest_ary'])) {
				//create a guest queue
				$guestLev = $vdata['door_guest_type'];
				$guestQueue = array();
				for ($i=0; $i<$vdata['door_guest_limit']; $i++) {
					$guestQueue[] = rand(1, $guestLev);
				}
				$dalMgDoor->update($vdata['uid'], $vdata['door_id'], array('wait_guest_ary' => $guestQueue));
				$lstDoor[$key]['wait_guest_ary'] = $guestQueue;
			}
		}
		return $lstDoor;
	}
	
	/**
	 * open door 
	 *
	 * @param integer $uid
	 * @param integer $doorId
	 * @return mixed  array / false / -1 (room is full) ERR0202 / -2 ERR0203 (is not time to open) / -3 ERR0204(has not guest queue ) / -4 ERR0205 not have this door / -5 ERR0206 have no learnt magic
	 */
	public static function openDoor($uid, $doorId)
	{
		try {
			
			$dalMgDoor = Happyfish_Magic_Dal_Mongo_UserDoor::getDefaultInstance();
			$rowDoor = $dalMgDoor->getInfo($uid, $doorId);
			$now = time();
			//not allow to open door
			if ( empty($rowDoor) || ($now - $rowDoor['last_open_time'] < $rowDoor['door_cooldown']) ) {
				return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0203')));
			}
			//has not guest queue 
			if ( empty($rowDoor['wait_guest_ary']) ) {
				return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0204')));
			}
			
			$dalBuilding = Happyfish_Magic_Dal_Building::getDefaultInstance();
			$rowBuild = $dalBuilding->getUserBuilding($uid, $doorId);
			if (empty($rowBuild) || empty($rowBuild['status'])) {
				return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0205')));
			}
			
			//user base info 
			$rowUser = Happyfish_Magic_Bll_User::getUserGameInfo($uid);
			$dalMgUser = Happyfish_Magic_Dal_Mongo_UserLoginInfo::getDefaultInstance();
			$limitGuest = $rowUser['nbLevInfo']['limit_person'];
			$lstRoomGuest = $rowUser['mgInfo']['in_house_guest_ary'];
			
			//find empty desk
			$dalMgDesk = Happyfish_Magic_Dal_Mongo_UserDesk::getDefaultInstance();
			$lstDesk = $dalMgDesk->lstDesk($uid, 1, 100);
			$aryEmptyDesk = array();
			$cntDeskGuest = 0;
			foreach ($lstDesk as $desk) {
				if (empty($desk['status'])) {
					$aryEmptyDesk[] = $desk;
				}
				//is guest still in desk
				//0-没人 1-等待学习 2-学习中 3-学习完了 4-紧急状态
				if ($desk['status']) {
					if (1 == $desk['status']) {
						$spentTime = -1;
					}
					else if ($desk['break_time'] == -1) {
						$spentTime = $now - $desk['start_time'];
					}
					else {
						//time not arrive
						if (empty($desk['help_uid']) || empty($desk['rescue_time'])) {
							$spentTime = -1;
						}
						$spentTime = $desk['break_time'] - $desk['start_time'] + ($now - $desk['rescue_time']);
					}
					if ($spentTime < (int)$desk['spend_time']) {
						$cntDeskGuest++;
					}
				}
			}
info_log($uid . ':walk|desk|limit:' . count($lstRoomGuest).'|'.$cntDeskGuest.'|'.$limitGuest, 'bbb');
			//room is full
			if ( !empty($lstRoomGuest) && (count($lstRoomGuest)+$cntDeskGuest) >= $limitGuest ) {
				return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0202')));
			}
			if (!empty($aryEmptyDesk) && count($aryEmptyDesk) > 0) {
				$lstMagic = Happyfish_Magic_Bll_Cache_User::lstUserMagic($uid);
				//get learnt magic
				$aryMagic = array();
				foreach ($lstMagic as $magicData) {
					$magType = substr($magicData['magic_id'], 0, 1);
					if ($magicData['status'] && $magType == $rowUser['major_magic'] ) {
						$aryMagic[] = $magicData['magic_id'];
					}
				}
				if (empty($aryMagic)) {
					return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0206')));
				}
			}
			
			//let guest in room
			$aryRtn = array();
			$lstRoomGuest = empty($lstRoomGuest) ? array() : $lstRoomGuest;
			$remainCnt = $limitGuest - count($lstRoomGuest);
			for ($i=0; $i<$remainCnt; $i++) {
				$popGuest = array_shift($rowDoor['wait_guest_ary']);
				if (empty($popGuest)) {
					break;
				}
				//find a empty desk 
				$popDesk = array_shift($aryEmptyDesk);
				$studentVo = array();
				$studentVo['avatar_id'] = $popGuest;
				$studentVo['decor_id'] = 0;
				$studentVo['state'] = 0;
				$studentVo['time'] = 0;
				$studentVo['magic_id'] = 0;
				$studentVo['event_time'] = 0;
				$studentVo['stone_time'] = 0;
				$studentVo['crystal'] = 0;
				$studentVo['can_steal'] = false;
				if ($popDesk) {
					$cntCanServeM = count($aryMagic) - 1;
					$magicId = $aryMagic[mt_rand(0,$cntCanServeM)];
					$dalMgDesk->update($uid, $popDesk['desk_id'], array('status'=>1, 'guest_id'=>$popGuest, 'magic_id'=>$magicId));
					//create Vo data
					$studentVo['decor_id'] = $popDesk['desk_id'];
					$studentVo['state'] = 1; //0闲逛中 1未教 2学习中 3已教完
					$studentVo['magic_id'] = $magicId;
				}
				//no desk - walk in room
				else {
					$lstRoomGuest[] = array('guestType' => $popGuest, 'inRoomTime' => microtime(true));
				}
				$aryRtn[] = $studentVo;
			}
			$dalMgUser->update($uid, array('in_house_guest_ary' => $lstRoomGuest));
			
			//door open time changed
			if (empty($rowDoor['wait_guest_ary'])) {
				$dalMgDoor->update($uid, $doorId, array('last_open_time' => $now));
			}
			else {
				//door status changed
				$dalMgDoor->update($uid, $doorId, array('wait_guest_ary' => $rowDoor['wait_guest_ary'], 'status' => 1));
			}
			
			//save to today's task achievement
			Happyfish_Magic_Bll_TaskDaily::updateTodayTask($uid, 709);
			
			//gain exp
			$dalUser = Happyfish_Magic_Dal_User::getDefaultInstance();
			$dalUser->updateUserByField($uid, 'exp', self::OPEN_DOOR_GAIN_EXP);
			Happyfish_Magic_Bll_Cache_User::clearAppUser($uid);
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
			$info['exp'] = self::OPEN_DOOR_GAIN_EXP;
			$resultVo = Happyfish_Magic_Bll_FormatVo::resultVo($info);
		}
		catch (Exception $e) {
            info_log($uid.'[Happyfish_Magic_Bll_GuestService]-[openDoor]:'.$e->getMessage(), 'err-GuestService-catched');
            info_log($uid.'[Happyfish_Magic_Bll_GuestService]-[openDoor]:'.$e->getTraceAsString(), 'err-GuestService-catched');
            return false;
		}
		
		return array('students' => $aryRtn, 'result' => $resultVo);
	}
	
	
	/**
	 * serve guest 
	 *
	 * @param integer $uid
	 * @param integer $deskId
	 * @return mixed  array / false / -1 ERR0207 (desk empty) / -2 ERR0208 (desk status error) / -3 ERR0209(magic not learnt) / -4 ERR0210 (mp not enough)
	 */
	public static function serveGuest($uid, $deskId)
	{
		try {
			
			$dalMgDesk = Happyfish_Magic_Dal_Mongo_UserDesk::getDefaultInstance();
			$rowDesk = $dalMgDesk->getInfo($uid, $deskId);
			
			if (empty($rowDesk['guest_id']) || empty($rowDesk['magic_id'])) {
				return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0207')));
			}
			if (1 !=$rowDesk['status']) {
				return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0208')));
			}
			
			$magicId = $rowDesk['magic_id'];
			$lstMagic = Happyfish_Magic_Bll_Cache_User::lstUserMagic($uid);
			if (!array_key_exists($uid . '_' .$magicId, $lstMagic) || empty($lstMagic[$uid . '_' .$magicId]['status'])) {
				return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0209')));
			}
			$rowMagic = $lstMagic[$uid . '_' .$magicId];
			
			$nbMagic = Happyfish_Magic_Bll_Cache_NbBasicInfo::getNbMagicA($magicId);
			$rowUser = Happyfish_Magic_Bll_User::getUserGameInfo($uid);
			$mp = (int)$rowUser['mp'];
			if ($mp < $nbMagic['need_mp']) {
				return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0210')));
			}
			
			$now = time();
			$deskInfo = array();
			$deskInfo['status'] = 2;
			$deskInfo['start_time'] = $now;
			$deskInfo['spend_time'] = (int)$nbMagic['spend_time'];
			//abnormal status happen rate 
			$eventPercent = $nbMagic['abnormal_percent'];//30% percent
			$rndNum = 1;//mt_rand(1, 100);
			$deskInfo['break_time'] = $rndNum > $eventPercent ? -1 : ($now+$nbMagic['spend_time']/2);
			$deskInfo['red'] = (int)$nbMagic['gain_red'];
			$deskInfo['blue'] = (int)$nbMagic['gain_blue'];
			$deskInfo['green'] = (int)$nbMagic['gain_green'];
			$deskInfo['help_uid'] = '';
			$deskInfo['steal_uid_ary'] = '';
			$gainExp = (int)$nbMagic['gain_exp'];
			$dalMgDesk->update($uid, $deskId, $deskInfo);
			
			//update teach count
			$dalMagic = Happyfish_Magic_Dal_Magic::getDefaultInstance();
			$dalMagic->updateUserMagicByField($uid, $magicId, 'use_count', 1);
			
			//update user exp and mp
			$dalUser = Happyfish_Magic_Dal_User::getDefaultInstance();
			$dalUser->updateUserByMultipleField($uid, array('exp' => $gainExp, 'mp' => (0-(int)$nbMagic['need_mp'])));
			//$dalUser->updateUserByField($uid, 'exp', $gainExp);
			//clear cache
			Happyfish_Magic_Bll_Cache_User::clearAppUser($uid);
			
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
			$info['exp'] = $gainExp;
			$resultVo = Happyfish_Magic_Bll_FormatVo::resultVo($info);

			$studentVo = array();
			$studentVo['avatar_id'] = $rowDesk['guest_id'];
			$studentVo['decor_id'] = $deskId;
			$studentVo['state'] = $deskInfo['status']; //0闲逛中 1未教 2学习中 3已教完
			$studentVo['time'] = $deskInfo['spend_time'];
			$studentVo['magic_id'] = $magicId;
			$studentVo['event_time'] = ($deskInfo['break_time'] == -1 ? -1 : ($deskInfo['break_time'] - $deskInfo['start_time']));
			$studentVo['stone_time'] = $deskInfo['spend_time'] + self::BE_STONE_TIME;
			if (1 == $nbMagic['type']) {
				$studentVo['crystal'] = $deskInfo['red'];
			}
			else if (2 == $nbMagic['type']) {
				$studentVo['crystal'] = $deskInfo['blue'];
			}
			else {
				$studentVo['crystal'] = $deskInfo['green'];
			}
			$studentVo['can_steal'] = false;
			
			return array('student'=>$studentVo, 'result'=>$resultVo);
		}
		catch (Exception $e) {
            info_log($uid.'[Happyfish_Magic_Bll_GuestService]-[serveGuest]:'.$e->getMessage(), 'err-GuestService-catched');
            info_log($uid.'[Happyfish_Magic_Bll_GuestService]-[serveGuest]:'.$e->getTraceAsString(), 'err-GuestService-catched');
            return false;
		}
	}
	
	
	/**
	 * rescue guest break status
	 *
	 * @param integer $actor
	 * @param integer $target
	 * @param array $deskIds
	 * @return mixed  array / false / -1 ERR0207 (desk empty) / -2 ERR0211 (desk status error) / -3 ERR0212 (no need to rescue) / -4 ERR0213 (has been rescued) / -5 ERR0210 (mp not enough)
	 */
	public static function rescueGuest($actor, $target, $deskIds)
	{
		try {
			
			//TODO:is friend check
			
			$dalMgDesk = Happyfish_Magic_Dal_Mongo_UserDesk::getDefaultInstance();
			$rowUser = Happyfish_Magic_Bll_User::getUserGameInfo($actor);
			$nowMp = (int)$rowUser['mp'];
			$majorMagic = $rowUser['major_magic'];
			$abnormalStatus = '';
			$aryMessage = array();
			if (1 == $majorMagic) {
				$abnormalStatus = Happyfish_Magic_Bll_Language_Local::getText('TXT2018');
			}
			else if (2 == $majorMagic) {
				$abnormalStatus = Happyfish_Magic_Bll_Language_Local::getText('TXT2019');
			}
			else {
				$abnormalStatus = Happyfish_Magic_Bll_Language_Local::getText('TXT2020');
			}
			
			$now = time();
			$aryRtn = array();
			$spentMp = 0;
			$gainExp = 0;
			foreach ($deskIds as $deskId) {
				$rowDesk = $dalMgDesk->getInfo($target, $deskId);
				if (empty($rowDesk) || empty($rowDesk['guest_id']) || empty($rowDesk['magic_id'])) {
					$aryRtn[] = array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0207')));
					continue;
				}
				if ($rowDesk['status']!=2 && $rowDesk['status']!=4) {
					$aryRtn[] = array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0211')));
					continue;
				}
				if (-1 == $rowDesk['break_time'] || $rowDesk['break_time'] > $now) {
					$aryRtn[] = array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0212')));
					continue;
				}
				if ($rowDesk['help_uid']) {
					$aryRtn[] = array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0213')));
					continue;
				}
				if ($nowMp < self::RESCUE_BREAK_NEED_MP) {
					$aryRtn[] = array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0210')));
					continue;
				}
				
				//change status
				$tmpRst = $dalMgDesk->update($target, $deskId, array('status'=>2, 'rescue_time'=>$now, 'help_uid'=>$actor));
				$nowMp -= self::RESCUE_BREAK_NEED_MP;
				$spentMp += self::RESCUE_BREAK_NEED_MP;
				$tmpExp = self::RESCUE_BREAK_GAIN_EXP;
				$gainExp += $tmpExp;
				
				$resultVo = array();
				$resultVo['status'] = 1;
				$resultVo['content'] = '';
				$resultVo['levelUp'] = false;
				$resultVo['red'] = 0;
				$resultVo['blue'] = 0;
				$resultVo['green'] = 0;
				$resultVo['gem'] = 0;
				$resultVo['exp'] = $tmpExp;
				$aryRtn[] = $resultVo;
				//$aryRtn[] = array('gainExp' => $tmpExp, 'spentMp' => self::RESCUE_BREAK_NEED_MP);
				
			}//end for
			
			if ($spentMp || $gainExp) {
				//update user exp and mp
				$dalUser = Happyfish_Magic_Dal_User::getDefaultInstance();
				$dalUser->updateUserByMultipleField($actor, array('exp' => $gainExp, 'mp' => (int)(0-$spentMp)));
				
				//clear cache
				Happyfish_Magic_Bll_Cache_User::clearAppUser($actor);
				//is level up 
				$levelUp = Happyfish_Magic_Bll_User::levelUp($actor);
				if ($levelUp) {
					$info = $levelUp;
					$info['levelUp'] = true;
				}
				else {
					$info = array();
					$info['levelUp'] = false;
				}
				$info['exp'] = $gainExp;
				$resultVo = Happyfish_Magic_Bll_FormatVo::resultVo($info);
								
				//send message
				$aryToTarget = array();
				$aryToTarget['uid'] = (string)$target;
				$aryToTarget['actor'] = (string)$actor;
				$aryToTarget['target'] = (string)$target;
				$aryToTarget['template'] = 2;
				$aryToTarget['properties'] = array('actor'=>$rowUser['name'], 'status_name'=>$abnormalStatus);
				$aryToTarget['create_time'] = $now;
				$aryMessage[] = $aryToTarget;
				if ($aryMessage && count($aryMessage)>0) {
					Happyfish_Magic_Bll_Message::addUserMessage($aryMessage);
				}
			}
		
			return array('results' => $aryRtn);
		}
		catch (Exception $e) {
            info_log($uid.'[Happyfish_Magic_Bll_GuestService]-[rescueGuest]:'.$e->getMessage(), 'err-GuestService-catched');
            info_log($uid.'[Happyfish_Magic_Bll_GuestService]-[rescueGuest]:'.$e->getTraceAsString(), 'err-GuestService-catched');
            return false;
		}
	}
	
	
	/**
	 * steal crystal
	 *
	 * @param integer $actor
	 * @param integer $target
	 * @param array $deskIds
	 * @return mixed  array / false / -1 ERR0207 (desk empty) / -2 ERR0211 (desk status error) / -3 ERR0214 (time not arrive) / -4 ERR0215(has stealed) / -5 ERR0216(steal limited)
	 */
	public static function stealCrystal($actor, $target, $deskIds)
	{
		try {
			
			//TODO:is friend check
			
			$dalMgDesk = Happyfish_Magic_Dal_Mongo_UserDesk::getDefaultInstance();
			$rowUser = Happyfish_Magic_Bll_User::getUserGameInfo($actor);
			$rowTarUser = Happyfish_Magic_Bll_User::getUserGameInfo($target);
			$dalMgUser = Happyfish_Magic_Dal_Mongo_UserLoginInfo::getDefaultInstance();
			$targetMajorMagic = $rowTarUser['major_magic'];
			$nowMp = $rowUser['mp'];
			$spentMp = 0;
			$crystalName = '';
			$aryMessage = array();
			if (1 == $targetMajorMagic) {
				$crystalName = Happyfish_Magic_Bll_Language_Local::getText('TXT2011');
			}
			else if (2 == $targetMajorMagic) {
				$crystalName = Happyfish_Magic_Bll_Language_Local::getText('TXT2012');
			}
			else {
				$crystalName = Happyfish_Magic_Bll_Language_Local::getText('TXT2013');
			}
			
			$now = time();
			$aryStudentVo = array();
			$aryResultVo = array();
			$aryLogs = array();
			$actGainRed = $actGainBlue = $actGainGreen = 0;
			$tarGainRed = $tarGainBlue = $tarGainGreen = 0;
			if ($actor == $target) {
				//wall guest list
				$lstRoomGuest = $rowUser['mgInfo']['in_house_guest_ary'];
				//rand magic to student
				$lstMagic = Happyfish_Magic_Bll_Cache_User::lstUserMagic($actor);
				//get learnt magic
				$aryMagic = array();
				foreach ($lstMagic as $magicData) {
					$magType = substr($magicData['magic_id'], 0, 1);
					if ($magicData['status'] && $magType == $rowUser['major_magic'] ) {
						$aryMagic[] = $magicData['magic_id'];
					}
				}
			}
			
			foreach ($deskIds as $deskId) {
				$rowDesk = $dalMgDesk->getInfo($target, $deskId);
				//desk empty
				if (empty($rowDesk) || empty($rowDesk['guest_id']) || empty($rowDesk['magic_id'])) {
					$aryLogs[] = Happyfish_Magic_Bll_Language_Local::getText('ERR0207');
					continue;
				}
				//desk status error
				if ($rowDesk['status']!=2 && $rowDesk['status']!=3) {
					$aryLogs[] = Happyfish_Magic_Bll_Language_Local::getText('ERR0211');
					continue;
				}
				
				if ($rowDesk['break_time'] == -1) {
					$spentTime = $now - $rowDesk['start_time'];
				}
				else {
					//time not arrive
					if (empty($rowDesk['help_uid']) || empty($rowDesk['rescue_time'])) {
						$aryLogs[] = Happyfish_Magic_Bll_Language_Local::getText('ERR0214');
						continue;
					}
					$spentTime = $rowDesk['break_time'] - $rowDesk['start_time'] + ($now - $rowDesk['rescue_time']);
				}
				//is teach time over
				if ($spentTime < $rowDesk['spend_time']) {
					$aryLogs[] = Happyfish_Magic_Bll_Language_Local::getText('ERR0214');
					continue;
				}
				
				//pick up crystal
				if ($actor == $target) {
					//become stone
					if ($spentTime - $rowDesk['spend_time'] > self::BE_STONE_TIME) {
						//pick up nothing a stone null crystal
						$rowDesk['red'] = $rowDesk['blue'] = $rowDesk['green'] = 0;
					}
					else {
						$actGainRed += $rowDesk['red'];
						$actGainBlue += $rowDesk['blue'];
						$actGainGreen += $rowDesk['green'];
					}
					
					//let walk guest into desk
					$newAvatar = $newMagic = $newStatus = 0;
					if (!empty($lstRoomGuest) && count($lstRoomGuest)) {
						$popGuest = array_shift($lstRoomGuest);
						
						//save changes to walk guest
						$dalMgUser->update($actor, array('in_house_guest_ary' => $lstRoomGuest));
						$newAvatar = $popGuest['guestType'];
						$cntCanServeM = count($aryMagic) - 1;
						$newMagic = $aryMagic[mt_rand(0,$cntCanServeM)];
						$newStatus = 1;
					}
					//update actor mongo desk
					$dalMgDesk->update($actor, $deskId, 
									   array('status'=>$newStatus, 'guest_id'=>$newAvatar, 'magic_id'=>$newMagic,
									         'red'=>0, 'blue'=>0, 'green'=>0,
									   		 'start_time'=>0, 'break_time'=>0, 'rescue_time'=>0,'spend_time'=>0,
									         'help_uid'=>0,'steal_uid_ary'=>''));
				
					//create VO
					$studentVo = array();
					$studentVo['avatar_id'] = $newAvatar;
					$studentVo['decor_id'] = $deskId;
					$studentVo['state'] = $newStatus;//0闲逛中 1未教 2学习中 3已教完
					$studentVo['time'] = 0;
					$studentVo['magic_id'] = $newMagic;
					$studentVo['event_time'] = 0;
					$studentVo['stone_time'] = 0;
					$studentVo['crystal'] = 0;
					$studentVo['can_steal'] = 0;
					$aryStudentVo[] = $studentVo;

					$resultVo = array();
					$resultVo['status'] = 1;
					$resultVo['content'] = '';
					$resultVo['levelUp'] = false;
					$resultVo['red'] = $rowDesk['red'];
					$resultVo['blue'] = $rowDesk['blue'];
					$resultVo['green'] = $rowDesk['green'];
					$resultVo['gem'] = 0;
					$resultVo['exp'] = 0;
					$aryResultVo[] = $resultVo;
					
					if (!empty($rowDesk['red'])) {
						$gainCrystal = $rowDesk['red'];
					}
					else if (!empty($rowDesk['blue'])) {
						$gainCrystal = $rowDesk['blue'];
					}
					else {
						$gainCrystal = $rowDesk['green'];
					}
					if (empty($gainCrystal)) {
						$aryLogs[] = 'desk:' . $deskId. ' crystal become stones!';
					}
					else {
						$aryLogs[] = 'desk:' . $deskId. ' gain ' .  $gainCrystal . ' crystal!';
					}
					
				}//end pick up self crystal
				
				//steal crystal
				else {
					//has stealed
					if (!empty($rowDesk['steal_uid_ary']) && array_key_exists($actor, $rowDesk['steal_uid_ary'])) {
						$aryLogs[] = Happyfish_Magic_Bll_Language_Local::getText('ERR0215');
						continue;
					}
					//nb magic info
					$nbMagic = Happyfish_Magic_Bll_Cache_NbBasicInfo::getNbMagicA($rowDesk['magic_id']);
					$stealRed = $stealBlue = $stealGreen = 0;
					$limitedRed = round($nbMagic['gain_red'] * $nbMagic['steal_limit_percent'] / 100, 0);
					$limitedBlue = round($nbMagic['gain_blue'] * $nbMagic['steal_limit_percent'] / 100, 0);
					$limitedGreen = round($nbMagic['gain_green'] * $nbMagic['steal_limit_percent'] / 100, 0);
					//steal limited 
					if ($rowDesk['red'] < $limitedRed || $rowDesk['blue'] < $limitedBlue || $rowDesk['green'] < $limitedGreen) {
						$aryLogs[] = Happyfish_Magic_Bll_Language_Local::getText('ERR0216');
						continue;
					}
					//is mp enough
					if ($nowMp < self::STEAL_CRYSTAL_NEED_MP) {
						$aryRtn[] = array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0210')));
						continue;
					}
					$nowMp -= self::STEAL_CRYSTAL_NEED_MP;
					$spentMp += self::STEAL_CRYSTAL_NEED_MP;
					
					//steal crystal count
					$stealRed = round($rowDesk['red'] * $nbMagic['steal_percent'] / 100, 0);
					$stealRed = (empty($stealRed) && $rowDesk['red']) ? 1 : $stealRed;
					$stealBlue = round($rowDesk['blue'] * $nbMagic['steal_percent'] / 100, 0);
					$stealBlue = (empty($stealBlue) && $rowDesk['blue']) ? 1 : $stealBlue;
					$stealGreen = round($rowDesk['green'] * $nbMagic['steal_percent'] / 100, 0);
					$stealGreen = (empty($stealGreen) && $rowDesk['green']) ? 1 : $stealGreen;
					//actor get
					$stealGetRedActor = round($stealRed * $nbMagic['steal_get_percent'] / 100, 0);
					$stealGetRedActor = (empty($stealGetRedActor) && $rowDesk['red']) ? 1 : $stealGetRedActor;
					$stealGetBlueActor = round($stealBlue * $nbMagic['steal_get_percent'] / 100, 0);
					$stealGetBlueActor = (empty($stealGetBlueActor) && $rowDesk['blue']) ? 1 : $stealGetBlueActor;
					$stealGetGreenActor = round($stealGreen * $nbMagic['steal_get_percent'] / 100, 0);
					$stealGetGreenActor = (empty($stealGetGreenActor) && $rowDesk['green']) ? 1 : $stealGetGreenActor;
					//target get
					$stealGetRedTarget = $stealRed - $stealGetRedActor;
					$stealGetBlueTarget = $stealBlue - $stealGetBlueActor;
					$stealGetGreenTarget = $stealGreen - $stealGetGreenActor;
					
					$actGainRed += $stealGetRedActor;
					$actGainBlue += $stealGetBlueActor;
					$actGainGreen += $stealGetGreenActor;
					
					$tarGainRed += $stealGetRedTarget;
					$tarGainBlue += $stealGetBlueTarget;
					$tarGainGreen += $stealGetGreenTarget;

					$rowDesk['steal_uid_ary'][$actor] = "$actGainRed,$actGainBlue,$actGainGreen";
					//update target mongo desk
					$dalMgDesk->update($target, $deskId, 
									   array('steal_uid_ary'=>$rowDesk['steal_uid_ary'],
									   		 'red'=>($rowDesk['red']-$stealRed),
									   		 'blue'=>($rowDesk['blue']-$stealBlue),
									   		 'green'=>($rowDesk['green']-$stealGreen)));
									   
									   
					//create VO
					$resultVo = array();
					$resultVo['status'] = 1;
					$resultVo['content'] = '';
					$resultVo['levelUp'] = false;
					$resultVo['red'] = $stealGetRedActor;
					$resultVo['blue'] = $stealGetBlueActor;
					$resultVo['green'] = $stealGetGreenActor;
					$resultVo['gem'] = 0;
					$resultVo['exp'] = 0;
					$aryResultVo[] = $resultVo;
					if (!empty($stealRed)) {
						$gainCrystal1 = $stealGetRedActor;
						$gainCrystal2 = $stealGetRedTarget;
					}
					else if (!empty($stealBlue)) {
						$gainCrystal1 = $stealGetBlueActor;
						$gainCrystal2 = $stealGetBlueTarget;
					}
					else {
						$gainCrystal1 = $stealGetGreenActor;
						$gainCrystal2 = $stealGetGreenTarget;
					}
					$aryLogs[] = "desk:$deskId $actor gain $gainCrystal1 crystal. / $target gain $gainCrystal2 crystal!";
					
					//message to send
					$aryToTarget = array();
					$aryToTarget['uid'] = (string)$target;
					$aryToTarget['actor'] = (string)$actor;
					$aryToTarget['target'] = (string)$target;
					$aryToTarget['template'] = 1;
					$aryToTarget['properties'] = array('actor'=>$rowUser['name'], 'num1'=>($gainCrystal1+$gainCrystal2),
													   'item_name'=>$crystalName, 'num2'=>$gainCrystal1);
					$aryToTarget['create_time'] = $now;
					$aryMessage[] = $aryToTarget;
				}
			}//end one desk
			
			//update target user
			$dalUser = Happyfish_Magic_Dal_User::getDefaultInstance();
			if ($tarGainRed || $tarGainBlue || $tarGainGreen) {
				//update user crystal
				$dalUser->updateUserByMultipleField($target, array('red'=>$tarGainRed, 'blue'=>$tarGainBlue, 'green'=>$tarGainGreen));
				//clear cache
				Happyfish_Magic_Bll_Cache_User::clearAppUser($target);
			}
			
			//update actor user
			$dalUser = Happyfish_Magic_Dal_User::getDefaultInstance();
			if ($actGainRed || $actGainBlue || $actGainGreen || $spentMp) {
				//update user crystal
				$dalUser->updateUserByMultipleField($actor, array('red'=>$actGainRed, 'blue'=>$actGainBlue, 'green'=>$actGainGreen, 'mp'=>(int)(0-$spentMp)));
				//clear cache
				Happyfish_Magic_Bll_Cache_User::clearAppUser($actor);
			}
			
			//send message
			if ($aryMessage && count($aryMessage)>0) {
				Happyfish_Magic_Bll_Message::addUserMessage($aryMessage);
			}
				
			return array('students' => $aryStudentVo, 'results' => $aryResultVo, 'messages' => $aryLogs);
		}
		catch (Exception $e) {
            info_log($actor.'_'.$target.'[Happyfish_Magic_Bll_GuestService]-[stealCrystal]:'.$e->getMessage(), 'err-GuestService-catched');
            info_log($actor.'_'.$target.'[Happyfish_Magic_Bll_GuestService]-[stealCrystal]:'.$e->getTraceAsString(), 'err-GuestService-catched');
            return false;
		}
	}
	
	
}