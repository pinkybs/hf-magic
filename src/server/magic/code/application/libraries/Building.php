<?php defined ( 'SYSPATH' ) or die ( 'No direct access allowed.' );
class Building {
	//单例
	private static $instance;
	private $role_id;
	const ERRLOG_FILENAME = 'decoration-';
	
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
			self::$instance[$role_id] = new Building($role_id);
		}

		return self::$instance[$role_id];
	}
	
	/**
	 * get user buildinglist & floors & walls
	 *
	 * @param integer $role_id
	 * @param integer $mode [//1-in bag 0-in room]
	 * @return array
	 */
	public static function getDecoration($role_id, $mode = 0)
	{
		//buliding info *
		$building_model = Building_Model::instance($role_id);
		$lstData = $building_model->getUserBuildingList();
        $aryRst = $aryData0 = $aryData1 = array();
		foreach ($lstData as $key=>$vdata) {
			$vdata = $vdata[0];
			//in bag
			if ( $vdata['bag_type'] == 1 ) {
        		$aryData1[] = $lstData[$key];
        	}
        	else {
        		$aryData0[] = $lstData[$key];
        	}
		}
        $aryRst['buildingList'] = $mode ? $aryData1 : $aryData0;

        //floor info **
        $floor_model = Floor_Model::instance($role_id);
        $floor_inbag_model = Floor_Inbag_Model::instance($role_id);
        //in decor
        if ( $mode != 1 ) {
	        $rowFloors = $floor_model->getDataByRoleId();
	        if (empty($rowFloors)) {
	        	$aryRst['floorList'] = null;
	        }
	        else {
	        	$aryRst['floorList'] = json_decode($rowFloors['data']);
	        }
        }
        //in bag
        else {
        	$lstFloors = $floor_inbag_model->getUserFloorInBag();
        	$aryFloors = array();
        	foreach ($lstFloors as $floorData) {
        		$floorData = $floorData[0];
        		if ($floorData['quantity']) {
        			$aryFloors[] = $floorData;
        		}
        	}
        	$aryRst['floorList'] = $aryFloors;
        }
        
		//wall info ***
        $wall_model = Wall_Model::instance($role_id);
        $wall_inbag_model = Wall_Inbag_Model::instance($role_id);
        //in decor
        if ( $mode != 1 ) {
	        $rowWalls = $wall_model->getDataByRoleId($role_id);
	        if (empty($rowWalls)) {
	        	$aryRst['wallList'] = null;
	        }
	        else {
	        	$aryRst['wallList'] = json_decode($rowWalls['data']);
	        }
        }
        //in bag
        else {
        	$lstWalls = $wall_inbag_model->getUserWallInBag($role_id);
        	$aryWalls = array();
        	foreach ($lstWalls as $wallData) {
        		$wallData = $wallData[0];
        		if ($wallData['quantity']) {
        			$aryWalls[] = $wallData;
        		}
        	}
        	$aryRst['wallList'] = $aryWalls;
        }
        
        return $aryRst;
	}
	
	public static function changeDecoration($role_id, $aryBuilding, $aryFloor, $aryWall)
	{
		$rst = array('status' => -1);
		//basic building info
		$basic_model = new Basic_Model();
		$getLstNbInfo = $basic_model->getBuildingList();
		$lstNbInfo = array();
		foreach ( $getLstNbInfo as $key=>$data ) {
			$lstNbInfo[$data[0]['id']] = $data[0];
		}
		
		$role = Role::create($role_id);
		
		//user info
		$userLevel = $role->get('level');
		$basicLevelInfo = usual::getLevelConfig($userLevel);
		$rowUser = array('role_id' => $role_id, 'basicLevelInfo' => $basicLevelInfo);
		
		//change building *
		$rst1 = array('status' => 1);
		if ($aryBuilding && count($aryBuilding) > 0) {
			$rst1 = self::_changeBuild($role_id, $aryBuilding, $rowUser, $lstNbInfo);
			if ($rst1['status'] != 1) {
				return $rst1;
			}
		}
		
		//change floor **
		$rst2 = array('status' => 1);
		if ( ($aryFloor['aryShowFloor'] && count($aryFloor['aryShowFloor']) > 0)
			 || ($aryFloor['arySellFloor'] && count($aryFloor['arySellFloor']) > 0) ) {
			$rst2 = self::_changeFloor($role_id, $aryFloor, $rowUser, $lstNbInfo);	 
			if ($rst2['status'] != 1) {
				return $rst2;
			}
		}
		
		//change wall ***
		$rst3 = array('status' => 1);
		if ( ($aryWall['aryShowWall'] && count($aryWall['aryShowWall']) > 0)
			 || ($aryWall['arySellWall'] && count($aryWall['arySellWall']) > 0) ) {
			$rst3 = self::_changeWall($role_id, $aryWall, $rowUser, $lstNbInfo);
			if ($rst3['status'] != 1) {
				return $rst3;
			}
		}
		if ( $rst1['status'] == 1 && $rst2['status'] == 1 && $rst3['status'] == 1 ) {
			$rst['status'] = 1;
		}
		
		return $rst;
	}
	
	private static function _changeBuild($role_id, $aryBuilding, $rowUser, $lstNbInfo)
	{
		$result = array('status' => -1);
		$limitSize = $rowUser['basicLevelInfo']['tile_size'];
		
		//check building validate (repeat position submit)
		$aryTmp = array();
		$aryCheckDesk = array();
		$aryToBagDesk = array();
		foreach ($aryBuilding as $cdata) {
			if ($cdata['x'] > $limitSize || $cdata['z'] > $limitSize ) {
				//var_dump('log-_changeBuild:error desk size');
				$result['content'] = 'error_desk_size';
				return $result;
			}
			if (1 == $cdata['bag_type']) {
				//desk
				if (1 == $lstNbInfo[$cdata['building_id']]['type']) {
					$aryToBagDesk[] = $cdata['id'];
				}
				continue;
			}
			$pos = $cdata['x'] . '|' . $cdata['y'] . '|' . $cdata['z'];
			if (in_array($pos, $aryTmp)) {
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
		$building_model = Building_Model::instance($role_id);
		$lstData = $building_model->getUserBuildingList();
		
		$lstBuildingOld = array();
		$intAdditionMpChanged = 0;
		foreach ($lstData as $vdata) {
			$vdata = $vdata[0];
			$lstBuildingOld[$vdata['id']] = array('id' => $vdata['id'],
        	                                   'building_id' => $vdata['building_id'],
        	                                   'x' => $vdata['x'],
        	                                   'y' => $vdata['y'],
			  								   'z' => $vdata['z'],
        	                                   'mirror' => $vdata['mirror'],
        	                                   'bag_type' => $vdata['bag_type']);
			//check desk count limit in room
			if ($vdata['bag_type'] != 1 && $lstNbInfo[$vdata['building_id']]['type'] == 1) {
				if (!in_array($vdata['id'], $aryCheckDesk) && !in_array($vdata['id'], $aryToBagDesk)) {
					$aryCheckDesk[] = $vdata['id'];
				}
			}
		}
		
		//check desk limit
		$role = Role::create($role_id);
		$cur_level_data = Basic::getHouseLevelData($role->get('house_level'));
		$limit_desk = $cur_level_data['desk_limit'];		
		if (count($aryCheckDesk) > $limit_desk) {//$rowUser['basicLevelInfo']['limit_desk']
			$result['content'] = 'error_limit_desk';
			return $result;
		}
		
		$numSellRed = $numSellBlue = $numSellGreen = 0;
		//do update building
		foreach ($aryBuilding as $vdata) {
			//check building is in bag
			if ( array_key_exists($vdata['id'], $lstBuildingOld) ) {
				$rowBuildingOld = $lstBuildingOld[$vdata['id']];
				$nbInfo = $lstNbInfo[$rowBuildingOld['building_id']];
				//check is floor or wall
				if (3 == $nbInfo['type'] || 4 == $nbInfo['type']) {
					return false;
				}
				//check is validate(door wallpaper walldeco)
				/*
				if ( (2 == $nbInfo['type'] || 7 == $nbInfo['type']) && 0 == $vdata['bag_type'] ) {
					if ($vdata['x'] && $vdata['z']) {
						var_dump('err_x_y');
						return false;
					}
				}*/
				
				//sell the building
				if (isset($vdata['sell']) && $vdata['sell'] == 1) {
					$numSellRed += $nbInfo['sell_coin'];
					$building_model->deleteBuildingById($vdata['id']);
					
					//if old building is in room
					if ( $rowBuildingOld['bag_type'] != 1 ) {
						$intAdditionMpChanged -= $nbInfo['effect_mp'];
						//out desk
						if ( 1 == $nbInfo['type'] ) {
							$student = Student::instance($role_id);
							$student->moveOutDesk($vdata['id']);
						}
					}
					
					//if delete door
					if ( 2 == $nbInfo['type'] ) {
						$door_task_model = Door_Task_Model::instance($role_id);
						$door_task_model->deleteDoorTask($vdata['id']);
					}
				}
				//update building
				else {
					//check is need to update
					if ( http_build_query($vdata) == http_build_query($rowBuildingOld) ) {
						continue;
					}
					//update
					$newBuilding = array('role_id' => $role_id,
										 'x'=>$vdata['x'],
										 'y'=>$vdata['y'],
										 'z'=>$vdata['z'],
				                         'mirror'=>$vdata['mirror'],
				                         'bag_type'=>$vdata['bag_type']);
					$building_model->updateBuildingById($newBuilding, $vdata['id']);
					
					//put building into room from bag
					if ( $rowBuildingOld['bag_type'] == 1 && $vdata['bag_type'] != 1 ) {
						$intAdditionMpChanged += $nbInfo['effect_mp'];
						
						//add door
						if ( 2 == $nbInfo['type']) {
							$door_task_model = Door_Task_Model::instance($role_id);
							$nowTime = PEAR::getStaticProperty('_APP', 'timestamp');
							$doorInfo = $door_task_model->getDataById($vdata['id']);
							if ( empty($doorInfo) ) {
								$newDoorTask = array('id' => $vdata['id'], 
													 'role_id' => $role_id,
													 'left_students_num' => $nbInfo['door_guest_limit'],
													 'start_time' => $nowTime,
													 'end_time' => $nowTime + $nbInfo['door_cooldown']);
								$door_task_model->insertDoorTask($newDoorTask);
							}
						}//add desk
						else if ( 1 == $nbInfo['type'] ) {
							$student = Student::instance($role_id);
							$student->addDesk($vdata['id']);
						}
					}
					//put building into bag from room
					else if ( $rowBuildingOld['bag_type'] != 1 && $vdata['bag_type'] == 1 ) {
						$intAdditionMpChanged -= $nbInfo['effect_mp'];
						//out desk
						if ( 1 == $nbInfo['type'] ) {
							$student = Student::instance($role_id);
							$student->moveOutDesk($vdata['id']);
						}
					}
				}//end deal one row
			}
		}//end update building
		
		$role = Role::create($role_id);
		$role->increment('max_mp_add', $intAdditionMpChanged);
		//update user info
//		if ( $numSellRed || $numSellBlue || $numSellGreen ) {
//			$role->increment('corin', $numSellRed);
//			$role->increment('blue', $numSellBlue);
//			$role->increment('green', $numSellGreen);
//		}
		
		return array('status' => 1);
	}
	
	private static function _changeFloor($role_id, $aryFloor, $rowUser, $lstNbInfo)
	{
		$result = array('status' => -1);
		$limitSize = $rowUser['basicLevelInfo']['tile_size'];
		//check floor count
		if ( count($aryFloor['aryShowFloor']) > $limitSize*$limitSize ) {
			$result['content'] = 'limit_floor_count';
			return $result;
		}
		
		$isChanged = false;
		$intAdditionMpChanged = 0;
		
		$floor_model = Floor_Model::instance($role_id);
		$floor_inbag_model = Floor_Inbag_Model::instance($role_id);
		$lstFloorBagOld = $floor_inbag_model->getUserFloorInBag($role_id);
		
		if ( $aryFloor['aryShowFloor'] && count($aryFloor['aryShowFloor']) > 0 ) {
			$rowFloorOld = $floor_model->getDataByRoleId();
			$lstFloorNow = json_decode($rowFloorOld['data']);
			if (empty($lstFloorBagOld)) {
				return false;
			}
			$aryFloorBagOldNum = array();
			$numOldAll = 0;
			foreach ($lstFloorBagOld as $key=>$value) {
				$value = $value[0];
				$fid = $value['floor_id'];
				$aryFloorBagOldNum[$fid] = $value['quantity'];
				$numOldAll += $value['quantity'];
			}
			if (empty($numOldAll)) {
				return false;
			}
			//how many changed floors
			$aryFloorBagChangeNum = array();
			foreach ($aryFloor['aryShowFloor'] as $key=>$data) {
				$x = $data['x'];
				$z = $data['z'];
				if ( $x > ($limitSize-1) || $z > ($limitSize-1) ) {
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
					return false;
				}
				if (!array_key_exists($fid, $aryFloorBagChangeNum)) {
					$aryFloorBagChangeNum[$fid] = 0;
				}
				$aryFloorBagChangeNum[$fid] -= (int)$sellFloor['quantity'];
				$nbInfo = $lstNbInfo[$fid];
				$numSellRed += ($nbInfo['sell_coin']*(int)$sellFloor['quantity']);
				//$numSellBlue += ($nbInfo['sell_blue']*(int)$sellFloor['quantity']);
				//$numSellGreen += ($nbInfo['sell_green']*(int)$sellFloor['quantity']);
			}
		}
		
		if ($aryFloorBagChangeNum && count($aryFloorBagChangeNum) > 0) {
			//update floor
			if ($isChanged && $lstFloorNow) {
				$floor_model->updateFloorByUid(array('data' => json_encode($lstFloorNow)), $role_id);
			}
			
			//update floor in bag 
			foreach ($aryFloorBagChangeNum as $fid=>$change) {
				$rowFloorInBag = $floor_inbag_model->getUserFloorInBagByFid($fid);
				if (empty($rowFloorInBag)) {
					$newUserFloor = array('role_id' => $role_id, 'floor_id' => $fid, 'quantity' => $change);
					$floor_inbag_model->insertUserFloorInBag($newUserFloor);
				}
				else {
					if ($change) {
						$changeFloor = array('quantity' => $change);
						$floor_inbag_model->updateUserFloorInBagByField($role_id, $fid, $changeFloor);
					}
				}
			}
			
			//update user info
			$role = Role::create($role_id);
			$role->increment('max_mp_add', $intAdditionMpChanged);
//			if ($numSellRed || $numSellBlue || $numSellGreen) {
//				$role->increment('red', $numSellRed);
//				$role->increment('blue', $numSellBlue);
//				$role->increment('green', $numSellGreen);
//			}
		}
		
		return array('status' => 1);
	}

	private static function _changeWall($role_id, $aryWall, $rowUser, $lstNbInfo)
	{
		$result = array('status' => -1);
		$limitSize = $rowUser['basicLevelInfo']['tile_size'];
		//check wall count
		if ( count($aryWall['aryShowWall']) > $limitSize*$limitSize ) {
			$result['content'] = 'limit_wall_count';
			return $result;
		}
		
		$isChanged = false;
		$intAdditionMpChanged = 0;
		$wall_model = Wall_Model::instance($role_id);
		$wall_inbag_model = Wall_Inbag_Model::instance($role_id);
		
		if ( $aryWall['aryShowWall'] && count($aryWall['aryShowWall']) > 0 ) {
			$rowWallOld = $wall_model->getDataByRoleId($role_id);
			$lstWallNow = json_decode($rowWallOld['data']);
			$lstWallBagOld = $wall_inbag_model->getUserWallInBag($role_id);
			if (empty($lstWallBagOld)) {
				//var_dump('empty_bag');
				return false;
			}
			$aryWallBagOldNum = array();
			$numOldAll = 0;
			foreach ($lstWallBagOld as $key=>$value) {
				$value = $value[0];
				$fid = $value['wall_id'];
				$aryWallBagOldNum[$fid] = $value['quantity'];
				$numOldAll += $value['quantity'];
			}
			if (empty($numOldAll)) {
				//var_dump('empty_bag_2');
				return false;
			}
			//how many changed walls
			$aryWallBagChangeNum = array();
			foreach ($aryWall['aryShowWall'] as $key=>$data) {
				$x = $data['x'];
				$z = $data['z'];
				if ( $x > ($limitSize-1) || $z > ($limitSize-1) ) {
					//var_dump('error_size');
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
				if(!isset($aryWallBagOldNum[$fid])) {
					break;
				}
				$aryWallBagNewNum[$fid] = $aryWallBagOldNum[$fid] + $change;
				if ($aryWallBagNewNum[$fid] < 0) {
					//var_dump($fid);
					//var_dump('error_new');
					return false;
				}
				$totalOld += $aryWallBagOldNum[$fid];
				$totalNew += $aryWallBagNewNum[$fid];
			}
			if ($totalOld != $totalNew) {
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
			}
			//no changed wall
			else {
				$lstWallBagOld = $wall_inbag_model->getUserWallInBag($role_id);
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
					return false;
				}
				if (!array_key_exists($fid, $aryWallBagChangeNum)) {
					$aryWallBagChangeNum[$fid] = 0;
				}
				$aryWallBagChangeNum[$fid] -= (int)$sellWall['quantity'];
				$nbInfo = $lstNbInfo[$fid];
				$numSellRed += ($nbInfo['sell_coin']*(int)$sellWall['quantity']);
//				$numSellBlue += ($nbInfo['sell_blue']*(int)$sellWall['quantity']);
//				$numSellGreen += ($nbInfo['sell_green']*(int)$sellWall['quantity']);
			}
		}
		
		if ($aryWallBagChangeNum && count($aryWallBagChangeNum) > 0) {
			//update wall
			if ($isChanged && $lstWallNow) {
				$wall_model->updateWallByUid(array('data' => json_encode($lstWallNow)), $role_id);
			}
			
			//update wall in bag 
			foreach ($aryWallBagChangeNum as $fid=>$change) {
				$rowWallInBag = $wall_inbag_model->getUserWallInBagById($fid);
				if (empty($rowWallInBag)) {					
					$newWall = array('role_id' => $role_id, 'wall_id' => $fid, 'quantity' => $change);
					$wall_inbag_model->insertUserWallInBag($newWall);
				}
				else {
					if ($change) {
						$changArray = array('quantity' => $change);
						$wall_inbag_model->updateUserWallInBagByField($role_id, $fid, $changArray);
					}
				}
			}
			
			//update user info
			$role = Role::create($role_id);
			$role->increment('max_mp_add', $intAdditionMpChanged);
//			if ($numSellRed || $numSellBlue || $numSellGreen) {
//				$role->increment('red', $numSellRed);
//				$role->increment('blue', $numSellBlue);
//				$role->increment('green', $numSellGreen);
//			}
		}
		
		return array('status' => 1);
	}
	
}