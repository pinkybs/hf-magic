<?php

class MaintenanceapiController extends Zend_Controller_Action
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

	public function loadnoticeAction()
	{
		$list = Hapyfish2_Island_Cache_BasicInfo::loadNoticeList();
		$data = array('noticelist' => $list);
		$this->echoResult($data);
	}

	public function getnoticeAction()
	{
		$cache = Hapyfish2_Island_Cache_BasicInfo::getBasicMC();
		$key = 'island:pubnoticelist';
		$list = $cache->get($key);
		$data = array('noticelist' => $list);
		$this->echoResult($data);
	}

	public function loadlocalnoticeAction()
	{
		$cache = Hapyfish2_Island_Cache_BasicInfo::getBasicMC();
		$key = 'island:pubnoticelist';
		$list = $cache->get($key);
		if ($list) {
			$localcache = Hapyfish2_Cache_LocalCache::getInstance();
			$localcache->set($key, $list, false, 900);
		}
		$data = array('noticelist' => $list);
		$this->echoResult($data);
	}

	public function getlocalnoticeAction()
	{
		$key = 'island:pubnoticelist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$list = $localcache->get($key);
		$data = array('noticelist' => $list);
		$this->echoResult($data);
	}

	public function loadgiftAction()
	{
		$list = Hapyfish2_Island_Cache_BasicInfo::loadGiftList();
		$data = array('giftlist' => $list);
		$this->echoResult($data);
	}

	public function getgiftAction()
	{
		$key = 'island:giftlist';
		$cache = Hapyfish2_Island_Cache_BasicInfo::getBasicMC();
		$list = $cache->get($key);
		$data = array('giftlist' => $list);
		$this->echoResult($data);
	}

	public function loadlocalgiftAction()
	{
		$key = 'island:giftlist';
		$cache = Hapyfish2_Island_Cache_BasicInfo::getBasicMC();
		$list = $cache->get($key);
		if ($list) {
			$localcache = Hapyfish2_Cache_LocalCache::getInstance();
			$localcache->set($key, $list);
		}
		$data = array('giftlist' => $list);
		$this->echoResult($data);
	}

	public function loadtitlelistAction()
	{
		$list = Hapyfish2_Island_Cache_BasicInfo::loadTitleList();
		$data = array('titlelist' => $list);
		$this->echoResult($data);
	}

	public function gettitlelistAction()
	{
		$key = 'island:titlelist';
		$cache = Hapyfish2_Island_Cache_BasicInfo::getBasicMC();
		$list = $cache->get($key);
		$data = array('titlelist' => $list);
		$this->echoResult($data);
	}

	public function loadlocaltitlelistAction()
	{
		$key = 'island:titlelist';
		$cache = Hapyfish2_Island_Cache_BasicInfo::getBasicMC();
		$list = $cache->get($key);
		if ($list) {
			$localcache = Hapyfish2_Cache_LocalCache::getInstance();
			$localcache->set($key, $list, false);
		}
		$data = array('titlelist' => $list);
		$this->echoResult($data);
	}

	public function getlocaltitlelistAction()
	{
		$key = 'island:titlelist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$list = $localcache->get($key);
		$data = array('titlelist' => $list);
		$this->echoResult($data);
	}

	public function getfeedtemplateAction()
	{
		$key = 'island:feedtemplate';
		$cache = Hapyfish2_Island_Cache_BasicInfo::getBasicMC();
		$list = $cache->get($key);
		$data = array('feedtemplate' => $list);
		$this->echoResult($data);
	}

	public function loadfeedtemplateAction()
	{
		$list = Hapyfish2_Island_Cache_BasicInfo::loadFeedTemplate();
		$data = array('feedtemplate' => $list);
		$this->echoResult($data);
	}

	public function loadlocalfeedtemplateAction()
	{
		$key = 'island:feedtemplate';
		$cache = Hapyfish2_Island_Cache_BasicInfo::getBasicMC();
		$list = $cache->get($key);
		if ($list) {
			$localcache = Hapyfish2_Cache_LocalCache::getInstance();
			$localcache->set($key, $list, false);
		}
		$data = array('feedtemplate' => $list);
		$this->echoResult($data);
	}

	public function getcardlistAction()
	{
		$key = 'island:cardlist';
		$cache = Hapyfish2_Island_Cache_BasicInfo::getBasicMC();
		$list = $cache->get($key);
		$data = array('cardlist' => $list);
		$this->echoResult($data);
	}

	public function loadcardlistAction()
	{
		$list = Hapyfish2_Island_Cache_BasicInfo::loadCardList();
		$data = array('cardlist' => $list);
		$this->echoResult($data);
	}

	public function loadlocalcardlistAction()
	{
		$key = 'island:cardlist';
		$cache = Hapyfish2_Island_Cache_BasicInfo::getBasicMC();
		$list = $cache->get($key);
		if ($list) {
			$localcache = Hapyfish2_Cache_LocalCache::getInstance();
			$localcache->set($key, $list, false);
		}
		$data = array('cardlist' => $list);
		$this->echoResult($data);
	}

	public function getbuildtaskAction()
	{
		$key = 'island:buildtasklist';
		$cache = Hapyfish2_Island_Cache_BasicInfo::getBasicMC();
		$list = $cache->get($key);
		$data = array('buildtasklist' => $list);
		$this->echoResult($data);
	}

	public function loadbuildtaskAction()
	{
		$list = Hapyfish2_Island_Cache_BasicInfo::loadBuildTaskList();
		$data = array('buildtasklist' => $list);
		$this->echoResult($data);
	}

	public function loadlocalbuildtaskAction()
	{
		$key = 'island:buildtasklist';
		$cache = Hapyfish2_Island_Cache_BasicInfo::getBasicMC();
		$list = $cache->get($key);
		if ($list) {
			$localcache = Hapyfish2_Cache_LocalCache::getInstance();
			$localcache->set($key, $list, false);
		}
		$data = array('buildtasklist' => $list);
		$this->echoResult($data);
	}

	public function loaddailytaskAction()
	{
		$list = Hapyfish2_Island_Cache_BasicInfo::loadDailyTaskList();
		$data = array('dailytasklist' => $list);
		$this->echoResult($data);
	}

	public function loadlocaldailytaskAction()
	{
		$key = 'island:dailytasklist';
		$cache = Hapyfish2_Island_Cache_BasicInfo::getBasicMC();
		$list = $cache->get($key);
		if ($list) {
			$localcache = Hapyfish2_Cache_LocalCache::getInstance();
			$localcache->set($key, $list, false);
		}
		$data = array('dailytasklist' => $list);
		$this->echoResult($data);
	}

	public function loadachievementtaskAction()
	{
		$list = Hapyfish2_Island_Cache_BasicInfo::loadAchievementTaskList();
		$data = array('achievementtasklist' => $list);
		$this->echoResult($data);
	}

	public function loadlocalachievementtaskAction()
	{
		$key = 'island:achievementtasklist';
		$cache = Hapyfish2_Island_Cache_BasicInfo::getBasicMC();
		$list = $cache->get($key);
		if ($list) {
			$localcache = Hapyfish2_Cache_LocalCache::getInstance();
			$localcache->set($key, $list, false);
		}
		$data = array('achievementtasklist' => $list);
		$this->echoResult($data);
	}

	public function loadtaskdataAction()
	{
		Hapyfish2_Island_Cache_BasicInfo::loadBuildTaskList();

		Hapyfish2_Island_Cache_BasicInfo::loadDailyTaskList();

		Hapyfish2_Island_Cache_BasicInfo::loadAchievementTaskList();

		$data = array('result' => 'OK');
		$this->echoResult($data);
	}

	public function loadlocaltaskdataAction()
	{
		$cache = Hapyfish2_Island_Cache_BasicInfo::getBasicMC();
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$key = 'island:buildtasklist';
		$list = $cache->get($key);
		$localcache->set($key, $list, false);

		$key = 'island:dailytasklist';
		$list = $cache->get($key);
		$localcache->set($key, $list, false);

		$key = 'island:achievementtasklist';
		$list = $cache->get($key);
		$localcache->set($key, $list, false);

		$data = array('result' => SERVER_ID.' OK');
		$this->echoResult($data);
	}

	public function loadleveldataAction()
	{
		Hapyfish2_Island_Cache_BasicInfo::loadUserLevelList();
		
		$data = array('result' => 'OK');
		$this->echoResult($data);
	}

	public function loadlocalleveldataAction()
	{
		$cache = Hapyfish2_Island_Cache_BasicInfo::getBasicMC();
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$key = 'island:userlevellist';
		$list = $cache->get($key);
		$localcache->set($key, $list, false);

		$data = array('result' => SERVER_ID.' OK');
		$this->echoResult($data);
	}
	public function loaddataAction()
	{
		Hapyfish2_Island_Cache_BasicInfo::loadBuildingList();

		Hapyfish2_Island_Cache_BasicInfo::loadPlantList();

		Hapyfish2_Island_Cache_BasicInfo::loadBackgroundList();

		Hapyfish2_Island_Cache_BasicInfo::loadCardList();

		$data = array('result' => 'OK');
		$this->echoResult($data);
	}

	public function loadlocaldataAction()
	{
	    $isdel = (int)$this->_request->getParam('isdel');

		$cache = Hapyfish2_Island_Cache_BasicInfo::getBasicMC();
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$key = 'island:buildinglist';
		$list = $cache->get($key);
		$localcache->set($key, $list, false);

		$key = 'island:plantlist';
		$list = $cache->get($key);
		$localcache->set($key, $list, false);

		$key = 'island:backgroundlist';
		$list = $cache->get($key);
		$localcache->set($key, $list, false);

		$key = 'island:cardlist';
		$list = $cache->get($key);
		$localcache->set($key, $list, false);

		if ($isdel) {
		    Hapyfish2_Island_Bll_BasicInfo::removeDumpFile();
		}

		$data = array('result' => SERVER_ID.' OK');
		$this->echoResult($data);
	}

	public function dumpuserAction()
	{
		$uid = $this->_request->getParam('uid');
		if (empty($uid)) {
			$uid = 1042;
		}
		$gmuid = 134;
		Hapyfish2_Island_Tool_Island::dumpInitIsland($uid, $gmuid);
		echo 'OK';
		exit;
	}
}