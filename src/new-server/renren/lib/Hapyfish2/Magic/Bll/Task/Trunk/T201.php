<?php

/**
 * 人物等级任务(累计)
 *
 */
class Hapyfish2_Magic_Bll_Task_Trunk_T201
{	
	public function trigger($uid, $openTask, $trunkTaskInfo, $data)
	{
		$num = $data['level'];
		if ($num <= 0) {
			return;
		}
		
		$oldNum = $num - 1;
		
		//已经达到任务完成条件则不需要统计了，直接返回
		if ($oldNum >= $trunkTaskInfo['num']) {
			return;
		}
		
		if ($num >= $trunkTaskInfo['num']) {
			$num = $trunkTaskInfo['num'];
			$state = 1;
		} else {
			$state = 0;
		}
		
		$changeTasks = array(
			array('t_id' => $trunkTaskInfo['id'], 'fc_curNums' => array($num), 'state' => $state)
		);
		Hapyfish2_Magic_Bll_UserResult::addField($uid, 'changeTasks', $changeTasks);
	}
}