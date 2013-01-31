<?php

/**
 * logic's Operation
 *
 * @package    Happyfish_Magic_Bll
 * @copyright  Copyright (c)
 * @create      2010/08/05    zhangxin
 */
class Happyfish_Magic_Bll_Decoration
{

	const ERRLOG_FILENAME = 'decoration-';
	
	/**
	 * get user buildinglist & floors & walls
	 *
	 * @param integer $uid
	 * @param integer $mode [//0-in bag 1-in room]
	 * @return array
	 */
	public static function getDecoration($uid, $mode = 1)
	{
		//buliding info *
		$dalBuilding = Happyfish_Magic_Dal_Building::getDefaultInstance();
		$lstData = $dalBuilding->lstUserBuilding($uid);
        $aryRst = $aryData0 = $aryData1 = array();
		foreach ($lstData as $key=>$vdata) {
			if ($vdata['status']) {
        		$aryData1[] = $lstData[$key];
        	}
        	else {
        		$aryData0[] = $lstData[$key];
        	}
		}
        $aryRst['buildingList'] = $mode ? $aryData1 : $aryData0;

        
        //floor info **
        $dalFloor = Happyfish_Magic_Dal_Floors::getDefaultInstance();
        //in decor
        if ($mode) {
	        $rowFloors = $dalFloor->getUserFloors($uid);
	        if (empty($rowFloors)) {
	        	$aryRst['floorList'] = null;
	        }
	        else {
	        	$aryRst['floorList'] = Zend_Json::decode($rowFloors['floor_decor']);
	        }
        }
        //in bag
        else {
        	$lstFloors = $dalFloor->lstUserFloorInBag($uid);
        	$aryFloors = array();
        	foreach ($lstFloors as $floorData) {
        		if ($floorData['quantity']) {
        			$aryFloors[] = $floorData;
        		}
        	}
        	$aryRst['floorList'] = $aryFloors;
        }
        
		//wall info ***
        $dalWall = Happyfish_Magic_Dal_Walls::getDefaultInstance();
        //in decor
        if ($mode) {
	        $rowWalls = $dalWall->getUserWalls($uid);
	        if (empty($rowWalls)) {
	        	$aryRst['wallList'] = null;
	        }
	        else {
	        	$aryRst['wallList'] = Zend_Json::decode($rowWalls['wall_decor']);
	        }
        }
        //in bag
        else {
        	$lstWalls = $dalWall->lstUserWallInBag($uid);
        	$aryWalls = array();
        	foreach ($lstWalls as $wallData) {
        		if ($wallData['quantity']) {
        			$aryWalls[] = $wallData;
        		}
        	}
        	$aryRst['wallList'] = $aryWalls;
        }
        
        return $aryRst;
	}

	/**
	 * change user buildinglist & floors & walls
	 *
	 * @param integer $uid
	 * @param array $aryBuilding
	 * @param array $aryFloor
	 * @param array $aryWall
	 * @return mixed
	 */
	public static function changeDecoration($uid, $aryBuilding, $aryFloor, $aryWall)
	{
		try {
			//user info
			$rowUser = Happyfish_Magic_Bll_User::getUserGameInfo($uid);
			//nb building info
			$lstNbInfo = Happyfish_Magic_Bll_Cache_NbBasicInfo::listNbBuilding();
			//change building *
			if ($aryBuilding && count($aryBuilding) > 0) {
				$rst1 = self::_changeBuild($uid, $aryBuilding, $rowUser, $lstNbInfo);
				if (!$rst1) {
					info_log($uid.'[Happyfish_Magic_Bll_Decoration]-[changeDecoration]-building update failed', self::ERRLOG_FILENAME.date('Ymd'));
					return false;
				}
			}
			$rst1 = !isset($rst1) ? array('red' => 0, 'blue' => 0, 'green' => 0, 'mp_addition' => 0) : $rst1;

			//change floor **
			if ( ($aryFloor['aryShowFloor'] && count($aryFloor['aryShowFloor']) > 0)
				 || ($aryFloor['arySellFloor'] && count($aryFloor['arySellFloor']) > 0) ) {
				$rst2 = self::_changeFloor($uid, $aryFloor, $rowUser, $lstNbInfo);	 
				if (!$rst2) {
					if ($rst1) {
						//update building's addition mp 
						if ($rst1['mp_addition'] != 0) {
							$dalUser = Happyfish_Magic_Dal_User::getDefaultInstance();
				    		$dalUser->updateUserByField($uid, 'mp_addition', (int)$rst1['mp_addition']);
						}
						Happyfish_Magic_Bll_Cache_User::clearAppUser($uid);
					}
					info_log($uid.'[Happyfish_Magic_Bll_Decoration]-[changeDecoration]-floor update failed', self::ERRLOG_FILENAME.date('Ymd'));
					return false;
				}
			}
			$rst2 = !isset($rst2) ? array('red' => 0, 'blue' => 0, 'green' => 0, 'mp_addition' => 0) : $rst2;
			
			//change wall ***
			if ( ($aryWall['aryShowWall'] && count($aryWall['aryShowWall']) > 0)
				 || ($aryWall['arySellWall'] && count($aryWall['arySellWall']) > 0) ) {
				$rst3 = self::_changeWall($uid, $aryWall, $rowUser, $lstNbInfo);
				if (!$rst3) {
					if ($rst1 && $rst2) {
						//update building's addition mp 
						if (($rst1['mp_addition']+$rst2['mp_addition']) != 0) {
							$dalUser = Happyfish_Magic_Dal_User::getDefaultInstance();
				    		$dalUser->updateUserByField($uid, 'mp_addition', (int)($rst1['mp_addition']+$rst2['mp_addition']));
						}
						Happyfish_Magic_Bll_Cache_User::clearAppUser($uid);
					}
					info_log($uid.'[Happyfish_Magic_Bll_Decoration]-[changeDecoration]-wall update failed', self::ERRLOG_FILENAME.date('Ymd'));
					return false;
				}
			}
			$rst3 = !isset($rst3) ? array('red' => 0, 'blue' => 0, 'green' => 0, 'mp_addition' => 0) : $rst3;
			
			$rst = array('red' => $rst1['red'] + $rst2['red'] + $rst3['red'], 
						 'blue' => $rst1['blue'] + $rst2['blue'] + $rst3['blue'], 
					 	 'green' => $rst1['green'] + $rst2['green'] + $rst3['green']);
			
			//update building's addition mp 
			$intAdditionMpChg = $rst1['mp_addition'] + $rst2['mp_addition'] + $rst3['mp_addition'];
			if ($intAdditionMpChg != 0) {
				$dalUser = Happyfish_Magic_Dal_User::getDefaultInstance();
	    		$dalUser->updateUserByField($uid, 'mp_addition', (int)$intAdditionMpChg);
	    		info_log($uid.'|'.$rst1['mp_addition'].'|'.$rst2['mp_addition'].'|'.$rst3['mp_addition'], 'decor-mpaddition-change');
			}
			
			//clear user cache
			Happyfish_Magic_Bll_Cache_User::clearAppUser($uid);
			
			//save to today's task achievement
			Happyfish_Magic_Bll_TaskDaily::updateTodayTask($uid, 710);

			return $rst;
		}
		catch (Exception $e) {
            info_log($uid.'[Happyfish_Magic_Bll_Decoration]-[changeDecoration]:'.$e->getMessage(), 'err-Decoration-catched');
            info_log($uid.'[Happyfish_Magic_Bll_Decoration]-[changeDecoration]:'.$e->getTraceAsString(), 'err-Decoration-catched');
            return false;
		}
	}
	
	
	
	
	private static function _changeBuild($uid, $aryBuilding, $rowUser, $lstNbInfo)
	{
		$limitSize = $rowUser['nbLevInfo']['house_size'];
		
		//check building validate (repeat position submit)
		$aryTmp = array();
		$aryCheckDesk = array();
		$aryToBagDesk = array();
		foreach ($aryBuilding as $cdata) {
			if ($cdata['pos_x'] > $limitSize || $cdata['pos_z'] > $limitSize ) {
				info_log($uid.'[Happyfish_Magic_Bll_Decoration]-[changeDecoration]-validateCheck:building position limited', self::ERRLOG_FILENAME.date('Ymd'));
				return false;
			}
			if (0 == $cdata['status']) {
				//desk
				if (1 == $lstNbInfo[$cdata['building_id']]['type']) {
					$aryToBagDesk[] = $cdata['id'];
				}
				continue;
			}
			$pos = $cdata['pos_x'] . '|' . $cdata['pos_y'] . '|' . $cdata['pos_z'];
			if (in_array($pos, $aryTmp)) {
				info_log($uid.'[Happyfish_Magic_Bll_Decoration]-[changeDecoration]-validateCheck:repeat building position commited', self::ERRLOG_FILENAME.date('Ymd'));
				return false;
			}
			$aryTmp[] = $pos;
			//check desk count limit in room
			if (1 == $lstNbInfo[$cdata['building_id']]['type']) {
				if (!in_array($cdata['id'], $aryCheckDesk)) {
					$aryCheckDesk[] = $cdata['id'];
				}
			}
		}
		
		//building update begin
		//get building info old
		$dalBuilding = Happyfish_Magic_Dal_Building::getDefaultInstance();
		$lstData = $dalBuilding->lstUserBuilding($uid);
		$lstBuildingOld = array();
		//$intAdditionMpOld = 0;
		$intAdditionMpChanged = 0;
		foreach ($lstData as $vdata) {
			$lstBuildingOld[$vdata['id']] = array('id' => $vdata['id'],
        	                                   'building_id' => $vdata['building_id'],
        	                                   'pos_x' => $vdata['pos_x'],
        	                                   'pos_y' => $vdata['pos_y'],
			  								   'pos_z' => $vdata['pos_z'],
        	                                   'mirror' => $vdata['mirror'],
        	                                   'status' => $vdata['status']);
			//if ($vdata['status']) {
			//	$intAdditionMpOld += $lstNbInfo[$vdata['building_id']]['effect_mp'];
			//}
			//check desk count limit in room
			if ($vdata['status'] && 1 == $lstNbInfo[$vdata['building_id']]['type']) {
				if (!in_array($vdata['id'], $aryCheckDesk) && !in_array($vdata['id'], $aryToBagDesk)) {
					$aryCheckDesk[] = $vdata['id'];
				}
			}
		}
		//check desk limit
		if (count($aryCheckDesk) > $rowUser['nbLevInfo']['limit_seat']) {
			info_log($uid.'[Happyfish_Magic_Bll_Decoration]-[changeDecoration]-validateCheck:building desk count limit!', self::ERRLOG_FILENAME.date('Ymd'));
			return false;
		}
		
		$dalMgDesk = Happyfish_Magic_Dal_Mongo_UserDesk::getDefaultInstance();
		$dalMgDoor = Happyfish_Magic_Dal_Mongo_UserDoor::getDefaultInstance();
		$numSellRed = $numSellBlue = $numSellGreen = 0;
		//do update building
		foreach ($aryBuilding as $vdata) {
			//check building is in bag
			if ( array_key_exists($vdata['id'], $lstBuildingOld) ) {
				$rowBuildingOld = $lstBuildingOld[$vdata['id']];
				$nbInfo = $lstNbInfo[$rowBuildingOld['building_id']];
				//check is floor or wall
				if (3 == $nbInfo['type'] || 4 == $nbInfo['type']) {
					info_log($uid.'[Happyfish_Magic_Bll_Decoration]-[changeDecoration]-validateCheck:building is a floor or a wall!invalidate!', self::ERRLOG_FILENAME.date('Ymd'));
					return false;
				}
				//check is validate(door wallpaper walldeco)
				if ( (2 == $nbInfo['type'] || 7 == $nbInfo['type']) && 1 == $vdata['status'] ) {
					if ($vdata['pos_x'] && $vdata['pos_z']) {
						info_log($uid.'[Happyfish_Magic_Bll_Decoration]-[changeDecoration]-validateCheck:door/wallpaper position not valid', self::ERRLOG_FILENAME.date('Ymd'));
						return false;
					}
				}
				
				//sell the building
				if (isset($vdata['sell']) && $vdata['sell']) {
					$numSellRed += $nbInfo['sell_red'];
					$numSellBlue += $nbInfo['sell_blue'];
					$numSellGreen += $nbInfo['sell_green'];
					$dalBuilding->delete($vdata['id'], $uid);
					//if old building is in room
					if ($rowBuildingOld['status']) {
						$intAdditionMpChanged -= $nbInfo['effect_mp'];
					}
					
					//desk
					if ( 1 == $nbInfo['type'] ) {
						$dalMgDesk->delete($uid, $vdata['id']);
					}
					//door
					if ( 2 == $nbInfo['type'] ) {
						$dalMgDoor->delete($uid, $vdata['id']);
					}
				}
				//update building
				else {
					//check is need to update
					if ( http_build_query($vdata) == http_build_query($rowBuildingOld) ) {
						continue;
					}
					//update
					$dalBuilding->update(array('uid' => $uid,//'building_id'=>$vdata['building_id'],'building_type'=>$nbInfo['type'],
											   'pos_x'=>$vdata['pos_x'],'pos_y'=>$vdata['pos_y'],'pos_z'=>$vdata['pos_z'],
					                           'mirror'=>$vdata['mirror'],'status'=>$vdata['status']), $vdata['id']);
					//put building into room from bag
					if (!$rowBuildingOld['status'] && $vdata['status']) {
						$intAdditionMpChanged += $nbInfo['effect_mp'];
					}
					//put building into bag from room
					else if ($rowBuildingOld['status'] && !$vdata['status']) {
						$intAdditionMpChanged -= $nbInfo['effect_mp'];
					}
	
					//desk
					if ( 1 == $nbInfo['type'] ) {
						//delete mongo user desk
						if ( 1 == $rowBuildingOld['status'] && 0 == $vdata['status'] ) {
							$dalMgDesk->delete($uid, $vdata['id']);
						}
						//create mongo user desk
						else if ( 0 == $rowBuildingOld['status'] && 1 == $vdata['status'] ) {
							$aryDesk = array('uid' => (string)$uid, 'desk_id' => (string)$vdata['id'], 'building_id'=>$rowBuildingOld['building_id'], 
							 'status' => 0,
							 'guest_id'=>0, 'magic_id'=>0, 'red'=>0, 'blue'=>0, 'green'=>0,
					   		 'start_time'=>0, 'break_time'=>0, 'rescue_time'=>0,'spend_time'=>0,
					         'help_uid'=>0,'steal_uid_ary'=>'');
							$dalMgDesk->update($uid, $vdata['id'], $aryDesk);
						}
					}
					
					//door
					if ( 2 == $nbInfo['type'] ) {
						//delete mongo user door
						if ( 1 == $rowBuildingOld['status'] && 0 == $vdata['status'] ) {
							$dalMgDoor->delete($uid, $vdata['id']);
						}
						//create mongo user door
						else if ( 0 == $rowBuildingOld['status'] && 1 == $vdata['status'] ) {
							$guestQueue = array();
							for ($i=0; $i<$nbInfo['door_guest_limit']; $i++) {
								$guestQueue[] = rand(1, $nbInfo['door_guest_type']);
							}
							$aryDoor = array();
							$aryDoor['uid'] = (string)$uid;
							$aryDoor['door_id'] = (string)$vdata['id'];
							$aryDoor['wait_guest_ary'] = $guestQueue;
							$aryDoor['status'] = 0;
							$aryDoor['last_open_time'] = time();
							$aryDoor['door_cooldown'] = $nbInfo['door_cooldown'];
							$aryDoor['door_guest_limit'] = $nbInfo['door_guest_limit'];
							$aryDoor['door_guest_type'] = $nbInfo['door_guest_type'];
							$dalMgDoor->update($uid, $vdata['id'], $aryDoor);
						}
					}
				}//end deal one row
			}
		}//end update building
		
		//update user info
		if ($numSellRed || $numSellBlue || $numSellGreen) {
			$dalUser = Happyfish_Magic_Dal_User::getDefaultInstance();
			$aryParam = array('red'=>$numSellRed,'blue'=>$numSellBlue,'green'=>$numSellGreen);
			$dalUser->updateUserByMultipleField($uid, $aryParam);
		}
		
		return array('red' => $numSellRed, 'blue' => $numSellBlue, 'green' => $numSellGreen, 'mp_addition' => ($intAdditionMpChanged));
	}
	
	
	private static function _changeFloor($uid, $aryFloor, $rowUser, $lstNbInfo)
	{
		$limitSize = $rowUser['nbLevInfo']['house_size'];
		//check floor count
		if ( count($aryFloor['aryShowFloor']) > $limitSize*$limitSize ) {
			info_log($uid.'[Happyfish_Magic_Bll_Decoration]-[changeDecoration]-validateCheck:floor count overflow', self::ERRLOG_FILENAME.date('Ymd'));
			return false;
		}
			
		$isChanged = false;
		$intAdditionMpChanged = 0;
		$dalFloor = Happyfish_Magic_Dal_Floors::getDefaultInstance();
		if ( $aryFloor['aryShowFloor'] && count($aryFloor['aryShowFloor']) > 0 ) {
			$rowFloorOld = $dalFloor->getUserFloors($uid);
			$lstFloorNow = Zend_Json::decode($rowFloorOld['floor_decor']);
			$lstFloorBagOld = $dalFloor->lstUserFloorInBag($uid);
			if (empty($lstFloorBagOld) || count($lstFloorBagOld) < 1) {
				info_log($uid.'[Happyfish_Magic_Bll_Decoration]-[changeDecoration]-validateCheck:floor no free floor to change', self::ERRLOG_FILENAME.date('Ymd'));
				return false;
			}
			$aryFloorBagOldNum = array();
			$numOldAll = 0;
			foreach ($lstFloorBagOld as $key=>$value) {
				$fid = $value['floor_id'];
				$aryFloorBagOldNum[$fid] = $value['quantity'];
				$numOldAll += $value['quantity'];
			}
			if (empty($numOldAll)) {
				info_log($uid.'[Happyfish_Magic_Bll_Decoration]-[changeDecoration]-validateCheck:floor 0 free floor to change', self::ERRLOG_FILENAME.date('Ymd'));
				return false;
			}
			//how many changed floors
			$aryFloorBagChangeNum = array();
			foreach ($aryFloor['aryShowFloor'] as $key=>$data) {
				$x = $data['pos_x'];
				$z = $data['pos_z'];
				if ( $x > ($limitSize-1) || $z > ($limitSize-1) ) {
					info_log($uid.'[Happyfish_Magic_Bll_Decoration]-[changeDecoration]-validateCheck:floor position limited', self::ERRLOG_FILENAME.date('Ymd'));
					return false;
				}
				$floorIdOld = $lstFloorNow[$x][$z];
				$floorIdNew = $data['floor_id'];
				//no change happened
				if ($floorIdOld == $floorIdNew) {
					continue;
				}
				//old +1
				if (!array_key_exists($floorIdOld, $aryFloorBagChangeNum)) {
					$aryFloorBagChangeNum[$floorIdOld] = 0;
				}
				$aryFloorBagChangeNum[$floorIdOld] += 1;
				//new -1 
				if (!array_key_exists($floorIdNew, $aryFloorBagChangeNum)) {
					$aryFloorBagChangeNum[$floorIdNew] = 0;
				}
				$aryFloorBagChangeNum[$floorIdNew] -= 1;
				//replace new floor 
				$lstFloorNow[$x][$z] = $floorIdNew;
				//calc additionmp changed
				$intAdditionMpChanged -= $lstNbInfo[$floorIdOld]['effect_mp'];
				$intAdditionMpChanged += $lstNbInfo[$floorIdNew]['effect_mp'];
			}
			
			//do validate
			$aryFloorBagNewNum = array();
			$totalOld = $totalNew = 0;
			foreach ($aryFloorBagChangeNum as $fid=>$change) {
				$aryFloorBagNewNum[$fid] = $aryFloorBagOldNum[$fid] + $change;
				if ($aryFloorBagNewNum[$fid] < 0) {
					info_log($uid.'[Happyfish_Magic_Bll_Decoration]-[changeDecoration]-validateCheck:move floor num not enough', self::ERRLOG_FILENAME.date('Ymd'));
					return false;
				}
				$totalOld += $aryFloorBagOldNum[$fid];
				$totalNew += $aryFloorBagNewNum[$fid];
			}
			if ($totalOld != $totalNew) {
				info_log($uid.'[Happyfish_Magic_Bll_Decoration]-[changeDecoration]-validateCheck:floor num not match', self::ERRLOG_FILENAME.date('Ymd'));
				return false;
			}
			$isChanged = true;
		}
		
		//deal sell floor
		$numSellRed = $numSellBlue = $numSellGreen = 0;
		if ( $aryFloor['arySellFloor'] && count($aryFloor['arySellFloor']) > 0 ) {
			//has changed floor
			if ($isChanged) {
				$remainNum = isset($aryFloorBagNewNum) ? $aryFloorBagNewNum : $aryFloorBagOldNum;
				foreach ($aryFloorBagOldNum as $oldKey=>$oldNum) {
					if (!array_key_exists($oldKey, $remainNum)) {
						$remainNum[$oldKey] = $oldNum;
					}
				}
			}
			//no changed floor
			else {
				$lstFloorBagOld = $dalFloor->lstUserFloorInBag($uid);
				$remainNum = array();
				foreach ($lstFloorBagOld as $value) {
					$fid = $value['floor_id'];
					$remainNum[$fid] = $value['quantity'];
				}
				$aryFloorBagChangeNum = array();
			}
			
			//how many floors to sell
			foreach ($aryFloor['arySellFloor'] as $sellFloor) {
				$fid = $sellFloor['floor_id'];
				if ( $sellFloor['quantity'] > $remainNum[$fid] ) {
					info_log($uid.'[Happyfish_Magic_Bll_Decoration]-[changeDecoration]-validateCheck:floor sell num not enough', self::ERRLOG_FILENAME.date('Ymd'));
					return false;
				}
				if (!array_key_exists($fid, $aryFloorBagChangeNum)) {
					$aryFloorBagChangeNum[$fid] = 0;
				}
				$aryFloorBagChangeNum[$fid] -= (int)$sellFloor['quantity'];
				$nbInfo = $lstNbInfo[$fid];
				$numSellRed += ($nbInfo['sell_red']*(int)$sellFloor['quantity']);
				$numSellBlue += ($nbInfo['sell_blue']*(int)$sellFloor['quantity']);
				$numSellGreen += ($nbInfo['sell_green']*(int)$sellFloor['quantity']);
			}
		}
		
		if ($aryFloorBagChangeNum && count($aryFloorBagChangeNum) > 0) {
			//update floor
			if ($isChanged && $lstFloorNow) {
				$dalFloor->update(array('floor_decor' => Zend_Json::encode($lstFloorNow)), $uid);
			}
			
			//update floor in bag 
			foreach ($aryFloorBagChangeNum as $fid=>$change) {
				$rowFloorInBag = $dalFloor->getUserFloorInBag($uid, $fid);
				if (empty($rowFloorInBag)) {
					$dalFloor->insertUserFloorInBag(array('uid' => $uid, 'floor_id' => $fid, 'quantity' => $change));
				}
				else {
					if ($change) {
						$dalFloor->updateUserFloorInBagByField($uid, $fid, 'quantity', $change);
					}
				}
			}
			
			//update user info
			if ($numSellRed || $numSellBlue || $numSellGreen) {
				$dalUser = Happyfish_Magic_Dal_User::getDefaultInstance();
				$aryParam = array('red'=>$numSellRed,'blue'=>$numSellBlue,'green'=>$numSellGreen);
				$dalUser->updateUserByMultipleField($uid, $aryParam);
			}
		}
		
		return array('red' => $numSellRed, 'blue' => $numSellBlue, 'green' => $numSellGreen, 'mp_addition' => $intAdditionMpChanged);
	}

	
	private static function _changeWall($uid, $aryWall, $rowUser, $lstNbInfo)
	{
		$limitSize = $rowUser['nbLevInfo']['house_size'];
		//check wall count
		if ( count($aryWall['aryShowWall']) > $limitSize*$limitSize ) {
			info_log($uid.'[Happyfish_Magic_Bll_Decoration]-[changeDecoration]-validateCheck:wall count overflow', self::ERRLOG_FILENAME.date('Ymd'));
			return false;
		}
			
		$isChanged = false;
		$intAdditionMpChanged = 0;
		$dalWall = Happyfish_Magic_Dal_Walls::getDefaultInstance();
		if ( $aryWall['aryShowWall'] && count($aryWall['aryShowWall']) > 0 ) {
			$rowWallOld = $dalWall->getUserWalls($uid);
			$lstWallNow = Zend_Json::decode($rowWallOld['wall_decor']);
			$lstWallBagOld = $dalWall->lstUserWallInBag($uid);
			if (empty($lstWallBagOld) || count($lstWallBagOld) < 1) {
				info_log($uid.'[Happyfish_Magic_Bll_Decoration]-[changeDecoration]-validateCheck:wall no free wall to change', self::ERRLOG_FILENAME.date('Ymd'));
				return false;
			}
			$aryWallBagOldNum = array();
			$numOldAll = 0;
			foreach ($lstWallBagOld as $key=>$value) {
				$fid = $value['wall_id'];
				$aryWallBagOldNum[$fid] = $value['quantity'];
				$numOldAll += $value['quantity'];
			}
			if (empty($numOldAll)) {
				info_log($uid.'[Happyfish_Magic_Bll_Decoration]-[changeDecoration]-validateCheck:wall 0 free wall to change', self::ERRLOG_FILENAME.date('Ymd'));
				return false;
			}
			//how many changed walls
			$aryWallBagChangeNum = array();
			foreach ($aryWall['aryShowWall'] as $key=>$data) {
				$x = $data['pos_x'];
				$z = $data['pos_z'];
				if ( $x > ($limitSize-1) || $z > ($limitSize-1) ) {
					info_log($uid.'[Happyfish_Magic_Bll_Decoration]-[changeDecoration]-validateCheck:wall position limited', self::ERRLOG_FILENAME.date('Ymd'));
					return false;
				}
				$wallIdOld = $lstWallNow[$x][$z];
				$wallIdNew = $data['wall_id'];
				//no change happened
				if ($wallIdOld == $wallIdNew) {
					continue;
				}
				//old +1
				if (!array_key_exists($wallIdOld, $aryWallBagChangeNum)) {
					$aryWallBagChangeNum[$wallIdOld] = 0;
				}
				$aryWallBagChangeNum[$wallIdOld] += 1;
				//new -1 
				if (!array_key_exists($wallIdNew, $aryWallBagChangeNum)) {
					$aryWallBagChangeNum[$wallIdNew] = 0;
				}
				$aryWallBagChangeNum[$wallIdNew] -= 1;
				//replace new wall 
				$lstWallNow[$x][$z] = $wallIdNew;
				//calc additionmp changed
				$intAdditionMpChanged -= $lstNbInfo[$wallIdOld]['effect_mp'];
				$intAdditionMpChanged += $lstNbInfo[$wallIdNew]['effect_mp'];
			}
			
			//do validate
			$aryWallBagNewNum = array();
			$totalOld = $totalNew = 0;
			foreach ($aryWallBagChangeNum as $fid=>$change) {
				$aryWallBagNewNum[$fid] = $aryWallBagOldNum[$fid] + $change;
				if ($aryWallBagNewNum[$fid] < 0) {
					info_log($uid.'[Happyfish_Magic_Bll_Decoration]-[changeDecoration]-validateCheck:move wall num not enough', self::ERRLOG_FILENAME.date('Ymd'));
					return false;
				}
				$totalOld += $aryWallBagOldNum[$fid];
				$totalNew += $aryWallBagNewNum[$fid];
			}
			if ($totalOld != $totalNew) {
				info_log($uid.'[Happyfish_Magic_Bll_Decoration]-[changeDecoration]-validateCheck:wall num not match', self::ERRLOG_FILENAME.date('Ymd'));
				return false;
			}
			$isChanged = true;
		}
		
		//deal sell wall
		$numSellRed = $numSellBlue = $numSellGreen = 0;
		if ( $aryWall['arySellWall'] && count($aryWall['arySellWall']) > 0 ) {
			//has changed wall
			if ($isChanged) {
				$remainNum = isset($aryWallBagNewNum) ? $aryWallBagNewNum : $aryWallBagOldNum;
				foreach ($aryWallBagOldNum as $oldKey=>$oldNum) {
					if (!array_key_exists($oldKey, $remainNum)) {
						$remainNum[$oldKey] = $oldNum;
					}
				}
				//$remainNum = array_merge($aryWallBagOldNum, $remainNum);
			}
			//no changed wall
			else {
				$lstWallBagOld = $dalWall->lstUserWallInBag($uid);
				$remainNum = array();
				foreach ($lstWallBagOld as $key=>$value) {
					$fid = $value['wall_id'];
					$remainNum[$fid] = $value['quantity'];
				}
				$aryWallBagChangeNum = array();
			}
			
			//how many walls to sell
			foreach ($aryWall['arySellWall'] as $sellWall) {
				$fid = $sellWall['wall_id'];
				if ( $sellWall['quantity'] > $remainNum[$fid] ) {
					info_log($uid.'[Happyfish_Magic_Bll_Decoration]-[changeDecoration]-validateCheck:wall sell num not enough', self::ERRLOG_FILENAME.date('Ymd'));
					return false;
				}
				if (!array_key_exists($fid, $aryWallBagChangeNum)) {
					$aryWallBagChangeNum[$fid] = 0;
				}
				$aryWallBagChangeNum[$fid] -= (int)$sellWall['quantity'];
				$nbInfo = $lstNbInfo[$fid];
				$numSellRed += ($nbInfo['sell_red']*(int)$sellWall['quantity']);
				$numSellBlue += ($nbInfo['sell_blue']*(int)$sellWall['quantity']);
				$numSellGreen += ($nbInfo['sell_green']*(int)$sellWall['quantity']);
			}
		}
		
		if ($aryWallBagChangeNum && count($aryWallBagChangeNum) > 0) {
			//update wall
			if ($isChanged && $lstWallNow) {
				$dalWall->update(array('wall_decor' => Zend_Json::encode($lstWallNow)), $uid);
				/*foreach ($lstWallNow as $row1) {
					foreach ($row1 as $data1) {
						$intAdditionMp += $lstNbInfo[$data1]['effect_mp'];;
					}
				}*/
			}
			
			//update wall in bag 
			foreach ($aryWallBagChangeNum as $fid=>$change) {
				$rowWallInBag = $dalWall->getUserWallInBag($uid, $fid);
				if (empty($rowWallInBag)) {
					$dalWall->insertUserWallInBag(array('uid' => $uid, 'wall_id' => $fid, 'quantity' => $change));
				}
				else {
					if ($change) {
						$dalWall->updateUserWallInBagByField($uid, $fid, 'quantity', $change);
					}
				}
			}
			
			//update user info
			if ($numSellRed || $numSellBlue || $numSellGreen) {
				$dalUser = Happyfish_Magic_Dal_User::getDefaultInstance();
				$aryParam = array('red'=>$numSellRed,'blue'=>$numSellBlue,'green'=>$numSellGreen);
				$dalUser->updateUserByMultipleField($uid, $aryParam);
			}
		}
		
		return array('red' => $numSellRed, 'blue' => $numSellBlue, 'green' => $numSellGreen, 'mp_addition' => $intAdditionMpChanged);
	}
	
	
	public function reCalcBuildingAdditionMp($uid)
	{
		$lstNbInfo = Happyfish_Magic_Bll_Cache_NbBasicInfo::listNbBuilding();
		
		$dalBuilding = Happyfish_Magic_Dal_Building::getDefaultInstance();
		$lstData = $dalBuilding->lstUserBuilding($uid);
		$intAdditionMpB = 0;
		foreach ($lstData as $bdata) {
			if ($bdata['status']) {
				$intAdditionMpB += $lstNbInfo[$bdata['building_id']]['effect_mp'];
			}
		}
		
		$dalFloor = Happyfish_Magic_Dal_Floors::getDefaultInstance();
    	$rowFloor = $dalFloor->getUserFloors($uid);
		$lstFloor = Zend_Json::decode($rowFloor['floor_decor']);
		$intAdditionMpF = 0;
    	foreach ($lstFloor as $row1) {
			foreach ($row1 as $data1) {
				$intAdditionMpF += $lstNbInfo[$data1]['effect_mp'];;
			}
		}
		
    	$dalWall = Happyfish_Magic_Dal_Walls::getDefaultInstance();
    	$rowWall = $dalWall->getUserWalls($uid);
		$lstWall = Zend_Json::decode($rowWall['wall_decor']);
		$intAdditionMpW = 0;
    	foreach ($lstWall as $row1) {
			foreach ($row1 as $data1) {
				$intAdditionMpW += $lstNbInfo[$data1]['effect_mp'];;
			}
		}
		
		$dalUser = Happyfish_Magic_Dal_User::getDefaultInstance();
    	$dalUser->updateUser(array('mp_addition'=>($intAdditionMpB + $intAdditionMpF + $intAdditionMpW)), $uid);
    	Happyfish_Magic_Bll_Cache_User::clearAppUser($uid);
    	return array($intAdditionMpB,$intAdditionMpF,$intAdditionMpW);
	}
	
	
}