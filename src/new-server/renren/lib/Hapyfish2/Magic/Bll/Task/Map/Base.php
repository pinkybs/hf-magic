<?php

class Hapyfish2_Magic_Bll_Task_Map_Base
{
	public static function check($uid, $taskId)
	{
		$basicTaskInfo = Hapyfish2_Magic_Cache_BasicInfo::getMapTaskInfo($taskId);
		if (!$basicTaskInfo) {
			return Hapyfish2_Magic_Bll_UserResult::Error('task_id_error');
		}

		//check task is arrived
        $task = Hapyfish2_Magic_HFC_TaskMap::getOne($uid, $taskId);
        $num = $task['cur_num'];
    	if ($num < $basicTaskInfo['num']) {
    		return Hapyfish2_Magic_Bll_UserResult::Error('task_state_error');
    	}

		//check task is completed for today
		$today = date('Ymd');
		if (1 == $task['award_status'] && $today==date('Ymd', $task['end_time'])) {
			return Hapyfish2_Magic_Bll_UserResult::Error('task_has_completed');
		}

		//complete task
		$task['cur_num'] = 0;
		$task['award_status'] = 1;
		$task['end_time'] = time();
		$task['complete_count'] = (int)($task['complete_count']) + 1;
		$ok = Hapyfish2_Magic_HFC_TaskMap::updateOne($uid, $taskId, $task);
		//task awards
		if ($ok) {
			//
			$awardRot = new Hapyfish2_Magic_Bll_Award();
			$award = json_decode($basicTaskInfo['award_conditions'], true);
			if (!empty($award)) {
				$rst = self::awardCondition($uid, $award);
			}

			//refresh task list vo
			$changeTasks = Hapyfish2_Magic_Bll_Task::getUserMapTask($uid, $basicTaskInfo['map_parent_id']);
			Hapyfish2_Magic_Bll_UserResult::addField($uid, 'changeTasks', $changeTasks);
		}

		return Hapyfish2_Magic_Bll_UserResult::all();
	}

	public static function trigger($uid, $type, $data)
	{
	    $pMapId = $data['pMapId'];
	    $cid = $data['cid'];
	    $num = $data['num'];
		$basicMapTask = Hapyfish2_Magic_Cache_BasicInfo::getMapTaskListByPMapId($pMapId);
		if (!$basicMapTask) {
			return;
		}

		$changeTasks = array();
		foreach ($basicMapTask as $tid=>$task) {
            if ($type == $task['type'] && $cid == $task['cid']) {
                $rowTask = Hapyfish2_Magic_HFC_TaskMap::getOne($uid, $tid);
                if ($rowTask && $rowTask['award_status'] != 1) {
                    $newCurNum = (int)$rowTask['cur_num'] + $num;
                    $state = 0;
                    //可领取状态
                    if ($newCurNum >= $task['num']) {
                        $newCurNum = $task['num'];
                        $state = 1;
                    }
                    $rowTask['cur_num'] = $newCurNum;
                    $ok = Hapyfish2_Magic_HFC_TaskMap::updateOne($uid, $tid, $rowTask);
                    if ($ok) {
                        $changeTasks[] = array('t_id' => $tid, 'fc_curNums' => array($newCurNum), 'state' => $state);
                    }
                }
            }
		}

        Hapyfish2_Magic_Bll_UserResult::addField($uid, 'changeTasks', $changeTasks);
	}


    //获得奖励
    private static function awardCondition($uid, $condition)
    {
        $coinChange = $goldChange = $expChange = 0;
        $decors = $items = array();

        $award = 0;
		foreach ($condition as $k => $v) {
		    //道具
			if ($v['type'] == 1) {
			    $items[] = array($v['id'], $v['num']);
			    $award = 1;
			}
			//装饰物
			else if ($v['type'] == 2) {
                $decors[] = array($v['id'], $v['num']);
                $award = 1;
			}
			//玩家属性
			else if ($v['type'] == 3) {
				if ($v['id'] == 'coin') {
					$coinChange += $v['num'];
					$award = 1;
				}
				else if ($v['id'] == 'gmoney') {
					$goldChange += $v['num'];
					$award = 1;
				}
			    else if ($v['id'] == 'exp') {
                    $expChange += $v['num'];
                    $award = 1;
				}
			}
		}//end for

		if ($award) {
    		$awardRot = new Hapyfish2_Magic_Bll_Award();
    		if ($coinChange) {
    		    $awardRot->setCoin($coinChange);
    		}
		    if ($goldChange) {
    		    $awardRot->setGold($goldChange, 8);
    		}
		    if ($expChange) {
    		    $awardRot->setExp($expChange);
    		}
		    if ($items) {
    			$awardRot->setItemList($items);
    		}
            if ($decors) {
    			$awardRot->setDecorList($decors);
    		}

    		$awardRot->sendOne($uid);
		}

		$changeResult = array('coin'=>$coinChange, 'gem'=>$goldChange, 'exp'=>$expChange, 'items'=>$items, 'decors'=>$decors);
		return $changeResult;
    }
}