<?php

/**
 * logic's Operation
 *
 * @package    Happyfish_Magic_Bll
 * @copyright  Copyright (c) 
 * @create      2010/09/10    zhangxin
 */
class Happyfish_Magic_Bll_Magician
{
	
	const OUTPUT_ERRCODE = -1;
		
	/**
	 * learn magic
	 *
	 * @param integer $uid
	 * @param integer $magicId
	 * @return mixed array / false / -1 ERR0302(level is not allow) / -2 ERR0303(has already learnt) / -3 ERR0304(crystal not enough) / -4 ERR0305(not major magic)
	 */
	public static function learnMagic($uid, $magicId)
	{
		
		$rowUser = Happyfish_Magic_Bll_User::getUserGameInfo($uid);
    	
    	//magic type
    	if ('8' == substr($magicId,0,1)) {
    		$nbMagic = Happyfish_Magic_Bll_Cache_NbBasicInfo::getNbMagicB($magicId);
    	}
    	else {
    		$nbMagic = Happyfish_Magic_Bll_Cache_NbBasicInfo::getNbMagicA($magicId);
    		if ($nbMagic['type'] != $rowUser['major_magic']) {
    			return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0305')));
    		}
    	}
    	
    	//level not arrived
    	if (empty($nbMagic) || $nbMagic['level'] > $rowUser['level']) {
    		return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0302')));
    	}
    	
    	//has already learnt
    	$dalMagic = Happyfish_Magic_Dal_Magic::getDefaultInstance();
    	$rowMagic = $dalMagic->getUserMagic($uid, $magicId);
		if (!empty($rowMagic) && $rowMagic['status']) {
    		return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0303')));
    	}
    	
    	//crystal not enough
    	if ( $nbMagic['red']>$rowUser['red'] || $nbMagic['blue']>$rowUser['blue'] 
    			|| $nbMagic['green']>$rowUser['green'] || $nbMagic['money']>$rowUser['money'] ) {
    		return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0304')));
    	}
    	
    	$wdb = $dalMagic->getWriter();
    	$now = time();
		try {
			//begin transaction
    		$wdb->beginTransaction();     
    		
    		//update magic
			if (empty($rowMagic)) {
				$dalMagic->insert(array('uid'=>$uid, 'magic_id'=>$magicId, 'status'=>1, 'create_time'=>$now));
			}
			else {
				$dalMagic->update(array('status' => 1), $uid, $magicId);
			}
			
			//update user
			$aryParam = array();
			if ($nbMagic['red']) {
				$aryParam['red'] = 0 - (int)$nbMagic['red'];
			}
			if ($nbMagic['blue']) {
				$aryParam['blue'] = 0 - (int)$nbMagic['blue'];
			}
			if ($nbMagic['green']) {
				$aryParam['green'] = 0 - (int)$nbMagic['green'];
			}
			if ($nbMagic['money']) {
				$aryParam['money'] = 0 - (int)$nbMagic['money'];
				//insert money change log
				$dalLog = Happyfish_Magic_Dal_MoneyLog::getDefaultInstance();
				$dalLog->insert(array('uid'=>$uid, 'money'=>(0 -(int)$nbMagic['money']), 'order_id'=>-51, 'reference_id'=>$magicId, 'create_time'=>$now));
			}
			if ($aryParam) {
				$dalUser = Happyfish_Magic_Dal_User::getDefaultInstance();
				$dalUser->updateUserByMultipleField($uid, $aryParam);
			}
			
			$wdb->commit();
			Happyfish_Magic_Bll_Cache_User::clearUserMagic($uid);
			Happyfish_Magic_Bll_Cache_User::clearAppUser($uid);
			
			return array('red'=>(0-(int)$nbMagic['red']), 'blue'=>(0-(int)$nbMagic['blue']),
						 'green'=>(0-(int)$nbMagic['green']), 'money'=>(0-(int)$nbMagic['money']));
			
		}
		catch (Exception $e) {
			$wdb->rollBack();
            info_log($uid.'[Happyfish_Magic_Bll_Magician]-[learnMagic]:'.$e->getMessage(), 'err-Magician-catched');
            info_log($uid.'[Happyfish_Magic_Bll_Magician]-[learnMagic]:'.$e->getTraceAsString(), 'err-Magician-catched');
            return false;
		}
	}
	
	
	/**
	 * recover user mp
	 *
	 * @param integer $uid
	 * @return integer
	 */
	public static function recoverMp($uid)
	{
		
		$rowUser = Happyfish_Magic_Bll_User::getUserGameInfo($uid);
		$maxMp = (int)($rowUser['mp_addition'] + $rowUser['nbLevInfo']['max_mp']);
		$mp = (int)$rowUser['mp'];
		$lastRecoveryTime = $rowUser['mp_last_recovery_time'];
    	$now = time();
    	$goneTime = $now - $lastRecoveryTime;
    	//recovery time has not arrive
		if ($goneTime < $rowUser['nbLevInfo']['mp_refresh_time']) {
			return 0;
		}
		
		try {
			//recovery mp
			$rate = (int)$rowUser['nbLevInfo']['mp_recovery_rate'] + (int)$rowUser['mp_recovery_rate_plus'];
			$addMp = round(($maxMp*$rate/100) * ($goneTime/$rowUser['nbLevInfo']['mp_refresh_time']), 0);
			$newMp = ($mp+$addMp>$maxMp) ? $maxMp : ($mp + $addMp);
			$dalUser = Happyfish_Magic_Dal_User::getDefaultInstance();
			$dalUser->updateUser(array('mp' => $newMp, 'mp_last_recovery_time' => $now), $uid);
			Happyfish_Magic_Bll_Cache_User::clearAppUser($uid);
info_log("$uid-goneTime:$goneTime", 'mprecove');
info_log("$uid-addMp:$addMp", 'mprecove');
			return $newMp;
		}
		catch (Exception $e) {
            info_log($uid.'[Happyfish_Magic_Bll_Magician]-[recoverMp]:'.$e->getMessage(), 'err-Magician-catched');
            info_log($uid.'[Happyfish_Magic_Bll_Magician]-[recoverMp]:'.$e->getTraceAsString(), 'err-Magician-catched');
            return false;
		}
	}
	
	/**
	 * trans shape magic
	 *
	 * @param integer $uid
	 * @param integer $magicId
	 * @return boolean
	 */
	protected static function isLearntMagic($uid, $magicId)
	{
		$lstMagic = Happyfish_Magic_Bll_Cache_User::lstUserMagic($uid);
		return $lstMagic[$uid.'_'.$magicId]['status'] > 0;
	}
	
	/**
	 * trans shape magic
	 *
	 * @param integer $uid
	 * @param integer $targetUid
	 * @param integer $magicId
	 * @return mixed array / false / -1 ERR0306 (can't trans self) / -2 ERR0100(not friend) / -3 ERR0209 (not learnt this magic) / -4 ERR0307(target is in shaping can't trans) / -5 ERR0210(mp not enough) /
	 */
	public static function transShape($uid, $targetUid, $magicId)
	{
		//is self trans self 
		if ($uid == $targetUid) {
			return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0306')));
		}
		
		//is friend TODO:
		
		//is learnt magic
		if (!self::isLearntMagic($uid, $magicId)) {
			return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0209')));
		}
		
		$rowUser = Happyfish_Magic_Bll_User::getUserGameInfo($uid);
		$rowTarUser = Happyfish_Magic_Bll_User::getUserGameInfo($targetUid);
		$now = time();
		//time not allow
		if ($now < $rowTarUser['mgInfo']['change_shape_stop_time']) {
			return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0307')));
		}
		
		$nbMagic = Happyfish_Magic_Bll_Cache_NbBasicInfo::getNbMagicB($magicId);
		//mp not enough
		if ((int)$rowUser['mp'] < $nbMagic['need_mp']) {
			return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0210')));
		}
		
		try {
			//update target info
			$dalMgUser = Happyfish_Magic_Dal_Mongo_UserLoginInfo::getDefaultInstance();
			$dalMgUser->update($targetUid, array('change_shape'=>$magicId, 
												 'change_shape_stop_time'=>(int)($now+$nbMagic['magic_time']),
												 'change_shape_count'=>(int)($rowTarUser['mgInfo']['change_shape_count']+1)));
			
			//update actor info
			//update user items
			$dalItem = Happyfish_Magic_Dal_Item::getDefaultInstance();
			$dalItem->addUserItem(array('uid'=>$uid,'mid'=>$nbMagic['gain_item'],'item_count'=>1));
			//update user exp and mp
			$dalUser = Happyfish_Magic_Dal_User::getDefaultInstance();
			$dalUser->updateUserByMultipleField($uid, array('exp' => $nbMagic['gain_exp'], 'mp' => (0-(int)$nbMagic['need_mp'])));
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
			$info['exp'] = $nbMagic['gain_exp'];
			$resultVo = Happyfish_Magic_Bll_FormatVo::resultVo($info);
		}
		catch (Exception $e) {
            info_log($uid.'[Happyfish_Magic_Bll_Magician]-[transShape]:'.$e->getMessage(), 'err-Magician-catched');
            info_log($uid.'[Happyfish_Magic_Bll_Magician]-[transShape]:'.$e->getTraceAsString(), 'err-Magician-catched');
            return false;
		}
		
		//send message
		$aryToMe = $aryToTarget = array();
		$nbItem = Happyfish_Magic_Bll_Cache_NbBasicInfo::getNbItem($nbMagic['gain_item']);
		//to myself message
		$aryToMe['uid'] = (string)$uid;
		$aryToMe['actor'] = (string)$uid;
		$aryToMe['target'] = (string)$targetUid;
		$aryToMe['template'] = 7;
		$aryToMe['properties'] = array('target'=>$rowTarUser['name'], 'animal_name'=>$nbMagic['name'], 'num'=>1, 'item_name'=>$nbItem['name']);
		$aryToMe['create_time'] = $now;
		//to target message
		$aryToTarget['uid'] = (string)$targetUid;
		$aryToTarget['actor'] = (string)$uid;
		$aryToTarget['target'] = (string)$targetUid;
		$aryToTarget['template'] = 3;
		$aryToTarget['properties'] = array('actor'=>$rowUser['name'], 'animal_name'=>$nbMagic['name'], 'num'=>1, 'item_name'=>$nbItem['name']);
		$aryToTarget['create_time'] = $now;
		$aryMessage = array($aryToMe, $aryToTarget);
		Happyfish_Magic_Bll_Message::addUserMessage($aryMessage);
		
		return $resultVo;
	}
	
	/**
	 * combine buildings
	 *
	 * @param integer $uid
	 * @param integer $bid
	 * @return mixed array / false / -1 ERR0308(building not exist) / -2 ERR0309(level not enough) / -3 ERR0304(crystal not enough) / -4 ERR0310(building not enough) / -5 ERR0310(item not enough) 
	 */
	public static function combineBuild($uid, $bid)
	{
		$dalBuild = Happyfish_Magic_Dal_Building::getDefaultInstance();
		
		//is building exist
		$nbBuilding = Happyfish_Magic_Bll_Cache_NbBasicInfo::getNbBuilding($bid);
		if (empty($nbBuilding)) {
			return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0308')));
		}
		
		//is level allow
		$rowUser = Happyfish_Magic_Bll_User::getUserGameInfo($uid);
		if ($rowUser['level'] < $nbBuilding['limit_user_level']) {
			return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0309')));
		}
		
		//is crystal enough
		if ($rowUser['red'] < $nbBuilding['red'] || $rowUser['blue'] < $nbBuilding['blue'] || $rowUser['green'] < $nbBuilding['green'] || $rowUser['money'] < $nbBuilding['money']) {
			return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0304')));
		}
		
		//is need building exist and in bag
		if ($nbBuilding['need_building']) {
			$needDecor = '';
			if (3 == $nbBuilding['type']) {
				$dalFloor = Happyfish_Magic_Dal_Floors::getDefaultInstance();
				$rowNeedBuild = $dalFloor->getUserFloorInBag($uid, $nbBuilding['need_building']);
				if (!empty($rowNeedBuild) && $rowNeedBuild['quantity']) {
					$needDecor = $rowNeedBuild['uid'] . '_' . $rowNeedBuild['floor_id'];
				}
			}
			else if (4 == $nbBuilding['type']) {
				$dalWall = Happyfish_Magic_Dal_Walls::getDefaultInstance();
				$rowNeedBuild = $dalWall->getUserWallInBag($uid, $nbBuilding['need_building']);
				if (!empty($rowNeedBuild) && $rowNeedBuild['quantity']) {
					$needDecor = $rowNeedBuild['uid'] . '_' . $rowNeedBuild['wall_id'];
				}
			}
			else {
				$rowNeedBuild = $dalBuild->getUserBuildingInBagByBid($uid, $nbBuilding['need_building']);
				$needDecor = empty($rowNeedBuild) ? '' : $rowNeedBuild['id'];
			}
			if (empty($needDecor)) {
				return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0310')));
			}
		}
		
		//is item enough
		if ($nbBuilding['need_item'] && $nbBuilding['need_item_count']) {
			$dalItem = Happyfish_Magic_Dal_Item::getDefaultInstance();
			$rowItem = $dalItem->getUserItem($uid, $nbBuilding['need_item']);
			if (empty($rowItem) || $rowItem['item_count'] < $nbBuilding['need_item_count']) {
				return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0310')));
			}
		}
		
		
		$wdb = $dalBuild->getWriter();
		$now = time();
		try {
			//begin transaction
    		$wdb->beginTransaction();
			
    		//update used building 
    		if ($nbBuilding['need_building']) {
	    		if (3 == $nbBuilding['type']) {
					$dalFloor = Happyfish_Magic_Dal_Floors::getDefaultInstance();
					$dalFloor->updateUserFloorInBagByField($uid, $nbBuilding['need_building'], 'quantity', -1);
				}
				else if (4 == $nbBuilding['type']) {
					$dalWall = Happyfish_Magic_Dal_Walls::getDefaultInstance();
					$dalWall->updateUserWallInBagByField($uid, $nbBuilding['need_building'], 'quantity', -1);
				}
				else {
					$dalBuild->delete($needDecor, $uid);
				}
    		}
    		
    		//update used item
    		if ($nbBuilding['need_item'] && $nbBuilding['need_item_count']) {
    			$dalItem->updateUserItemByField($uid, $nbBuilding['need_item'], 'item_count', (0-(int)$nbBuilding['need_item_count']));
    		}
    		
    		//update used crystal
			$aryParam = array();
			if ($nbBuilding['red']) {
				$aryParam['red'] = 0 - (int)$nbBuilding['red'];
			}
			if ($nbBuilding['blue']) {
				$aryParam['blue'] = 0 - (int)$nbBuilding['blue'];
			}
			if ($nbBuilding['green']) {
				$aryParam['green'] = 0 - (int)$nbBuilding['green'];
			}
			if ($nbBuilding['money']) {
				$aryParam['money'] = 0 - (int)$nbBuilding['money'];
				//insert money change log
				$dalLog = Happyfish_Magic_Dal_MoneyLog::getDefaultInstance();
				$dalLog->insert(array('uid'=>$uid, 'money'=>(0 -(int)$nbBuilding['money']), 'order_id'=>-52, 'reference_id'=>$bid, 'create_time'=>$now));
			}
			if ($aryParam) {
				$dalUser = Happyfish_Magic_Dal_User::getDefaultInstance();
				$dalUser->updateUserByMultipleField($uid, $aryParam);
				Happyfish_Magic_Bll_Cache_User::clearAppUser($uid);
			}
    		
    		//create new building 
    		$info = array();
    		$info['uid'] = $uid;
    		$info['building_id'] = $bid;
    		$info['building_type'] = $nbBuilding['type'];
    		$info['effect_mp'] = $nbBuilding['effect_mp'];
    		$info['create_time'] = $now;
    		$newDecorId = $dalBuild->insert($info);
    		
			$wdb->commit();
			return $newDecorId;
		}
		catch (Exception $e) {
			$wdb->rollBack();
            info_log($uid.'[Happyfish_Magic_Bll_Magician]-[combineBuild]:'.$e->getMessage(), 'err-Magician-catched');
            info_log($uid.'[Happyfish_Magic_Bll_Magician]-[combineBuild]:'.$e->getTraceAsString(), 'err-Magician-catched');
            return false;
		}
	}
	
	
	/**
	 * user magic level up
	 *
	 * @param integer $uid
	 * @param integer $magicType: 1-火 2-水 3-木 8-人型变 9-装饰变
	 * @return boolean
	 */
	public static function magicLevelUp($uid, $magicType) 
	{
		$dalUser = Happyfish_Magic_Dal_User::getDefaultInstance();
		$rowUser = $dalUser->getUser($uid);
    	if (!$rowUser) {
    	    return false;
    	}
    	
    	$wdb = $dalUser->getWriter();
        $now = time();
		try {
    		//begin transaction
    		$wdb->beginTransaction();
    		
    		$dalMagLev = Happyfish_Magic_Dal_MagicLevel::getDefaultInstance();
    		$rowMagLev = $dalMagLev->getInfo($uid, $magicType);
    		if (empty($rowMagLev)) {
    			throw new Exception('magic level not exist');
    		}
    		$nextLevel = $rowMagLev['level'] + 1;
			$nbInfo = Happyfish_Magic_Bll_Cache_NbBasicInfo::getNbLevel($rowUser['level']);
    		//if ($nextLevel > $nbInfo['limit_magic_lev']) {
    		//	throw new Exception('user limited level');
    		//}
    		$spentR = 1;
    		$spentB = 2;
    		$spentG = 3;
			if ($rowUser['red'] < $spentR || $rowUser['blue'] < $spentB || $rowUser['green'] < $spentG) {
    			throw new Exception('not enough diamond');
    		}
    		
    		//update user 
    		$aryUsrParam = array('red'=>(0-$spentR), 'blue'=>(0-$spentB), 'green'=>(0-$spentG));
    		$dalUser->updateUserByMultipleField($uid, $aryUsrParam);
    		//update magic level
    		$aryParam = array('level'=>1, 'spent_red'=>$spentR, 'spent_blue'=>$spentB, 'spent_green'=>$spentG);
    		$dalMagLev->updateUserMagicLevelByMultipleField($uid, $magicType, $aryParam);
    		
			/*
    		$lstMagLev = $dalMagLev->lstUserMagicLevel($uid);
    		$sumLev = 0;
    		foreach ($lstMagLev as $data) {
    			$sumLev += $data['level'];
    		}*/
    		
    		$wdb->commit();
    	}
		catch (Exception $e) {
			$wdb->rollBack();
            info_log($uid.'[Happyfish_Magic_Bll_User]-[magicLevelUp]:'.$e->getMessage(), 'err-Magician-catched');
            info_log($uid.'[Happyfish_Magic_Bll_User]-[magicLevelUp]:'.$e->getTraceAsString(), 'err-Magician-catched');
            return false;
		}
		
		return true;
    	
	}
	
}