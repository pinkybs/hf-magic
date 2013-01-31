<?php

/**
 * logic's Operation
 *
 * @package    Happyfish_Magic_Bll
 * @copyright  Copyright (c) 
 * @create      2010/08/05    zhangxin
 */
class Happyfish_Magic_Bll_User
{   

	/**
	 * join user
	 *
	 * @param integer $uid
	 * @return boolean
	 */
	public static function join($uid)
	{
		$mongoUser = Happyfish_Magic_Dal_Mongo_SnsUser::getDefaultInstance();
		$userPerson = $mongoUser->getPerson($uid);
		if (empty($userPerson)) {
			return false;
		}

		$dalUser = Happyfish_Magic_Dal_User::getDefaultInstance();
    	if ($dalUser->getUser($uid)) {
    	    return false;
    	}
        
    	$wdb = $dalUser->getWriter();
        $now = time();
		try {
    		//begin transaction
    		$wdb->beginTransaction();            

    		//user building
    		$dalBuilding = Happyfish_Magic_Dal_Building::getDefaultInstance();
    		$dalFloor = Happyfish_Magic_Dal_Floors::getDefaultInstance();
    		$dalWall = Happyfish_Magic_Dal_Walls::getDefaultInstance();
			
    		$aryBuilding = array();
    		$aryBuilding['uid'] = $uid;
    		$aryBuilding['create_time'] = $now;
    		//木质小窗
			$aryBuilding['building_id'] = 197001;
    		$aryBuilding['building_type'] = 7;
    		$aryBuilding['effect_mp'] = 1;
    		$dalBuilding->insert($aryBuilding);
    		//猫头鹰信箱    		
    		$aryBuilding['building_id'] = 195019;
    		$aryBuilding['building_type'] = 5;
    		$aryBuilding['effect_mp'] = 1;
    		$dalBuilding->insert($aryBuilding);
    		
    		//初级小木桌x2
    		$aryBuilding['building_id'] = 191001;
    		$aryBuilding['building_type'] = 1;
    		$aryBuilding['effect_mp'] = 1;
    		$aryBuilding['pos_x'] = 5;
    		$aryBuilding['pos_z'] = 5;
    		$aryBuilding['status'] = 1;
    		$id1 = $dalBuilding->insert($aryBuilding);
    		$aryBuilding['pos_x'] = 3;
    		$id2 = $dalBuilding->insert($aryBuilding);
    		//mongo_magic_user_desk
			$dalMgDesk = Happyfish_Magic_Dal_Mongo_UserDesk::getDefaultInstance();
			$aryDesk1 = array('uid' => (string)$uid, 'desk_id' => (string)$id1, 'building_id'=>191001, 'status' => 0,
							 'guest_id'=>0, 'magic_id'=>0, 'red'=>0, 'blue'=>0, 'green'=>0,
					   		 'start_time'=>0, 'break_time'=>0, 'rescue_time'=>0,'spend_time'=>0,
					         'help_uid'=>0,'steal_uid_ary'=>'');
			$dalMgDesk->insert($aryDesk1);
			$aryDesk2 = array('uid' => (string)$uid, 'desk_id' => (string)$id2, 'building_id'=>191001, 'status' => 0,
							 'guest_id'=>0, 'magic_id'=>0, 'red'=>0, 'blue'=>0, 'green'=>0,
					   		 'start_time'=>0, 'break_time'=>0, 'rescue_time'=>0,'spend_time'=>0,
					         'help_uid'=>0,'steal_uid_ary'=>'');
			$dalMgDesk->insert($aryDesk2);
			
			//木质传送门
			$aryBuilding['building_id'] = 192001;
    		$aryBuilding['building_type'] = 2;
    		$aryBuilding['effect_mp'] = 1;
    		$aryBuilding['pos_x'] = 5;
    		$aryBuilding['pos_z'] = 0;
    		$id3 = $dalBuilding->insert($aryBuilding);
    		//mongo_magic_user_door
    		$rowNbDoor = Happyfish_Magic_Bll_Cache_NbBasicInfo::getNbBuilding(192001);
    		$dalMgDoor = Happyfish_Magic_Dal_Mongo_UserDoor::getDefaultInstance();
    		$aryDoor = array('uid'=>(string)$uid, 'door_id'=>(string)$id3, 'building_id'=>192001, 'wait_guest_ary'=>array(1), 'status'=>0, 'last_open_time'=>$now,
    						 'door_cooldown'=>(int)$rowNbDoor['door_cooldown'], 'door_guest_limit'=>(int)$rowNbDoor['door_guest_limit'], 'door_guest_type'=>$rowNbDoor['door_guest_type']);
    		$dalMgDoor->insert($aryDoor);
			
    		$initLev = Happyfish_Magic_Bll_Cache_NbBasicInfo::getNbLevel(10);
    		//初始地板
    		$aryPos = array();
    		for ($x=0; $x<($initLev['house_size']-1); $x++) {
    			for ($z=0; $z<($initLev['house_size']-1); $z++) {
    				$aryPos[$x][$z] = 193005;
    			}
    		}
    		$aryFloor = array('uid' => $uid, 'floor_decor' => Zend_Json::encode($aryPos));
    		$dalFloor->insert($aryFloor);
    		
			//初始墙壁
    		$aryPos = array();
    		for ($i=0; $i<2; $i++) {
    			for ($j=0; $j<($initLev['house_size']-1); $j++) {
					$aryPos[$i][$j] = 194001;
    			}
    		}
    		$aryWall = array('uid' => $uid, 'wall_decor' => Zend_Json::encode($aryPos));
    		$dalWall->insert($aryWall);
    		
    		$dalFloor->insertUserFloorInBag(array('uid'=>$uid,'floor_id'=>193001,'quantity'=>2));
    		$dalFloor->insertUserFloorInBag(array('uid'=>$uid,'floor_id'=>193002,'quantity'=>1));
    		$dalWall->insertUserWallInBag(array('uid'=>$uid,'wall_id'=>194002,'quantity'=>1));
    		$dalWall->insertUserWallInBag(array('uid'=>$uid,'wall_id'=>194003,'quantity'=>1));
    		
    		//user card
    		$dalCard = Happyfish_Magic_Dal_Card::getDefaultInstance();
    		//1级智慧符文x3
    		$aryCard = array('uid' => $uid, 'cid' => 11, 'card_count' => 3);
    		$dalCard->insert($aryCard);
    		//1级魔法药水x2
    		$aryCard['cid'] = 21;
    		$aryCard['card_count'] = 2;
    		$dalCard->insert($aryCard);
    		
  
    		//user magic
    		$majorMagic = 1;
			$lstNbMagicA = Happyfish_Magic_Bll_Cache_NbBasicInfo::listNbMagicA();
			$lstNbMagicB = Happyfish_Magic_Bll_Cache_NbBasicInfo::listNbMagicB();
			$dalMagic = Happyfish_Magic_Dal_Magic::getDefaultInstance();
			foreach ($lstNbMagicA as $mdata) {
				//can learn more magic
				if ($mdata['type']==$majorMagic && $mdata['level']==1) {
					$dalMagic->insert(array('uid'=>$uid, 'magic_id'=>$mdata['id'], 'status'=>1, 'create_time'=>$now));
				}
			}
			foreach ($lstNbMagicB as $mdata) {
				//can learn more magic
				if ($mdata['level']==1) {
					$dalMagic->insert(array('uid'=>$uid, 'magic_id'=>$mdata['id'], 'create_time'=>$now));
				}
			}
    		
    		//user market 
    		//$dalMarket = Happyfish_Magic_Dal_Market::getDefaultInstance();
    		//$dalMarket->insert(array('uid'=>$uid, 'create_time'=>$now));
    		
			//init magic_user table
			$aryUser = array('uid' => $uid,
							 'level' => $initLev['level'],
							 'next_lev_exp' => $initLev['exp'],
							 'mp_addition' => 3,
							 'mp' => (int)$initLev['max_mp'],
							 'mp_last_recovery_time' => $now,
			                 'red' => 500,
			                 'blue' => 500,
			                 'green' => 500,
			                 'house_name' => $userPerson['name'],
			                 'create_time' => $now);
			$dalUser->insertUser($aryUser);

			//mongo db
			//init login info
			$dalMgLogin = Happyfish_Magic_Dal_Mongo_UserLoginInfo::getDefaultInstance();
			$aryLoginInfo = array('uid' =>(string)$uid, 
								  'last_login_time' => $now, 'login_days' =>1, 'series_login_days' => 1, 
								  'in_house_guest_ary'=>'', 
								  'change_shape'=>0, 
								  'change_shape_stop_time'=>0,
								  'change_shape_count'=>0,
								  'eat_count'=>0, 
								  'eat_last_time'=>0, 
								  'create_time' => $now);
			$dalMgLogin->insert($aryLoginInfo);
			
			
			$wdb->commit();

		}
		catch (Exception $e) {
			$wdb->rollBack();
            info_log($uid.'[Happyfish_Magic_Bll_User]-[join]:'.$e->getMessage(), 'err-User-catched');
            info_log($uid.'[Happyfish_Magic_Bll_User]-[join]:'.$e->getTraceAsString(), 'err-User-catched');
            return false;
		}
		
		return true;
	}
	
	/**
	 * remove user
	 *
	 * @param integer $uid
	 * @return boolean
	 */
	public static function remove($uid)
	{

		$dalUser = Happyfish_Magic_Dal_User::getDefaultInstance();
    	if (!$dalUser->getUser($uid)) {
    	    return false;
    	}
        
    	$wdb = $dalUser->getWriter();
		try {
    		//begin transaction
    		$wdb->beginTransaction();            

    		//user building
    		$dalBuilding = Happyfish_Magic_Dal_Building::getDefaultInstance();
    		$dalBuilding->deleteUserBuilding($uid);
    		
    		$dalFloors = Happyfish_Magic_Dal_Floors::getDefaultInstance();
    		$dalFloors->delete($uid);
    		$dalFloors->deleteUserFloorInBagAll($uid);
    		
    		$dalWalls = Happyfish_Magic_Dal_Walls::getDefaultInstance();
    		$dalWalls->delete($uid);
    		$dalWalls->deleteUserWallInBagAll($uid);
    		
    		//user card
    		$dalCard = Happyfish_Magic_Dal_Card::getDefaultInstance();
    		$dalCard->deleteUserCard($uid);
    		
    		//user magic
    		$dalMagic = Happyfish_Magic_Dal_Magic::getDefaultInstance();
    		$dalMagic->deleteUserMagic($uid);
    		
    		//user magic level
    		$dalMagicLev = Happyfish_Magic_Dal_MagicLevel::getDefaultInstance();
    		$dalMagicLev->deleteUserUserMagicLevel($uid);
    		
    		//user market 
    		$dalMarket = Happyfish_Magic_Dal_Market::getDefaultInstance();
    		$dalMarket->deleteUserMarket($uid);
    		
			//magic_user table			
			$dalUser->deleteUser($uid);
			
			//user mongo login info
			$dalMgLogin = Happyfish_Magic_Dal_Mongo_UserLoginInfo::getDefaultInstance();
			$dalMgLogin->delete($uid);
			
			//user mongo door
			$dalMgDoor = Happyfish_Magic_Dal_Mongo_UserDoor::getDefaultInstance();
			$dalMgDoor->deleteDoor($uid);
			//user mongo desk
			$dalMgDesk = Happyfish_Magic_Dal_Mongo_UserDesk::getDefaultInstance();
			$dalMgDesk->deleteDesk($uid);
			//user mongo message
			$dalMgMsg = Happyfish_Magic_Dal_Mongo_UserMessage::getDefaultInstance();
			$dalMgMsg->deleteMessage($uid);
			
			$wdb->commit();

		}
		catch (Exception $e) {
			$wdb->rollBack();
            info_log($uid.'[Happyfish_Magic_Bll_User]-[remove]:'.$e->getMessage(), 'err-User-catched');
            info_log($uid.'[Happyfish_Magic_Bll_User]-[remove]:'.$e->getTraceAsString(), 'err-User-catched');
            return false;
		}
		
		return true;
	}
	

	/**
	 * user level up
	 *
	 * @param integer $uid
	 * @return boolean
	 */
	public static function levelUp($uid) 
	{
		$rowUser = self::getUserGameInfo($uid);
    	if (!$rowUser) {
    	    return false;
    	}
    	
    	//is need to level up 
		if ($rowUser['exp'] >= $rowUser['next_lev_exp']) {
    		
    		$dalUser = Happyfish_Magic_Dal_User::getDefaultInstance();
			$now = time();
			$nextLev = $rowUser['level']+1;
			$nbInfo = Happyfish_Magic_Bll_Cache_NbBasicInfo::getNbLevel($nextLev);
			
			//level up & level up award
			$aryParam = array();
			$aryParam['level'] = 1;
			$aryParam['next_lev_exp'] = ($nbInfo['exp'] - $rowUser['next_lev_exp']);
			$aryParam['money'] = $nbInfo['levup_money'];
			if (1 == $rowUser['major_magic']) {
				$aryParam['red'] = $nbInfo['levup_crystal'];
			}
			else if (2 == $rowUser['major_magic']) {
				$aryParam['blue'] = $nbInfo['levup_crystal'];
			}
			else if (3 == $rowUser['major_magic']) {
				$aryParam['green'] = $nbInfo['levup_crystal'];
			}
			$dalUser->updateUserByMultipleField($uid, $aryParam);
			//clear cache
			Happyfish_Magic_Bll_Cache_User::clearAppUser($uid);
			
			//insert money change log
			$dalLog = Happyfish_Magic_Dal_MoneyLog::getDefaultInstance();
			$dalLog->insert(array('uid'=>$uid, 'money'=>$nbInfo['levup_money'], 'order_id'=>-1, 'create_time'=>$now));

			//level up magic 
			$lstNbMagicA = Happyfish_Magic_Bll_Cache_NbBasicInfo::listNbMagicA();
			$lstNbMagicB = Happyfish_Magic_Bll_Cache_NbBasicInfo::listNbMagicB();
			$dalMagic = Happyfish_Magic_Dal_Magic::getDefaultInstance();
			foreach ($lstNbMagicA as $mdata) {
				//can learn more magic
				if ($mdata['type']==$rowUser['major_magic'] && $mdata['level']==$nextLev) {
					$rowMagic = $dalMagic->getUserMagic($uid, $mdata['id']);
					if (empty($rowMagic)) {
						$dalMagic->insert(array('uid'=>$uid, 'magic_id'=>$mdata['id'], 'create_time'=>$now));
					}
				}
			}
			foreach ($lstNbMagicB as $mdata) {
				//can learn more magic
				if ($mdata['level']==$nextLev) {
					$rowMagic = $dalMagic->getUserMagic($uid, $mdata['id']);
					if (empty($rowMagic)) {
						$dalMagic->insert(array('uid'=>$uid, 'magic_id'=>$mdata['id'], 'create_time'=>$now));
					}
				}
			}
			Happyfish_Magic_Bll_Cache_User::clearUserMagic($uid);
			
			//level up card
			if ($nbInfo['levup_card'] && $nbInfo['levup_card_count']) {
				$dalCard = Happyfish_Magic_Dal_Card::getDefaultInstance();
				$rowCard = $dalCard->getUserCard($uid, $nbInfo['levup_card']);
				if (empty($rowCard)) {
					$dalCard->insert(array('uid'=>$uid, 'cid'=>$nbInfo['levup_card'], 'card_count'=>$nbInfo['levup_card_count']));
				}
				else {
					$dalCard->updateUserCardByField($uid, $nbInfo['levup_card'], 'card_count', $nbInfo['levup_card_count']);
				}
			}
			
			return array('red'=>$nbInfo['levup_crystal'],'blue'=>$nbInfo['levup_crystal'],'green'=>$nbInfo['levup_crystal'],'gem'=>$nbInfo['levup_money']);
		}
		
		return false;
	}
	
	
	
	
	/**
	 * get user infos in game
	 *
	 * @param integer $uid
	 * @return array
	 */
	public static function getUserGameInfo($uid)
	{
		//game info
		$rowUser = Happyfish_Magic_Bll_Cache_User::getAppUser($uid);
		if (empty($rowUser)) {
			return null;
		}
		//sns info
		$rowSnsUser = Happyfish_Magic_Bll_Cache_User::getPerson($uid);
		$rowUser['name'] = $rowSnsUser['name'];
		$rowUser['headurl'] = $rowSnsUser['headurl'];
		$rowUser['fids'] = Happyfish_Magic_Bll_Cache_User::getFriends($uid);

		//nblevel info
		$nbInfo = Happyfish_Magic_Bll_Cache_NbBasicInfo::getNbLevel($rowUser['level']);
		$rowUser['nbLevInfo'] = $nbInfo;
		
		//mongo info
	    $dalMgUser = Happyfish_Magic_Dal_Mongo_UserLoginInfo::getDefaultInstance();
		$rowMgUser = $dalMgUser->getInfo($uid);
		$rowUser['mgInfo'] = $rowMgUser;
		
		return $rowUser;
	}
	
	
	
	/**
	 * get friends
	 * @param integer $uid
	 * @param integer $page
	 * @param integer $size
	 * @return array $friends
	 */
	public static function getRanking($uid, $page=1, $size=10)
	{
		$ranking = Happyfish_Magic_Bll_Cache_User::getRanking($uid);
		if (empty($ranking)) {
			$friendIds = Happyfish_Magic_Bll_SnsUser::getFriends($uid);
	        $friendIds[] = $uid;
	        //get friend sns info
	        $ranking = Happyfish_Magic_Bll_SnsUser::getPeople($friendIds);
	        //get friend game info
	        foreach ($ranking as $fid=>$data) {
	        	$rowGameInfo = Happyfish_Magic_Bll_Cache_User::getAppUser($fid);
	        	if (!empty($rowGameInfo)) {
	        		$ranking[$fid]['exp'] = $rowGameInfo['exp'];
	        		$ranking[$fid]['level'] = $rowGameInfo['level'];
	        		$ranking[$fid]['major_magic'] = $rowGameInfo['major_magic'];
	        	}
	        }
	        //sort by exp
	        $aryExp = array();
			foreach ($ranking as $fid=>$row) {
			    $aryExp[$fid] = $row['exp'];
			}
	        array_multisort($aryExp, SORT_NUMERIC, SORT_DESC, $ranking);
	        Happyfish_Magic_Bll_Cache_User::setRanking($uid, $ranking);
info_log($uid.':'.getexecutetime(), 'ranking');
		}
		
		//get ranking paging
		$start = ($page - 1) * $size;
		$count = count($ranking);
       	if ($count > 0 && $start < $count) {
       		return array_slice($ranking, $start, $size);
       	}
        return null;
	}
}