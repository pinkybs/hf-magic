<?php

/*
 * 事件派发工具类
 */
class Hapyfish2_Magic_Bll_Event
{
    //装饰
    //$event = array('uid' => $uid);
	public static function diy($event)
    {
        $taskOpenInfo = Hapyfish2_Magic_HFC_TaskOpen::getInfo($event['uid']);
        if ($taskOpenInfo['branch'] && count($taskOpenInfo['branch']) > 0) {
            Hapyfish2_Magic_Bll_Task_Branch_Base::triggerMul($event['uid'], $taskOpenInfo['branch']);
        }
    }

    //拾取金币
    //$event = array('uid' => $uid, 'coin' => $coinChange);
	public static function pickup($event)
    {
		$type = 105;
		$data = array('coin' => $event['coin']);
		Hapyfish2_Magic_Bll_Task::listen($event['uid'], $type, $data);
    }

    //学习变化术
    //$event = array('uid' => $uid, 'transInfo' => $transBasicInfo);
	public static function studyTrans($event)
    {
		$type = 202;
		$data = array('mid' => $event['transInfo']['id']);
		Hapyfish2_Magic_Bll_Task::listen($event['uid'], $type, $data);

        $taskOpenInfo = Hapyfish2_Magic_HFC_TaskOpen::getInfo($event['uid']);
        if ($taskOpenInfo['branch'] && count($taskOpenInfo['branch']) > 0) {
            Hapyfish2_Magic_Bll_Task_Branch_Base::triggerMul($event['uid'], $taskOpenInfo['branch']);
        }
    }

    //学习魔法
    //$event = array('uid' => $uid, 'studyInfo' => $studyBasicInfo);
	public static function studyTeach($event)
    {
		$type = 202;
		$data = array('mid' => $event['studyInfo']['id']);
		Hapyfish2_Magic_Bll_Task::listen($event['uid'], $type, $data);

        $taskOpenInfo = Hapyfish2_Magic_HFC_TaskOpen::getInfo($event['uid']);
        if ($taskOpenInfo['branch'] && count($taskOpenInfo['branch']) > 0) {
            Hapyfish2_Magic_Bll_Task_Branch_Base::triggerMul($event['uid'], $taskOpenInfo['branch']);
        }
    }

    //使用合成
    //$event = array('uid' => $uid, 'mixInfo' => $mixBasicInfo, 'num' => $num);
	public static function mix($event)
    {
		$type = 101;
		$data = array('cid' => $event['mixInfo']['building'], 'num' => $event['num']);
		Hapyfish2_Magic_Bll_Task::listen($event['uid'], $type, $data);

        $taskOpenInfo = Hapyfish2_Magic_HFC_TaskOpen::getInfo($event['uid']);
        if ($taskOpenInfo['branch'] && count($taskOpenInfo['branch']) > 0) {
            Hapyfish2_Magic_Bll_Task_Branch_Base::triggerMul($event['uid'], $taskOpenInfo['branch']);
        }
    }

    //使用变化术
    //$event = array('uid' => $uid, 'fid' => $fid, 'transInfo' => $transBasicInfo);
	public static function trans($event)
    {
		$type = 111;
		Hapyfish2_Magic_Bll_Task::listen($event['uid'], $type, array());

		Hapyfish2_Magic_Bll_Task_Daily_Base::trigger($event['uid'], 9003);
    }

    //给学生传授魔法
    //array('uid' => $uid, 'student' => $std, 'magicInfo' => $magicInfo)
	public static function teachStudent($event)
    {
		$type = 103;
		Hapyfish2_Magic_Bll_Task::listen($event['uid'], $type, array());

		Hapyfish2_Magic_Bll_Task_Daily_Base::trigger($event['uid'], 9001);
    }

    //解除学生异常状态
    //$event = array('uid' => $uid, 'student' => $std);
	public static function helpStudent($event)
    {
		$type = 104;
		Hapyfish2_Magic_Bll_Task::listen($event['uid'], $type, array());

		Hapyfish2_Magic_Bll_Task_Daily_Base::trigger($event['uid'], 9002);
    }

    //成功邀请
	public static function invite($event)
    {

    }

    //访问好友
	public static function visitFriend($event)
    {
		$type = 110;
    	Hapyfish2_Magic_Cache_Visit::dailyVisit($event['uid'], $event['fid']);
    }

    //用户等级升级
    //$event = array('uid' => $uid, 'level' => $user['level']);
	public static function levelUp($event)
    {
		$uid = $event['uid'];

    	//是否有主线任务开启
		$openTask = Hapyfish2_Magic_HFC_TaskOpen::getInfo($event['uid']);
		$taskId = $openTask['trunk'];
		if($taskId > 0 && $openTask['trunk_start'] == 0) {
			$taskInfo = Hapyfish2_Magic_Cache_BasicInfo::getTaskTrunkInfo($taskId);
			if ($taskInfo && $event['level'] >= $taskInfo['level']) {
				$openTask['trunk_start'] = 1;
				$ok = Hapyfish2_Magic_HFC_TaskOpen::save($uid, $openTask, true);
				if ($ok) {
					$changeTasks = array(
						array('t_id' => $taskId, 'fc_curNums' => array(0), 'state' => 0)
					);

					Hapyfish2_Magic_Bll_UserResult::addField($uid, 'changeTasks', $changeTasks);
				}
			}
		}

		$type = 201;
		Hapyfish2_Magic_Bll_Task::listen($event['uid'], $type, array('level' => $event['level']));
    }


    //副本打怪
    //array('uid' => $uid, 'pMapId' => $pMapId, 'cid' => $cid, 'num' => $num)
	public static function hitMonster($event)
    {
        $data = array();
        $data['pMapId'] = $event['pMapId'];
        $data['cid'] = $event['cid'];
        $data['num'] = $event['num'];
//info_log('301:'.json_encode($data), 'triggermaptask');
		Hapyfish2_Magic_Bll_Task_Map_Base::trigger($event['uid'], 301, $data);
    }

    //副本收集物
    //array('uid' => $uid, 'pMapId' => $pMapId, 'items'=>array(array($cid=>$num)),......)
	public static function collectItem($event)
    {
        $data = array();
        $data['pMapId'] = $event['pMapId'];
        $items = $event['items'];
        foreach ($items as $key=>$num) {
            $data['cid'] = $key;
            $data['num'] = $num;
//info_log('302:'.json_encode($data), 'triggermaptask');
    		Hapyfish2_Magic_Bll_Task_Map_Base::trigger($event['uid'], 302, $data);
        }
    }
}