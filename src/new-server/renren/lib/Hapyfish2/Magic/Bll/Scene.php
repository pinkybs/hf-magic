<?php

class Hapyfish2_Magic_Bll_Scene
{
    public static function getData($uid, &$sceneType)
    {
        $userVo = Hapyfish2_Magic_Bll_User::getUserInit($uid);
        $mapId = $userVo['currentSceneId'];
        if ($mapId == HOME_SCENE_ID) {
            $sceneType = 3;
            return self::getHomeData($uid);
        }

        return self::getMapData($uid, $mapId, $sceneType);
    }

    //家/self or friend
	public static function getHomeData($uid, $fid = null, $checkScene = false)
	{
		if (empty($fid)) {
			$fid = $uid;
		}

		if ($uid == $fid) {
			$isHome = true;
		} else {
			$isHome = false;
		}

		//get user info
        $userVo = Hapyfish2_Magic_Bll_User::getUserInit($fid);

		$buildingData = Hapyfish2_Magic_HFC_Building::getInScene($fid);
		$doorData = Hapyfish2_Magic_HFC_Door::getInScene($fid);
		$deskData = Hapyfish2_Magic_HFC_Desk::getInScene($fid);
		$t = time();
		$decorList = array();
		if (!empty($buildingData)) {
			foreach ($buildingData as $b) {
				$decorList[] = array(
					'id' => $b['id'],
					'x' => $b['x'],
					'y' => $b['y'],
					'z' => $b['z'],
					'mirror' => $b['mirro'],
					'bag_type' => ($b['status'] == 0 ? 1 : 0),
					'd_id' => $b['cid'],
					'type' => $b['item_type']
				);
			}
		}
		if (!empty($doorData)) {
			foreach ($doorData['doors'] as $d) {
				$decorList[] = array(
					'id' => $d['id'],
					'x' => $d['x'],
					'y' => $d['y'],
					'z' => $d['z'],
					'mirror' => $d['mirro'],
					'bag_type' => ($d['status'] == 0 ? 1 : 0),
					'd_id' => $d['cid'],
					'type' => $d['item_type'],
					'door_left_students_num' => $d['left_student_num'],
					'door_left_time' => $d['end_time'] - $t
				);
			}
		}

		$studentList1 = array();

		if (!empty($deskData)) {
			foreach ($deskData['desks'] as $d) {
				$decorList[] = array(
					'id' => $d['id'],
					'x' => $d['x'],
					'y' => $d['y'],
					'z' => $d['z'],
					'mirror' => $d['mirro'],
					'bag_type' => ($d['status'] == 0 ? 1 : 0),
					'd_id' => $d['cid'],
					'type' => $d['item_type']
				);

				//为了符合老结构,有钱没有收掉的也算在studentList中
				if ($d['student_id'] > 0 && $d['coin'] > 0 && $d['end_time'] > 0 && $d['end_time'] <= $t) {
					$std = array(
						'sid' => $d['student_id'],
						'decor_id' => $d['id'],
						'state' => 3,
						'magic_id' => $d['magic_id'],
						'coin' => $d['coin'],
						'time' => 0,
						'event_time' => -1,
						'stone_time' => ($d['stone_time'] - $t < 0) ? 0 : ($d['stone_time'] - $t)
					);
					if (!$isHome) {
						//判断是否偷过
						$moochInfo = Hapyfish2_Magic_Cache_Mooch::getMoochDesk($fid, $d['id']);
						if (!empty($moochInfo) && in_array($uid, $moochInfo)) {
							$canSteal = 0;
		        		} else {
		        			$canSteal = 1;
		        		}
					} else {
						$canSteal = 1;
					}
					$std['can_steal'] = $canSteal;

					$studentList1[$d['id']] = $std;
				}
			}
		}

		$floorList = Hapyfish2_Magic_Cache_Floor::getInScene($fid);
		$wallList = Hapyfish2_Magic_Cache_Wall::getInScene($fid);

		$isRepaired = false;
		//学生
		$studentList = Hapyfish2_Magic_Bll_Student::getInScene($fid);
		if (empty($studentList)) {
			$studentList = array();
		} else if (!empty($studentList1)){
			foreach ($studentList as $k => $std) {
				if (isset($studentList1[$std['decor_id']])) {
					//数据冲突
					//以desk数据为准
					$std1 = Hapyfish2_Magic_HFC_Student::getOne($fid, $std['sid']);
					if ($std1) {
					    if ($std1['state']==2 || $std1['state']==4) {
					        info_log($fid.' repair 1 student in coin desk:'.json_encode($std1), 'repairstudent');
						    $std1['state'] = 3;
						    $std1['decor_id'] = 0;
						    Hapyfish2_Magic_HFC_Student::updateOne($fid, $std['sid'], $std1);
					        unset($studentList[$k]);
					        $isRepaired = true;
					    }
					}
				}
			}
		}
		foreach ($studentList1 as $std) {
			$studentList[] = $std;
		}

		//修复桌子和学生状态不一致的情况
		foreach ($studentList as $k => $std) {
		    if (!$std['decor_id']) {
		        if ($std['state'] == 0) {
		            continue;
		        }
		        if ($std['state'] != 3) {
		            $std1 = Hapyfish2_Magic_HFC_Student::getOne($fid, $std['sid']);
		            if ($std1) {
    		            info_log($fid.' repair student in empty deskid:'.json_encode($std1), 'repairstudent');
    		            $std1['state'] = 3;
    				    $std1['decor_id'] = 0;
    				    Hapyfish2_Magic_HFC_Student::updateOne($fid, $std['sid'], $std1);
    			        unset($studentList[$k]);
    			        $isRepaired = true;
		            }
		        }
		        else {
		            unset($studentList[$k]);
		        }
		    }
		    if ($deskData['desks']) {
    		    foreach ($deskData['desks'] as $d) {
                    if ($d['id'] == $std['decor_id']) {
                        //repair
                        if ($d['student_id']!=$std['sid'] && ($std['state']==2 || $std['state']==4)) {
                            info_log($fid.' repair desk not same with student:'.json_encode($d), 'repairdesk');
                            $d['student_id'] = $std['sid'];
                            $d['magic_id'] = $std['magic_id'];
                            $d['coin'] = $std['coin'];
                            $d['end_time'] = $std['time'] + $t;
                            $d['stone_time'] = $std['stone_time'];
                            Hapyfish2_Magic_HFC_Desk::updateOne($fid, $d['id'], $d);
                        }
                        break;
                    }
    		    }
		    }
		}
	    if ($isRepaired) {
		    sort($studentList);
		}

		//解锁学生信息
		$studentStateList = Hapyfish2_Magic_Bll_Student::getStudentStateList($fid);

        if ($checkScene) {
        	if ($userVo['currentSceneId'] != HOME_SCENE_ID) {
        		if ($isHome) {
        			$userSceneInfo = Hapyfish2_Magic_HFC_User::getUserScene($uid);
        			if ($userSceneInfo['cur_scene_id'] != HOME_SCENE_ID) {
        				$userVo['currentSceneId'] = HOME_SCENE_ID;
        				$userSceneInfo['cur_scene_id'] = HOME_SCENE_ID;
        				Hapyfish2_Magic_HFC_User::updateUserScene($uid, $userSceneInfo, true);
        			}
        		} else {
        			$userVo['currentSceneId'] = HOME_SCENE_ID;
        		}
        	}
        }

		//怪物
		//$monsterList = Hapyfish2_Magic_Bll_Monster::getInScene($fid, $userVo['currentSceneId']);

        $portalList = array();
		if (!$isHome) {
			//派发事件
			$event = array('uid' => $uid, 'fid' => $fid);
			Hapyfish2_Magic_Bll_Event::visitFriend($event);
		}
		else {
		    $lstData = Hapyfish2_Magic_Cache_BasicInfo::getMapCopyTranscriptList(HOME_SCENE_ID);
    		foreach ($lstData['portalList'] as $data) {
    	        $portalList[] = array(
        					'id' => $data['id'],
        					'x' => $data['pos_x'],
        					'z' => $data['pos_z'],
        					'mirror' => $data['mirror'],
        					'targetSceneId' => $data['tar_map_id'],
        					'd_id' => $data['cid']
        				);
    	    }
		}

		$scene = array(
		    'sceneId' => $userVo['currentSceneId'],
			'decorList' => $decorList,
			'studentStates' => $studentStateList,
			'floorList' => $floorList,
			'wallList' => $wallList,
			'students' => $studentList,
			'user' => $userVo,
			'mineList' => array(),
			'monsterList' => array(),
			'portalList' => $portalList
		);

		return $scene;
	}

	//大地图 or 副本场景
	public static function getMapData($uid, $mapId, &$sceneType)
	{
	    if ($mapId == HOME_SCENE_ID) {
	        $sceneType = 3;
            return self::getHomeData($uid, $uid, true);
        }

        //get user info
        $userVo = Hapyfish2_Magic_Bll_User::getUserInit($uid);

        $basicMapInfo = Hapyfish2_Magic_Cache_BasicInfo::getMapAllInfo($mapId);
        //副本地图
        if ($basicMapInfo['type'] == 2) {
            $listVo = Hapyfish2_Magic_Bll_MapCopy::getMapCopyScene($uid, $mapId, $userVo, $basicMapInfo);
            if (!$listVo) {
                return self::getHomeData($uid, $uid, true);
            }
            $scene = array(
    		    'sceneId' => $userVo['currentSceneId'],
    			'user' => $userVo,
    			'mineList' => $listVo['mineList'],
    			'monsterList' => $listVo['monsterList'],
    			'portalList' => $listVo['portalList'],
    			'decorList' => $listVo['decorList'],
    			'floorList' => $listVo['floorList']
    		);
    		$sceneType = 2;
        }
        //大地图
        else {
            $scene = array(
    		    'sceneId' => $mapId,
    			'user' => $userVo,
    			'mineList' => array(),
    			'monsterList' => array(),
    			'portalList' => array(),
    			'decorList' => array(),
    			'floorList' => array()
    		);
    		$sceneType = 1;
        }
        return $scene;
	}

	public static function changeScene($uid, $mapId, $portalId)
	{

	    if ($mapId == HOME_SCENE_ID) {
	        $result = Hapyfish2_Magic_Bll_UserResult::all();
            $result['scene'] = self::getHomeData($uid, $uid, true);
            return $result;
        }

	    //get user info
        $userVo = Hapyfish2_Magic_Bll_User::getUserInit($uid);
        $basicMapInfo = Hapyfish2_Magic_Cache_BasicInfo::getMapAllInfo($mapId);
        //地图副本
        if ($basicMapInfo['type'] == 2) {
            $result = Hapyfish2_Magic_Bll_MapCopy::enterMap($uid, $basicMapInfo, $portalId);
        }
        //大地图
        else {
            $result = self::change($uid, $mapId);
            $sceneType = 1;
            $scene = self::getMapData($uid, $mapId, $sceneType);
            $result['scene'] = $scene;
        }

        return $result;
	}

	public static function getOtherData($uid)
	{
		//get user info
        $userVo = Hapyfish2_Magic_Bll_User::getUserInit($uid);

		//怪物
		$monsterList = Hapyfish2_Magic_Bll_Monster::getInScene($uid, $userVo['currentSceneId']);

		$scene = array(
		    'sceneId' => $userVo['currentSceneId'],
			'decorList' => array(),
			'studentStates' => array(),
			'floorList' => array(),
			'wallList' => array(),
			'students' => array(),
			'user' => $userVo,
			'enemys' => $monsterList
		);

		return $scene;
	}

	public static function getState($uid)
	{
	    $sceneState = array();
        $sceneList = Hapyfish2_Magic_Cache_BasicInfo::getMapSceneList();
        $userScene = Hapyfish2_Magic_HFC_User::getUserScene($uid);
        $data = $userScene['open_scene_list'];
        $tmp = explode(',', $data);
        $openList = array();
        foreach ($tmp as $id) {
        	$openList[$id] = 1;
        }
        foreach ($sceneList as $v) {
        	if (isset($openList[$v['id']])) {
	        	$sceneState[] = array((int)$v['id'], 1);
        	} else {
        		$sceneState[] = array((int)$v['id'], (int)$v['state']);
        	}
        }

        return $sceneState;
	}

	public static function checkRoomLevelUp($uid)
	{
		$userMpInfo = Hapyfish2_Magic_HFC_User::getUserMp($uid);
		if ($userMpInfo && $userMpInfo['max_mp'] > 0) {
			$userLevelInfo = Hapyfish2_Magic_HFC_User::getUserLevel($uid);
			if ($userLevelInfo) {
				$oldLevel = $level = $userLevelInfo['house_level'];
				$houseLevelList = Hapyfish2_Magic_Cache_BasicInfo::getHouseLevelList();
				for($i = 0, $len = count($houseLevelList); $i < $len; $i++) {
					if ($userMpInfo['max_mp'] >= $houseLevelList[$i]['mp']) {
						$level = $houseLevelList[$i]['id'];
					} else {
						break;
					}
				}
				//房间升级
				if ($level > $oldLevel) {
					$userLevelInfo['house_level'] = $level;
					$ok = Hapyfish2_Magic_HFC_User::updateUserLevel($uid, $userLevelInfo);
					if ($ok) {
						Hapyfish2_Magic_Bll_UserResult::setRoomLevelUp($uid, $level - $oldLevel);

						//学生解锁
						Hapyfish2_Magic_Bll_Student::unlockStudent($uid, $oldLevel, $level);

						//刷新当前学生等级列表
						$studentStates = Hapyfish2_Magic_Bll_Student::getStudentStateList($uid);
						Hapyfish2_Magic_Bll_UserResult::addField($uid, 'studentStates', $studentStates);

						//奖励
						$now = time();
						$coin = $gold = 0;
						$items = $decors = array();
						for($i = $oldLevel + 1; $i <= $level; $i++) {
							if ($houseLevelList[$i]['coin'] > 0) {
								$coin += $houseLevelList[$i]['coin'];
							}
							if ($houseLevelList[$i]['gold'] > 0) {
								$gold += $houseLevelList[$i]['gold'];
							}
							if (!empty($houseLevelList[$i]['items'])) {
								$t = json_decode($houseLevelList[$i]['items'], true);
								foreach ($t as $v) {
									if (isset($items[$v[0]])) {
										$items[$v[0]][1] += $v[1];
									} else {
										$items[$v[0]] = array($v[0], $v[1]);
									}
								}
							}
							if (!empty($houseLevelList[$i]['decors'])) {
								$t = json_decode($houseLevelList[$i]['decors'], true);
								foreach ($t as $v) {
									if (isset($decors[$v[0]])) {
										$decors[$v[0]][1] += $v[1];
									} else {
										$decors[$v[0]] = array($v[0], $v[1]);
									}
								}
							}
						}

						$awardRot = new Hapyfish2_Magic_Bll_Award();
						if ($coin > 0) {
							$awardRot->setCoin($coin);
						}
						if ($gold > 0) {
							$awardRot->setGold($gold, 2);
						}
						if (!empty($items)) {
							$awardRot->setItemList($items);
						}
						if (!empty($decors)) {
							$awardRot->setDecorList($decors);
						}
						$awardRot->sendOne($uid);

						return true;
					}
				}
			}
		}

		return false;
	}

	public static function checkBuildingDecoration($size, $dataAdd, $dataOld, &$buildingBasic, &$posMapping)
	{

		foreach ($dataAdd as $key=>$v) {
			//越界
			if ($v['x'] > $size || $v['y'] > $size || $v['z'] > $size) {
				return false;
			}

			// 1_1
			$pos = $v['x'] . '_' . $v['z'];

			$oldPos = '';
			//exist in old pos
			if (isset($dataOld[$key])) {
                $oldPos = $dataOld[$key]['x'] . '_' . $dataOld[$key]['z'];
			}

			//remove from old pos
			if ($oldPos) {
			    if (isset($posMapping[$oldPos])) {
			        $posMapping[$oldPos] -= 1;
			    }
			}

			//add to new pos
			if (isset($posMapping[$pos])) {
                $posMapping[$pos] += 1;
			}
			else {
			    $posMapping[$pos] = 1;
			}
		}

		//重叠
		foreach ($posMapping as $val) {
		    if ((int)$val > 1) {
                return false;
		    }
		}
		return true;
	}

	public static function checkFloorDecoration($size, $data)
	{
		$Declist = array();
		foreach ($data as $k => $v) {
			//越界
			if ($v['x'] < 0 || $v['x'] > $size || $v['z'] < 0 || $v['z'] > $size) {
				return false;
			}
			//重叠
			if (isset($Declist[$v['x'] . ',' . $v['z']])) {
				return false;
			}

			$Declist[$v['x'] . ',' . $v['z']] = $v;
		}

		return true;
	}

	public static function checkWallDecoration($size, $data)
	{
		$Declist = array();
		foreach ($data as $v) {
			//越界
			if ($v['x'] > $size || $v['z'] > $size) {
				return false;
			}

			//重叠
			if (isset($Declist[$v['x'] . ',' . $v['z']])) {
				return false;
			}

			$Declist[$v['x'] . ',' . $v['z']] = $v;
		}

		return true;
	}

	protected static function getChangeBuildingList1(&$building, &$buildingBasic, $size, &$changeDeskList, &$changeDoorList, &$changeBuildingList, $userDeskList, $userDoorList, $userBuildingList, &$posMapping)
	{
	    $aryAddPos = array();
		foreach ($building as $v) {
			$id = $v['id'];
			$cid = $v['d_id'];
			if (isset($buildingBasic[$cid]) && $v['bag_type'] == 0) {
				$type = $buildingBasic[$cid]['type'];
				//desk
				if ($type == 1) {
					$changeDeskList[$id] = array(
						'id' => $id,
						'cid' => $cid,
						'status' => 1,
						'mirro' => $v['mirror'],
						'x' => $v['x'],
						'y' => $v['y'],
						'z' => $v['z']
					);

					$aryAddPos[($id.$type)] = array('id' => $id,'cid' => $cid,'mirro' => $v['mirror'],'x' => $v['x'],'y' => $v['y'],'z' => $v['z']);
				} else if ($type == 2) { //door
					$changeDoorList[$id] = array(
						'id' => $id,
						'cid' => $cid,
						'status' => 1,
						'mirro' => $v['mirror'],
						'x' => $v['x'],
						'y' => $v['y'],
						'z' => $v['z']
					);

					$aryAddPos[($id.$type)] = array('id' => $id,'cid' => $cid,'mirro' => $v['mirror'],'x' => $v['x'],'y' => $v['y'],'z' => $v['z']);
				} else if ($type != 3 && $type != 4) {
					$changeBuildingList[$id] = array(
						'id' => $id,
						'cid' => $cid,
						'status' => 1,
						'mirro' => $v['mirror'],
						'x' => $v['x'],
						'y' => $v['y'],
						'z' => $v['z']
					);

					$aryAddPos[($id.$type)] = array('id' => $id,'cid' => $cid,'mirro' => $v['mirror'],'x' => $v['x'],'y' => $v['y'],'z' => $v['z']);
				}
			}
		}

		$aryOldPos = array();
		foreach ($userDeskList as $key=>$val) {
		    if ($val['status'] == 1) {
		        $id = $val['id'];
    		    $type = $val['item_type'];
                $aryOldPos[($id.$type)] = array('id' => $id,'cid' => $val['cid'],'mirro' => $val['mirro'],'x' => $val['x'],'y' => $val['y'],'z' => $val['z']);
		    }
		}
	    foreach ($userDoorList as $key=>$val) {
		    if ($val['status'] == 1) {
		        $id = $val['id'];
    		    $type = $val['item_type'];
                $aryOldPos[($id.$type)] = array('id' => $id,'cid' => $val['cid'],'mirro' => $val['mirro'],'x' => $val['x'],'y' => $val['y'],'z' => $val['z']);
		    }
		}
	    foreach ($userBuildingList as $key=>$val) {
		    if ($val['status'] == 1) {
		        $id = $val['id'];
    		    $type = $val['item_type'];
                $aryOldPos[($id.$type)] = array('id' => $id,'cid' => $val['cid'],'mirro' => $val['mirro'],'x' => $val['x'],'y' => $val['y'],'z' => $val['z']);
		    }
		}

		//$data = array_merge($changeDeskList, $changeDoorList, $changeBuildingList);
		//check decoration
		return self::checkBuildingDecoration($size, $aryAddPos, $aryOldPos, $buildingBasic, $posMapping);
	}

	protected static function getChangeBuildingList2(&$building, &$buildingBasic, &$changeDeskList, &$changeDoorList, &$changeBuildingList, $userDeskList, $userDoorList, $userBuildingList, &$posMapping)
	{
		foreach ($building as $v) {
			$id = $v['id'];
			$cid = $v['d_id'];
			if (isset($buildingBasic[$cid]) && $v['bag_type'] == 1) {
			    $pos = 0;
				$type = $buildingBasic[$cid]['type'];
				if ($type == 1 && !isset($changeDeskList[$id])) { //desk
					$changeDeskList[$id] = array(
						'id' => $id,
						'cid' => $cid,
						'status' => 0
					);
					if ($userDeskList[$id]['status'] == 1) {
					    $pos = $userDeskList[$id]['x'] . '_' . $userDeskList[$id]['z'];
					}
				} else if ($type == 2 && !isset($changeDoorList[$id])) { //door
					$changeDoorList[$id] = array(
						'id' => $id,
						'cid' => $cid,
						'status' => 0
					);
				    if ($userDoorList[$id]['status'] == 1) {
					    $pos = $userDoorList[$id]['x'] . '_' . $userDoorList[$id]['z'];
					}
				} else if ($type != 3 && $type != 4 && !isset($changeBuildingList[$id])) { //other building
					$changeBuildingList[$id] = array(
						'id' => $id,
						'cid' => $cid,
						'status' => 0
					);
				    if ($userBuildingList[$id]['status'] == 1) {
					    $pos = $userBuildingList[$id]['x'] . '_' . $userBuildingList[$id]['z'];
					}
				}
                if ($pos) {
				    unset($posMapping[$pos]);
                }
			}
		}
	}

	protected static function getChangeFloorList(&$floor, &$buildingBasic, $size, &$changeFloorList)
	{
		foreach ($floor as $k => $v) {
			$cid = $v['d_id'];
			if (isset($buildingBasic[$cid]) && $buildingBasic[$cid]['type'] == 3) {
				$changeFloorList[$k]['floor_id'] = $cid;
				$changeFloorList[$k]['x'] = $v['x'];
				$changeFloorList[$k]['z'] = $v['z'];
			}
		}

		//check floor decoration
		return self::checkFloorDecoration($size, $changeFloorList);
	}

	protected static function getChangeWallList(&$wall, &$buildingBasic, $size, &$changeWallList)
	{
		foreach ($wall as $k => $v) {
			$cid = $v['d_id'];
			if (isset($buildingBasic[$cid]) && $buildingBasic[$cid]['type'] == 4) {
				$changeWallList[$k]['wall_id'] = $cid;
				//x-wall
				if (0 == $v['z']) {
					$changeWallList[$k]['x'] = 0;
					$changeWallList[$k]['z'] = $v['x'];
				}
				//y-wall
				else if (0 == $v['x']) {
					$changeWallList[$k]['x'] = 1;
					$changeWallList[$k]['z'] = $v['z'];
				}
			}
		}

		//check wall decoration
		return self::checkWallDecoration($size, $changeWallList);
	}

	private static function checkDesk(&$userDeskList, &$data)
	{
		if (empty($userDeskList)) {
			return false;
		}

		foreach ($data as $k => $v) {
			//id不存在
			if (!isset($userDeskList[$v['id']])) {
				return false;
			}

			//类型不匹配
			if ($userDeskList[$v['id']]['cid'] != $v['cid']) {
				return false;
			}

			if ($v['status'] == 1) {
    			//越界-桌子放墙上
    			if ($v['x'] <= 0 || $v['z'] <= 0) {
                    return false;
    			}
			}
		}

		return true;
	}

	private static function checkDoor(&$userDoorList, &$data)
	{
		if (empty($userDoorList)) {
			return false;
		}

		foreach ($data as $k => $v) {
			//id不存在
			if (!isset($userDoorList[$v['id']])) {
				return false;
			}

			//类型不匹配
			if ($userDoorList[$v['id']]['cid'] != $v['cid']) {
				return false;
			}

			if ($v['status'] == 1) {
    			//坐标非法
    		    if ($v['x'] < 0 || $v['z'] < 0) {
                    return false;
    			}
    		    if ($v['x'] == 0 && $v['z'] == 0) {
                    return false;
    			}
    		    //越界-门放在房间里
    			if ($v['x'] > 0 && $v['z'] > 0) {
                    return false;
    			}
			}
		}

		return true;
	}

	private static function checkBuilding(&$userBuildingList, &$data, &$buildingBasic)
	{
		if (empty($userBuildingList)) {
			return false;
		}

		foreach ($data as $k => $v) {
			//id不存在
			if (!isset($userBuildingList[$v['id']])) {
				return false;
			}

			//类型不匹配
			if ($userBuildingList[$v['id']]['cid'] != $v['cid']) {
				return false;
			}

			if ($v['status'] == 1) {
    			//越界-墙上装饰放在房间里
    			if ($buildingBasic[$v['cid']]['type'] == 7) {
        			//坐标非法
        		    if ($v['x'] < 0 || $v['z'] < 0) {
                        return false;
        			}
        		    if ($v['x'] == 0 && $v['z'] == 0) {
                        return false;
        			}
        			if ($v['x'] > 0 && $v['z'] > 0) {
                        return false;
        			}
    			}
    			else {
        			//越界-decor放墙上
        			if ($v['x'] <= 0 || $v['z'] <= 0) {
                        return false;
        			}
    			}
			}

		}

		return true;
	}

	private static function checkFloor($uid, &$buildingBasic, &$userFloorInScene, &$userFloorInBag, $size, &$data, &$mpChange)
	{
		if (empty($userFloorInScene) || empty($userFloorInBag)) {
			return false;
		}

		$oldFloorInScene = array();
		$i = $j = 1;

		foreach ($userFloorInScene as $v) {
			$j = 1;
			foreach ($v as $d) {
				$oldFloorInScene[$i][$j] = $d;
				$j++;
			}
			$i++;
		}

		//先拿下来对应的地板
		foreach ($data as $k => $v) {
			$x = $v['x'];
			$z = $v['z'];
			$cid = $oldFloorInScene[$x][$z];
		    if (isset($userFloorInBag[$cid])) {
    			$userFloorInBag[$cid]['count'] += 1;
    			$userFloorInBag[$cid]['update'] = 1;
    		} else {
    			$userFloorInBag[$cid] = array('count' => 1, 'update' => 1);
    		}

    		if ($buildingBasic[$cid]['effect_mp'] > 0) {
    			$mpChange -= $buildingBasic[$cid]['effect_mp'];
    		}
		}

		//再放上需要的地板
		foreach ($data as $k => $v) {
			$x = $v['x'];
			$z = $v['z'];
			$cid = $v['floor_id'];
		    if (!isset($userFloorInBag[$cid]) || $userFloorInBag[$cid]['count'] <= 0) {
				return false;
    		} else {
    			$userFloorInBag[$cid]['count'] -= 1;
    			$userFloorInBag[$cid]['update'] = 1;
    		}
    		$oldFloorInScene[$x][$z] = $cid;

    		if ($buildingBasic[$cid]['effect_mp'] > 0) {
    			$mpChange += $buildingBasic[$cid]['effect_mp'];
    		}
		}

		$newFloorInScene = array();
		foreach ($oldFloorInScene as $v1) {
			$a = array();
			foreach ($v1 as $v2) {
				$a[] = $v2;
			}
			$newFloorInScene[] = $a;
		}

		$userFloorInScene = $newFloorInScene;

		return true;
	}

	private static function checkWall($uid, &$buildingBasic, &$userWallInScene, &$userWallInBag, $size, &$data, &$mpChange)
	{
		if (empty($userWallInScene) || empty($userWallInBag)) {
			return false;
		}

		$oldWallInScene = array();
		$i = 0;

		foreach ($userWallInScene as $v) {
			$j = 1;
			foreach ($v as $d) {
				$oldWallInScene[$i][$j] = $d;
				$j++;
			}
			$i++;
		}

		//先拿下来对应的墙纸
		foreach ($data as $k => $v) {
			$x = $v['x'];
			$z = $v['z'];
			$cid = $oldWallInScene[$x][$z];
		    if (isset($userWallInBag[$cid])) {
    			$userWallInBag[$cid]['count'] += 1;
    			$userWallInBag[$cid]['update'] = 1;
    		} else {
    			$userWallInBag[$cid] = array('count' => 1, 'update' => 1);
    		}

		    if ($buildingBasic[$cid]['effect_mp'] > 0) {
    			$mpChange -= $buildingBasic[$cid]['effect_mp'];
    		}
		}

		//再放上需要的墙纸
		foreach ($data as $k => $v) {
			$x = $v['x'];
			$z = $v['z'];
			$cid = $v['wall_id'];
		    if (!isset($userWallInBag[$cid]) || $userWallInBag[$cid]['count'] <= 0) {
				return false;
    		} else {
    			$userWallInBag[$cid]['count'] -= 1;
    			$userWallInBag[$cid]['update'] = 1;
    		}
    		$oldWallInScene[$x][$z] = $cid;

		    if ($buildingBasic[$cid]['effect_mp'] > 0) {
    			$mpChange += $buildingBasic[$cid]['effect_mp'];
    		}
		}

		$newWallInScene = array();
		foreach ($oldWallInScene as $v1) {
			$a = array();
			foreach ($v1 as $v2) {
				$a[] = $v2;
			}
			$newWallInScene[] = $a;
		}

		$userWallInScene = $newWallInScene;

		return true;
	}

	public static function generatePositionMap($userDesk, $userDoor, $userBuilding)
	{
	    $mapping = array();
	    //desk
	    foreach ($userDesk as $k => $v) {
			//在场景中
			if ($v['status'] == 1) {
			    $pos = $v['x'].'_'. $v['z'];
				$mapping[$pos] = 1;
			}
		}

	    //door
	    foreach ($userDoor as $k => $v) {
			//在场景中
			if ($v['status'] == 1) {
			    $pos = $v['x'].'_'. $v['z'];
				$mapping[$pos] = 1;
			}
		}

	    //other building ()
	    foreach ($userBuilding as $k => $v) {
			//在场景中
			if ($v['status'] == 1) {
			    $pos = $v['x'].'_'. $v['z'];
				$mapping[$pos] = 1;
			}
		}
		return $mapping;
	}

	public static function diy($uid, $data)
	{
		//check
		$changeDeskList = array();
		$changeDoorList = array();
		$changeFloorList = array();
		$changeWallList = array();
		$changeBuildingList = array();

		$buildingBasic = Hapyfish2_Magic_Cache_BasicInfo::getBuildingList();
		$userSceneInfo = Hapyfish2_Magic_HFC_User::getUserScene($uid);
		$size = $userSceneInfo['tile_x_length'];
//info_log('decorChangeList:'.json_encode($data['building1']), 'aaa');
//info_log('decorBagChangeList:'.json_encode($data['building2']), 'aaa');
		//info_log(json_encode($data), 'diy-data');

		/* generate position mapping table logic -add by zx */
		$userDeskList = Hapyfish2_Magic_HFC_Desk::getAll($uid);
		$userDoorList = Hapyfish2_Magic_HFC_Door::getAll($uid);
		$userBuildingList = Hapyfish2_Magic_HFC_Building::getAll($uid);
		$posMapping = self::generatePositionMap($userDeskList, $userDoorList, $userBuildingList);

//info_log('curdbmapping:'.json_encode($posMapping), 'aaa');
		if (!empty($data['building2'])) {
			self::getChangeBuildingList2($data['building2'], $buildingBasic, $changeDeskList, $changeDoorList, $changeBuildingList, $userDeskList, $userDoorList, $userBuildingList, $posMapping);
		}
//info_log('after--tobag:'.json_encode($posMapping), 'aaa');
		if (!empty($data['building1'])) {
			$ok = self::getChangeBuildingList1($data['building1'], $buildingBasic, $size, $changeDeskList, $changeDoorList, $changeBuildingList, $userDeskList, $userDoorList, $userBuildingList, $posMapping);
			if (!$ok) {
				return Hapyfish2_Magic_Bll_UserResult::Error('decoration_error');
			}
		}

		if (!empty($data['floor'])) {
			$ok = self::getChangeFloorList($data['floor'], $buildingBasic, $size, $changeFloorList);
			if (!$ok) {
				return Hapyfish2_Magic_Bll_UserResult::Error('decoration_error');
			}
		}

		if (!empty($data['wall'])) {
			$ok = self::getChangeWallList($data['wall'], $buildingBasic, $size, $changeWallList);
			if (!$ok) {
				return Hapyfish2_Magic_Bll_UserResult::Error('decoration_error');
			}
		}

		$maxMpChange = 0;

		//info_log(json_encode($changeDeskList), 'changeDeskList');
		//info_log(json_encode($changeDoorList), 'changeDoorList');
		//info_log(json_encode($changeBuildingList), 'changeBuildingList');
		//info_log(json_encode($changeFloorList), 'changeFloorList');
		//info_log(json_encode($changeWallList), 'changeWallList');

		//desk
		$deskCheck = false;
		if (!empty($changeDeskList)) {
			$deskCheck = self::checkDesk($userDeskList, $changeDeskList);
			if (!$deskCheck) {
				return Hapyfish2_Magic_Bll_UserResult::Error('decoration_error');
			}
		}

		//door
		$doorCheck = false;
		if (!empty($changeDoorList)) {
			$doorCheck = self::checkDoor($userDoorList, $changeDoorList);
			if (!$doorCheck) {
				return Hapyfish2_Magic_Bll_UserResult::Error('decoration_error');
			}
		}

		//building
		$buildingCheck = false;
		if (!empty($changeBuildingList)) {
			$buildingCheck = self::checkBuilding($userBuildingList, $changeBuildingList, $buildingBasic);
			if (!$buildingCheck) {
				return Hapyfish2_Magic_Bll_UserResult::Error('decoration_error');
			}
		}

		//floor
		$floorCheck = false;
		if (!empty($changeFloorList)) {
			$userFloorInScene = Hapyfish2_Magic_Cache_Floor::getInScene($uid);
			$userFloorInBag = Hapyfish2_Magic_HFC_FloorBag::getUserFloor($uid);
			$floorCheck = self::checkFloor($uid, $buildingBasic, $userFloorInScene, $userFloorInBag, $size, $changeFloorList, $maxMpChange);
		}

		//wall
		$wallCheck = false;
		if (!empty($changeWallList)) {
			$userWallInScene = Hapyfish2_Magic_Cache_Wall::getInScene($uid);
			$userWallInBag = Hapyfish2_Magic_HFC_WallBag::getUserWall($uid);
			$wallCheck = self::checkWall($uid, $buildingBasic, $userWallInScene, $userWallInBag, $size, $changeWallList, $maxMpChange);
		}

		//开始更新数据
		$t = time();
		if ($deskCheck) {
			foreach ($changeDeskList as $k => $v) {
				$desk = $userDeskList[$v['id']];
				//放回背包
				if ($v['status'] == 0) {
					//如果以前就是在背包中，则忽略
					if ($desk['status'] == 0) {
						continue;
					}

					//如果是已经有学生占位，需要清理对应的学生状态
					if ($desk['student_id'] != 0) {
						$sid = $desk['student_id'];
						$std = Hapyfish2_Magic_HFC_Student::getOne($uid, $sid);
						//如果对应的学生还在
						if ($std && $std['desk_id'] == $desk['id']) {
							//把学生设置成空闲状态
							$std['state'] = 3;
							$std['desk_id'] = 0;

							Hapyfish2_Magic_HFC_Student::updateOne($uid, $sid, $std, true);
						}
					}

					//清空其它属性
					$desk['x'] = 0;
					$desk['y'] = 0;
					$desk['z'] = 0;
					$desk['mirro'] = 0;
					$desk['status'] = 0;
					$desk['student_id'] = 0;
					$desk['coin'] = 0;
					$desk['end_time'] = 0;
					$desk['stone_time'] = 0;

					$ok = Hapyfish2_Magic_HFC_Desk::updateOne($uid, $desk['id'], $desk, true);
					if ($ok) {
						Hapyfish2_Magic_Cache_Desk::popOneIdInScene($uid, $desk['id']);
						//降低最大魔法值
						$maxMpChange -= $buildingBasic[$desk['cid']]['effect_mp'];
					}
				} else if ($v['status'] == 1) { //放入场景中
					//如果是从背包中拖入的
					if ($desk['status'] == 0) {
						$desk['x'] = $v['x'];
						$desk['y'] = $v['y'];
						$desk['z'] = $v['z'];
						$desk['mirro'] = $v['mirro'];
						$desk['status'] = 1;
						$desk['student_id'] = 0;
						$desk['coin'] = 0;
						$desk['end_time'] = 0;
						$desk['stone_time'] = 0;

						//如果有闲逛学生，取一个
						/*$fiddleStd = Hapyfish2_Magic_Bll_Student::updateOneFiddle($uid, $desk);
						if ($fiddleStd) {
							$changeStudent = array(
									'sid' => $fiddleStd['sid'],
									'decor_id' => $fiddleStd['desk_id'],
									'state' => $fiddleStd['state'],
									'time' => 0,
									'magic_id' => $fiddleStd['magic_id'],
									'event_time' => -1,
									'coin' => 0,
									'stone_time' => 0,
									'can_steal' => 0
							);

							Hapyfish2_Magic_Bll_UserResult::addField($uid, 'changeStudents', array($changeStudent));
						}*/

						$ok = Hapyfish2_Magic_HFC_Desk::updateOne($uid, $desk['id'], $desk, true);
						if ($ok) {
							Hapyfish2_Magic_Cache_Desk::pushOneIdInScene($uid, $desk['id']);
							//增加最大魔法值
							$maxMpChange += $buildingBasic[$desk['cid']]['effect_mp'];

							//如果有闲逛学生，需要分配一个
							$fiddleStd = Hapyfish2_Magic_Bll_Student::updateOneFiddle($uid, $desk);
							if ($fiddleStd) {
								$changeStudent = array(
									'sid' => $fiddleStd['sid'],
									'decor_id' => $fiddleStd['desk_id'],
									'state' => $fiddleStd['state'],
									'time' => 0,
									'magic_id' => $fiddleStd['magic_id'],
									'event_time' => -1,
									'coin' => 0,
									'stone_time' => 0,
									'can_steal' => 0
							    );
								Hapyfish2_Magic_Bll_UserResult::addField($uid, 'changeStudents', array($changeStudent));
							}
						}
					} else { //原来就是在场景中，只是换个了地方
						$desk['x'] = $v['x'];
						$desk['y'] = $v['y'];
						$desk['z'] = $v['z'];
						$desk['mirro'] = $v['mirro'];
						$desk['status'] = 1;

						$ok = Hapyfish2_Magic_HFC_Desk::updateOne($uid, $desk['id'], $desk, true);
					}
				}
			}
		}

		if ($doorCheck) {
			foreach ($changeDoorList as $k => $v) {
				$door = $userDoorList[$v['id']];
				//放回背包
				if ($v['status'] == 0) {
					//如果以前就是在背包中，则忽略
					if ($door['status'] == 0) {
						continue;
					}
					//清空其它属性
					$door['x'] = 0;
					$door['y'] = 0;
					$door['z'] = 0;
					$door['mirro'] = 0;
					$door['status'] = 0;
					$door['left_student_num'] = 0;
					$door['start_time'] = 0;
					$door['end_time'] = 0;

					$ok = Hapyfish2_Magic_HFC_Door::updateOne($uid, $door['id'], $door, true);
					if ($ok) {
						Hapyfish2_Magic_Cache_Door::popOneIdInScene($uid, $door['id']);
						//降低最大魔法值
						$maxMpChange -= $buildingBasic[$door['cid']]['effect_mp'];
					}
				} else if ($v['status'] == 1) {
					//如果是从背包中拖入的
					if ($door['status'] == 0) {
						$door['x'] = $v['x'];
						$door['y'] = $v['y'];
						$door['z'] = $v['z'];
						$door['mirro'] = $v['mirro'];
						$door['status'] = 1;
						$door['left_student_num'] = $buildingBasic[$door['cid']]['door_guest_limit'];
						$door['start_time'] = $t;
						$door['end_time'] = $t + $buildingBasic[$door['cid']]['door_cooldown']/SPEED_BASE/SPEED_DOOR_TIME;

						$ok = Hapyfish2_Magic_HFC_Door::updateOne($uid, $door['id'], $door, true);
						if ($ok) {
							Hapyfish2_Magic_Cache_Door::pushOneIdInScene($uid, $door['id']);
							//增加最大魔法值
							$maxMpChange += $buildingBasic[$door['cid']]['effect_mp'];
						}
					} else { //原来就是在场景中，只是换个了地方
						$door['x'] = $v['x'];
						$door['y'] = $v['y'];
						$door['z'] = $v['z'];
						$door['mirro'] = $v['mirro'];
						$door['status'] = 1;

						$ok = Hapyfish2_Magic_HFC_Door::updateOne($uid, $door['id'], $door, true);
					}
				}
			}
		}

		if ($buildingCheck) {
			foreach ($changeBuildingList as $k => $v) {
				$building = $userBuildingList[$v['id']];
				//放回背包
				if ($v['status'] == 0) {
					//如果以前就是在背包中，则忽略
					if ($building['status'] == 0) {
						continue;
					}
					//清空其它属性
					$building['x'] = 0;
					$building['y'] = 0;
					$building['z'] = 0;
					$building['mirro'] = 0;
					$building['status'] = 0;

					$ok = Hapyfish2_Magic_HFC_Building::updateOne($uid, $building['id'], $building, true);
					if ($ok) {
						Hapyfish2_Magic_Cache_Building::popOneIdInScene($uid, $building['id']);
						//降低最大魔法值
						$maxMpChange -= $buildingBasic[$building['cid']]['effect_mp'];
					}
				} else if ($v['status'] == 1) {
					//如果是从背包中拖入的
					if ($building['status'] == 0) {
						$building['x'] = $v['x'];
						$building['y'] = $v['y'];
						$building['z'] = $v['z'];
						$building['mirro'] = $v['mirro'];
						$building['status'] = 1;

						$ok = Hapyfish2_Magic_HFC_Building::updateOne($uid, $building['id'], $building, true);
						if ($ok) {
							Hapyfish2_Magic_Cache_Building::pushOneIdInScene($uid, $building['id']);
							//增加最大魔法值
							$maxMpChange += $buildingBasic[$building['cid']]['effect_mp'];
						}
					} else { //原来就是在场景中，只是换个了地方
						$building['x'] = $v['x'];
						$building['y'] = $v['y'];
						$building['z'] = $v['z'];
						$building['mirro'] = $v['mirro'];
						$building['status'] = 1;

						$ok = Hapyfish2_Magic_HFC_Building::updateOne($uid, $building['id'], $building, true);
					}
				}
			}
		}

		if ($wallCheck) {
			//$userWallInScene, $userWallInBag
			//更新
			$ok = Hapyfish2_Magic_Cache_Wall::updateInScene($uid, $userWallInScene);
			if ($ok) {
				Hapyfish2_Magic_HFC_WallBag::updateUserWall($uid, $userWallInBag, true);
			}
		}

		if ($floorCheck) {
			//$userFloorInScene, $userFloorInBag
			//更新
			$ok = Hapyfish2_Magic_Cache_Floor::updateInScene($uid, $userFloorInScene);
			if ($ok) {
				Hapyfish2_Magic_HFC_FloorBag::updateUserFloor($uid, $userFloorInBag, true);
			}
		}


		if ($maxMpChange > 0) {
			Hapyfish2_Magic_HFC_User::incUserMaxMp($uid, $maxMpChange, true);
			$ok = self::checkRoomLevelUp($uid);
		} else if ($maxMpChange < 0) {
			$maxMpChange = $maxMpChange * (-1);
			Hapyfish2_Magic_HFC_User::decUserMaxMp($uid, $maxMpChange, true);
		}

		//派发事件
		$event = array('uid' => $uid);
		Hapyfish2_Magic_Bll_Event::diy($event);

		return Hapyfish2_Magic_Bll_UserResult::all();
	}

	public static function expandhouse($uid, $id, $type)
	{
		$sceneSizeInfo = Hapyfish2_Magic_Cache_BasicInfo::getSceneSizeInfo($id);
		if (!$sceneSizeInfo) {
			return Hapyfish2_Magic_Bll_UserResult::Error('id_error');
		}

		/*
		//判断等级
		$userLevelInfo = Hapyfish2_Magic_HFC_User::getUserLevel($uid);
		if ($userLevelInfo['house_level'] < $sceneSizeInfo['level']) {
			return Hapyfish2_Magic_Bll_UserResult::Error('level_not_ok');
		}*/
		//判断最大魔法值
		$userMpInfo = Hapyfish2_Magic_HFC_User::getUserMp($uid);
		$level = $sceneSizeInfo['level'];
		$houseLevelInfo = Hapyfish2_Magic_Cache_BasicInfo::getHouseLevelInfo($level);
		if ($userMpInfo['max_mp'] < $houseLevelInfo['mp']) {
			return Hapyfish2_Magic_Bll_UserResult::Error('max_mp_not_enough');
		}

		if ($type == 1) {
			if ($sceneSizeInfo['friend_num'] > 0) {
				//判断好友数要求
				$userFriendInfo = Hapyfish2_Platform_Cache_Friend::getFriend($uid);
				if (!$userFriendInfo || $userFriendInfo['count'] < $sceneSizeInfo['friend_num']) {
					return Hapyfish2_Magic_Bll_UserResult::Error('friend_not_enough');
				}
			}
		}

		$userSceneInfo = Hapyfish2_Magic_HFC_User::getUserScene($uid);
		if (!$userSceneInfo || $userSceneInfo['tile_x_length'] >= $sceneSizeInfo['size']
			|| $userSceneInfo['tile_z_length'] >= $sceneSizeInfo['size']) {
				return Hapyfish2_Magic_Bll_UserResult::Error('size_not_ok');
		}

		$coinChange = 0;
		$goldChange = 0;

		$stepX = $sceneSizeInfo['size'] - $userSceneInfo['tile_x_length'];
		$stepZ = $sceneSizeInfo['size'] - $userSceneInfo['tile_z_length'];

		//判断金币
		if ($type == 1) {
			$userCoin = Hapyfish2_Magic_HFC_User::getUserCoin($uid);
			if (empty($sceneSizeInfo['coin']) || $userCoin < $sceneSizeInfo['coin']) {
				return Hapyfish2_Magic_Bll_UserResult::Error('coin_not_enough');
			}
			$coinChange = $sceneSizeInfo['coin'];
			$ok = Hapyfish2_Magic_HFC_User::decUserCoin($uid, $coinChange);
			if ($ok) {
			}
		} else if ($type == 2) {
			$userGold = Hapyfish2_Magic_HFC_User::getUserGold($uid);
			if (empty($sceneSizeInfo['gold']) || $userGold < $sceneSizeInfo['gold']) {
				return Hapyfish2_Magic_Bll_UserResult::Error('gold_not_enough');
			}
			$goldChange = $sceneSizeInfo['gold'];
        	$goldInfo = array(
        		'uid' => $uid,
        		'cost' => $goldChange,
        		'summary' => '扩地[' . $sceneSizeInfo['id'] . '-' . $sceneSizeInfo['size'] . ']'
        	);
			Hapyfish2_Magic_Bll_Gold::consume($uid, $goldInfo);
		} else {
			return Hapyfish2_Magic_Bll_UserResult::Error('type_not_ok');
		}

		$userSceneInfo['tile_x_length'] = $sceneSizeInfo['size'];
		$userSceneInfo['tile_z_length'] = $sceneSizeInfo['size'];

		Hapyfish2_Magic_HFC_User::updateUserScene($uid, $userSceneInfo, true);

		//插入默认地板
		$floorList = Hapyfish2_Magic_Cache_Floor::getInScene($uid);
		if (!empty($floorList)) {
			$defaultFloor = 193005;
			for($i = 1; $i <= $stepX; $i++) {
				for($j = 0; $j < $sceneSizeInfo['size']; $j++) {
					$index = (int)$sceneSizeInfo['size'] - $i;
					$floorList[$index][$j] = $defaultFloor;
					$floorList[$j][$index] = $defaultFloor;
				}
			}
			$floorData = json_encode($floorList);
			Hapyfish2_Magic_Cache_Floor::updateInScene($uid, $floorData);
		}

		//插入默认墙纸
		$wallList = Hapyfish2_Magic_Cache_Wall::getInScene($uid);
		if (!empty($wallList)) {
			$defaultWall = 194005;
			$maxMpChange = 0;
			for($i = 1; $i <= $stepZ; $i++) {
				$index = (int)$sceneSizeInfo['size'] - $i;
				$wallList[0][$index] = $defaultWall;
				$wallList[1][$index] = $defaultWall;
				$maxMpChange += 2*3;
			}
			$wallData = json_encode($wallList);
			Hapyfish2_Magic_Cache_Wall::updateInScene($uid, $wallData);

			//增加max_mp
			Hapyfish2_Magic_HFC_User::incUserMaxMp($uid, $maxMpChange, true);
		}

		$levelupScene = self::getHomeData($uid);

		Hapyfish2_Magic_Bll_UserResult::addField($uid, 'levelupScene', $levelupScene);

		return Hapyfish2_Magic_Bll_UserResult::all();
	}

	public static function unlock($uid, $sceneId, $type)
	{
		$sceneInfo = Hapyfish2_Magic_Cache_BasicInfo::getMapSceneInfo($sceneId);
		if (!$sceneInfo) {
			return Hapyfish2_Magic_Bll_UserResult::Error('scene_id_error');
		}

		if ($type != 1 && $type != 2) {
			return Hapyfish2_Magic_Bll_UserResult::Error('type_error');
		}

		$userScene = Hapyfish2_Magic_HFC_User::getUserScene($uid);
        $data = $userScene['open_scene_list'];
        $userOpenSceneList = explode(',', $data);
        $openList = array();
        foreach ($userOpenSceneList as $id) {
        	$openList[$id] = 1;
        }

        if (isset($openList[$sceneId])) {
        	return Hapyfish2_Magic_Bll_UserResult::Error('scene_has_opened');
        }

		//判断等级
		$userLevelInfo = Hapyfish2_Magic_HFC_User::getUserLevel($uid);
		if ($userLevelInfo['level'] < $sceneInfo['need_level']) {
			return Hapyfish2_Magic_Bll_UserResult::Error('level_not_enough');
		}

        if ($type == 1) {
        	$condition = json_decode($sceneInfo['condition1'], true);
        } else if ($type == 2) {
        	$condition = json_decode($sceneInfo['condition2'], true);
        } else {
        	return Hapyfish2_Magic_Bll_UserResult::Error('type_error');
        }

        $ret = array();
        $coinChange = $goldChange = 0;
		foreach ($condition as $k => $v) {
			if ($v['type'] == 1) {
				//道具
				$userItemList = Hapyfish2_Magic_HFC_Item::getUserItem($uid);
				if (empty($userItemList) || !isset($userItemList[$v['id']]) || $userItemList[$v['id']]['count'] <= 0) {
					$result['content'] = 'condition_error';
					return array('result' => $result);
				} else {
					//
					Hapyfish2_Magic_HFC_Item::useUserItem($uid, $v['id'], $v['num'], $userItemList);
					$ret['removeItems'] = array(array($v['id'], $v['num']));
				}
			} else if ($v['type'] == 3) {
				//玩家属性
				if ($v['id'] == 'coin') {
					$userCoin = Hapyfish2_Magic_HFC_User::getUserCoin($uid);
					if ($userCoin < $v['num']) {
						$result['content'] = 'condition_error';
						return array('result' => $result);
					} else {
						Hapyfish2_Magic_HFC_User::decUserCoin($uid, $v['num']);
						$coinChange -= $v['num'];
					}
				} else if ($v['id'] == 'gmoney') {
					$userGold = Hapyfish2_Magic_HFC_User::getUserGold($uid);
					if ($userGold < $v['num']) {
						$result['content'] = 'condition_error';
						return array('result' => $result);
					} else {
			        	$goldInfo = array(
			        		'uid' => $uid,
			        		'cost' => $v['num'],
			        		'summary' => '解锁场景' . $userScene['name']
			        	);
						$ok = Hapyfish2_Magic_Bll_Gold::consume($uid, $goldInfo);
						$goldChange -= $v['num'];
					}
				}
			}
		}

		//
		$userOpenSceneList[] = $sceneId;
		$userScene['open_scene_list'] = join(',', $userOpenSceneList);

		$ok = Hapyfish2_Magic_HFC_User::updateUserScene($uid, $userScene, true);
		if ($ok) {
		    //insert minifeed
			$feed = array(
				'uid' => $uid,
				'template_id' => 8,
				'actor' => $uid,
				'target' => $uid,
				'type' => 2,//1好友 2系统
				'icon' => 1,//1笑脸 2哭脸
				'title' => array('sc_name' => $userScene['name']),
				'create_time' => time()
			);
			Hapyfish2_Magic_Bll_Feed::insertMiniFeed($feed);
			return Hapyfish2_Magic_Bll_UserResult::all();
		} else {
			return Hapyfish2_Magic_Bll_UserResult::Error('unlock_error');
		}
	}

	public static function change($uid, $sceneId)
	{
		$sceneInfo = Hapyfish2_Magic_Cache_BasicInfo::getMapSceneInfo($sceneId);
		if (!$sceneInfo) {
			return Hapyfish2_Magic_Bll_UserResult::Error('scene_id_error');
		}

		$userScene = Hapyfish2_Magic_HFC_User::getUserScene($uid);
        $data = $userScene['open_scene_list'];
        $userOpenSceneList = explode(',', $data);
        $openList = array();
        foreach ($userOpenSceneList as $id) {
        	$openList[$id] = 1;
        }

        if (!isset($openList[$sceneId])) {
        	return Hapyfish2_Magic_Bll_UserResult::Error('scene_not_opened');
        }

        $userMp = Hapyfish2_Magic_HFC_User::getUserMp($uid);
		if ($userMp['mp'] < $sceneInfo['mp']) {
			return Hapyfish2_Magic_Bll_UserResult::Error('mp_not_enough');
		}

		Hapyfish2_Magic_HFC_User::decUserMp($uid, $sceneInfo['mp']);

		$userScene['cur_scene_id'] = $sceneId;
		$ok = Hapyfish2_Magic_HFC_User::updateUserScene($uid, $userScene, false);
		if ($ok) {
			return Hapyfish2_Magic_Bll_UserResult::all();
		} else {
			return Hapyfish2_Magic_Bll_UserResult::Error('change_error');
		}
	}
}