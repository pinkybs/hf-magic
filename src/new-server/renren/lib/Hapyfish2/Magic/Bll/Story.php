
<?php

class Hapyfish2_Magic_Bll_Story
{
    public static function getInfo($storyId, $showAward = true)
    {
    	$actions = array();
    	$storyInfo = Hapyfish2_Magic_Cache_BasicInfo::getOneStory($storyId);
    	if ($storyInfo) {
    		$n = count($storyInfo);
    		foreach ($storyInfo as $k => $v) {
	    		$actions[] = array(
					'npcId' => $v['npc_id'],
					'avatarId' => $v['avatar_id'],
					'x' => $v['x'],
					'y' => $v['y'],
					'faceX' => $v['face_x'],
					'faceY' => $v['face_y'],
					'content' => $v['content'],
					'camera' => $v['camera'],
					'wait' => $v['wait'],
					'immediately' => $v['immediately'],
					'hide' => $v['hide'],
	    			'chatTime' => $v['chat_time']
	    		);
    		}

    		if ($showAward) {
	    		$lastIndex = $n - 1;
	    	    if ($v['task_id'] > 0) {
	    			$actions[$lastIndex]['taskId'] = $v['task_id'];
	    		}
	    		if ($v['decors'] != '' && $v['decors'] != '[]') {
	    			$actions[$lastIndex]['decorId'] = json_decode($v['decors'], true);
	    		}
	    	    if ($v['items'] != '' && $v['items'] != '[]') {
	    			$actions[$lastIndex]['itemId'] = json_decode($v['items'], true);
	    		}
	    	    if ($v['coin'] > 0) {
	    			$actions[$lastIndex]['coin'] = $v['coin'];
	    		}
	    	    if ($v['gold'] > 0) {
	    			$actions[$lastIndex]['gem'] = $v['gold'];
	    		}
    		}
    	}

    	return array('actions' => $actions);
    }

    public static function create($uid, $storyId)
    {
        $actions = array();
    	$storyInfo = Hapyfish2_Magic_Cache_BasicInfo::getOneStory($storyId);
    	if ($storyInfo) {
    		$n = count($storyInfo);
    		foreach ($storyInfo as $k => $v) {
	    		$actions[] = array(
					'npcId' => $v['npc_id'],
					'avatarId' => $v['avatar_id'],
					'x' => $v['x'],
					'y' => $v['y'],
					'faceX' => $v['face_x'],
					'faceY' => $v['face_y'],
					'content' => $v['content'],
					'camera' => $v['camera'],
					'wait' => $v['wait'],
					'immediately' => $v['immediately'],
					'hide' => $v['hide'],
	    			'chatTime' => $v['chat_time']
	    		);
    		}

    		$lastIndex = $n - 1;

    		//有剧情任务
    	    if ($v['task_id'] > 0) {
    	    	$taskId = (int)$v['task_id'];
    			$actions[$lastIndex]['taskId'] = $taskId;
    			$info = Hapyfish2_Magic_HFC_TaskOpen::getInfo($uid);
    			if ($info) {
    				$info['branch'][] = $taskId;
    				$ok2 = Hapyfish2_Magic_HFC_TaskOpen::save($uid, $info, true);
    				if ($ok2) {
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
    			}
    		}

    		$awardRot = new Hapyfish2_Magic_Bll_Award();
    		$hasAward = false;
    		if ($v['decors'] != '' && $v['decors'] != '[]') {
    			$decors = json_decode($v['decors'], true);
    			$actions[$lastIndex]['decorId'] = $decors;
    			$awardRot->setDecorList($decors);
    			$hasAward = true;
    		}
    	    if ($v['items'] != '' && $v['items'] != '[]') {
    	    	$items = json_decode($v['items'], true);
    			$actions[$lastIndex]['itemId'] = $items;
    			$awardRot->setItemList($items);
    			$hasAward = true;
    		}
    	    if ($v['coin'] > 0) {
    			$actions[$lastIndex]['coin'] = $v['coin'];
    			$awardRot->setCoin($v['coin']);
    			$hasAward = true;
    		}
    	    if ($v['gold'] > 0) {
    			$actions[$lastIndex]['gem'] = $v['gold'];
    			$awardRot->setGold($v['gold'], 6);
    			$hasAward = true;
    		}

    		if ($hasAward) {
    			$awardRot->sendOne($uid);
    		}

    		Hapyfish2_Magic_Bll_UserResult::addField($uid, 'story', array('actions' => $actions));
    	}

    }
}