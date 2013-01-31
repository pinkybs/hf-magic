<?php

class Hapyfish2_Magic_Bll_Task_Tutorial_Base
{
    public static function tutorialCompleted($uid)
    {
    	$ret = true;

    	$taskList = array(1002, 1003, 1005, 1006);
    	foreach ($taskList as $taskId) {
	    	$ok = Hapyfish2_Magic_Cache_Task::isCompletedTask($uid, $taskId);
	    	if (!$ok) {
				$ret = false;
				break;
	    	}
    	}

    	return $ret;
    }

	public static function check($uid, $taskId)
	{
		$taskInfo = Hapyfish2_Magic_Cache_BasicInfo::getTaskTutorialInfo($taskId);
		if (!$taskInfo) {
			return Hapyfish2_Magic_Bll_UserResult::Error('task_id_error');
		}

		$ok = Hapyfish2_Magic_Cache_Task::isCompletedTask($uid, $taskId);
		if ($ok) {
			return Hapyfish2_Magic_Bll_UserResult::Error('task_has_completed');
		}

		$helpInfo = Hapyfish2_Magic_Cache_UserHelp::getHelpInfo($uid);
		$userHelpList = $helpInfo['helpList'];
		$helpIndex = (int)$taskId - 1000;
		if ($userHelpList[$helpIndex] == 0) {
			return Hapyfish2_Magic_Bll_UserResult::Error('task_not_finshed');
		}

		$ok = Hapyfish2_Magic_Cache_Task::completeTask($uid, $taskId);
		if ($ok) {
			//新手任务全部完成，开始主线任务
			$over = self::tutorialCompleted($uid);
			if ($over) {
				$info = Hapyfish2_Magic_HFC_TaskOpen::getInfo($uid);
				$info['trunk'] = 2001;
				$info['trunk_track_num'] = 0;
				$info['trunk_start'] = 1;
				$ok2 = Hapyfish2_Magic_HFC_TaskOpen::save($uid, $info, true);
				if ($ok2) {
					$changeTasks = array(
						array('t_id' => 2001, 'fc_curNums' => array(0), 'state' => 0)
					);
					Hapyfish2_Magic_Bll_UserResult::addField($uid, 'changeTasks', $changeTasks);
				}
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
		$taskInfo = Hapyfish2_Magic_Cache_BasicInfo::getTaskTutorialInfo($taskId);
		if (!$taskInfo) {
			return;
		}

		$changeTasks = array(
			array('t_id' => $taskId, 'fc_curNums' => array(1), 'state' => 1)
		);

		Hapyfish2_Magic_Bll_UserResult::addField($uid, 'changeTasks', $changeTasks);
	}
}