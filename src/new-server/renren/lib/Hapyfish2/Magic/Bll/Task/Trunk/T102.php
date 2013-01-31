<?php

/**
 * 收集材料数量任务
 *
 */
class Hapyfish2_Magic_Bll_Task_Trunk_T102
{	
	public function trigger($uid, $openTask, $trunkTaskInfo, $data)
	{
		if (!$data || !isset($data['cid']) || $data['cid'] != $trunkTaskInfo['cid']) {
			return;
		}
		
		$incNum = $data['num'];
		
		$cid = $trunkTaskInfo['cid'];
		$num = Hapyfish2_Magic_HFC_Item::getUserItemCount($uid, $cid);
		$oldNum = $num - $incNum;
		
		//已经达到任务完成条件则不需要统计了，直接返回
		if ($oldNum >= $trunkTaskInfo['num']) {
			return;
		}
		
		if ($num >= $trunkTaskInfo['num']) {
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