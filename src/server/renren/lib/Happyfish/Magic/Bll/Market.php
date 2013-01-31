<?php

/**
 * logic's Operation
 *
 * @package    Happyfish_Magic_Bll
 * @copyright  Copyright (c) 
 * @create      2010/10/14    zhangxin
 */
class Happyfish_Magic_Bll_Market
{
	//const 
	const OUTPUT_ERRCODE = -1;
	const MARKET_SIZE_DEFAULT = 500;
	const MARKET_SIZE_ENLARGE = 20000;
	const MARKET_ENLARGE_TIME = 604800;//86400*7; seven days
	const MARKET_ENLARGE_COST = 2;//2 money

	/**
	 * get market info and deal market enlarge time out
	 *
	 * @param integer $uid
	 * @return array
	 */
	public static function checkMarketStatus($uid)
	{
		
		$now = time();
		$dalMarket = Happyfish_Magic_Dal_Market::getDefaultInstance();
		$rowMarket = $dalMarket->getUserMarket($uid);
		//init market
		if (empty($rowMarket)) {
			$rowUser = Happyfish_Magic_Bll_User::getUserGameInfo($uid);
			$majorMagic = $rowUser['major_magic'];
			$rowMarket = array();
			$rowMarket['uid'] = $uid;
			$rowMarket['red'] = 0;
			$rowMarket['blue'] = 0;
			$rowMarket['green'] = 0;
			$rowMarket['type'] = $majorMagic;
			$rowMarket['is_enlarged'] = 0;
			$rowMarket['enlarged_time'] = 0;
			$rowMarket['create_time'] = $now;
			$dalMarket->insert($rowMarket);
			return $rowMarket;
		}
		
		//normal market or enlarged in time market
		if ( empty($rowMarket['enlarged_time']) || ($now - $rowMarket['enlarged_time'] < self::MARKET_ENLARGE_TIME) ) {
			return $rowMarket;
		}
		
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
		
		$wdb = $dalMarket->getWriter();
		try {
			//begin transaction
    		$wdb->beginTransaction();
			
    		//set market to default normal status
    		$dalMarket->update(array('is_enlarged'=>0, 'enlarged_time'=>0), $uid);
    		
    		//move overflow market crystal back to user's 
    		$overCrystal = (int)($rowMarket[$crystalType] - self::MARKET_SIZE_DEFAULT);
    		if ($overCrystal) {
				//market info update
				$dalMarket->updateUserMarketByField($uid, $crystalType, 0-$overCrystal);
				
				//user info update
				$dalUser = Happyfish_Magic_Dal_User::getDefaultInstance();
				$dalUser->updateUserByField($uid, $crystalType, $overCrystal);
				Happyfish_Magic_Bll_Cache_User::clearAppUser($uid);
    		}
    		$rowMarket[$crystalType] = $rowMarket[$crystalType]-$overCrystal;
    		$rowMarket['is_enlarged'] = 0;
			$rowMarket['enlarged_time'] = 0;

			$wdb->commit();
			
			//market change log
			if ($overCrystal) {
				$dalMgMarket = Happyfish_Magic_Dal_Mongo_UserMarketDetail::getDefaultInstance();
				$logInfo = array();
				$logInfo['uid'] = (string)$uid;
				$logInfo['actor'] = (string)$uid;
				$logInfo['crystal'] = (int)(0-$overCrystal);
				$logInfo['type'] = $majorMagic;
				$logInfo['create_time'] = $now;
				$dalMgMarket->insert($logInfo);
			}

			return $rowMarket;
		}
		catch (Exception $e) {
			$wdb->rollBack();
            info_log($uid.'[Happyfish_Magic_Bll_Market]-[checkMarketStatus]:'.$e->getMessage(), 'err-Market-catched');
            info_log($uid.'[Happyfish_Magic_Bll_Market]-[checkMarketStatus]:'.$e->getTraceAsString(), 'err-Market-catched');
            return false;
		}
	}
	
	/**
	 * add crystal to market
	 *
	 * @param integer $uid
	 * @param integer $fillCrystal count
	 * @return mixed array / false / -1 ERR0402(crystal empty) / -2 ERR0304(crystal not enough) / -3 ERR0403(market is full) / -4 ERR0404(enlarged market timeout) / 
	 */
	public static function fillInMarket($uid, $fillCrystal)
	{
		
		$rowUser = Happyfish_Magic_Bll_User::getUserGameInfo($uid);
		$majorMagic = $rowUser['major_magic'];
		//is crystal empty
		if (empty($majorMagic) || empty($fillCrystal)) {
			return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0402')));
		}
		
		//is crystal enough
		$ownCrystal = 0;
		$crystalType = '';
		if (1 == $majorMagic) {
			$ownCrystal = $rowUser['red'];
			$crystalType = 'red';
		}
		else if (2 == $majorMagic) {
			$ownCrystal = $rowUser['blue'];
			$crystalType = 'blue';
		}
		else {
			$ownCrystal = $rowUser['green'];
			$crystalType = 'green';
		}
		if ($ownCrystal < $fillCrystal) {
			return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0304')));
		}
		
    	$now = time();
		$dalMarket = Happyfish_Magic_Dal_Market::getDefaultInstance();
		$rowMarket = $dalMarket->getUserMarket($uid);
		if (!empty($rowMarket)) {
			//normal market full
			if (empty($rowMarket['enlarged_time']) && (int)($rowMarket[$crystalType] + $fillCrystal) > self::MARKET_SIZE_DEFAULT) {
				return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0403')));
			}
			//enlarged market time out
			if ($rowMarket['enlarged_time'] && (int)($now-$rowMarket['enlarged_time']) >= self::MARKET_ENLARGE_TIME) {
				return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0404')));
			}
			//enlarged market full
			if ($rowMarket['enlarged_time'] && (int)($rowMarket[$crystalType] + $fillCrystal) > self::MARKET_SIZE_ENLARGE) {
				return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0403')));
			}
		}

    	$wdb = $dalMarket->getWriter();
    	
		try {
			//begin transaction
    		$wdb->beginTransaction();
    		
    		//market info update
			if (empty($rowMarket)) {
				$info = array();
				$info['uid'] = $uid;
				$info[$crystalType] = $fillCrystal;
				$info['type'] = $majorMagic;
				$info['create_time'] = $now;
				$dalMarket->insert($info);
			}
			else {
				$dalMarket->updateUserMarketByField($uid, $crystalType, $fillCrystal);
			}
			
			//user info update
			$dalUser = Happyfish_Magic_Dal_User::getDefaultInstance();
			$dalUser->updateUserByField($uid, $crystalType, (0-(int)$fillCrystal));
			Happyfish_Magic_Bll_Cache_User::clearAppUser($uid);
			
			$wdb->commit();
			
			//market change log
			$dalMgMarket = Happyfish_Magic_Dal_Mongo_UserMarketDetail::getDefaultInstance();
			$logInfo = array();
			$logInfo['uid'] = (string)$uid;
			$logInfo['actor'] = (string)$uid;
			$logInfo['crystal'] = (int)$fillCrystal;
			$logInfo['type'] = $majorMagic;
			$logInfo['create_time'] = $now;
			$dalMgMarket->insert($logInfo);

			return true;
		}
		catch (Exception $e) {
			$wdb->rollBack();
            info_log($uid.'[Happyfish_Magic_Bll_Market]-[fillInMarket]:'.$e->getMessage(), 'err-Market-catched');
            info_log($uid.'[Happyfish_Magic_Bll_Market]-[fillInMarket]:'.$e->getTraceAsString(), 'err-Market-catched');
            return false;
		}
	}
	
	/**
	 * take back crystal from market
	 *
	 * @param integer $uid
	 * @param integer $backNum
	 * @return mixed array / false / -1 ERR0405(market not exist) / -2 ERR0406(back crystal empty) / -3 ERR0407(no more crystal to take back) /
	 */
	public static function takeBackMarket($uid, $backNum)
	{
		
		$dalMarket = Happyfish_Magic_Dal_Market::getDefaultInstance();
		$rowMarket = $dalMarket->getUserMarket($uid);
		//market not exist
		if (empty($rowMarket) || empty($rowMarket['type'])) {
			return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0405')));
		}
		
		$majorMagic = $rowMarket['type'];
		//is crystal empty
		if (empty($majorMagic) || empty($backNum)) {
			return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0406')));
		}
		
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
		
		//no more crystal to take back
		if (empty($rowMarket[$crystalType]) || $rowMarket[$crystalType]<$backNum) {
			return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0407')));
		}

		$now = time();
		$wdb = $dalMarket->getWriter();
    	
		try {
			//begin transaction
    		$wdb->beginTransaction();
    		
			$dalMarket->updateUserMarketByField($uid, $crystalType, (0-(int)$backNum));
			
			//user info update
			$dalUser = Happyfish_Magic_Dal_User::getDefaultInstance();
			$dalUser->updateUserByField($uid, $crystalType, $backNum);
			Happyfish_Magic_Bll_Cache_User::clearAppUser($uid);
			
			$wdb->commit();
			
			//market change log
			$dalMgMarket = Happyfish_Magic_Dal_Mongo_UserMarketDetail::getDefaultInstance();
			$logInfo = array();
			$logInfo['uid'] = (string)$uid;
			$logInfo['actor'] = (string)$uid;
			$logInfo['crystal'] = (int)(0-(int)$backNum);
			$logInfo['type'] = $majorMagic;
			$logInfo['create_time'] = $now;
			$dalMgMarket->insert($logInfo);

			return (0-(int)$backNum);
		}
		catch (Exception $e) {
			$wdb->rollBack();
            info_log($uid.'[Happyfish_Magic_Bll_Market]-[takeBackMarket]:'.$e->getMessage(), 'err-Market-catched');
            info_log($uid.'[Happyfish_Magic_Bll_Market]-[takeBackMarket]:'.$e->getTraceAsString(), 'err-Market-catched');
            return false;
		}
	}
	
	/**
	 * enlarge market
	 *
	 * @param integer $uid
	 * @return mixed array / false / -1 ERR0410(money not enough) / -2 ERR0408(already in enlarge time) 
	 */
	public static function enlargeMarket($uid)
	{
		//user info
		$rowUser = Happyfish_Magic_Bll_User::getUserGameInfo($uid);
		if ($rowUser['money'] < self::MARKET_ENLARGE_COST) {
			return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0410')));
		}
		
		$now = time();
		$dalMarket = Happyfish_Magic_Dal_Market::getDefaultInstance();
		$wdb = $dalMarket->getWriter();
		try {
			//begin transaction
    		$wdb->beginTransaction();
			
			$rowMarket = $dalMarket->getUserMarket($uid);
			if (!empty($rowMarket)) {
				if ($now - $rowMarket['enlarged_time'] < self::MARKET_ENLARGE_TIME) {
					$wdb->rollBack();
					return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0408')));
				}
				//update enlarge time
				$dalMarket->update(array('is_enlarged'=>1, 'enlarged_time'=>$now), $uid);
			}
			else {
				$info = array();
				$info['uid'] = $uid;
				$rowMarket['type'] = $rowUser['major_magic'];
				$info['is_enlarged'] = 1;
				$info['enlarged_time'] = $now;
				$info['create_time'] = $now;
				$dalMarket->insert($info);
			}
		
			//user info update
			$dalUser = Happyfish_Magic_Dal_User::getDefaultInstance();
			$dalUser->updateUserByField($uid, 'money', (0-(int)self::MARKET_ENLARGE_COST));
			Happyfish_Magic_Bll_Cache_User::clearAppUser($uid);

			$wdb->commit();
			
			//insert money change log
			$dalLog = Happyfish_Magic_Dal_MoneyLog::getDefaultInstance();
			$dalLog->insert(array('uid'=>$uid, 'money'=>(0-(int)self::MARKET_ENLARGE_COST), 'order_id'=>-53, 'create_time'=>$now));
			
			return $now;
		}
		catch (Exception $e) {
			$wdb->rollBack();
            info_log($uid.'[Happyfish_Magic_Bll_Market]-[enlargeMarket]:'.$e->getMessage(), 'err-Market-catched');
            info_log($uid.'[Happyfish_Magic_Bll_Market]-[enlargeMarket]:'.$e->getTraceAsString(), 'err-Market-catched');
            return false;
		}
	}
	
	
	/**
	 * market exchange crystal
	 *
	 * @param integer $marketOwner - market owner
	 * @param integer $actorUid - 
	 * @param integer $num 
	 * @return mixed array / false / -1 ERR0409(crystal empty) / -2 ERR0304(crystal not enough) / -3 ERR0411(can't change same type) / -4 ERR0412(market is empty) / 
	 */
	public static function exchange($marketOwner, $actorUid, $num)
	{
		//is friend check TODO::
		$actUser = Happyfish_Magic_Bll_User::getUserGameInfo($actorUid);
		$majorMagic = $actUser['major_magic'];
		//is crystal empty
		if (empty($majorMagic) || empty($num)) {
			return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0409')));
		}
		
		//is crystal enough
		$crystalFrom = 0;
		$crystalTypeFrom = ''; //from crystal
		$crystalNameFrom = '';
		if (1 == $majorMagic) {
			$crystalFrom = $actUser['red'];
			$crystalTypeFrom = 'red';
			$crystalNameFrom = Happyfish_Magic_Bll_Language_Local::getText('TXT2011');
		}
		else if (2 == $majorMagic) {
			$crystalFrom = $actUser['blue'];
			$crystalTypeFrom = 'blue';
			$crystalNameFrom = Happyfish_Magic_Bll_Language_Local::getText('TXT2012');
		}
		else {
			$crystalFrom = $actUser['green'];
			$crystalTypeFrom = 'green';
			$crystalNameFrom = Happyfish_Magic_Bll_Language_Local::getText('TXT2013');
		}
		if ($crystalFrom < $num) {
			return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0304')));
		}
		
		$dalMarket = Happyfish_Magic_Dal_Market::getDefaultInstance();
		$rowMarket = $dalMarket->getUserMarket($marketOwner);
		//same type can not exchange
		if (empty($rowMarket['type']) || $rowMarket['type'] == $majorMagic) {
			return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0411')));
		}

		$crystalTypeTo = ''; //to crystal
		$crystalNameTo = '';
		if (1 == $rowMarket['type']) {
			$crystalTypeTo = 'red';
			$crystalNameTo = Happyfish_Magic_Bll_Language_Local::getText('TXT2011');
		}
		else if (2 == $rowMarket['type']) {
			$crystalTypeTo = 'blue';
			$crystalNameTo = Happyfish_Magic_Bll_Language_Local::getText('TXT2012');
		}
		else {
			$crystalTypeTo = 'green';
			$crystalNameTo = Happyfish_Magic_Bll_Language_Local::getText('TXT2013');
		}
		//is market empty
		if ($rowMarket[$crystalTypeTo] < $num) {
			return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0412')));
		}

		$now = time();
    	$wdb = $dalMarket->getWriter();
    	
		try {
			//begin transaction
    		$wdb->beginTransaction();
    		
    		//market info update
    		$aryParamM = array();
    		$aryParamM[$crystalTypeTo] = (0-(int)$num);
			$aryParamM[$crystalTypeFrom] = $num;
			$dalMarket->updateUserMarketByMultipleField($marketOwner, $aryParamM);
	
			//actor user info update
			$dalUser = Happyfish_Magic_Dal_User::getDefaultInstance();
			$aryParam = array();
			$aryParam[$crystalTypeTo] = $num;
			$aryParam[$crystalTypeFrom] = (0-(int)$num);
			$dalUser->updateUserByMultipleField($actorUid, $aryParam);
			Happyfish_Magic_Bll_Cache_User::clearAppUser($actorUid);
			
			$wdb->commit();
		}
		catch (Exception $e) {
			$wdb->rollBack();
            info_log($actorUid . ':' . $marketOwner.'[Happyfish_Magic_Bll_Market]-[exchange]:'.$e->getMessage(), 'err-Market-catched');
            info_log($actorUid . ':' . $marketOwner.'[Happyfish_Magic_Bll_Market]-[exchange]:'.$e->getTraceAsString(), 'err-Market-catched');
            return false;
		}
		
		//market change log
		$dalMgMarket = Happyfish_Magic_Dal_Mongo_UserMarketDetail::getDefaultInstance();
		$logInfo = array();
		$logInfo['uid'] = (string)$marketOwner;
		$logInfo['actor'] = (string)$actorUid;
		$logInfo['crystal'] = (int)(0-(int)$num);
		$logInfo['type'] = $majorMagic;
		$logInfo['create_time'] = $now;
		$dalMgMarket->insert($logInfo);
		
		//send message
		$aryToTarget = array();
		$aryToTarget['uid'] = (string)$marketOwner;
		$aryToTarget['actor'] = (string)$actorUid;
		$aryToTarget['target'] = (string)$marketOwner;
		$aryToTarget['template'] = 4;
		$aryToTarget['properties'] = array('actor'=>$actUser['name'], 'num1'=>$num, 'item_name1'=>$crystalNameFrom, 'item_name2'=>$crystalNameTo);
		$aryToTarget['create_time'] = $now;
		$aryMessage = array($aryToTarget);
		Happyfish_Magic_Bll_Message::addUserMessage($aryMessage);
		return true;
	}
	
	
	/**
	 * gain market exchanged crystal
	 *
	 * @param integer $uid
	 * @return mixed array / false / -1 ERR0405(market not exist) / -2 ERR0407(nothing to gain) / 
	 */
	public static function gainMarketCrystal($uid)
	{
		
		$dalMarket = Happyfish_Magic_Dal_Market::getDefaultInstance();
		$rowMarket = $dalMarket->getUserMarket($uid);

		//is market not open
		if (empty($rowMarket) || empty($rowMarket['type'])) {
			return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0405')));
		}
		$crystalType1 = '';
		$crystalType2 = '';
		if (1 == $rowMarket['type']) {
			$crystalType1 = 'blue';
			$crystalType2 = 'green';

		}
		else if (2 == $rowMarket['type']) {
			$crystalType1 = 'red';
			$crystalType2 = 'green';
		}
		else {
			$crystalType1 = 'red';
			$crystalType2 = 'blue';
		}
		//is market nothing to gain
		if (empty($rowMarket[$crystalType1]) && empty($rowMarket[$crystalType2])) {
			return array('result' => array('status'=>self::OUTPUT_ERRCODE, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0407')));
		}

		//$now = time();
		$wdb = $dalMarket->getWriter();
    	
		try {
			//begin transaction
    		$wdb->beginTransaction();
    		
			//market info update
    		$aryParamM = array();
    		if ($rowMarket[$crystalType1]) {
    			$aryParamM[$crystalType1] = (0-(int)$rowMarket[$crystalType1]);
    		}
    		if ($rowMarket[$crystalType2]) {
    			$aryParamM[$crystalType2] = (0-(int)$rowMarket[$crystalType2]);
    		}
			$dalMarket->updateUserMarketByMultipleField($uid, $aryParamM);
	
			//user info update
			$dalUser = Happyfish_Magic_Dal_User::getDefaultInstance();
			$aryParam = array();
			if ($rowMarket[$crystalType1]) {
				$aryParam[$crystalType1] = $rowMarket[$crystalType1];
			}
			if ($rowMarket[$crystalType2]) {
    			$aryParam[$crystalType2] = $rowMarket[$crystalType2];
    		}
			$dalUser->updateUserByMultipleField($uid, $aryParam);
			Happyfish_Magic_Bll_Cache_User::clearAppUser($uid);
			
			$wdb->commit();
		
			return array($crystalType1 => $rowMarket[$crystalType1], $crystalType2 => $rowMarket[$crystalType2]);
		}
		catch (Exception $e) {
			$wdb->rollBack();
            info_log($uid.'[Happyfish_Magic_Bll_Market]-[gainMarketCrystal]:'.$e->getMessage(), 'err-Market-catched');
            info_log($uid.'[Happyfish_Magic_Bll_Market]-[gainMarketCrystal]:'.$e->getTraceAsString(), 'err-Market-catched');
            return false;
		}
	}
	
}