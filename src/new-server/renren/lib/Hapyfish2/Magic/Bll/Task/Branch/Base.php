<?php

class Hapyfish2_Magic_Bll_Task_Branch_Base
{
	public static function check($uid, $taskId)
	{
		$taskInfo = Hapyfish2_Magic_Cache_BasicInfo::getTaskBranchInfo($taskId);
		if (!$taskInfo) {
			return Hapyfish2_Magic_Bll_UserResult::Error('task_id_error');
		}

		//检查是否已经满足要求
	    $taskOpenInfo = Hapyfish2_Magic_HFC_TaskOpen::getInfo($uid);
    	if (!$taskOpenInfo || empty($taskOpenInfo['branch']) || !in_array($taskId, $taskOpenInfo['branch'])) {
    		return Hapyfish2_Magic_Bll_UserResult::Error('task_id_error');
    	}

   		$taskType = $taskInfo['type'];
   		//材料物品拥有数量类
   		if ($taskType == 113) {
   			$cid = $taskInfo['cid'];
   			$num = Hapyfish2_Magic_HFC_Item::getUserItemCount($uid, $cid);
   		} else if ($taskType == 112) { //装饰物
   			$cid = $taskInfo['cid'];
   			$numInfo = Hapyfish2_Magic_Bll_Bag::getDecorCount($uid, $cid, true);
   			$num = $numInfo['num'];
   		} else {
   			return Hapyfish2_Magic_Bll_UserResult::Error('task_type_error');
   	    }

    	if ($num < $taskInfo['num']) {
			return Hapyfish2_Magic_Bll_UserResult::Error('task_state_error');
		}

		$ok = Hapyfish2_Magic_Cache_Task::isCompletedTask($uid, $taskId);
		if ($ok) {
			return Hapyfish2_Magic_Bll_UserResult::Error('task_has_completed');
		}

		$ok = Hapyfish2_Magic_Cache_Task::completeTask($uid, $taskId);
		if ($ok) {
			//删掉完成的剧情任务
			$branch = array();
			foreach ($taskOpenInfo['branch'] as $id) {
				if ($id != $taskId) {
					$branch[] = $id;
				}
			}
			$taskOpenInfo['branch'] = $branch;
			$ok2 = Hapyfish2_Magic_HFC_TaskOpen::save($uid, $taskOpenInfo, true);

			//扣除物品
			if ($taskType == 113) {
				Hapyfish2_Magic_HFC_Item::useUserItem($uid, $cid, $taskInfo['num']);
			} else if ($taskType == 112) {
				Hapyfish2_Magic_Bll_Bag::removeDecor($uid, $cid, $numInfo['type'], $numInfo['idList'], $taskInfo['num']);
			}

			//
			$awardRot = new Hapyfish2_Magic_Bll_Award();
			$prop = json_decode($taskInfo['award_prop'], true);
			if (!empty($prop)) {
				$awardRot->setProp($prop);
			}

			$items = json_decode($taskInfo['award_items'], true);
			if (!empty($items)) {
				$awardRot->setItemList($items);
			}

			$decors = json_decode($taskInfo['award_decors'], true);
			if (!empty($decors)) {
				$awardRot->setDecorList($decors);
			}

			$awardRot->sendOne($uid);
		}

		return Hapyfish2_Magic_Bll_UserResult::all();
	}

    public static function trigger($uid, $taskId)
	{
	    $taskInfo = Hapyfish2_Magic_Cache_BasicInfo::getTaskBranchInfo($taskId);
        $taskType = $taskInfo['type'];
		if ($taskType == 113) {
			$cid = $taskInfo['cid'];
			$num = Hapyfish2_Magic_HFC_Item::getUserItemCount($uid, $cid);
		} else if ($taskType == 112) {
			$cid = $taskInfo['cid'];
			$num = Hapyfish2_Magic_Bll_Bag::getDecorCount($uid, $cid);
		}

		if ($num >= $taskInfo['num']) {
			$state = 1;
			$num = $taskInfo['num'];
		} else {
			$state = 0;
		}

		$changeTasks = array(
			array('t_id' => $taskId, 'fc_curNums' => array($num), 'state' => $state)
		);
		Hapyfish2_Magic_Bll_UserResult::addField($uid, 'changeTasks', $changeTasks);
	}

    public static function triggerMul($uid, $taskIdList)
	{
	    foreach ($taskIdList as $taskId) {
	        self::trigger($uid, $taskId);
	    }
	}
}