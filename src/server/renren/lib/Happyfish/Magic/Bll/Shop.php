<?php

/**
 * logic's Operation
 *
 * @package    Happyfish_Magic_Bll
 * @copyright  Copyright (c) 
 * @create      2010/10/22    zhangxin
 */
class Happyfish_Magic_Bll_Shop
{
	//const 
	const OUTPUT_ERRCODE = -1;
	const EAT_RECOVERMP_FOODLIMIT = 5;

	/**
	 * buy card
	 *
	 * @param integer $uid
	 * @param integer $cid
	 * @param integer $num
	 * @return mixed array / false / -1 ERR0502(card not exist ) / -2 ERR0304(crystal not enough) / 
	 */
	public static function buyCard($uid, $cid, $num=1)
	{
		//is card exist
		$nbCard = Happyfish_Magic_Bll_Cache_NbBasicInfo::getNbCard($cid);
		if (empty($nbCard) || empty($num)) {
			return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0502')));
		}
		
		//is crystal enough
		$rowUser = Happyfish_Magic_Bll_User::getUserGameInfo($uid);
		$majorMagic = $rowUser['major_magic'];
    	$crystalType = '';
		if (1 == $majorMagic) {
			$crystalType = 'red';
		}
		else if (2 == $majorMagic) {
			$crystalType = 'blue';
		}
		else {
			$crystalType = 'green';
		}
		if ($rowUser[$crystalType]<$nbCard[$crystalType]*$num || $rowUser['money']<$nbCard['money']*$num) {
			return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0304')));
		}
		
		$dalCard = Happyfish_Magic_Dal_Card::getDefaultInstance();
		$now = time();
		$wdb = $dalCard->getWriter();
		try {
			//begin transaction
    		$wdb->beginTransaction();
			
    		//update card
    		$rowUserCard = $dalCard->getUserCard($uid, $cid);
    		if (empty($rowUserCard)) {
    			$dalCard->insert(array('uid'=>$uid, 'cid'=>$cid, 'card_count'=>$num));
    		}
    		else {
    			$dalCard->updateUserCardByField($uid, $cid, 'card_count', $num);
    		}
    		
    		//update user
    		$dalUser = Happyfish_Magic_Dal_User::getDefaultInstance();
			$aryParam = array();
			if ($nbCard[$crystalType]) {
				$aryParam[$crystalType] = 0 - (int)($nbCard[$crystalType]*$num);
			}
			if ($nbCard['money']) {
    			$aryParam['money'] = 0 - (int)($nbCard['money']*$num);
    		}
    		if ($aryParam) {
    			$dalUser->updateUserByMultipleField($uid, $aryParam);
				Happyfish_Magic_Bll_Cache_User::clearAppUser($uid);
    		}

			$wdb->commit();
			
			//refresh user card info
			Happyfish_Magic_Bll_Cache_User::clearUserCard($uid);
			
			//insert money change log
			if (isset($aryParam['money'])) {
				$dalLog = Happyfish_Magic_Dal_MoneyLog::getDefaultInstance();
				$dalLog->insert(array('uid'=>$uid, 'money'=>(0-(int)($nbCard['money']*$num)), 'order_id'=>-54, 'reference_id'=>$cid, 'create_time'=>$now));
			}
			return true;
		}
		catch (Exception $e) {
			$wdb->rollBack();
            info_log($uid.'_'.$cid.'[Happyfish_Magic_Bll_Shop]-[buyCard]:'.$e->getMessage(), 'err-Shop-catched');
            info_log($uid.'_'.$cid.'[Happyfish_Magic_Bll_Shop]-[buyCard]:'.$e->getTraceAsString(), 'err-Shop-catched');
            return false;
		}
	}
	
	
	/**
	 * use card (add mp)
	 *
	 * @param integer $uid
	 * @param integer $cid
	 * @return mixed array / false / -1 ERR0502(card not exist) / -2 ERR0503(haven't got this card) / -3 ERR0504(mp is full) / -4 ERR0505(today can not use more)
	 */
	public static function useCard($uid, $cid)
	{
		//is card exist
		$nbCard = Happyfish_Magic_Bll_Cache_NbBasicInfo::getNbCard($cid);
		if (empty($nbCard)) {
			return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0502')));
		}
		
		//has got at least a card
		$dalCard = Happyfish_Magic_Dal_Card::getDefaultInstance();
		$rowUserCard = $dalCard->getUserCard($uid, $cid);
    	if (empty($rowUserCard) || empty($rowUserCard['card_count'])) {
    		return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0503')));
    	}
    	
    	//has already arrived today's use limit count
    	$rowUser = Happyfish_Magic_Bll_User::getUserGameInfo($uid);
    	//is need to add mp
    	if ($rowUser['mp'] == $rowUser['nbLevInfo']['max_mp']) {
    		return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0504')));
    	}
    	if (1 == $nbCard['type']) {
    		if ($rowUser['mgInfo']['eat_last_time'] && $rowUser['mgInfo']['eat_count']>=self::EAT_RECOVERMP_FOODLIMIT) {
    			$today = date('Y-m-d');
    			$last = date('Y-m-d', $rowUser['mgInfo']['eat_last_time']);
    			if ($last == $today) {
    				return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0505')));
    			}
    		}
    	}
    	
		$now = time();
		$wdb = $dalCard->getWriter();
		try {
			//begin transaction
    		$wdb->beginTransaction();
			
    		//use card
    		$dalCard->updateUserCardByField($uid, $cid, 'card_count', -1);
    		
    		//add mp card
			if (1 == $nbCard['type'] || 2 == $nbCard['type']) {    			
				$dalUser = Happyfish_Magic_Dal_User::getDefaultInstance();
				if ($rowUser['mp'] + (int)$nbCard['effect'] > $rowUser['nbLevInfo']['max_mp']) {
					$dalUser->updateUser(array('mp'=>(int)$rowUser['nbLevInfo']['max_mp']), $uid);
				}
				else {
					$dalUser->updateUserByField($uid, 'mp', (int)$nbCard['effect']);
				}
				Happyfish_Magic_Bll_Cache_User::clearAppUser($uid);
				
				//today allow to use card count -1
				if (1 == $nbCard['type']) {
					$dalMgUser = Happyfish_Magic_Dal_Mongo_UserLoginInfo::getDefaultInstance();
					//never used before
					if ( empty($rowUser['mgInfo']['eat_last_time']) || empty($rowUser['mgInfo']['eat_count']) ) {
						$dalMgUser->update($uid, array('eat_count'=>1, 'eat_last_time'=>$now));
					}
					//used yesterday or before or today
					else if ($rowUser['mgInfo']['eat_last_time']) {
						$today = date('Y-m-d');
    					$last = date('Y-m-d', $rowUser['mgInfo']['eat_last_time']);
    					if ($last == $today) {
    						$dalMgUser->update($uid, array('eat_count'=>(int)($rowUser['mgInfo']['eat_count']+1), 'eat_last_time'=>$now));
    					}
    					else {
    						$dalMgUser->update($uid, array('eat_count'=>1, 'eat_last_time'=>$now));
    					}
					}
				}
    		}
    		
			$wdb->commit();
			
			//refresh user card info
			Happyfish_Magic_Bll_Cache_User::clearUserCard($uid);
			
			return $nbCard['effect'];
		}
		catch (Exception $e) {
			$wdb->rollBack();
            info_log($uid.'_'.$cid.'[Happyfish_Magic_Bll_Shop]-[useCard]:'.$e->getMessage(), 'err-Shop-catched');
            info_log($uid.'_'.$cid.'[Happyfish_Magic_Bll_Shop]-[useCard]:'.$e->getTraceAsString(), 'err-Shop-catched');
            return false;
		}
	}
	
	
}