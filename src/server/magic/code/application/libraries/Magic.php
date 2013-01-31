<?php

class Magic {
	//单例
	private static $instance;
	private $role_id;
	
	public function __construct($role_id)
	{
		$this->role_id = $role_id;
	}
	
	/**
	 * Singleton instance of Basic
	 */
	public static function instance($role_id)
	{
		if (!isset(self::$instance[$role_id]))
		{
			// Create a new instance
			self::$instance[$role_id] = new Magic($role_id);
		}

		return self::$instance[$role_id];
	}
	
	/**
	 * study teach magic
	 *
	 * @param integer $role_id
	 * @param integer $msid
	 * @return array
	 */
	public static function studyteach($role_id, $msid)
	{
		$result = array('status' => -1);
		
		$basic_model = new Basic_Model();
		$magicTeachBasic = $basic_model->getMagicStudyDataById($msid);
		if ( !$magicTeachBasic ) {
			$result['content'] = 'magic_is_not_exist';
			return $result;
		}
		
		$role = Role::create($role_id);
		
		//check user level
		$userLevel = $role->get('level');
		if ( $userLevel < $magicTeachBasic['level'] ) {
			$result['content'] = 'level_not_enough';
			return $result;
		}
		
		$crystalType = 'coin';
		
		//check user crystal
		$userCrystal = $role->get($crystalType);
		if ( $userCrystal < $magicTeachBasic[$crystalType] ) {
			$result['content'] = 'crystal_not_enough';
			return $result;
		}
		
		//check user money
		if ( $magicTeachBasic['money'] > 0 ) {
			$userMoney = $role->get('gmoney');
			if ( $userMoney < $magicTeachBasic['money'] ) {
				$result['content'] = 'money_not_enough';
				return $result;
			}
		}
		
		//check is exist
		$magic_model = Magic_Model::instance($role_id);
		$userMagic = $magic_model->getDataByRoleId();
		$userStudyIds = json_decode($userMagic['study_ids']);
		if ( in_array($msid, $userStudyIds) ) {
			$result['content'] = 'magic_is_exist';
			return $result;
		}
		
		//start: study teach magic 
		$userStudyIds[] = $msid;
		
		//update user magic
		$magic_model->updateUserMagic(array('study_ids' => json_encode($userStudyIds)));
		$role->increment($crystalType, -$magicTeachBasic[$crystalType]);
		if ( $magicTeachBasic['money'] > 0 ) {
			$role->increment('gmoney', -$magicTeachBasic['money']);
		}
		
		$result['status'] = 1;
		
		return $result;
	}

	/**
	 * study trans magic
	 *
	 * @param integer $role_id
	 * @param integer $mtid
	 * @return array
	 */
	public static function studytrans($role_id, $mtid)
	{
		$result = array('status' => -1);
		
		$basic_model = new Basic_Model();
		$magicTransBasic = $basic_model->getMagicTransDataById($mtid);
		if ( !$magicTransBasic ) {
			$result['content'] = 'magic_is_not_exist';
			return $result;
		}
		
		$role = Role::create($role_id);
		
		//check user level
		$userLevel = $role->get('level');
		if ( $userLevel < $magicTransBasic['level'] ) {
			$result['content'] = 'level_not_enough';
			return $result;
		}
		
		//check is exist
		$magic_model = Magic_Model::instance($role_id);
		$userMagic = $magic_model->getDataByRoleId();
		$userTransIds = json_decode($userMagic['trans_ids']);
		if ( in_array($mtid, $userTransIds) ) {
			$result['content'] = 'magic_is_exist';
			return $result;
		}
	
		//check user money
		if ( $magicTransBasic['money'] > 0 ) {
			$userMoney = $role->get('gmoney');
			if ( $userMoney < $magicTransBasic['money'] ) {
				$result['content'] = 'money_not_enough';
				return $result;
			}
		}
		
		if ( $magicTransBasic['crystal'] > 0 ) {
			//check user crystal
			$magicField = 'coin';
			$userCrystal = $role->get($magicField);
			if ( $userCrystal < $magicTransBasic['crystal'] ) {
				$result['content'] = $magicField . '_not_enough';
				return $result;
			}
			//update user crystal
			$role->increment($magicField, -$magicTransBasic['crystal']);
		}
		else {
			$magicField = 'coin';
			
			//check user crystal num
			$userCrystal = $role->get($magicField);
			if ( $userCrystal < $magicTransBasic[$magicField] ) {
				$result['content'] = $magicField . '_not_enough';
				return $result;
			}
			//update user crystal
			$role->increment($magicField, -$magicTransBasic[$magicField]);
		}
		
		//start: study trans magic 
		$userTransIds[] = $mtid;
		
		//update user magic
		$magic_model->updateUserMagic(array('trans_ids' => json_encode($userTransIds)));
		
		if ( $magicTransBasic['money'] > 0 ) {
			$role->increment('gmoney', -$magicTransBasic['money']);
		}
		
		$result['status'] = 1;
		
		return $result;
	}
	
	/**
	 * use mix magic
	 *
	 * @param integer $role_id
	 * @param integer $mtid
	 * @return array
	 */
	public static function mixmagic($role_id, $mmid, $nums)
	{
		$result = array('status' => -1);
		
		//check $nums is int
		if ( !is_numeric($nums) ) {
			$result['content'] = 'param_error';
			return $result;
		}
		
		$basic_model = new Basic_Model();
		$magicMixBasic = $basic_model->getMagicMixDataById($mmid);
		if ( !$magicMixBasic ) {
			$result['content'] = 'magic_is_not_exist';
			return $result;
		}
		
		$role = Role::create($role_id);
		
		//check user level
		$userLevel = $role->get('level');
		if ( $userLevel < $magicMixBasic['level'] ) {
			$result['content'] = 'level_not_enough';
			return $result;
		}
		
		//check user crystal
		$userRed = $role->get('coin');
		if ( $userRed < $magicMixBasic['coin'] * $nums ) {
			$result['content'] = 'coin_not_enough';
			return $result;
		}
		
		//check user money
		if ( $magicMixBasic['money'] > 0 ) {
			$userMoney = $role->get('gmoney');
			if ( $userMoney < $magicMixBasic['money'] * $nums ) {
				$result['content'] = 'money_not_enough';
				return $result;
			}
		}
		
		if ( $magicMixBasic['type'] == 3 ) {
			$floor_inbag_model = Floor_Inbag_Model::instance($role_id);
		}
		else if ( $magicMixBasic['type'] == 4 ) {
			$wall_inbag_model = Wall_Inbag_Model::instance($role_id);
		}
		else {
			$building_model = Building_Model::instance($role_id);
		}
		
		//check user building
		$needBuildingArray = array();
		if ( $magicMixBasic['need_building'] != '(NULL)' && $magicMixBasic['need_building'] != NULL ) {
			$needBuilding = json_decode($magicMixBasic['need_building']);
			foreach ( $needBuilding as $data ) {
				//3:floor,4:wall,other:building
				if ( $magicMixBasic['type'] == 3 ) {
					$userBuildingList = $floor_inbag_model->getUserFloorInBagByFid($data[0]);
					if ( $userBuildingList['quantity'] < $data[1] * $nums) {
						$result['content'] = 'item_not_enough';
						return $result;
					}
					$needBuildingArray[] = array('i_id' => $data[0], 'num' => $data[1] * $nums);
				}
				else if ( $magicMixBasic['type'] == 4 ) {
					$userBuildingList = $wall_inbag_model->getUserWallInBagById($data[0]);
					if ( $userBuildingList['quantity'] < $data[1] * $nums) {
						$result['content'] = 'item_not_enough';
						return $result;
					}
					$needBuildingArray[] = array('i_id' => $data[0], 'num' => $data[1] * $nums);
				}
				else {
					$userBuildingList = $building_model->getUserBuildingInBagByBid($data[0]);
					$userBuildingArray = array();
					foreach ( $userBuildingList as $building ) {
						$userBuildingArray[] = $building[0]['id'];
					}
					if ( count($userBuildingArray) < $data[1] * $nums ) {
						$result['content'] = 'item_not_enough';
						return $result;
					}
					for ( $i=0,$iCount=($data[1] * $nums); $i<$iCount; $i++ ) {
						$needBuildingArray[] = $userBuildingArray[$i];
					}
				}
				
			}
		}
		
		//check user item
		$needItemArray = array();
		if ( $magicMixBasic['need_item'] != '(NULL)' ) {
			$item_model = Item_Model::instance($role_id);
			$needItem = json_decode($magicMixBasic['need_item']);
			foreach ( $needItem as $data ) {
				$userItem = $item_model->getUserItem($data[0]);
				if ( empty($userItem) || $userItem['count'] < $data[1] * $nums ) {
					$result['content'] = 'item_not_enough';
					return $result;
				}
				$needItemArray[] = array('item_id' => $data[0], 'count' => $data[1] * $nums);
			}
		}
		
		//start: use mix magic 
	
		//add user new building
		$buildingBasic = $basic_model->getBuildingDataById($magicMixBasic['building']);
		//3:floor,4:wall,other:building
		if ( $magicMixBasic['type'] == 3 ) {
			$newFloor = array('role_id' => $role_id,
							  'floor_id' => $magicMixBasic['building'],
							  'quantity' => $nums);
			$addFloorResult = $floor_inbag_model->addUserFloorInBag($newFloor);
		}
		else if ( $magicMixBasic['type'] == 4 ) {
			$newWall = array('role_id' => $role_id,
							 'wall_id' => $magicMixBasic['building'],
							 'quantity' => $nums);
			$addWallResult = $wall_inbag_model->addUserWallInBag($newWall);
		}
		else {
			$newBuilding = array('role_id' => $role_id,
								 'building_id' => $magicMixBasic['building'],
								 'building_type' => $buildingBasic['type'],
								 'effect_mp' => $buildingBasic['effect_mp'],
								 'bag_type' => 1);
			$addDecor = array();
			for ( $j=0; $j<$nums; $j++ ) {
				//$newId = $building_model->insertBuilding($newBuilding);
				$building_model->insertById($magicMixBasic['building']);
			}
			$result['addDecor'] = $addDecor;
		}
		
		//update userinfo: minus 
		$role->increment('coin', -$magicMixBasic['coin'] * $nums);

		$role->increment('gmoney', -$magicMixBasic['money'] * $nums);
		
		//delete need building
		if ( $magicMixBasic['type'] == 3 ) {
			foreach ( $needBuildingArray as $building ) {
				$floor_inbag_model->updateUserFloorInBagByField($role_id, $building['i_id'], array('quantity' => -$building['num']));
			}
		}
		else if ( $magicMixBasic['type'] == 4 ) {
			foreach ( $needBuildingArray as $building ) {
				$wall_inbag_model->updateUserWallInBagByField($role_id, $building['i_id'], array('quantity' => -$building['num']));
			}
		}
		else {
			foreach ( $needBuildingArray as $building ) {
				$building_model->deleteBuildingById($building);
			}
		}
		
		//delete need item
		foreach ( $needItemArray as $item ) {
			$item_model->incrementUserItem($role_id, $item['item_id'], array('count' => -$item['count']));
			
			GameEvent::processRoleEvent(EventConditionType::GIVE_ITEMS, $item['item_id'], -$item['count']);
		}
		
		GameEvent::processRoleEvent(EventConditionType::MIX_BUILDING, $magicMixBasic['building'], 1);
		
		$result['status'] = 1;
		
		return $result;
	}

	/**
	 * use trans magic
	 *
	 * @param integer $role_id
	 * @param integer $fid
	 * @param integer $mtid
	 * @return array
	 */
	public static function transmagic($role_id, $fid, $mtid)
	{
		$result = array('status' => -1);
		
		//check is friend
		$isFriend = true;
		if ( !$isFriend && $fid != $role_id ) {
			$result['content'] = 'not_friend';
			return $result;
		}
		
		//check is exist
		$basic_model = new Basic_Model();
		$magicTransBasic = $basic_model->getMagicTransDataById($mtid);
		if ( !$magicTransBasic ) {
			$result['content'] = 'magic_is_not_exist';
			return $result;
		}
		
		//check has study
		$magic_model = Magic_Model::instance($role_id);
		$userMagic = $magic_model->getDataByRoleId();
		$userTransIds = json_decode($userMagic['trans_ids']);
		if ( !in_array($mtid, $userTransIds) ) {
			$result['content'] = 'trans_magic_is_not_exist';
			return $result;
		}
		
		$role = Role::create($role_id);
		
		//check user mina
		$userMp = $role->get('mp');
		if ( $userMp < $magicTransBasic['need_mp'] ) {
			$result['content'] = 'mina_not_enough';
			return $result;
		}
		
		$nowTime = time();
        $friendTransEndTime = self::getTransEndTime($fid);
		$friendTransRemainTime = $friendTransEndTime - $nowTime;
        
		if ( $friendTransRemainTime > 0 ) {
			$result['content'] = 'friend_trans_is_exist';
			return $result;
		}
		
		//get magic gain item
		$gainItems = json_decode($magicTransBasic['gain_items']);
		//start: use trans magic 
		$item_model = Item_Model::instance($role_id);
		
		$basic_model = new Basic_Model();
		$feedItem = '';
		//$addItem = array();
		foreach ( $gainItems as $gain ) {
			$item_model->incrementUserItem($role_id, $gain[0], array('count' => $gain[1]));
			//$addItem[] = array($gain[0], $gain[1]);
			//feed item info
			$itemBasic = $basic_model->getItemById($gain[0]);
			$feedItem .= '<font color="#CC0000">'. $gain[1] .'</font> <fontcolor="#CC0000">'. $itemBasic['name'] .'</font> ';
		}
 		
		//update userinfo
		$role->increment('exp', $magicTransBasic['gain_exp']);
		$role->increment('mp', -$magicTransBasic['need_mp']);
		
		//update friend info
		$role_friend = Role::create($fid);
		$role_friend->set('trans_type', $mtid);
		$role_friend->set('trans_start_time', $nowTime);
		
		//task
		GameEvent::processRoleEvent(EventConditionType::USE_TRANS, 0, 1);
		if ( $isFriend ) {
			GameEvent::processRoleEvent(EventConditionType::USE_FRIENDS_TRANS, 0, 1);
		}
		
		//feed
        $feed = Feed::instance($role_id);
        $title = array('trans_name'=>$magicTransBasic['name'], 'item'=>$feedItem);
        $feed->addMiniFeed($fid, $role_id, $fid, FeedTemplate::TRANS_MAGIC, 1, 2, $title);
		
		$result['status'] = 1;
		//$result['addItem'] = $addItem;

		return $result;
	}
	
	/**
	 * reduce trans magic
	 *
	 * @return array
	 */
	public static function reducetrans($role_id)
	{
		$result = array('status' => -1);
		
		$role = Role::create($role_id);
		
		//update friend info
		$role->set('trans_type', 0);
		
		$result['status'] = 1;
		
		return $result;
	}
	
	/*
	 * get trans magic end time
	 * 
	 */
	public static function getTransEndTime($role_id)
	{
		$endTime = 0;
		$role = Role::create($role_id);
		$transType = $role->get('trans_type');
		$transStartTime = $role->get('trans_start_time');
		$nowTime = time();
		if ( $transType > 0 ) {
			$basic_model = new Basic_Model();
			$transMagic = $basic_model->getMagicTransDataById($transType);
			$endTime = $transStartTime + $transMagic['magic_time'];
			
			//$endTime = $transMagic['magic_time'] - ($nowTime - $transStartTime);
			//$endTime = max(0, $endTime);
		}
		return $endTime;
	}
	
	/**
	 * change user magic type
	 *
	 * @param integer $role_id
	 * @param integer $type
	 * @return array
	 */
	public static function changemagictype($role_id, $type)
	{
		$result = array('status' => -1);
		
		if ( !in_array($type, array(1,2,3)) ) {
			$result['content'] = 'wrong_magic_type';
			return $result;
		}
		
		//check user current magic type
		$role = Role::create($role_id);
		$userMagicType = $role->get('major_magic');
		if ( $userMagicType == $type ) {
			$result['content'] = 'wrong_magic_type';
			return $result;
		}
		
		$changeNeedMoney = 5;
		//check user money
		$userMoney = $role->get('gmoney');
		if ( $userMoney < $changeNeedMoney ) {
			$result['content'] = 'money_not_enough';
			return $result;
		}
		
		//start: change magic type
		$magic_model = Magic_Model::instance($role_id);
		$newStudyIds = '['.$type.'001]';
		$magic_model->updateUserMagic(array('study_ids' => $newStudyIds));
		
		//update user info
		$role->set('major_magic', $type);
		
		$role->increment('gmoney', -$changeNeedMoney);
		
		$result['status'] = 1;
		
		return $result;
	}
}