<?php

class Hapyfish2_Magic_Bll_Task
{
    private static $openTask = null;
    private static $trunkTaskInfo = null;

	/**
     * check user task info
     *
     * @param integer $uid
     * @param integer $taskId
     * @return array
     */
    public static function finishTask($uid, $taskId)
    {
    	$taskType = substr($taskId, 0, 1);

        //task type,
        //1: tutorial, 2: trunk, 3: branch, 9:daily 4:map
        if ($taskType == 1) {
            $typeName = 'Tutorial';
            $name = 'Base';
        } else if ($taskType == 2) {
            $typeName = 'Trunk';
            $name = 'Base';
        } else if ($taskType == 3) {
            $typeName = 'Branch';
            $name = 'Base';
        } else if ($taskType == 9) {
            $typeName = 'Daily';
            $name = 'Base';
        } else if ($taskType == 4) {
            $typeName = 'Map';
            $name = 'Base';
        } else {
        	return Hapyfish2_Magic_Bll_UserResult::Error('task_id_error');
        }

        $implFile = 'Hapyfish2/Magic/Bll/Task/' . $typeName . '/' . $name . '.php';
        if (is_file(LIB_DIR . '/' . $implFile)) {
            require_once $implFile;
            $implClassName = 'Hapyfish2_Magic_Bll_Task_' . $typeName . '_' . $name;
            $impl = new $implClassName();

            $result = $impl->check($uid, $taskId);

            return $result;
        }

        return Hapyfish2_Magic_Bll_UserResult::Error('task_error');
    }

    public static function getUserTutorialTask($uid, $userHelpList)
    {
    	$list = array();

    	$taskList = array(1002, 1003, 1005, 1006);
    	foreach ($taskList as $taskId) {
	    	$ok = Hapyfish2_Magic_Cache_Task::isCompletedTask($uid, $taskId);
	    	if (!$ok) {
	    		$helpIndex = $taskId - 1000;
	    		$list[] = array(
	    			't_id' => $taskId,
	    			'fc_curNums' => array(1),
	    			'state' => $userHelpList[$helpIndex]
	    		);
	    	}
    	}

    	return $list;
    }

    public static function getUserTrunkTask($uid)
    {
    	$list = array();

    	$taskOpenInfo = Hapyfish2_Magic_HFC_TaskOpen::getInfo($uid);
    	if ($taskOpenInfo && !empty($taskOpenInfo['trunk']) && $taskOpenInfo['trunk_start']>0) {
    		$taskId = $taskOpenInfo['trunk'];
    		$taskInfo = Hapyfish2_Magic_Cache_BasicInfo::getTaskTrunkInfo($taskId);
    		$taskType = $taskInfo['type'];
    		//材料物品拥有数量类
    		if ($taskType == 102) {
    			$cid = $taskInfo['cid'];
    			$num = Hapyfish2_Magic_HFC_Item::getUserItemCount($uid, $cid);
    		} else if ($taskType == 201) {
				$userLevelInfo = Hapyfish2_Magic_HFC_User::getUserLevel($uid);
				$num = $userLevelInfo['level'];
    		} else if ($taskType == 202) {
				$userMagic = Hapyfish2_Magic_Cache_Magic::getList($uid, true);
				$num = count($userMagic['study_ids']) + count($userMagic['trans_ids']);
    		} else if ($taskType == 203) {
    			//邀请好友数
    			//$userAchie = Hapyfish2_Magic_HFC_Achievement::getUserAchievement($uid);
    			//$num = $userAchie['num_1'];
    			//改成好友数了
    			$userFriendInfo = Hapyfish2_Platform_Cache_Friend::getFriend($uid);
    			if (!$userFriendInfo) {
    				$num = 0;
    			} else {
    				$num = $userFriendInfo['count'];
    			}
    		} else {
    			$num = $taskOpenInfo['trunk_track_num'];
    	    }

   	    	if ($num >= $taskInfo['num']) {
   				$state = 1;
   				$num = $taskInfo['num'];
   				$ok = Hapyfish2_Magic_Cache_Task::isCompletedTask($uid, $taskId);
   			} else {
				$state = 0;
				$ok = false;
   			}

			if (!$ok) {
	    		$list[] = array(
	    			't_id' => $taskId,
	    			'fc_curNums' => array($num),
	    			'state' => $state
	    		);
    		}

    	}

    	return $list;
    }

    public static function getBranchTask($uid)
    {
    	$list = array();

    	$taskOpenInfo = Hapyfish2_Magic_HFC_TaskOpen::getInfo($uid);
    	if ($taskOpenInfo && !empty($taskOpenInfo['branch'])) {
    		$branchList = $taskOpenInfo['branch'];
    		foreach ($branchList as $taskId) {
    			$taskInfo = Hapyfish2_Magic_Cache_BasicInfo::getTaskBranchInfo($taskId);
    			$taskType = $taskInfo['type'];
    			if ($taskType == 113) {
					$cid = $taskInfo['cid'];
					$num = Hapyfish2_Magic_HFC_Item::getUserItemCount($uid, $cid);
    			} else if ($taskType == 112) {
					$cid = $taskInfo['cid'];
	    			$num = Hapyfish2_Magic_Bll_Bag::getDecorCount($uid, $cid);
    			} else {
    				continue;
    			}

				if ($num >= $taskInfo['num']) {
	   				$state = 1;
	   				$num = $taskInfo['num'];
	   				$ok = Hapyfish2_Magic_Cache_Task::isCompletedTask($uid, $taskId);
	   			} else {
					$state = 0;
					$ok = false;
	   			}

   				if (!$ok) {
		    		$list[] = array(
		    			't_id' => $taskId,
		    			'fc_curNums' => array($num),
		    			'state' => $state
		    		);
	    		}
    		}
    	}

    	return $list;
    }

    public static function getDailyTask($uid)
    {
        $list = array();

    	$taksList = Hapyfish2_Magic_Cache_BasicInfo::getTaskDailyList();
        if (empty($taksList)) {
        	return $list;
        }

    	//get user daily achievement info
        $achie = Hapyfish2_Magic_HFC_AchievementDaily::getUserAchievementDaily($uid);

        $tids = Hapyfish2_Magic_Cache_TaskDaily::getIds($uid);

        foreach ($taksList as $task) {
        	$fieldName = 'num_' . $task['need_field'];
        	$num = $achie[$fieldName];
        	$ok = false;
        	if ($num >= $task['num']) {
        	    if (is_array($tids) && in_array($task['id'], $tids)) {
		    		$ok = true;
		    	}
        		$num = $task['num'];
        		$state = 1;
        	} else {
        		$state = 0;
        	}
        	if (!$ok) {
	    		$list[] = array(
	    			't_id' => $task['id'],
	    			'fc_curNums' => array($num),
	    			'state' => $state
	    		);
	    		//只显示一个
	    		break;
        	}
        }

        return $list;
    }

    public static function getUserTask($uid)
    {
    	//
    }

    public static function listen($uid, $type, $data)
    {
    	if (self::$openTask == null) {
    		self::$openTask = Hapyfish2_Magic_HFC_TaskOpen::getInfo($uid);
    	}

    	if (self::$openTask) {
    		if (self::$openTask['trunk']) {
    			if (self::$trunkTaskInfo == null) {
    				self::$trunkTaskInfo = Hapyfish2_Magic_Cache_BasicInfo::getTaskTrunkInfo(self::$openTask['trunk']);
    			}

    			if (self::$trunkTaskInfo && self::$trunkTaskInfo['type'] == $type) {
    				self::handleTrunkTask($uid, $type, self::$openTask, self::$trunkTaskInfo, $data);
    			}
    		}
    	}
    }

    private static function handleTrunkTask($uid, $type, $openTask, $trunkTaskInfo, $data)
    {
        $name = 'T' . $type;
    	$implFile = 'Hapyfish2/Magic/Bll/Task/Trunk/' . $name . '.php';
        if (is_file(LIB_DIR . '/' . $implFile)) {
            require_once $implFile;
            $implClassName = 'Hapyfish2_Magic_Bll_Task_Trunk_' . $name;
            $impl = new $implClassName();

            $impl->trigger($uid, $openTask, $trunkTaskInfo, $data);
        }
    }


    /* map task */
    public static function getUserMapTask($uid, $pMapId)
    {
        $list = array();
        $basicMapTask = Hapyfish2_Magic_Cache_BasicInfo::getMapTaskListByPMapId($pMapId);
        if (empty($basicMapTask)) {
        	return $list;
        }

        $today = date('Ymd');
    	$taskList = Hapyfish2_Magic_HFC_TaskMap::getAllInMap($uid, $pMapId);
    	if (!$taskList) {
            Hapyfish2_Magic_HFC_TaskMap::initMapTask($uid, $pMapId, $basicMapTask, $taskList);
    	}

    	$completeList = array();
    	$subTaskList = array();
    	foreach ($taskList as $tid=>$tmpData) {
    	    if ($tmpData['map_parent_id'] == $pMapId) {
        	    //今天已经完成过list
    	        if (1 == $tmpData['award_status'] && $today==date('Ymd', $tmpData['end_time'])) {
    	            $completeList[] = $tmpData['tid'];
    	        }
    	        //有子任务的list
    	        if ($basicMapTask[$tid]['prev_task_id']) {
    	            $prevId = $basicMapTask[$tid]['prev_task_id'];
    	            $subTaskList[$prevId] = $tid;
    	        }
    	    }
    	}
    	foreach ($taskList as $tid=>$data) {
    	    if ($data['map_parent_id'] == $pMapId) {
    	        //是否今天已经完成过了
    	        if (in_array($tid, $completeList)) {
    	            //$completeList[] = $data['tid'];
                    continue;
    	        }
        	    //任务已经完成过（昨天或者更早前），需要重置成开始状态
    	        if (1 == $data['award_status'] && $today>date('Ymd', $data['end_time'])) {
    	            //检查子任务是否完成了
    	            $parentId = $tid;
    	            $allCpt = true;
    	            while (isset($subTaskList[$parentId])) {
        	            if (!in_array($subTaskList[$parentId], $completeList)) {
                            $allCpt = false;
                        }
                        $parentId = $subTaskList[$parentId];
    	            }
    	            //子任务都被完成了，重置任务
    	            if ($allCpt) {
    	                $data['cur_num'] = 0;
    	                $data['award_status'] = 0;
    	                $data['award_status'] = 0;
    	                $data['begin_time'] = time();
    	                $data['end_time'] = 0;
                        Hapyfish2_Magic_HFC_TaskMap::updateOne($uid, $tid, $data);
    	            }

    	        }
    	        //是否子任务
    	        if ($basicMapTask[$tid]['prev_task_id']) {
    	            //父级任务未完成  不显示
    	            if (!in_array($basicMapTask[$tid]['prev_task_id'], $completeList)) {
                        continue;
    	            }
    	        }
    	        $state = 0;
    	        if ($data['cur_num'] >= $basicMapTask[$tid]['num']) {
    	            $state = 1;
    	            $data['cur_num'] = $basicMapTask[$tid]['num'];
    	        }

    	        $list[] = array(
	    			't_id' => $data['tid'],
	    			'fc_curNums' => array($data['cur_num']),
	    			'state' => $state  //0-不可领取 1-可以领取
	    		);
    	    }
    	}

    	return $list;
    }

    public static function clearMapTask($uid, $pMapId)
    {
        try {
            $dal = Hapyfish2_Magic_Dal_TaskMap::getDefaultInstance();
            $dal->clear($uid, $pMapId);
            return Hapyfish2_Magic_HFC_TaskMap::clearAll($uid, $pMapId);
        }
        catch (Exception $e) {
		    info_log('clearMapTask:'.$e->getMessage(), 'err_HFC_Task');
		    return null;
		}
    }
    /* map task*/
}