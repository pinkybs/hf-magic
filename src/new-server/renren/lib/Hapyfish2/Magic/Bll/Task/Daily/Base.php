<?php

class Hapyfish2_Magic_Bll_Task_Daily_Base
{
	public static function check($uid, $taskId)
	{
		$taskInfo = Hapyfish2_Magic_Cache_BasicInfo::getTaskDailyInfo($taskId);
		if (!$taskInfo) {
			return Hapyfish2_Magic_Bll_UserResult::Error('task_id_error');
		}

		//检查是否已经满足要求
		$achie = Hapyfish2_Magic_HFC_AchievementDaily::getUserAchievementDaily($uid);
        $fieldName = 'num_' . $taskInfo['need_field'];
        $num = $achie[$fieldName];
    	if ($num < $taskInfo['num']) {
    		return Hapyfish2_Magic_Bll_UserResult::Error('task_state_error');
    	}

		$ok = Hapyfish2_Magic_Cache_TaskDaily::isCompletedTask($uid, $taskId);
		if ($ok) {
			return Hapyfish2_Magic_Bll_UserResult::Error('task_has_completed');
		}

		$ok = Hapyfish2_Magic_Cache_TaskDaily::completeTask($uid, $taskId);
		if ($ok) {
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

			//显示下一个日常任务
			$nextTaskId = $taskId + 1;
			$nextTaskInfo = Hapyfish2_Magic_Cache_BasicInfo::getTaskDailyInfo($nextTaskId);
			if ($nextTaskInfo) {
				$fieldNum = 'num_' . $nextTaskInfo['need_field'];
				if ($achie[$fieldNum] >= $nextTaskInfo['num']) {
					$num = $nextTaskInfo['num'];
					$state = 1;
				} else {
					$num = $achie[$fieldNum];
					$state = 0;
				}

				$changeTasks = array(
					array('t_id' => $nextTaskId, 'fc_curNums' => array($num), 'state' => $state)
				);

				Hapyfish2_Magic_Bll_UserResult::addField($uid, 'changeTasks', $changeTasks);
			}
		}

		return Hapyfish2_Magic_Bll_UserResult::all();
	}

	public static function trigger($uid, $taskId)
	{
		$taskInfo = Hapyfish2_Magic_Cache_BasicInfo::getTaskDailyInfo($taskId);
		if (!$taskInfo) {
			return;
		}

		$achie = Hapyfish2_Magic_HFC_AchievementDaily::getUserAchievementDaily($uid);
		if (!$achie) {
			return;
		}

		$fieldNum = 'num_' . $taskInfo['need_field'];

		if ($achie[$fieldNum] >= $taskInfo['num']) {
			return;
		}

		$achie[$fieldNum] += 1;

		$ok = Hapyfish2_Magic_HFC_AchievementDaily::saveUserAchievementDaily($uid, $achie);
		if ($ok) {
			//上个任务完成了，才会显示本任务
			//是第一个任务，则显示
			if ($taskId == 9001) {
				$ok2 = true;
			} else {
				$preTaskId = $taskId - 1;
				$ok2 = Hapyfish2_Magic_Cache_TaskDaily::isCompletedTask($uid, $preTaskId);
			}

			if ($ok2) {
				if ($achie[$fieldNum] >= $taskInfo['num']) {
					$num = $taskInfo['num'];
					$state = 1;
				} else {
					$num = $achie[$fieldNum];
					$state = 0;
				}

				$changeTasks = array(
					array('t_id' => $taskId, 'fc_curNums' => array($num), 'state' => $state)
				);

				Hapyfish2_Magic_Bll_UserResult::addField($uid, 'changeTasks', $changeTasks);
			}
		}
	}
}