<?php

class OpenapiController extends Zend_Controller_Action
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
		
		$isAppUser = Hapyfish2_Island_Cache_User::isAppUser($uid);
		if (!$isAppUser) {
			$this->echoError(1002, 'uid error, not app user');
			exit;
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
    
    public function noopAction()
    {
    	$data = array('id' => SERVER_ID, 'time' => time());
    	$this->echoResult($data);
    }
    
    public function watchuserAction()
    {
		$uid = $this->check();
		$t = time();
		$sig = md5($uid . $t . APP_KEY);
		
		$url = HOST . '/watch?uid=' . $uid . '&t=' . $t . '&sig=' . $sig;
		$data = array('url' => $url);
		$this->echoResult($data);
    }
	
	public function userinfoAction()
	{
		$uid = $this->check();
		$platformUser = Hapyfish2_Platform_Bll_User::getUser($uid);
		$islandUser = Hapyfish2_Island_HFC_User::getUser($uid, array('exp' => 1, 'coin' => 1, 'level' => 1));
		$data = array(
			'face' => $platformUser['figureurl'],
			'uid' => $uid,
			'nickname' => $platformUser['name'],
			'gender' => $platformUser['gender'],
			'level' => $islandUser['level'],
			'exp' => $islandUser['exp'],
			'coin' => $islandUser['coin']
		);

		$data['status'] = Hapyfish2_Platform_Cache_User::getStatus($uid);
		
		$this->echoResult($data);
	}
	
	public function userinfobypuidAction()
	{
		$puid = $this->_request->getParam('puid');
		if (empty($puid)) {
			$this->echoError(1001, 'puid can not empty');
		}
		
		try {
			$platformUidInfo = Hapyfish2_Platform_Cache_UidMap::getUser($puid);
		} catch (Exception $e) {
			$platformUidInfo = null;
		}

		if (!$platformUidInfo) {
			$this->echoError(1002, 'puid error, not app user');
			exit;
		}
		$uid = $platformUidInfo['uid'];
		
		$platformUser = Hapyfish2_Platform_Bll_User::getUser($uid);
		$islandUser = Hapyfish2_Island_HFC_User::getUser($uid, array('exp' => 1, 'coin' => 1, 'level' => 1));
		$data = array(
			'face' => $platformUser['figureurl'],
			'uid' => $uid,
			'nickname' => $platformUser['name'],
			'gender' => $platformUser['gender'],
			'level' => $islandUser['level'],
			'exp' => $islandUser['exp'],
			'coin' => $islandUser['coin']
		);

		$data['status'] = Hapyfish2_Platform_Cache_User::getStatus($uid);
		
		$this->echoResult($data);
	}
	
	public function usercardinfoAction()
	{
		$uid = $this->check();
		$cardInfoList = Hapyfish2_Island_Cache_BasicInfo::getCardList();
		$userCardInfo = Hapyfish2_Island_HFC_Card::getUserCard($uid);
		$cards = array();
		if ($userCardInfo) {
			foreach ($userCardInfo as $cid => $item) {
				if ($item['count'] > 0) {
					$cards[] = array(
						'cid' => $cid,
						'name' => $cardInfoList[$cid]['name'],
						'introduce' => $cardInfoList[$cid]['introduce'],
						'count' => $item['count']
					);
				}
			}
		}
		$data = array(
			'cards' => $cards
		);
		
		$this->echoResult($data);
	}
	
	public function coinlogAction()
	{
		$uid = $this->check();
		$time = time();
		$year = $this->_request->getParam('year');
		if (!$year) {
			$year = date('Y');
		}
		$month = $this->_request->getParam('month');
		if (!$month) {
			$month = date('n');
		}
		$limit = $this->_request->getParam('limit');
		if (!$limit) {
			$limit = 100;
		}
		
		$logs = Hapyfish2_Island_Bll_ConsumeLog::getCoin($uid, $year, $month, $limit);
		if (!$logs) {
			$logs = array();
		}
		$data = array('logs' => $logs);
		$this->echoResult($data);
	}
	
	public function goldlogAction()
	{
		$uid = $this->check();
		$time = time();
		$year = $this->_request->getParam('year');
		if (!$year) {
			$year = date('Y');
		}
		$month = $this->_request->getParam('month');
		if (!$month) {
			$month = date('n');
		}
		$limit = $this->_request->getParam('limit');
		if (!$limit) {
			$limit = 100;
		}
		
		$logs = Hapyfish2_Island_Bll_ConsumeLog::getGold($uid, $year, $month, $limit);
		if (!$logs) {
			$logs = array();
		}
		$data = array('logs' => $logs);
		$this->echoResult($data);
	}
	
	public function invitelogAction()
	{
		$uid = $this->check();
		$logs = Hapyfish2_Island_Bll_InviteLog::getAll($uid);
		if (!$logs) {
			$logs = array();
		}
		$data = array('logs' => $logs);
		$this->echoResult($data);
	}
	
	public function leveluplogAction()
	{
		$uid = $this->check();
		$logs = Hapyfish2_Island_Bll_LevelUpLog::getAll($uid);
		if (!$logs) {
			$logs = array();
		}
		$data = array('logs' => $logs);
		$this->echoResult($data);
	}
	
	public function itemlistAction()
	{
		$type = $this->_request->getParam('type', '0');
		if ($type == 1) {
			$backgroundlist = Hapyfish2_Island_Cache_BasicInfo::getBackgroundList();
			$data = array('backgroundlist' => $backgroundlist);
		} else if ($type == 2) {
			$buildinglist = Hapyfish2_Island_Cache_BasicInfo::getBuildingList();
			$data = array('buildinglist' => $buildinglist);
		} else if ($type == 3) {
			$plantlist = Hapyfish2_Island_Cache_BasicInfo::getPlantList();
			$data = array('plantlist' => $plantlist);
		} else if ($type == 4) {
			$cardlist = Hapyfish2_Island_Cache_BasicInfo::getCardList();
			$data = array('cardlist' => $cardlist);
		} else {
			$cardlist = Hapyfish2_Island_Cache_BasicInfo::getCardList();
			$backgroundlist = Hapyfish2_Island_Cache_BasicInfo::getBackgroundList();
			$buildinglist = Hapyfish2_Island_Cache_BasicInfo::getBuildingList();
			$plantlist = Hapyfish2_Island_Cache_BasicInfo::getPlantList();
			$data = array('backgroundlist' => $backgroundlist, 'buildinglist' => $buildinglist, 'plantlist' => $plantlist, 'cardlist' => $cardlist);
		}

		$this->echoResult($data);
	}
	
}