<?php

class ManageapiController extends Zend_Controller_Action
{
	function vaild()
	{

	}

	function check()
	{
		$uid = $this->_request->getParam('uid');
		if (empty($uid)) {
			$this->echoError(1001, 'uid can not empty');
		}

		$isAppUser = Hapyfish2_Magic_Cache_User::isAppUser($uid);
		if (!$isAppUser) {
			$this->echoError(1002, 'uid error, not app user');
		}

		return $uid;
	}

    protected function echoResult($data)
    {
    	$data['errno'] = 0;
    	echo json_encode($data);
    	exit;
    }

    protected function echoError($errno, $errmsg)
    {
    	$result = array('errno' => $errno, 'errmsg' => $errmsg);
    	echo json_encode($result);
    	exit;
    }

	public function updatenoticeAction()
	{
		$id = $this->_request->getParam('id');
		if (empty($id)) {
			$this->echoError(1001, 'id can not empty');
		}

		$params = $this->_request->getParams();
		$info = array();
		if (isset($params['position'])) {
			$info['position'] = $params['position'];
		}
		if (isset($params['title'])) {
			$info['title'] = $params['title'];
		}
		if (isset($params['link'])) {
			$info['link'] = $params['link'];
		}
		if (isset($params['priority'])) {
			$info['priority'] = $params['priority'];
		}
		if (isset($params['opened'])) {
			$info['opened'] = $params['opened'];
		}
		if (isset($params['time'])) {
			$info['create_time'] = $params['time'];
		}

		$ok = Hapyfish2_Magic_Bll_Notice::update($id, $info);
		$result = $ok ? 1 : 0;
		$data = array('result' => $result);
		$this->echoResult($data);
	}

	public function addnoticeAction()
	{
		$params = $this->_request->getParams();
		$info = array();
		if (isset($params['position'])) {
			$info['position'] = $params['position'];
		}
		if (isset($params['title'])) {
			$info['title'] = $params['title'];
		}
		if (isset($params['link'])) {
			$info['link'] = $params['link'];
		}
		if (isset($params['priority'])) {
			$info['priority'] = $params['priority'];
		}
		if (isset($params['opened'])) {
			$info['opened'] = $params['opened'];
		}
		if (isset($params['time'])) {
			$info['create_time'] = $params['time'];
		} else {
			$info['create_time'] = time();
		}

		$ok = Hapyfish2_Magic_Bll_Notice::add($info);
		$result = $ok ? 1 : 0;
		$data = array('result' => $result);
		$this->echoResult($data);
	}

	public function getnoticeAction()
	{
		$notice = Hapyfish2_Magic_Cache_BasicInfo::getNoticeList();
		$data = array('notice' => $notice);
		$this->echoResult($data);
	}

	public function changeuserstatusAction()
	{
		$uid = $this->check();
		$status = $this->_request->getParam('status', 0);
		$ok = Hapyfish2_Platform_Cache_User::updateStatus($uid, $status, true);
		$result = $ok ? 1 : 0;
		$data = array('result' => $result);
		$this->echoResult($data);
	}

	public function blockuserAction()
	{
		$uid = $this->check();
		$status = Hapyfish2_Platform_Cache_User::getStatus($uid);
		if ($status != 0) {
			$this->echoError(1101, 'user status is not normal');
		}
		$ok = Hapyfish2_Magic_Bll_Block::add($uid, 1, 1);
		$result = $ok ? 1 : 0;
		$data = array('result' => $result);
		$this->echoResult($data);
	}

	public function unblockuserAction()
	{
		$uid = $this->check();
		$status = Hapyfish2_Platform_Cache_User::getStatus($uid);
		if ($status <= 0) {
			$this->echoError(1102, 'user status is normal');
		}
		$ok = Hapyfish2_Magic_Bll_Block::add($uid, 0, 1);
		$result = $ok ? 1 : 0;
		$data = array('result' => $result);
		$this->echoResult($data);
	}

	public function getuserstatusAction()
	{
		$uid = $this->check();
		$status = Hapyfish2_Platform_Cache_User::getStatus($uid);
		$data = array('uid' => $uid, 'status' => $status);
		$this->echoResult($data);
	}

	public function compensationAction()
	{
		$type = $this->_request->getParam('type');

		$compensation = new Hapyfish2_Magic_Bll_Award();

		$coin = $this->_request->getParam('coin', 0);
		if ($coin > 0) {
			$compensation->setCoin($coin);
		}
		$items = $this->_request->getParam('items');
		foreach ($items as $data) {
			$item = explode('*', $data);
			$compensation->setItem($item[0], $item[1]);
		}

		$total = 0;
		if ($type == '1') {
			$uid = $this->_request->getParam('uid');
			$compensation->setUid($uid);
			$total = 1;
		} else if ($type == '2') {
			$uids = $this->_request->getParam('uids');
			$uids = split(',', $uids);
			$total = count($uids);
			$compensation->setUids($uids);
		} else if ($type == '3') {
			$begin = $this->_request->getParam('begin');
			$end = $this->_request->getParam('end');
			$total = $end - $begin;
			$compensation->setBlockUids($begin, $end);
		}

		$num = $compensation->send('[系统赠送]');
		$data = array('total' => $total, 'succ_num' => $num);
		$this->echoResult($data);
	}

	/*
	public function sysgiftAction()
	{
		$compensation = new Hapyfish2_Magic_Bll_Compensation();

		$coin = $this->_request->getParam('coin', 0);
		if ($coin > 0) {
			$compensation->setCoin($coin);
		}
		$items = $this->_request->getParam('items');
		if (!empty($items)) {
			foreach ($items as $data) {
				$item = explode('*', $data);
				$compensation->setItem($item[0], $item[1]);
			}
		}

		$uid = $this->_request->getParam('uid');
		$compensation->setUid($uid);
		$compensation->setFeedTitle('恭喜获得【加乐乐微博活动】：3星灯笼店一座。');
		$total = 1;

		$num = $compensation->send('');
		$data = array('total' => $total, 'succ_num' => $num);
		$this->echoResult($data);
	}*/

	public function sysgiftAction()
	{
		$compensation = new Hapyfish2_Magic_Bll_Compensation();

		$coin = $this->_request->getParam('coin', 0);
		if ($coin > 0) {
			$compensation->setCoin($coin);
		}
		$gold = $this->_request->getParam('gold');
		if($gold > 0){
			$compensation->setGold($gold);
		}
		$items = $this->_request->getParam('items');
		if (!empty($items)) {
			foreach ($items as $data) {
				$item = explode('*', $data);
				$compensation->setItem($item[0], $item[1]);
			}
		}

		$uid = $this->_request->getParam('uid');
		$compensation->setUid($uid);
		$compensation->setFeedTitle('恭喜获得开心论坛奖励。');
		$total = 1;

		$num = $compensation->send('');
		$data = array('total' => $total, 'succ_num' => $num);
		$this->echoResult($data);
	}

	public function clearuserAction()
	{
		$uid = $this->check();
		$security = $this->_request->getParam('security', '');
		$ok = Hapyfish2_Magic_Bll_Manage::clearUser($uid);

		$result = $ok ? 1 : 0;
		$data = array('result' => $result);
		$this->echoResult($data);
	}

	public function blocklistAction()
	{
		$dalBlockLog = Hapyfish2_Magic_Dal_BlockLog::getDefaultInstance();
		$span = $this->_request->getParam('span', 864000);
		$type = $this->_request->getParam('type', 0);
		$t = time();
		$start = $t - $span;
		$end = $t;
		$list = array();
		for($i = 0; $i < 24; $i++) {
			$a1 = $dalBlockLog->getRange($id, $start, $end);
			if ($a1) {
				$list = array_merge($list, $a1);
				unset($a1);
			}
		}
		if ($type == 1) {
			if ($list) {
				$tmp = array();
				foreach ($list as $item) {
					$uid = $item['uid'];
					if (isset($tmp[$uid])) {
						$tmp[$uid] += 1;
					} else {
						$tmp[$uid] = 1;
					}
				}
				unset($list);
				arsort($tmp);
				$uid1 = array();
				$uid2 = array();
				$n1 = 0;
				$n2 = 0;
				foreach ($tmp as $uid => $num) {
					if ($num > 2) {
						$uid1[] = $uid;
						$n1++;
					} else {
						$uid2[] = $uid;
						$n2++;
					}
				}
				$uid1 = implode(',', $uid1);
				$uid2 = implode(',', $uid2);
				$list = array('uid1' => $uid1, 'n1' => $n1, 'uid2' => $uid2, 'n2' => $n2);
			}
		}
		$data = array('list' => $list);
		$this->echoResult($data);
	}

	public function clearfbidAction()
	{
		$file = CONFIG_DIR . '/clearuid.txt';
		$num = 0;
		$num1 = 0;
		$begin = $this->_request->getParam('begin');
		if (is_file($file)) {
			$content = file_get_contents($file);
			$uids = explode(',', $content);
			$status = 0;
			$i = $begin;
			$end = $begin + 100;
			$len = count($uids);
			if ($end > $len) {
				$end = $len;
			}
			for($i = $begin; $i < $end; $i++) {
				$uid = (int)$uids[$i];
				$ok = Hapyfish2_Magic_Bll_Manage::clearUser($uid);
				if ($ok) {
					$num++;
				}
				$ok2 = Hapyfish2_Platform_Cache_User::updateStatus($uid, $status, true);
				if ($ok2) {
					$num1++;
				}
			}
		}
		$data = array('num' => $num, 'num1' => $num1);
		$this->echoResult($data);
	}

	public function freeuserAction()
	{
		$file = CONFIG_DIR . '/freeuid.txt';
		if (is_file($file)) {
			$content = file_get_contents($file);
			$data = explode(',', $content);
			$num = 0;
			$dalBlock = Hapyfish2_Magic_Dal_BlockLog::getDefaultInstance();
			foreach ($data as $item) {
				$uid = trim($item);
				$ok = Hapyfish2_Platform_Cache_User::updateStatus($uid, 0, true);
				if ($ok) {
					$num++;
					$dalBlock->delete($uid);
				}
			}
			echo 'NUM: ' . $num . '<br/>';
		}
		echo 'OK';
		exit;
	}

	public function setezineAction()
	{
		$status = $this->_request->getParam('status', '0');
		if ($status == '0') {
			$show = 0;
		} else {
			$show = 1;
		}
		$mc = $this->_request->getParam('mc', '1');

		$key = 'island:ezinestatus';
		$data = array('show' => $show, 'ver' => date('Ymd'));
		if ($mc == '1') {
			$cache = Hapyfish2_Magic_Cache_BasicInfo::getBasicMC();
			$cache->set($key, $data);
		}

		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$localcache->set($key, $data, false);
		echo 'OK';
		exit;
	}

	public function moreinfoAction()
	{
		$file = CONFIG_DIR . '/moreinfo.txt';
		if (is_file($file)) {
			$content = file_get_contents($file);
			$data = explode(',', $content);
			$info = array();
			for($i = 1; $i < 60; $i++) {
				$info[$i] = array();
			}
			foreach ($data as $item) {
				$uid = trim($item);
				$ok = Hapyfish2_Platform_Cache_User::updateStatus($uid, 0, true);
				if ($ok) {
					$num++;
					$dalBlock->delete($uid);
				}
			}
			echo 'NUM: ' . $num . '<br/>';
		}
		echo 'OK';
		exit;
	}
     public function addfriendAction(){
     	$uid=$this->_request->getParam('uid');
    	$fid=$this->_request->getParam('fid');
    	$fid = explode(",", $fid);
    	if(empty($fid) || empty($uid))
    	{
    		echo "参数不能为空";exit;
    	}
     	Hapyfish2_Platform_Bll_Friend::addFriend($uid, $fid);
     	echo "OK";exit;
     }
    public function addstarfishAction(){
    	$uid=$this->_request->getParam('id');
    	$num=$this->_request->getParam('num');
    	Hapyfish2_Magic_Bll_StarFish::add($uid,$num,'');
    	$title = '恭喜你获得微博奖励<font color="#9F01A0">'.$num.'个海星</font>,赶快去海星商城看下吧！';
			$feed = array(
				'uid' => $uid,
				'actor' => $uid,
				'target' => $uid,
				'template_id' => 0,
//				'title' => array('cardName' => '加速卡II'),
				'title' => array('title' => $title),
				'type' => 3,
				'create_time' => time()
			);
			Hapyfish2_Magic_Bll_Feed::insertMiniFeed($feed);
    	echo $uid."--".$num. "--OK";
    	exit;
    }
}