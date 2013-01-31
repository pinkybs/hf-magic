<?php

class Deal extends SingleBase {
	
	/**
	 * add new deal
	 *
	 * @param integer $crystal
	 * @return array
	 */
	public function addDeal($num)
	{
		$result = array('status' => -1);
		
		$role = Role::create($this->role_id);
		
		//check user major magic
		$crystalType = 'coin';
		
		//check user crystal num
		$userCrystal = $role->get($crystalType);
		if ( $userCrystal < $num ) {
			$result['content'] = 'crystal_not_enough';
			return $result;
		}
		
		//check user deal level，max num
		$userDealLevel = $role->get('deal_level');
		$basic_model = new Basic_Model();
		$basicDealLevel = $basic_model->getDealLevel();
		$basicDealArray = array();
		foreach ( $basicDealLevel as $basicDeal ) {
			$basicDealArray[$basicDeal[0]['id']] = $basicDeal[0];
		}
		$userDealInfo = $basicDealArray[$userDealLevel];

		if ( $userDealInfo['num'] < $num ) {
			$result['content'] = 'deal_num_error';
			return $result;
		}
		
		$deal_model = Deal_Model::instance($this->role_id);
		$userDeal = $deal_model->getUserDeal();
		
		//add deal start
		if ( empty($userDeal) ) {
			$newDeal = array('role_id' => $this->role_id,
							 $crystalType => $num,
							 'time' => PEAR::getStaticProperty('_APP', 'timestamp'));
			$deal_model->insertDeal($newDeal);
			$role->increment($crystalType, -$num);
		}
		else {
			$crystalChange = $num - $userDeal[$crystalType];
			$newDeal = array($crystalType => $num);
			$deal_model->updateUserDeal($newDeal);
			$role->increment($crystalType, -$crystalChange);
		}

		GameEvent::processRoleEvent(EventConditionType::PUT_CRYSTAL_FOR_CHANGE, 0, $num);
		
		$result['status'] = 1;
		
		return $result;
	}
	
	/**
	 * do deal
	 *
	 * @param integer $ownerUid
	 * @param integer $num
	 * @param integer $dealType
	 * @return array
	 */
	public function doDeal($ownerUid, $num, $dealType)
	{
		$result = array('status' => -1);
		
		$role = Role::create($this->role_id);
		
		//check user major magic
		$userCrystalType = 'coin';
		
		//check user crystal num
		$userCrystal = $role->get($userCrystalType);
		if ( $userCrystal < $num ) {
			$result['content'] = 'crystal_not_enough';
			return $result;
		}
	
		$userCrystalType = 'coin';
		//check owner user deal info
		$deal_model = Deal_Model::instance($ownerUid);
		$ownerDeal = $deal_model->getUserDeal();
		if ( empty($ownerDeal) || $ownerDeal[$dealCrystalType] < $num ) {
			$result['content'] = 'deal_not_exist';
			return $result;
		}
		
		if ( $dealCrystalType == $userCrystalType ) {
			$result['content'] = 'deal_type_undifferent';
			return $result;
		}
		
		//do deal start
		$remainNum = $ownerDeal[$dealCrystalType] - $num;
		$role->increment($userCrystalType, -$num);
		$role->increment($dealCrystalType, $num);
		
		//update user deal
		$newDeal = array($dealCrystalType => $remainNum);
		$deal_model->updateUserDeal($newDeal);
		
		//add deal log
		$newDealLog = array('actor' => $this->role_id,
							'role_id' => $ownerUid,
							'type' => $dealType,
							'num' => $num,
							'time' => PEAR::getStaticProperty('_APP', 'timestamp'));
		$deal_log_model = Deal_Log_Model::instance($this->role_id);
		$deal_log_model->insertDealLog($newDealLog);
		
		GameEvent::processRoleEvent(EventConditionType::EXCHANGE_CRYSTAL, 0, $num);
		GameEvent::processRoleEvent(EventConditionType::EXCHANGE_CRYSTAL, 0, $num, $ownerUid);
		
		$result['status'] = 1;
		
		return $result;
	}

	/**
	 * get deal
	 *
	 * @return array
	 */
	public function getDeal()
	{
		$result = array('status' => -1);
		
		$role = Role::create($this->role_id);
		$deal_log_model = Deal_Log_Model::instance($this->role_id);
		//get user deal log list
		$userDealLog = $deal_log_model->getUserTargetNewDeal();
		if ( empty($userDealLog) ) {
			$result['content'] = 'deal_log_not_exist';
			return $result;
		}

		//check deal crystal type
		foreach ( $userDealLog as $dealLog ) {
			$dealLog = $dealLog[0];
			$userCrystalType = 'coin';
			//add user crystal
			$role->increment($crystalType, $dealLog['num']);
			//update user deal log
			$deal_log_model->updateDealLogById(array('status' => 0), $dealLog['id']);
		}
		
		$result['status'] = 1;
		
		return $result;
	}
	
	/**
	 * upgrade deal
	 *
	 * @return array
	 */
	public function upgradeDeal()
	{
		$result = array('status' => -1);
		
		$role = Role::create($this->role_id);
		$userDealLevel = $role->get('deal_level');
		
		//get basic deal level info 
		$basic_model = new Basic_Model();
		$basicDealLevel = $basic_model->getDealLevel();
		$basicDealArray = array();
		foreach ( $basicDealLevel as $basicDeal ) {
			$basicDealArray[$basicDeal[0]['id']] = $basicDeal[0];
		}
		$userDealInfo = $basicDealArray[$userDealLevel];
		$nextDealLevel = $userDealLevel + 1;
		if ( !isset($userDealInfo) ) {
			$result['content'] = 'deal_level_not_exist';
			return $result;
		}
		$nextLevelInfo = $userDealInfo;
		
		//check user gmoney
		$userGmoney = $role->get('gmoney');
		if ( $userGmoney < $nextLevelInfo['price'] ) {
			$result['content'] = 'money_not_enough';
			return $result;
		}
		
		//start upgrade 
		$role_model = Role_Model::instance($this->role_id);
		//update user deal level,gmoney
		$role_model->update('deal_level', $nextDealLevel);
		$role->increment('gmoney', -$nextLevelInfo['price']);
		
		$result['status'] = 1;
		
		return $result;
	}
	
	/**
	 * read deal
	 *
	 * @return array
	 */
	public function readDeal()
	{
		$result = $this->getSwitchVo($this->role_id);
		return $result;
	}
	
	public function getSwitchVo()
	{
		$switchList = '';
		$crystals = '';
		$currentPutNum = '';
		
		$deal_log_model = Deal_Log_Model::instance($this->role_id);
		//get user deal log list
		$userDealLog = $deal_log_model->getUserTargetNewDeal();
		$switchList = array();
		$coin = 0;
		if ( !empty($userDealLog) ) {
			foreach ( $userDealLog as $dealLog ) {
				$dealLog = $dealLog[0];
				$dealLog['uid'] = $dealLog['actor'];
				$dealLog['crystalType'] = $dealLog['type'];

				$coin = $coin + $dealLog['num'];
				
				unset($dealLog['id']);
				unset($dealLog['actor']);
				unset($dealLog['role_id']);
				unset($dealLog['type']);
				
				$dealLog['uname'] = '魔法1';
				$switchList[] = $dealLog;
			}
		}
		
		$crystals = array('coin' => $coin);
		
		$deal_model = Deal_Model::instance($this->role_id);
		$ownerDeal = $deal_model->getUserDeal();
		if ( empty($ownerDeal) ) {
			$currentPutNum = 0;
		}
		else {
			$role = Role::create($this->role_id);
			$crystalType = 'coin';
			$currentPutNum = $ownerDeal[$crystalType];
		}
		
		return array('switchList' => $switchList,
					 'crystals' => $crystals,
					 'currentPutNum' => $currentPutNum);
	}
	
	
	
}