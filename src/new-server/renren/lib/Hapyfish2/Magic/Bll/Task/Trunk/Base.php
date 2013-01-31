<?php

class Hapyfish2_Magic_Bll_Task_Trunk_Base
{
	public static function check($uid, $taskId)
	{
		$taskInfo = Hapyfish2_Magic_Cache_BasicInfo::getTaskTrunkInfo($taskId);
		if (!$taskInfo) {
			return Hapyfish2_Magic_Bll_UserResult::Error('task_id_error');
		}

		//检查是否已经满足要求
	    $taskOpenInfo = Hapyfish2_Magic_HFC_TaskOpen::getInfo($uid);
    	if (!$taskOpenInfo || empty($taskOpenInfo['trunk']) || $taskOpenInfo['trunk'] != $taskId) {
    		return Hapyfish2_Magic_Bll_UserResult::Error('task_id_error');
    	}

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

    	if ($num < $taskInfo['num']) {
			return Hapyfish2_Magic_Bll_UserResult::Error('task_state_error');
		}

		/*$ok = Hapyfish2_Magic_Cache_Task::isCompletedTask($uid, $taskId);
		if ($ok) {
			return Hapyfish2_Magic_Bll_UserResult::Error('task_has_completed');
		}*/

		$ok = Hapyfish2_Magic_Cache_Task::completeTask($uid, $taskId);
		if ($ok) {
			//完成，触发下一个任务
			$info = Hapyfish2_Magic_HFC_TaskOpen::getInfo($uid);
			$nextTaskId = (int)$taskInfo['child_id'];
			$start = 1;
			if ($nextTaskId > 0) {
				$nextTaskInfo = Hapyfish2_Magic_Cache_BasicInfo::getTaskTrunkInfo($nextTaskId);
				if ($nextTaskInfo) {
					$userLevelInfo = Hapyfish2_Magic_HFC_User::getUserLevel($uid);
					//如果等级不够触发下一个任务
					if ($userLevelInfo['level'] < $nextTaskInfo['level']) {
						$info['trunk_start'] = 0;
						$start = 0;
					}
				} else {
					$start = 0;
				}
			} else {
				$start = 0;
			}

			$info['trunk'] = $nextTaskId;
			$info['trunk_track_num'] = 0;

			$ok2 = Hapyfish2_Magic_HFC_TaskOpen::save($uid, $info, true);
			//info_log($uid . ':' . $taskId, 'taskOver');
			if ($ok2 && $start == 1) {
				$taskType = $nextTaskInfo['type'];

				//材料物品拥有数量类
	    		if ($taskType == 102 || $taskType == 112 || $taskType == 113) {
	    			$cid = $nextTaskInfo['cid'];
	    			$num = Hapyfish2_Magic_HFC_Item::getUserItemCount($uid, $cid);
	    		} else if ($taskType == 201) {
					$userLevelInfo = Hapyfish2_Magic_HFC_User::getUserLevel($uid);
					$num = $userLevelInfo['level'];
	    		} else if ($taskType == 202) {
					$userMagic = Hapyfish2_Magic_Cache_Magic::getList($uid, true);
					$num = count($userMagic['study_ids']) + count($userMagic['trans_ids']);
	    		} else if ($taskType == 203) {
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
	    			$num = 0;
	    	    }

	   	    	if ($num >= $nextTaskInfo['num']) {
	   				$state = 1;
	   				$num = $nextTaskInfo['num'];
	   			} else {
					$state = 0;
	   			}

				$changeTasks = array(
					array('t_id' => $nextTaskId, 'fc_curNums' => array($num), 'state' => $state)
				);

				Hapyfish2_Magic_Bll_UserResult::addField($uid, 'changeTasks', $changeTasks);
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

			//是否要触发剧情
			$storyId = (int)$taskInfo['story_id'];
			if ($storyId > 0) {
				Hapyfish2_Magic_Bll_Story::create($uid, $storyId);
			}
		}

		return Hapyfish2_Magic_Bll_UserResult::all();
	}
}