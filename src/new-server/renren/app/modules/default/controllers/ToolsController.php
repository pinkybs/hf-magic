<?php

class ToolsController extends Zend_Controller_Action
{
	function vaild()
	{

	}

	function check()
	{
		$uid = $this->_request->getParam('uid');
		if (empty($uid)) {
			echo 'uid can not empty';
			exit;
		}

		$isAppUser = Hapyfish2_Magic_Cache_User::isAppUser($uid);
		if (!$isAppUser) {
			echo 'uid error, not app user';
			exit;
		}

		return $uid;
	}

	public function addcoinAction()
	{
		$uid = $this->check();
		$coin = $this->_request->getParam('coin');
		if (empty($coin) || $coin <= 0) {
			echo 'add coin error, must > 1';
			exit;
		}

		Hapyfish2_Magic_HFC_User::incUserCoin($uid, $coin);

		echo 'OK';
		exit;
	}

	public function addgoldAction()
	{
		$uid = $this->check();
		$gold = $this->_request->getParam('gold');
		if (empty($gold) || $gold <= 0) {
			echo 'add gold error, must > 1';
			exit;
		}

		$goldInfo = array(
			'uid' => $uid,
			'gold' => $gold,
			'type' => 99
		);
		Hapyfish2_Magic_Bll_Gold::add($uid, $goldInfo);

		echo 'OK';
		exit;
	}

	public function addexpAction()
	{
		$uid = $this->check();
		$exp = $this->_request->getParam('exp');
		if (empty($exp) || $exp <= 0) {
			echo 'add exp error, must > 1';
			exit;
		}

		Hapyfish2_Magic_HFC_User::incUserExp($uid, $exp);

		echo 'OK';
		exit;
	}

	public function additemAction()
	{
		$uid = $this->check();
		$itemId = $this->_request->getParam('item_id');
		if (empty($itemId)) {
			echo 'item id can not empty';
			exit;
		}

		$count = $this->_request->getParam('count');
		if (empty($count) || $count <= 0) {
			echo 'add item number error, must > 1';
			exit;
		}

		$itemInfo = Hapyfish2_Magic_Cache_BasicInfo::getItemInfo($itemId);
		if (!$itemInfo) {
			echo 'item id error, not exists';
			exit;
		}

		Hapyfish2_Magic_HFC_Item::addUserItem($uid, $itemId, $count);

		echo 'OK';
		exit;
	}

	public function changeexpAction()
	{
		$uid = $this->check();
		$exp = $this->_request->getParam('exp');
		if (empty($exp) || $exp <= 10) {
			echo 'exp error, must > 10';
			exit;
		}

		Hapyfish2_Magic_HFC_User::updateUserExp($uid, $exp, true);

		echo 'OK';
		exit;
	}

	public function changempAction()
	{
		$uid = $this->check();
		$mp = $this->_request->getParam('mp');
		if (empty($mp) || $mp <= 10) {
			echo 'mp error, must > 10';
			exit;
		}

		$userMp = Hapyfish2_Magic_HFC_User::getUserMp($uid);
		$userMp['mp'] = $mp;
		Hapyfish2_Magic_HFC_User::updateUserMp($uid, $userMp);

		echo 'OK';
		exit;
	}

	public function changemaxmpAction()
	{
		$uid = $this->check();
		$maxmp = $this->_request->getParam('maxmp');
		if (empty($maxmp) || $maxmp <= 100) {
			echo 'max mp error, must > 100';
			exit;
		}

		$userMp = Hapyfish2_Magic_HFC_User::getUserMp($uid);
		$userMp['max_mp'] = $maxmp;
		Hapyfish2_Magic_HFC_User::updateUserMp($uid, $userMp);

		echo 'OK';
		exit;
	}

	public function changelevelAction()
	{
		$uid = $this->check();
		$level = $this->_request->getParam('level');
		if (empty($level) || $level <= 0 || $level > 75) {
			echo 'level error, must > 0 and < 75';
			exit;
		}

		$userLevelInfo = Hapyfish2_Magic_HFC_User::getUserLevel($uid);
		$userLevelInfo['level'] = $level;
		Hapyfish2_Magic_HFC_User::updateUserLevel($uid, $userLevelInfo);

		echo 'OK';
		exit;
	}

	public function changesceneAction()
	{
		$uid = $this->check();
		$sceneId = $this->_request->getParam('sceneId');
		if (empty($sceneId)) {
			echo 'max mp error';
			exit;
		}

		$userScene = Hapyfish2_Magic_HFC_User::getUserScene($uid);
        $data = $userScene['open_scene_list'];
        $userOpenSceneList = explode(',', $data);
        $openList = array();
        foreach ($userOpenSceneList as $id) {
        	$openList[$id] = 1;
        }

        if (!isset($openList[$sceneId])) {
			echo 'scene_not_opened';
			exit;
        }

		$userScene['cur_scene_id'] = $sceneId;
		$ok = Hapyfish2_Magic_HFC_User::updateUserScene($uid, $userScene, true);

		echo 'OK';
		exit;
	}

	public function clearstudentAction()
	{
		$uid = $this->check();
		$students = Hapyfish2_Magic_HFC_Student::getAll($uid);
		$deskList = Hapyfish2_Magic_HFC_Desk::getInScene($uid);

		foreach ($students as $v) {
			$v['state'] = 3;
			$v['desk_id'] = 0;
			$v['start_time'] = 0;
			$v['end_time'] = 0;
			$v['spend_time'] = 0;
			$v['event'] = 0;
			$v['event_time'] = 0;
			$v['magic_id'] = $mid;
			$v['coin'] = 0;
			$v['stone_time'] = 0;
			Hapyfish2_Magic_HFC_Student::updateOne($uid, $v['sid'], $v);
		}

		foreach ($deskList['desks'] as $v) {
			$v['student_id'] = 0;
			$v['magic_id'] = 0;
			$v['coin'] = 0;
			$v['end_time'] = 0;
			$v['stone_time'] = 0;
			Hapyfish2_Magic_HFC_Desk::updateOne($uid, $v['id'], $v);
		}

		echo 'OK';
		exit;
	}

	public function joinuserAction()
	{
		$puid = $this->_request->getParam('puid');
		$uidInfo = Hapyfish2_Platform_Cache_UidMap::getUser($puid);
		if (!$uidInfo) {
    		$uidInfo = Hapyfish2_Platform_Cache_UidMap::newUser($puid);
    		if (!$uidInfo) {
    			echo 'inituser error: 1';
    			exit;
    		}
		}
		$name = $this->_request->getParam('name');
		if (empty($name)) {
			$name = '测试' . $puid;
		}
		$figureurl = $this->_request->getParam('figureurl');
		if (empty($figureurl)) {
			$figureurl = 'http://hdn.xnimg.cn/photos/hdn521/20091210/1355/tiny_E7Io_11729b019116.jpg';
		}

		$uid = $uidInfo['uid'];
        $user = array();
        $user['uid'] = $uid;
        $user['puid'] = $puid;
        $user['name'] = $name;
        $user['figureurl'] = $figureurl;
        $user['gender'] = rand(0,1);

		Hapyfish2_Platform_Bll_User::addUser($user);

		Hapyfish2_Magic_Bll_User::joinUser($uid);

		echo 'OK: ' . $uid;
		exit;
	}

	public function addfriendAction()
	{
		$uid = $this->_request->getParam('uid');
		$fid = $this->_request->getParam('fid');

		$fids = Hapyfish2_Platform_Cache_Friend::getFriend($uid);
		if (empty($fids)) {
			$fids = array(
				'uid' => $uid,
        		'fids' => $fid,
        		'count' => 1
			);
		} else {
			if (empty($fids['fids'])) {
				$fids['fids'] = $fid;
				$fids['count'] = 1;
			} else {
				$fids['fids'] .= ',' . $fid;
				$fids['count'] += 1;
			}
		}

		Hapyfish2_Platform_Cache_Friend::updateFriend($uid, $fids['fids'], $fids['count']);

		echo 'OK: ' . $uid;
		exit;
	}

	public function reloadbasicAction()
	{
		Hapyfish2_Magic_Bll_Tools::loadBasicAll();

		$v = '1.0';
		$file = TEMP_DIR . '/initvo.' . $v . '.cache';
		@unlink($file);

		echo SERVER_ID . 'OK';
		exit;
	}

	public function reloadgiftAction()
	{
		$key = 'magic:giftlist';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$cache->delete($key);
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$localcache->delete($key);

        Hapyfish2_Magic_Cache_Gift::loadBasicGiftList();

		$v = '1.0';
		$file = TEMP_DIR . '/giftvo.' . $v . '.cache';
		@unlink($file);

		echo SERVER_ID . 'OK';
		exit;
	}

	public function reinituserAction()
	{
		$uid = $this->check();
		Hapyfish2_Magic_Bll_Tools::reInitUser($uid);
		echo 'OK: ' . $uid;
		exit;
	}


    public function clearstudentallAction()
	{
	    for ($i=10010; $i<=10090; $i++) {
	        $uid = $i;
	        if (!Hapyfish2_Magic_Cache_User::isAppUser($uid)) {
                continue;
	        }
    	    $students = Hapyfish2_Magic_HFC_Student::getAll($uid);
    		$deskList = Hapyfish2_Magic_HFC_Desk::getInScene($uid);

    		foreach ($students as $v) {
    			$v['state'] = 3;
    			$v['desk_id'] = 0;
    			$v['start_time'] = 0;
    			$v['end_time'] = 0;
    			$v['spend_time'] = 0;
    			$v['event'] = 0;
    			$v['event_time'] = 0;
    			$v['magic_id'] = $mid;
    			$v['coin'] = 0;
    			$v['stone_time'] = 0;
    			Hapyfish2_Magic_HFC_Student::updateOne($uid, $v['sid'], $v);
    		}

    		foreach ($deskList['desks'] as $v) {
    			$v['student_id'] = 0;
    			$v['magic_id'] = 0;
    			$v['coin'] = 0;
    			$v['end_time'] = 0;
    			$v['stone_time'] = 0;
    			Hapyfish2_Magic_HFC_Desk::updateOne($uid, $v['id'], $v);
    		}

    		echo $uid.'done!<br/>';
	    }

		echo 'OK';
		exit;
	}

	public function changestudentlevAction()
	{
		$uid = $this->check();
		$sid = $this->_request->getParam('sid');
		$level = $this->_request->getParam('level');
		$studentInfo = Hapyfish2_Magic_HFC_Student::getOne($uid, $sid);
		$studentInfo['level'] = $level;
		$studentInfo['exp'] = 0;
		$studentInfo['award_flg'] = 0;
		Hapyfish2_Magic_HFC_Student::updateOne($uid, $sid, $studentInfo);
		echo 'OK: ' . $uid;
		exit;
	}

    public function repairtaskAction()
	{
		$uid = $this->check();
		$taskOpenInfo = Hapyfish2_Magic_HFC_TaskOpen::getInfo($uid);
    	if ($taskOpenInfo && empty($taskOpenInfo['trunk'])) {
            $dalTask = Hapyfish2_Magic_Dal_Task::getDefaultInstance();
            $lstCompleteTask = $dalTask->getAll($uid);
            if ($lstCompleteTask) {
                sort($lstCompleteTask);
            }
            $maxId = $lstCompleteTask[count($lstCompleteTask)-1];
            $nextTaskId = $maxId+1;
            $nextTaskInfo = Hapyfish2_Magic_Cache_BasicInfo::getTaskTrunkInfo($nextTaskId);
            if ($nextTaskInfo) {
                $info = array();

    			$start = 1;
    			if ($nextTaskId > 0) {
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
                $info['trunk_start'] = $start;
			    $info['trunk_track_num'] = 0;
			    $info['branch'] = '[]';
                Hapyfish2_Magic_HFC_TaskOpen::save($uid, $info, true);
                //是否要触发剧情
    			$storyId = (int)$nextTaskInfo['story_id'];
    			if ($storyId > 0) {
    				Hapyfish2_Magic_Bll_Story::create($uid, $storyId);
    			}
            }

    	}
    	else {
    	    $nextTaskId = 2015;
    	    $info['trunk'] = $nextTaskId;
            $info['trunk_start'] = 1;
		    $info['trunk_track_num'] = 0;
		    $info['branch'] = '[]';
            Hapyfish2_Magic_HFC_TaskOpen::save($uid, $info, true);
    	}
		echo 'OK: ' . $uid . ' nexttask:' .$nextTaskId;
		exit;
	}


public function cleargifttodaywishAction()
	{
	    $uid = $this->check();
        $dalGift = Hapyfish2_Magic_Dal_Gift::getDefaultInstance();
        $dalGift->deleteWish($uid);
        $mkey = 'm:u:gift:wish:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $cache->delete($mkey);
        echo 'ok';
        exit;
	}

    public function cleargifttodaysentAction()
	{
        $uid = $this->check();
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $mkey = 'm:u:gift:sent:g:uids:' . $uid;
        $mkey2 = 'm:u:gift:sent:w:uids:' . $uid;
        echo $uid.'<br/>gift sent:'.json_encode($cache->get($mkey));
        echo '<br/>wish sent:'.json_encode($cache->get($mkey2));
        $cache->delete($mkey);
        $cache->delete($mkey2);
        echo 'clear ok';
        exit;
	}

    public function clearreceivegiftAction()
	{
	    $uid = $this->check();
        $dalGift = Hapyfish2_Magic_Dal_Gift::getDefaultInstance();
        $dalGift->deleteBag($uid);
        echo 'ok';
        exit;
	}


    function loadinitdataAction()
	{

	    //basic
        Hapyfish2_Magic_Bll_Tools::loadBasicAll();
		$v = '1.0';
		$file = TEMP_DIR . '/initvo.' . $v . '.cache';
		@unlink($file);

		//gift
		$key = 'magic:giftlist';
		$cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
		$cache->delete($key);
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$localcache->delete($key);

        Hapyfish2_Magic_Cache_Gift::loadBasicGiftList();

		$v = '1.0';
		$file = TEMP_DIR . '/giftvo.' . $v . '.cache';
		@unlink($file);

		echo SERVER_ID . 'OK';
		exit;
	}

    function loadinitdataofallAction()
	{
		$list = Hapyfish2_Magic_Tool_Server::getWebList();
		if (!empty($list)) {
			$host = str_replace('http://', '', HOST);
			foreach ($list as $server) {
				$url = 'http://' . $server['local_ip'] . '/tools/loadinitdata';
				$result = Hapyfish2_Magic_Tool_Server::requestWeb($host, $url);
				echo $server['name']. '--' . $url . ':' . $result . '<br/>';
			}
		}
		echo "OK";
		exit;
	}

    function loadeventdataAction()
	{
	    $eCode = $this->_request->getParam('eCode');
	    //basic
	    $localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$list = Hapyfish2_Magic_Event_Cache_Basic::loadCollection($eCode);
		$key = 'magic:event:collect:'.$eCode;
		$localcache->set($key, $list, false);
		echo SERVER_ID . 'OK';
		echo json_encode($list);
		exit;
	}
}