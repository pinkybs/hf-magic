<?php

class Hapyfish2_Magic_Bll_Magic
{
	/*
	 * get trans magic end time
	 *
	 */
	public static function getTransEndTime($uid, $transType, $transStartTime)
	{
		$endTime = 0;
		if ($transType > 0) {
			$transMagic = Hapyfish2_Magic_Cache_BasicInfo::getMagicTransInfo($transType);
			if ($transMagic) {
				$endTime = $transStartTime + $transMagic['magic_time'];
				//$nowTime = time();
				//$endTime = $transMagic['magic_time'] - ($nowTime - $transStartTime);
				//$endTime = max(0, $endTime);
			}
		}

		return $endTime;
	}

	public static function mix($uid, $mid, $num)
	{
		if ($num <= 0) {
			return Hapyfish2_Magic_Bll_UserResult::Error('param_error');
		}

		$mixBasicInfo = Hapyfish2_Magic_Cache_BasicInfo::getMagicMixInfo($mid);
		if (!$mixBasicInfo) {
			return Hapyfish2_Magic_Bll_UserResult::Error('magic_is_not_exist');
		}

		$userLevelInfo = Hapyfish2_Magic_HFC_User::getUserLevel($uid);
		if (!$userLevelInfo) {
			return $result;
		}

		//check user level
		if ($userLevelInfo['level'] < $mixBasicInfo['level'] ) {
			return Hapyfish2_Magic_Bll_UserResult::Error('level_not_enough');
		}

		$coinNeed = 0;
		if ($mixBasicInfo['coin'] > 0) {
			$coinNeed = $mixBasicInfo['coin']*$num;
			$userCoin = Hapyfish2_Magic_HFC_User::getUserCoin($uid);
			if ($userCoin < $coinNeed) {
				return Hapyfish2_Magic_Bll_UserResult::Error('coin_not_enough');
			}
		}

		$goldNeed = 0;
		if ($mixBasicInfo['gold'] > 0) {
			$goldNeed = $mixBasicInfo['gold']*$num;
			$userGold = Hapyfish2_Magic_HFC_User::getUserGold($uid);
			if ($userGold < $goldNeed) {
				return Hapyfish2_Magic_Bll_UserResult::Error('glod_not_enough');
			}
		}

		$type = $mixBasicInfo['type'];

		//check user building
		$needBuildingList = array();
		if (!empty($mixBasicInfo['need_building'])) {
			$needBuilding = json_decode($mixBasicInfo['need_building']);
			if (!empty($needBuilding)) {
				foreach ($needBuilding as $data) {
					$needCid = $data[0];
					$needNum = $data[1];
					//type
					//1:desk, 2:door, 3:floor, 4:wall, other:building
					if ($type == 1) {
						$userBagDesk = array();
						$list = Hapyfish2_Magic_HFC_Desk::getInBag($uid);
						foreach ($list as $v) {
							if ($v['cid'] == $needCid) {
								$userBagDesk[] = $v['id'];
							}
						}
						if (count($userBagDesk) < $needNum * $num) {
							return Hapyfish2_Magic_Bll_UserResult::Error('item_not_enough');
						}

						$needBuildingList[] = array('cid' => $needCid, 'num' => $needNum * $num, 'idlist' => $userBagDesk);
					} else if ($type == 2) {
						$userBagDoor = array();
						$list = Hapyfish2_Magic_HFC_Door::getInBag($uid);
						foreach ($list as $v) {
							if ($v['cid'] == $needCid) {
								$userBagDoor[] = $v['id'];
							}
						}
						if (count($userBagDoor) < $needNum * $num) {
							return Hapyfish2_Magic_Bll_UserResult::Error('item_not_enough');
						}

						$needBuildingList[] = array('cid' => $needCid, 'num' => $needNum * $num, 'idlist' => $userBagDoor);
					} else if ($type == 3) {
						$list = Hapyfish2_Magic_HFC_FloorBag::getUserFloor($uid);
						if (isset($list[$needCid])) {
							if ($list[$needCid]['count'] < $needNum * $num) {
								return Hapyfish2_Magic_Bll_UserResult::Error('item_not_enough');
							}
						} else {
							return Hapyfish2_Magic_Bll_UserResult::Error('item_not_enough');
						}

						$needBuildingList[] = array('cid' => $needCid, 'num' => $needNum * $num);
					} else if ($type == 4) {
						$list = Hapyfish2_Magic_HFC_WallBag::getUserWall($uid);
						if (isset($list[$needCid])) {
							if ($list[$needCid]['count'] < $needNum * $num) {
								return Hapyfish2_Magic_Bll_UserResult::Error('item_not_enough');
							}
						} else {
							return Hapyfish2_Magic_Bll_UserResult::Error('item_not_enough');
						}

						$needBuildingList[] = array('cid' => $needCid, 'num' => $needNum * $num);
					} else {
						$userBagBuilding = array();
						$list = Hapyfish2_Magic_HFC_Building::getInBag($uid);
						foreach ($list as $v) {
							if ($v['cid'] == $needCid) {
								$userBagBuilding[] = $v['id'];
							}
						}
						if (count($userBagBuilding) < $needNum * $num) {
							return Hapyfish2_Magic_Bll_UserResult::Error('item_not_enough');
						}

						$needBuildingList[] = array('cid' => $needCid, 'num' => $needNum * $num, 'idlist' => $userBagBuilding);
					}
				}
			}
		}

		//check user item
		$needItemList = array();
		if (!empty($mixBasicInfo['need_item'])) {
			$needItem = json_decode($mixBasicInfo['need_item']);
			if (!empty($needItem)) {
				foreach ($needItem as $data) {
					$needCid = $data[0];
					$needNum = $data[1];
					$list = Hapyfish2_Magic_HFC_Item::getUserItem($uid);
					if (!isset($list[$needCid]) || $list[$needCid]['count'] < $needNum * $num) {
						return Hapyfish2_Magic_Bll_UserResult::Error('item_not_enough');
					}

					$needItemList[] = array('cid' => $needCid, 'num' => $needNum * $num);
				}
			}
		}

		//start

		$addDecorBag = array();
		$removeItems = array();
		if ($coinNeed > 0) {
			Hapyfish2_Magic_HFC_User::decUserCoin($uid, $coinNeed);
		}

		if ($goldNeed > 0) {
        	$goldInfo = array(
        		'uid' => $uid,
        		'cost' => $goldNeed,
        		'summary' => '合成' . $mixBasicInfo['name']
        	);
			Hapyfish2_Magic_Bll_Gold::consume($uid, $goldInfo);
		}

		//delete need item
		foreach ($needItemList as $b) {
			Hapyfish2_Magic_HFC_Item::useUserItem($uid, $b['cid'], $b['num']);
		}

		if ($type == 1) {
			//desk
			foreach ($needBuildingList as $b) {
				for($i = 0; $i < $b['num']; $i++) {
					Hapyfish2_Magic_HFC_Desk::delOne($uid, $b['idlist'][$i], 0, $b['cid']);
				}
			}

			$desk = array(
				'uid' => $uid,
				'cid' => $mixBasicInfo['building'],
				'item_type' => $type,
				'status' => 0
			);
			for($i = 0; $i < $num; $i++) {
				$ok = Hapyfish2_Magic_HFC_Desk::addOne($uid, $desk);
				if ($ok) {
				}
			}
		} else  if ($type == 2) {
			//door
			foreach ($needBuildingList as $b) {
				for($i = 0; $i < $b['num']; $i++) {
					Hapyfish2_Magic_HFC_Door::delOne($uid, $b['idlist'][$i], 0, $b['cid']);
				}
			}
			$door = array(
				'uid' => $uid,
				'cid' => $mixBasicInfo['building'],
				'item_type' => $type,
				'status' => 0
			);
			for($i = 0; $i < $num; $i++) {
				$ok = Hapyfish2_Magic_HFC_Door::addOne($uid, $door);
				if ($ok) {
				}
			}
		} else if ($type == 3) {
			//floor
			foreach ($needBuildingList as $b) {
				Hapyfish2_Magic_HFC_FloorBag::useUserFloor($uid, $b['cid'], $b['num']);
			}
			$ok = Hapyfish2_Magic_HFC_FloorBag::addUserFloor($uid, $mixBasicInfo['building'], $num);
		} else if ($type == 4) {
			//wall
			foreach ($needBuildingList as $b) {
				Hapyfish2_Magic_HFC_WallBag::useUserWall($uid, $b['cid'], $b['num']);
			}
			Hapyfish2_Magic_HFC_WallBag::addUserWall($uid, $mixBasicInfo['building'], $num);
		} else if ($type == 5 || $type == 6 || $type == 7) {
			//building
			foreach ($needBuildingList as $b) {
				for($i = 0; $i < $b['num']; $i++) {
					Hapyfish2_Magic_HFC_Building::delOne($uid, $b['idlist'][$i], 0, $b['cid']);
				}
			}
			$building = array(
				'uid' => $uid,
				'cid' => $mixBasicInfo['building'],
				'item_type' => $type,
				'status' => 0
			);
			for($i = 0; $i < $num; $i++) {
				$ok = Hapyfish2_Magic_HFC_Building::addOne($uid, $building);
				if ($ok) {
				}
			}
		}
		//新类型-道具合成
		else if ($type == 8) {
            Hapyfish2_Magic_HFC_Item::addUserItem($uid, $mixBasicInfo['building'], $num);
		}

		//派发事件
		$event = array('uid' => $uid, 'mixInfo' => $mixBasicInfo, 'num' => $num);
		Hapyfish2_Magic_Bll_Event::mix($event);

		return Hapyfish2_Magic_Bll_UserResult::all();
	}

	public static function trans($uid, $fid, $mid)
	{
		//check is friend
		$isFriend = true;
		//$isFriend = Hapyfish2_Platform_Bll_Friend::isFriend($uid, $fid);
		if ( !$isFriend && $fid != $uid ) {
			return Hapyfish2_Magic_Bll_UserResult::Error('not_friend');
		}

		//check is exist
		$transBasicInfo = Hapyfish2_Magic_Cache_BasicInfo::getMagicTransInfo($mid);
		if (!$transBasicInfo) {
			return Hapyfish2_Magic_Bll_UserResult::Error('magic_is_not_exist');
		}

		//check has study
		$magicList = Hapyfish2_Magic_Cache_Magic::getList($uid, true);
		if (empty($magicList) || !in_array($mid, $magicList['trans_ids'])) {
			return Hapyfish2_Magic_Bll_UserResult::Error('trans_magic_is_not_exist');
		}

		//check user mina
		$userMpInfo = Hapyfish2_Magic_HFC_User::getUserMp($uid);
		if ($userMpInfo['mp'] < $transBasicInfo['need_mp']) {
			return Hapyfish2_Magic_Bll_UserResult::Error('mina_not_enough');
		}

		$t = time();

		$friendTransInfo = Hapyfish2_Magic_HFC_User::getUserTrans($fid);
        $friendTransEndTime = self::getTransEndTime($fid, $friendTransInfo['trans_type'], $friendTransInfo['trans_start_time']);
		$friendTransRemainTime = $friendTransEndTime - $t;

		if ( $friendTransRemainTime > 0 ) {
		    $changeUser = array('trans_time'=>$friendTransRemainTime, 'trans_mid'=>$friendTransInfo['trans_type']);
		    //Hapyfish2_Magic_Bll_UserResult::addField($uid, 'changeUsers', array($changeUser));
			$retErr = Hapyfish2_Magic_Bll_UserResult::Error('friend_trans_is_exist');
			$retErr['changeUsers'] = array($changeUser);
			return $retErr;
		}

		//get magic gain item
		$gainItems = json_decode($transBasicInfo['gain_items']);
		$feedItem = '';
		if (!empty($gainItems)) {
			$userItems = Hapyfish2_Magic_HFC_Item::getUserItem($uid);
			foreach ( $gainItems as $gain ) {
				Hapyfish2_Magic_HFC_Item::addUserItem($uid, $gain[0], $gain[1], $userItems);
				$itemBasicInfo = Hapyfish2_Magic_Cache_BasicInfo::getItemInfo($gain[0]);
				$feedItem .= '<font color="#CC0000">'. $gain[1] .'</font> <fontcolor="#CC0000">'. $itemBasicInfo['name'] .'</font> ';
			}
		}

		//update userinfo
		$mpChange = $transBasicInfo['need_mp'];
		$expChange = $transBasicInfo['gain_exp'];
		Hapyfish2_Magic_HFC_User::decUserMp($uid, $mpChange);
		Hapyfish2_Magic_HFC_User::incUserExp($uid, $expChange);

		//update friend info
		$friendTransInfo['trans_type'] = $mid;
		$friendTransInfo['trans_start_time'] = $t;
		Hapyfish2_Magic_HFC_User::updateUserTrans($fid, $friendTransInfo);

		if ($uid != $fid) {
			//insert minifeed
			$feed = array(
				'uid' => $fid,
				'template_id' => 3,
				'actor' => $uid,
				'target' => $fid,
				'type' => 1,//1好友 2系统
				'icon' => 2,//1笑脸 2哭脸
				'title' => array('trans_name' => $transBasicInfo['name'], 'item' => $feedItem),
				'create_time' => $t
			);
			Hapyfish2_Magic_Bll_Feed::insertMiniFeed($feed);
		}

		//派发事件
		$event = array('uid' => $uid, 'fid' => $fid, 'transInfo' => $transBasicInfo);
		Hapyfish2_Magic_Bll_Event::trans($event);

		return Hapyfish2_Magic_Bll_UserResult::all();
	}

	public static function studyTrans($uid, $mid)
	{
		//check is exist
		$transBasicInfo = Hapyfish2_Magic_Cache_BasicInfo::getMagicTransInfo($mid);
		if (!$transBasicInfo) {
			return Hapyfish2_Magic_Bll_UserResult::Error('magic_is_not_exist');
		}

		//check user level
		$userLevelInfo = Hapyfish2_Magic_HFC_User::getUserLevel($uid);
		if (!$userLevelInfo) {
			return Hapyfish2_Magic_Bll_UserResult::Error('level_error');
		}
		if ($userLevelInfo['level'] < $transBasicInfo['level'] ) {
			return Hapyfish2_Magic_Bll_UserResult::Error('level_not_enough');
		}

		//check has study
		$magicList = Hapyfish2_Magic_Cache_Magic::getList($uid, true);
		if (empty($magicList) || in_array($mid, $magicList['trans_ids'])) {
			return Hapyfish2_Magic_Bll_UserResult::Error('trans_magic_is_exist');
		}

		$goldNeed = 0;
		$goldChange = 0;
		if ($transBasicInfo['gold'] > 0) {
			$goldNeed = $transBasicInfo['gold'];
			$goldChange -= $goldNeed;
			$userGold = Hapyfish2_Magic_HFC_User::getUserGold($uid);
			if ($userGold < $goldNeed) {
				return Hapyfish2_Magic_Bll_UserResult::Error('glod_not_enough');
			}
		}

		$coinNeed = 0;
		$coinChange = 0;
		if ($transBasicInfo['coin'] > 0) {
			$coinNeed = $transBasicInfo['coin'];
			$coinChange -= $coinNeed;
			$userCoin = Hapyfish2_Magic_HFC_User::getUserCoin($uid);
			if ($userCoin < $coinNeed) {
				return Hapyfish2_Magic_Bll_UserResult::Error('coin_not_enough');
			}
		}

		if ($coinNeed > 0) {
			Hapyfish2_Magic_HFC_User::decUserCoin($uid, $coinNeed);
		}

		if ($goldNeed > 0) {
        	$goldInfo = array(
        		'uid' => $uid,
        		'cost' => $goldNeed,
        		'summary' => '学习' . $transBasicInfo['name']
        	);
			Hapyfish2_Magic_Bll_Gold::consume($uid, $goldInfo);
		}

		//study trans magic
		$magicList['trans_ids'][] = $mid;

		Hapyfish2_Magic_Cache_Magic::update($uid, $magicList);

		//派发事件
		$event = array('uid' => $uid, 'transInfo' => $transBasicInfo);
		Hapyfish2_Magic_Bll_Event::studyTrans($event);

		return Hapyfish2_Magic_Bll_UserResult::all();
	}

	public static function studyTeach($uid, $mid)
	{
		//check is exist
		$studyBasicInfo = Hapyfish2_Magic_Cache_BasicInfo::getMagicStudyInfo($mid);
		if (!$studyBasicInfo) {
			return Hapyfish2_Magic_Bll_UserResult::Error('magic_is_not_exist');
		}

		//check user level
		$userLevelInfo = Hapyfish2_Magic_HFC_User::getUserLevel($uid);
		if (!$userLevelInfo) {
			return Hapyfish2_Magic_Bll_UserResult::Error('level_error');
		}
		if ($userLevelInfo['level'] < $studyBasicInfo['level'] ) {
			return Hapyfish2_Magic_Bll_UserResult::Error('level_not_enough');
		}

		//check has study
		$magicList = Hapyfish2_Magic_Cache_Magic::getList($uid, true);
		if (empty($magicList) || in_array($mid, $magicList['study_ids'])) {
			return Hapyfish2_Magic_Bll_UserResult::Error('study_magic_is_exist');
		}

		$goldNeed = 0;
		$goldChange = 0;
		if ($studyBasicInfo['gold'] > 0) {
			$goldNeed = $studyBasicInfo['gold'];
			$goldChange -= $goldNeed;
			$userGold = Hapyfish2_Magic_HFC_User::getUserGold($uid);
			if ($userGold < $goldNeed) {
				return Hapyfish2_Magic_Bll_UserResult::Error('glod_not_enough');
			}
		}

		$coinNeed = 0;
		$coinChange = 0;
		if ($studyBasicInfo['coin'] > 0) {
			$coinNeed = $studyBasicInfo['coin'];
			$coinChange -= $coinNeed;
			$userCoin = Hapyfish2_Magic_HFC_User::getUserCoin($uid);
			if ($userCoin < $coinNeed) {
				return Hapyfish2_Magic_Bll_UserResult::Error('coin_not_enough');
			}
		}

		if ($coinNeed > 0) {
			Hapyfish2_Magic_HFC_User::decUserCoin($uid, $coinNeed);
		}

		if ($goldNeed > 0) {
        	$goldInfo = array(
        		'uid' => $uid,
        		'cost' => $goldNeed,
        		'summary' => '学习' . $studyBasicInfo['name']
        	);
			Hapyfish2_Magic_Bll_Gold::consume($uid, $goldInfo);
		}

		//study trans magic
		$magicList['study_ids'][] = $mid;

		Hapyfish2_Magic_Cache_Magic::update($uid, $magicList);

		//派发事件
		$event = array('uid' => $uid, 'studyInfo' => $studyBasicInfo);
		Hapyfish2_Magic_Bll_Event::studyTeach($event);

		return Hapyfish2_Magic_Bll_UserResult::all();
	}

}