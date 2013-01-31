<?php

class Item extends SingleBase {
	
	/**
	 * buy item
	 *
	 * @param integer $itemId
	 * @param integer $num
	 * @return array
	 */
	public function buyItem($itemId, $num)
	{
		$result = array('status' => -1);
		
		$basic_model = new Basic_Model();
		$itemBasic = $basic_model->getItemById($itemId);
		if ( empty($itemBasic) || $itemBasic['canbuy'] != 1 ) {
			$result['content'] = 'item_not_can_buy';
			return $result;
		}
		
		$role = Role::create($this->role_id);
		if ( $itemBasic['gem'] > 0 ) {
			$needGmoney = $num * $itemBasic['gem'];
			//check user gem
			$userGmoney = $role->get('gmoney');
			if ( $userGmoney < $needGmoney ) {
				$result['content'] = 'money_not_enough';
				return $result;
			}
			//update user gmoney
			$role->increment('gmoney', -$needGmoney);
		}
		else if ( $itemBasic['crystal'] > 0 ) {
			//check user major magic
			$crystalType = 'coin';
			$needCrystal = $num * $itemBasic['crystal'];
			//check user crystal num
			$userCrystal = $role->get($crystalType);
			if ( $userCrystal < $needCrystal ) {
				$result['content'] = 'crystal_not_enough';
				return $result;
			}
			//update user crystal
			$role->increment($crystalType, -$needCrystal);
		}
		else {
			$crystalType = 'coin';
			
			$needCrystal = $num * $itemBasic[$crystalType];
			//check user crystal num
			$userCrystal = $role->get($crystalType);
			if ( $userCrystal < $needCrystal ) {
				$result['content'] = 'crystal_not_enough';
				return $result;
			}
			//update user crystal
			$role->increment($crystalType, -$needCrystal);
		}
		
		//add user item
		$item_model = Item_Model::instance($this->role_id);
		$item_model->incrementUserItem($this->role_id, $itemId, array('count' => $num));
		
		$result['status'] = 1;
		//$addItem = array();
		//$addItem[] = array($itemId, $num);
		//$result['addItem'] = $addItem;
		
		return $result;
	}
	
	/**
	 * use item
	 *
	 * @param integer $itemId
	 * @return array
	 */
	public function useitem($itemId)
	{
		$result = array('status' => -1);
		
		//check user item count
		$item_model = Item_Model::instance($this->role_id);
		$userItem = $item_model->getUserItem($itemId);
		if ( empty($userItem) || $userItem['count'] < 1 ) {
			$result['content'] = 'item_not_enough';
			return $result;
		}
		
		//check item type
		$basic_model = new Basic_Model();
		$itemBasic = $basic_model->getItemById($itemId);
		if ( $itemBasic['type'] != 1 && $itemBasic['type'] != 2 ) {
			$result['content'] = 'item_use_error';
			return $result;
		}
		
		//check today use count
		$todayUnixTime = strtotime(date('Y-m-d'), time());
		if ( $userItem['last_use_time'] >= $todayUnixTime ) {
			if ( $userItem['today_use_count'] >= $itemBasic['limit_time'] ) {
				$result['content'] = 'item_limit_time';
				return $result;
			}
			$newTodayUseCount = $userItem['today_use_count'] + 1;
		}
		else {
			$newTodayUseCount = 1;
		}
		
		//start use item
		$role = Role::create($this->role_id);
		$userMp = $role->get('mp');
		$userMaxMp = $role->get('max_mp');
		$newMp = $userMp + $itemBasic['add_mp'];
		$newMp = $newMp > $userMaxMp ? $userMaxMp : $newMp;
		$mpChange = $newMp - $userMp;
		
		//add user mp
		$role->increment('mp', $mpChange);
		
		$newUserItem = array('count' => $userItem['count'] - 1,
						     'today_use_count' => $newTodayUseCount,
						     'last_use_time' => PEAR::getStaticProperty('_APP', 'timestamp'));
		//update user item
		$item_model->updateUserItem($this->role_id, $itemId, $newUserItem);
		
		GameEvent::processRoleEvent(EventConditionType::USE_MEDICINE, 0, 1);
		
		$result['status'] = 1;
		$removeItems = array();
		$removeItems[] = array($itemId, 1);
		$result['removeItems'] = $removeItems;
		return $result;
	}
	
	public function addItems($items)
	{
	    $item_model = Item_Model::instance($this->role_id);
    	foreach ($items as $vl) {
    		//TODO 任务增加道具
    		$item_model->incrementUserItem($this->role_id, $vl[0], array('count' => $vl[1]));
    	}
	}
}