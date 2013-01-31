<?php

/**
 * 合成术装饰物 次数任务
 *
 */
class Hapyfish2_Magic_Bll_Task_Trunk_T101
{	
	public function trigger($uid, $openTask, $trunkTaskInfo, $data)
	{		
		if (!$data || !isset($data['cid']) || $data['cid'] != $trunkTaskInfo['cid']) {
			return;
		}
		
		$oldNum = $openTask['trunk_track_num'];
		
		//已经达到任务完成条件则不需要统计了，直接返回
		if ($oldNum >= $trunkTaskInfo['num']) {
			return;
		}
		
		$openTask['trunk_track_num'] += $data['num'];
		$ok = Hapyfish2_Magic_HFC_TaskOpen::update($uid, $openTask);
		if ($ok) {
			if ($oldNum < $trunkTaskInfo['num']) {
				if ($openTask['trunk_track_num'] >= $trunkTaskInfo['num']) {
					$num = $trunkTaskInfo['num'];
					$state = 1;
				} else {
					$num = $openTask['trunk_track_num'];
					$state = 0;
				}
				$changeTasks = array(
					array('t_id' => $trunkTaskInfo['id'], 'fc_curNums' => array($num), 'state' => $state)
				);
				Hapyfish2_Magic_Bll_UserResult::addField($uid, 'changeTasks', $changeTasks);
			}
		}
	}
}