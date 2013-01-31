<?php

class Hapyfish2_Magic_Bll_MapCopy
{

    //击打 怪or矿
    public static function hitMonster($uid, $id)
    {
        $userSceneInfo = Hapyfish2_Magic_HFC_User::getUserScene($uid);
        $mapId = $userSceneInfo['cur_scene_id'];
        if (!$mapId || (int)$mapId>=(int)HOME_SCENE_ID) {
            return Hapyfish2_Magic_Bll_UserResult::Error('mapcopy_not_in_map');
        }

        $today = date('Ymd');
        //get current map copy status
        $curMap = Hapyfish2_Magic_HFC_MapCopy::getUserMapCopyInfo($uid);
        if (!$curMap || date('Ymd',$curMap['enter_time'])<$today) {
             return Hapyfish2_Magic_Bll_UserResult::Error('mapcopy_not_init_map');
        }

        $basicMapInfo = Hapyfish2_Magic_Cache_BasicInfo::getMapCopyInfo($mapId);
        if ($basicMapInfo['parent_id'] != $curMap['map_parent']) {
            return Hapyfish2_Magic_Bll_UserResult::Error('mapcopy_invalid_request');
        }

        //get current map copy monsters status
        $rowMap = Hapyfish2_Magic_HFC_MapCopy::getOne($uid, $mapId);
        if (!$rowMap) {
            return Hapyfish2_Magic_Bll_UserResult::Error('mapcopy_not_init_mapmonster');
        }

        $mapMonsters = json_decode($rowMap['data'], true);
        if (!isset($mapMonsters[$id])) {
            return Hapyfish2_Magic_Bll_UserResult::Error('mapcopy_monster_not_found');
        }

        $basicMapMonster = Hapyfish2_Magic_Cache_BasicInfo::getMapMonsterList();
        $rowMonster = $mapMonsters[$id];
        if ((int)$rowMonster['cur_hp'] <= 0) {
            return Hapyfish2_Magic_Bll_UserResult::Error('mapcopy_monster_has_dead');
        }

        //need condition
        $condition = json_decode($basicMapMonster[$rowMonster['cid']]['need_conditions'], true);
        $rst = self::needCondition($uid, $condition, $removeResult1);
        if ($rst) {
            return Hapyfish2_Magic_Bll_UserResult::Error($rst);
        }
        $newHp = ((int)$rowMonster['cur_hp'] - 1) > 0 ? ((int)$rowMonster['cur_hp'] - 1) : 0;
        $mapMonsters[$id]['cur_hp'] = $newHp;

        //update monster status
        $rowMap['data'] = json_encode($mapMonsters);
        $ok = Hapyfish2_Magic_HFC_MapCopy::updateOne($uid, $mapId, $rowMap);

        if (!$ok) {
            return Hapyfish2_Magic_Bll_UserResult::Error('mapcopy_hit_monster_failed');
        }

        //award condition
        $awdCondition = json_decode($basicMapMonster[$rowMonster['cid']]['award_conditions'], true);
        self::awardCondition($uid, $awdCondition, $addResult1);

        $react = false;
        $removeResult2 = array('coin'=>0, 'gem'=>0, 'mp'=>0);
        $addResult2 = array('coin'=>0, 'gem'=>0, 'mp'=>0, 'exp'=>0);
        //final attack
        if ($newHp == 0) {
            $finCondition = json_decode($basicMapMonster[$rowMonster['cid']]['final_conditions'], true);
            self::awardCondition($uid, $finCondition, $addResult2);
        }
        //monster react
        else {
            $defCondition = json_decode($basicMapMonster[$rowMonster['cid']]['defend_conditions'], true);
            self::reactCondition($uid, $defCondition, $removeResult2);
            if ($removeResult2['coin']!=0 || $removeResult2['gem']!=0 || $removeResult2['mp']!=0) {
                $react = true;
            }
        }

        $addResult = array('coin'=>($addResult1['coin']+$addResult2['coin']),
        				   'gem'=>($addResult1['gem']+$addResult2['gem']),
        				   'mp'=>($addResult1['mp']+$addResult2['mp']),
                           'exp'=>($addResult1['exp']+$addResult2['exp']));

        $commonRst = Hapyfish2_Magic_Bll_UserResult::all();

        //if is level up
        $ok = Hapyfish2_Magic_HFC_User::incUserExp($uid, $addResult['exp']);

        $actScript = array();
        foreach ($removeResult1 as $key=>$val) {
            $removeResult1[$key] = abs($val);
        }
        $actScript[] = array(
            'hpChange' => '-1',
            'roles' => array(array(0, 1), array($id, 2)),
            'removeResult' => $removeResult1,
            'addResult' => $addResult,
            'addItem' => isset($commonRst['addItem']) ? $commonRst['addItem'] : array(),
            'removeItem' => isset($commonRst['removeItems']) ? $commonRst['removeItems'] : array(),
            'addDecor' => isset($commonRst['addDecorBag']) ? $commonRst['addDecorBag'] : array(),
            'removeDecor' => isset($commonRst['removeDecorBag']) ? $commonRst['removeDecorBag'] : array(),
        );

        //怪有反击
        if ($react) {
            foreach ($removeResult2 as $key=>$val) {
                $removeResult2[$key] = abs($val);
            }
            $actScript[] = array(
                'roles' => array(array($id, 1), array(0, 2)),
                'removeResult' => $removeResult2 ,
                'addResult' => array(),
            );
        }

        //Hapyfish2_Magic_Bll_UserResult::addField($uid, 'hpChange', '-1');
        Hapyfish2_Magic_Bll_UserResult::addField($uid, 'actScript', $actScript);

        //trigger event
        if ($newHp == 0) {
            //ko monster
    		$event = array('uid' => $uid, 'pMapId' => $basicMapInfo['parent_id'], 'cid' => $rowMonster['cid'], 'num' => 1);
            Hapyfish2_Magic_Bll_Event::hitMonster($event);
        }
        if (isset($commonRst['addItem']) && $commonRst['addItem']) {
            $eventItem = array();
            foreach ($commonRst['addItem'] as $data) {
                if (array_key_exists($data[0], $eventItem)) {
                    $eventItem[$data[0]] = $eventItem[$data[0]] + (int)$data[1];
                }
                else {
                    $eventItem[$data[0]] = (int)$data[1];
                }
            }
            if ($eventItem) {
                $event = array('uid' => $uid, 'pMapId' => $basicMapInfo['parent_id'], 'items' => $eventItem);
                Hapyfish2_Magic_Bll_Event::collectItem($event);
            }
        }

        return Hapyfish2_Magic_Bll_UserResult::all();
    }

    //进入副本地图
    public static function enterMap($uid, $basicMapInfo, $portalId)
	{
	    $tarMapId = $basicMapInfo['id'];
        //get user info
        $userVo = Hapyfish2_Magic_Bll_User::getUserInit($uid);

        //check user level and enter conditions
        if ($userVo['level']<$basicMapInfo['need_level']) {
            return Hapyfish2_Magic_Bll_UserResult::Error('level_not_enough');
        }
        //other condition TODO::


        $curMap = $userVo['currentSceneId'];
        if ($curMap == $tarMapId) {
            return Hapyfish2_Magic_Bll_UserResult::Error('mapcopy_already_in_map');
        }

        //check can enter next map
        if ($portalId) {
            $portalList = Hapyfish2_Magic_Cache_BasicInfo::getMapCopyTranscript($curMap, 'portalList');
            if (!isset($portalList[$portalId])) {
                return Hapyfish2_Magic_Bll_UserResult::Error('mapcopy_invalid_jump_map');
            }
            if ($portalList[$portalId]['tar_map_id'] != $tarMapId) {
                return Hapyfish2_Magic_Bll_UserResult::Error('mapcopy_invalid_jump_map2');
            }
        }

        $needInit = true;
        //get current map copy status
        $curMap = Hapyfish2_Magic_HFC_MapCopy::getUserMapCopyInfo($uid);
        if ($curMap) {
            $today = date('Ymd');
            if (date('Ymd',$curMap['enter_time'])==$today) {
                if ($curMap['map_parent'] == $basicMapInfo['parent_id']) {
                    if (!$curMap['map_ids']) {
                        $curMap['map_ids'] = $tarMapId;
                    }
                    else {
                        $tmp = explode(',', $curMap['map_ids']);
                        if (!in_array($tarMapId, $tmp)) {
                            $curMap['map_ids'] .= ',' . $tarMapId;
                        }
                    }
                    $needInit = false;
                }
            }
        }

	    if ($needInit) {
    	    //check if condition is ok
            if ($basicMapInfo['condition1']) {
                $condition = json_decode($basicMapInfo['condition1'], true);
                $changeRst = array();
                $rst = self::needCondition($uid, $condition, $changeRst);
                if ($rst) {
                    return Hapyfish2_Magic_Bll_UserResult::Error($rst);
                }
            }
            self::initMapCopy($uid, $basicMapInfo);
        }
        else {
            $curMap['update_time'] = time();
            Hapyfish2_Magic_HFC_MapCopy::addUserMapCopyInfo($uid, $curMap, true);
        }

	    //update current user scene
        $userSceneInfo = Hapyfish2_Magic_HFC_User::getUserScene($uid);
        if ($userSceneInfo['cur_scene_id'] != $tarMapId) {
            $userVo['currentSceneId'] = $tarMapId;
    		$userSceneInfo['cur_scene_id'] = $tarMapId;
    		Hapyfish2_Magic_HFC_User::updateUserScene($uid, $userSceneInfo, true);
        }

        $listVo = Hapyfish2_Magic_Bll_MapCopy::getMapCopyScene($uid, $tarMapId, $userVo, $basicMapInfo);
        if ($listVo) {
            $scene = array(
    		    'sceneId' => $userVo['currentSceneId'],
    			'user' => $userVo,
    			'mineList' => $listVo['mineList'],
    			'monsterList' => $listVo['monsterList'],
    			'portalList' => $listVo['portalList'],
    			'decorList' => $listVo['decorList'],
    			'floorList' => $listVo['floorList']
    		);
    		Hapyfish2_Magic_Bll_UserResult::addField($uid, 'scene', $scene);

            //map task
            $tasks = Hapyfish2_Magic_Bll_Task::getUserMapTask($uid, $basicMapInfo['parent_id']);
            Hapyfish2_Magic_Bll_UserResult::addField($uid, 'tasks', $tasks);

    		return Hapyfish2_Magic_Bll_UserResult::all();
        }
        else {
            return Hapyfish2_Magic_Bll_UserResult::Error('mapcopy_enter_map_failed');
        }
	}

    //取得副本地图场景
    public static function getMapCopyScene($uid, $mapId, &$userVo, $basicMapInfo)
	{
	    $today = date('Ymd');

	    //get current map copy status
        $curMap = Hapyfish2_Magic_HFC_MapCopy::getUserMapCopyInfo($uid);
        //副本已经被刷新了，回到上级地图
        if (!$curMap || date('Ymd',$curMap['enter_time'])<$today) {
            $backSceneId = $basicMapInfo['parent_scene_id'];
            $userSceneInfo = Hapyfish2_Magic_HFC_User::getUserScene($uid);
			$userVo['currentSceneId'] = $backSceneId;
			$userSceneInfo['cur_scene_id'] = $backSceneId;
			Hapyfish2_Magic_HFC_User::updateUserScene($uid, $userSceneInfo, true);
            return null;
        }
        //get current map copy monsters status
        $rowMap = Hapyfish2_Magic_HFC_MapCopy::getOne($uid, $mapId);
        if (!$rowMap) {
            $listMap = self::initMapCopy($uid, $basicMapInfo);
            if (!$listMap) {
                return null;
            }
            $rowMap = $listMap[$mapId];
        }
        if (!$rowMap) {
            return null;
        }

        //create map transcript info VO
	    $lstData = Hapyfish2_Magic_Cache_BasicInfo::getMapCopyTranscriptList($mapId);
	    $mineList = array();
	    $listMonster = json_decode($rowMap['data'], true);
	    foreach ($lstData['mineList'] as $data) {
	        if (isset($listMonster[$data['id']]) && $listMonster[$data['id']]['cur_hp'] > 0) {
    	        $mineList[] = array(
        					'id' => $data['id'],
        					'x' => $data['pos_x'],
        					'z' => $data['pos_z'],
        					'currentHp' => $listMonster[$data['id']]['cur_hp'],
        					'cid' => $data['cid']
        				);
	        }
	    }

	    $monsterList = array();
	    foreach ($lstData['ghostList'] as $data) {
	        if (isset($listMonster[$data['id']]) && $listMonster[$data['id']]['cur_hp'] > 0) {
	            $monsterList[] = array(
        					'id' => $data['id'],
        					'x' => $data['pos_x'],
        					'z' => $data['pos_z'],
        					'currentHp' => $listMonster[$data['id']]['cur_hp'],
        					'fiddleRangeX' => $data['fiddle_range_x'],
        					'fiddleRangeZ' => $data['fiddle_range_z'],
        					'cid' => $data['cid']
        				);
	        }
	    }

	    $portalList = array();
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

	    $decorList = array();
	    foreach ($lstData['decorList'] as $data) {
	        $decorList[] = array(
    					'id' => $data['id'],
    					'x' => $data['pos_x'],
    					'z' => $data['pos_z'],
    					'mirror' => $data['mirror'],
    					'd_id' => $data['cid']
    				);
	    }

	    $floorList = array();
	    foreach ($lstData['floorList'] as $data) {
	        $floorList = json_decode($data['data'], true);
	    }

	    return array('mineList'=>$mineList, 'monsterList'=>$monsterList, 'portalList'=>$portalList,
	    			 'decorList'=>$decorList, 'floorList'=>$floorList);
	}

    //初始化副本地图
    public static function initMapCopy($uid, $basicMapInfo)
    {
        $info = array();
        $info['uid'] = $uid;
        $info['map_parent'] = $basicMapInfo['parent_id'];
        $info['map_ids'] = $basicMapInfo['id'];
        $info['fids'] = '';
        $info['enter_time'] = time();
        $info['update_time'] = 0;
        $ok = Hapyfish2_Magic_HFC_MapCopy::addUserMapCopyInfo($uid, $info, true);
        if ($ok) {
            $lstMaps = Hapyfish2_Magic_Cache_BasicInfo::getMapCopyList();
            $basicMonster = Hapyfish2_Magic_Cache_BasicInfo::getMapMonsterList();
            $aryMonster = array();
            foreach ($lstMaps as $map) {
                //same series map (Ex 幽灵矿洞)
                if ($map['parent_id'] == $basicMapInfo['parent_id']) {
                    $lstData = Hapyfish2_Magic_Cache_BasicInfo::getMapCopyTranscriptList($map['id']);
                    $tmpData = array();
                    foreach ($lstData['ghostList'] as $data) {
                        $dispRate = (((int)$data['rate_onstage'] > 100) ? 100 : (int)$data['rate_onstage']);
                        $randNum = mt_rand(1, $dispRate);
                        if ($randNum<=$dispRate) {
                            $tmpData[$data['id']] = array('id'=>$data['id'], 'cid'=>$data['cid'], 'max_hp'=>$basicMonster[$data['cid']]['hp'], 'cur_hp'=>$basicMonster[$data['cid']]['hp']);
                        }
                    }
                    foreach ($lstData['mineList'] as $data) {
                        $tmpData[$data['id']] = array('id'=>$data['id'], 'cid'=>$data['cid'], 'max_hp'=>$basicMonster[$data['cid']]['hp'], 'cur_hp'=>$basicMonster[$data['cid']]['hp']);
                    }
                    $aryMonster[] = array('map_id'=>$map['id'], 'data'=>json_encode($tmpData));
                }
                else {
                    break;
                }
            }

            try {
                $dal = Hapyfish2_Magic_Dal_MapCopyMonster::getDefaultInstance();
                $dal->clear($uid);
                $aa = $dal->init($uid, $aryMonster);
            }
            catch (Exception $e) {
    		    info_log('initMapCopy:init:'.$e->getMessage(), 'err_Bll_MapCopy');
    		    return null;
    		}
    		//clear cache
    		Hapyfish2_Magic_HFC_MapCopy::clearAll($uid);
        }
        else {
            info_log('initMapCopy:update-mapcopyinfo-failed', 'err_Bll_MapCopy');
            return null;
        }

        $listMonster = Hapyfish2_Magic_HFC_MapCopy::getAll($uid);
        return $listMonster;
    }

    public static function clearMapCopy($uid)
    {
        try {
            //cur map copy info
            $info = array();
            $info['uid'] = $uid;
            $info['map_parent'] = 0;
            $info['map_ids'] = '';
            $info['fids'] = '';
            $info['enter_time'] = 0;
            $info['update_time'] = 0;
            Hapyfish2_Magic_HFC_MapCopy::addUserMapCopyInfo($uid, $info, true);

            $dal = Hapyfish2_Magic_Dal_MapCopyMonster::getDefaultInstance();
            $dal->clear($uid);
            return Hapyfish2_Magic_HFC_MapCopy::clearAll($uid);
        }
        catch (Exception $e) {
		    info_log('clearMapCopy:'.$e->getMessage(), 'err_HFC_MapCopy');
		    return null;
		}
    }

    //进入地图消耗 or 打击怪时消耗
    private static function needCondition($uid, $condition, &$changeResult)
    {
        $coinChange = $goldChange = $mpChange = 0;
		foreach ($condition as $k => $v) {
		    //道具
			if ($v['type'] == 1) {
				$userItemList = Hapyfish2_Magic_HFC_Item::getUserItem($uid);
				if (empty($userItemList) || !isset($userItemList[$v['id']]) || $userItemList[$v['id']]['count'] <= 0) {
					return 'item_not_enough';
				}
				else {
					Hapyfish2_Magic_HFC_Item::useUserItem($uid, $v['id'], $v['num'], $userItemList);
					//$ret['removeItems'] = array(array($v['id'], $v['num']));
				}
			}
			//玩家属性
			else if ($v['type'] == 3) {
				if ($v['id'] == 'coin') {
					$userCoin = Hapyfish2_Magic_HFC_User::getUserCoin($uid);
					if ($userCoin < $v['num']) {
						return 'coin_not_enough';
					}
					else {
						Hapyfish2_Magic_HFC_User::decUserCoin($uid, $v['num']);
						$coinChange -= $v['num'];
					}
				}
				else if ($v['id'] == 'gmoney') {
					$userGold = Hapyfish2_Magic_HFC_User::getUserGold($uid);
					if ($userGold < $v['num']) {
						return 'gold_not_enough';
					}
					else {
			        	$goldInfo = array(
			        		'uid' => $uid,
			        		'cost' => $v['num'],
			        		'summary' => '进入副本/打怪消耗'
			        	);
						$ok = Hapyfish2_Magic_Bll_Gold::consume($uid, $goldInfo);
						$goldChange -= $v['num'];
					}
				}
				else if ($v['id'] == 'mp') {
                    $userMp = Hapyfish2_Magic_HFC_User::getUserMp($uid);
				    if ($userMp < $v['num']) {
						return 'mp_not_enough';
					}
					else {
                        Hapyfish2_Magic_HFC_User::decUserMp($uid, $v['num']);
                        $mpChange -= $v['num'];
					}
				}
			}
		}
		$changeResult = array('coin'=>$coinChange, 'gem'=>$goldChange, 'mp'=>$mpChange);
		return '';
    }

    //打击怪后获得奖励
    private static function awardCondition($uid, $condition, &$changeResult)
    {
        $coinChange = $goldChange = $mpChange = $expChange = 0;
        $decors = null;
        $numPar = 100;
		foreach ($condition as $k => $v) {
		    //check if in random percent
		    $bingo = true;
		    if (isset($v['per'])) {
                if ($v['per'] < 1) {
                    $aryKeys['hit'] = $v['per']*$numPar;
                    $aryKeys['nohit'] = 100*$numPar - $v['per']*$numPar;
                }
                else if ($v['per'] <= 100) {
                    $aryKeys['hit'] = $v['per'];
                    $aryKeys['nohit'] = 100 - $v['per'];
                }
                else {
                    $aryKeys['hit'] = 100;
                }
                $hit = self::_randomKeyForOdds($aryKeys);
                if ($hit == 'nohit') {
                    $bingo = false;
                }
		    }
		    if (!$bingo) {
		        continue;
		    }
		    //道具
			if ($v['type'] == 1) {
				Hapyfish2_Magic_HFC_Item::addUserItem($uid, $v['id'], $v['num']);
			}
			//装饰物
			else if ($v['type'] == 2) {
                $decors[] = array($v['id'], $v['num']);
			}
			//玩家属性
			else if ($v['type'] == 3) {
				if ($v['id'] == 'coin') {
				    $ok = Hapyfish2_Magic_HFC_User::incUserCoin($uid, $v['num']);
				    if ($ok) {
					    $coinChange += $v['num'];
				    }
				}
				else if ($v['id'] == 'gmoney') {
				    $ok = Hapyfish2_Magic_Bll_Gold::add($uid, array('gold'=>$v['num'], 'type'=>'5'));
				    if ($ok) {
					    $goldChange += $v['num'];
				    }
				}
				else if ($v['id'] == 'mp') {
                    $ok = Hapyfish2_Magic_HFC_User::incUserMp($uid, $v['num']);
                    if ($ok) {
                        $mpChange += $v['num'];
                    }
				}
			    else if ($v['id'] == 'exp') {
                    //$ok = Hapyfish2_Magic_HFC_User::incUserExp($uid, $v['num']);
                    //if ($ok) {
                    $expChange += $v['num'];
                    //}
				}
			}
		}//end for

        if ($decors) {
            $awardRot = new Hapyfish2_Magic_Bll_Award();
			$awardRot->setDecorList($decors);
			$awardRot->sendOne($uid);
		}

		$changeResult = array('coin'=>$coinChange, 'gem'=>$goldChange, 'mp'=>$mpChange, 'exp'=>$expChange);
		return '';
    }

    //怪反击， 如果不够消耗 则不扣除相应的消耗
    private static function reactCondition($uid, $condition, &$changeResult)
    {
        $coinChange = $goldChange = $mpChange = 0;
        $decors = null;
        $numPar = 100;
		foreach ($condition as $k => $v) {
		    //check if in randam percent
		    $bingo = true;
		    if (isset($v['per'])) {
                if ($v['per'] < 1) {
                    $aryKeys['hit'] = $v['per']*$numPar;
                    $aryKeys['nohit'] = 100*$numPar - $v['per']*$numPar;
                }
                else if ($v['per'] <= 100) {
                    $aryKeys['hit'] = $v['per'];
                    $aryKeys['nohit'] = 100 - $v['per'];
                }
                else {
                    $aryKeys['hit'] = 100;
                }
                $hit = self::_randomKeyForOdds($aryKeys);
                if ($hit == 'nohit') {
                    $bingo = false;
                }
		    }
		    if (!$bingo) {
		        continue;
		    }
		    //道具
			if ($v['type'] == 1) {
				$userItemList = Hapyfish2_Magic_HFC_Item::getUserItem($uid);
				if (empty($userItemList) || !isset($userItemList[$v['id']]) || $userItemList[$v['id']]['count'] <= 0) {
					//return 'item_not_enough';
				}
				else {
					Hapyfish2_Magic_HFC_Item::useUserItem($uid, $v['id'], $v['num'], $userItemList);
				}
			}
			//玩家属性
			else if ($v['type'] == 3) {
				if ($v['id'] == 'coin') {
					$userCoin = Hapyfish2_Magic_HFC_User::getUserCoin($uid);
					if ($userCoin < $v['num']) {
						//return 'coin_not_enough';
						Hapyfish2_Magic_HFC_User::decUserCoin($uid, $userCoin);
						$coinChange -= $userCoin;
					}
					else {
						Hapyfish2_Magic_HFC_User::decUserCoin($uid, $v['num']);
						$coinChange -= $v['num'];
					}
				}
				/*else if ($v['id'] == 'gmoney') {
					$userGold = Hapyfish2_Magic_HFC_User::getUserGold($uid);
					if ($userGold < $v['num']) {
						//return 'gold_not_enough';
						$goldInfo = array(
			        		'uid' => $uid,
			        		'cost' => $userGold,
			        		'summary' => '副本怪反击'
			        	);
						$ok = Hapyfish2_Magic_Bll_Gold::consume($uid, $goldInfo);
						$goldChange -= $v['num'];
					}
					else {
			        	$goldInfo = array(
			        		'uid' => $uid,
			        		'cost' => $v['num'],
			        		'summary' => '副本怪反击'
			        	);
						$ok = Hapyfish2_Magic_Bll_Gold::consume($uid, $goldInfo);
						$goldChange -= $v['num'];
					}
				}*/
				else if ($v['id'] == 'mp') {
                    $userMp = Hapyfish2_Magic_HFC_User::getUserMp($uid);
				    if ($userMp < $v['num']) {
						//return 'mp_not_enough';
						Hapyfish2_Magic_HFC_User::decUserMp($uid, $userMp);
                        $mpChange -= $userMp;
					}
					else {
                        Hapyfish2_Magic_HFC_User::decUserMp($uid, $v['num']);
                        $mpChange -= $v['num'];
					}
				}
			}
		}//end for

		$changeResult = array('coin'=>$coinChange, 'gem'=>$goldChange, 'mp'=>$mpChange);
		return '';
    }

	/**
	 * generate random by key=>odds
	 *
	 * @param array $aryKeys
	 * @return integer
	 */
	private static function _randomKeyForOdds($aryKeys)
	{
		$tot = 0;
		$aryTmp = array();
		foreach ($aryKeys as $key => $odd) {
			$tot += $odd;
			$aryTmp[$key] = $tot;
		}
		$rnd = mt_rand(1,$tot);

		foreach ($aryTmp as $key=>$value) {
			if ($rnd <= $value) {
				return $key;
			}
		}
	}
}