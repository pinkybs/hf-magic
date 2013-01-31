<?php

class Hapyfish2_Magic_Cache_BasicInfo
{
	public static function getBasicMC()
	{
		$key = 'mc_0';
		return Hapyfish2_Cache_Factory::getBasicMC($key);
	}

	public static function getBasicVersion()
	{
        $key = 'magic:basicstaticver';
		$cache = self::getBasicMC();
		$ver = $cache->get($key);
		if (!$ver) {
		    $ver = date('Ymd').'1';
		}
		return $ver;
	}

    public static function setBasicVersion($ver)
	{
        $key = 'magic:basicstaticver';
		$cache = self::getBasicMC();
		$ok = $cache->set($key, $ver);
		return $ok;
	}

	public static function getFeedTemplate()
	{
		$key = 'magic:feedtemplate';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$tpl = $localcache->get($key);
		if (!$tpl) {
			$cache = self::getBasicMC();
			$tpl = $cache->get($key);
			if (!$tpl) {
				$tpl = self::loadFeedTemplate();
			}
			if ($tpl) {
				$localcache->set($key, $tpl);
			}
		}

		return $tpl;
	}

	public static function getFeedTemplateTitle($template_id)
	{
		$tpl = self::getFeedTemplate();
		if ($tpl && isset($tpl[$template_id])) {
			return $tpl[$template_id];
		}

		return null;
	}

	public static function loadFeedTemplate()
	{
		$db = Hapyfish2_Magic_Dal_BasicInfo::getDefaultInstance();
		$tpl = $db->getFeedTemplate();
		if ($tpl) {
			$key = 'magic:feedtemplate';
			$cache = self::getBasicMC();
			$cache->set($key, $tpl);
		}

		return $tpl;
	}

	public static function getBuildingList()
	{
		$key = 'magic:buildinglist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadBuildingList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getBuildingInfo($id)
	{
		$list = self::getBuildingList();
		if (isset($list[$id])) {
			return $list[$id];
		}

		return null;
	}

	public static function loadBuildingList()
	{
		$db = Hapyfish2_Magic_Dal_BasicInfo::getDefaultInstance();
		$list = $db->getBuildingList();
		if ($list) {
			$key = 'magic:buildinglist';
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

	public static function getItemList()
	{
		$key = 'magic:itemlist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadItemList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getItemInfo($id)
	{
		$list = self::getItemList();
		if (isset($list[$id])) {
			return $list[$id];
		}

		return null;
	}

	public static function loadItemList()
	{
		$db = Hapyfish2_Magic_Dal_BasicInfo::getDefaultInstance();
		$list = $db->getItemList();
		if ($list) {
			$key = 'magic:itemlist';
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

	public static function getGoodsList()
	{
		$key = 'magic:goodslist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadGoodsList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getGoodsInfo($id)
	{
		$list = self::getGoodsList();
		if (isset($list[$id])) {
			return $list[$id];
		}

		return null;
	}

	public static function loadGoodsList()
	{
		$db = Hapyfish2_Magic_Dal_BasicInfo::getDefaultInstance();
		$list = $db->getGoodsList();
		if ($list) {
			$key = 'magic:goodslist';
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

	public static function getMonsterList()
	{
		$key = 'magic:monsterlist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadMonsterList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getMonsterInfo($id)
	{
		$list = self::getMonsterList();
		if (isset($list[$id])) {
			return $list[$id];
		}

		return null;
	}

	public static function loadMonsterList()
	{
		$db = Hapyfish2_Magic_Dal_BasicInfo::getDefaultInstance();
		$list = $db->getMonsterList();
		if ($list) {
			$key = 'magic:monsterlist';
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

	public static function getUserLevelList()
	{
		$key = 'magic:userlevellist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadUserLevelList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getUserLevelInfo($id)
	{
		$list = self::getUserLevelList();
		if (isset($list[$id])) {
			return $list[$id];
		}

		return null;
	}

	public static function getUserLevelExp($id)
	{
		$list = self::getUserLevelList();
		if (isset($list[$id])) {
			return $list[$id]['exp'];
		}

		return 999999999;
	}

	public static function loadUserLevelList()
	{
		$db = Hapyfish2_Magic_Dal_BasicInfo::getDefaultInstance();
		$list = $db->getUserLevelList();
		if ($list) {
			$key = 'magic:userlevellist';
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

	public static function getHouseLevelList()
	{
		$key = 'magic:houselevellist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadHouseLevelList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getHouseLevelInfo($id)
	{
		$list = self::getHouseLevelList();
		if (isset($list[$id])) {
			return $list[$id];
		}

		return null;
	}

	public static function loadHouseLevelList()
	{
		$db = Hapyfish2_Magic_Dal_BasicInfo::getDefaultInstance();
		$list = $db->getHouseLevelList();
		if ($list) {
			$key = 'magic:houselevellist';
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

	public static function getGiftLevelList()
	{
		$key = 'magic:giftlevellist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadGiftLevelList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getGiftByUserLevel($level)
	{
		$list = self::getGiftLevelList();
		if (isset($list[$level])) {
			return $list[$level];
		}

		return null;
	}

	public static function loadGiftLevelList()
	{
		$db = Hapyfish2_Magic_Dal_BasicInfo::getDefaultInstance();
		$list = $db->getGiftLevelList();
		if ($list) {
			$key = 'magic:giftlevellist';
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

	public static function getSceneSizeList()
	{
		$key = 'magic:scenesizelist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadSceneSizeList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getSceneSizeInfo($id)
	{
		$list = self::getSceneSizeList();
		if ($list && isset($list[$id])) {
			return $list[$id];
		}

		return null;
	}

	public static function loadSceneSizeList()
	{
		$db = Hapyfish2_Magic_Dal_BasicInfo::getDefaultInstance();
		$list = $db->getSceneSizeList();
		if ($list) {
			$key = 'magic:scenesizelist';
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

	public static function getStudentLevelList()
	{
		$key = 'magic:studentlevellist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadStudentLevelList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getStudentLevelInfo($id)
	{
		$list = self::getStudentLevelList();
		if ($list && isset($list[$id])) {
			return $list[$id];
		}

		return 999999999;
	}

	public static function loadStudentLevelList()
	{
		$db = Hapyfish2_Magic_Dal_BasicInfo::getDefaultInstance();
		$list = $db->getStudentLevelList();
		if ($list) {
			$key = 'magic:studentlevellist';
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

	public static function getMagicStudyList()
	{
		$key = 'magic:magicstudylist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadMagicStudyList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getMagicStudyInfo($id)
	{
		$list = self::getMagicStudyList();
		if ($list && isset($list[$id])) {
			return $list[$id];
		}

		return null;
	}

	public static function loadMagicStudyList()
	{
		$db = Hapyfish2_Magic_Dal_BasicInfo::getDefaultInstance();
		$list = $db->getMagicStudyList();
		if ($list) {
			$key = 'magic:magicstudylist';
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

	public static function getMagicMixList()
	{
		$key = 'magic:magicmixlist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadMagicMixList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getMagicMixInfo($id)
	{
		$list = self::getMagicMixList();
		if ($list && isset($list[$id])) {
			return $list[$id];
		}

		return null;
	}

	public static function loadMagicMixList()
	{
		$db = Hapyfish2_Magic_Dal_BasicInfo::getDefaultInstance();
		$list = $db->getMagicMixList();
		if ($list) {
			$key = 'magic:magicmixlist';
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

	public static function getMagicTransList()
	{
		$key = 'magic:magictranslist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadMagicTransList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getMagicTransInfo($id)
	{
		$list = self::getMagicTransList();
		if ($list && isset($list[$id])) {
			return $list[$id];
		}

		return null;
	}

	public static function loadMagicTransList()
	{
		$db = Hapyfish2_Magic_Dal_BasicInfo::getDefaultInstance();
		$list = $db->getMagicTransList();
		if ($list) {
			$key = 'magic:magictranslist';
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

	public static function getNoticeList()
	{
		$key = 'magic:pubnoticelist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = array();
			}
			$localcache->set($key, $list, false, 900);
		}

		return $list;
	}

	public static function loadNoticeList()
	{
		$db = Hapyfish2_Magic_Dal_BasicInfo::getDefaultInstance();
		$list = $db->getNoticeList();
		if ($list) {
			$main = array();
			$sub = array();
			$pic = array();
			foreach ($list as $item){
				if ($item['position'] == 1) {
					$main[] = $item;
				} else if($value['position'] == 2){
					$sub[] = $item;
            	} else if($value['position'] == 3){
					$pic[] = $item;
				}
			}
            $info = array('main' => $main, 'sub' => $sub, 'pic' => $pic);

			$key = 'magic:pubnoticelist';
			$cache = self::getBasicMC();
			$cache->set($key, $info);
		} else {
			$info = array();
		}

		return $info;
	}

	public static function getStudentAwardList($sid)
	{
		$key = 'magic:studentawardlist:' . $sid;
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadStudentAwardList($sid);
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getStudentAwardInfo($sid, $level)
	{
		$list = self::getStudentAwardList($sid);
		if (isset($list[$level])) {
			return $list[$level];
		}

		return null;
	}

	public static function loadStudentAwardList($sid)
	{
		$db = Hapyfish2_Magic_Dal_BasicInfo::getDefaultInstance();
		$list = $db->getStudentAwardList($sid);
		if ($list) {
			$key = 'magic:studentawardlist:' . $sid;
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

	////////////

	public static function getTaskTrunkList()
	{
		$key = 'magic:tasktrunklist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadTaskTrunkList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getTaskTrunkInfo($id)
	{
		$list = self::getTaskTrunkList();
		if (isset($list[$id])) {
			return $list[$id];
		}

		return null;
	}

	public static function loadTaskTrunkList()
	{
		$db = Hapyfish2_Magic_Dal_BasicInfo::getDefaultInstance();
		$list = $db->getTaskTrunkList();
		if ($list) {
			$key = 'magic:tasktrunklist';
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

	public static function getTaskBranchList()
	{
		$key = 'magic:taskbranchlist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadTaskBranchList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getTaskBranchInfo($id)
	{
		$list = self::getTaskBranchList();
		if (isset($list[$id])) {
			return $list[$id];
		}

		return null;
	}

	public static function loadTaskBranchList()
	{
		$db = Hapyfish2_Magic_Dal_BasicInfo::getDefaultInstance();
		$list = $db->getTaskBranchList();
		if ($list) {
			$key = 'magic:taskbranchlist';
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

	public static function getTaskDailyList()
	{
		$key = 'magic:taskdailylist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadTaskDailyList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getTaskDailyInfo($id)
	{
		$list = self::getTaskDailyList();
		if (isset($list[$id])) {
			return $list[$id];
		}

		return null;
	}

	public static function loadTaskDailyList()
	{
		$db = Hapyfish2_Magic_Dal_BasicInfo::getDefaultInstance();
		$list = $db->getTaskDailyList();
		if ($list) {
			$key = 'magic:taskdailylist';
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

	public static function getTaskTutorialList()
	{
		$key = 'magic:tasktutoriallist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadTaskTutorialList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getTaskTutorialInfo($id)
	{
		$list = self::getTaskTutorialList();
		if (isset($list[$id])) {
			return $list[$id];
		}

		return null;
	}

	public static function loadTaskTutorialList()
	{
		$db = Hapyfish2_Magic_Dal_BasicInfo::getDefaultInstance();
		$list = $db->getTaskTutorialList();
		if ($list) {
			$key = 'magic:tasktutoriallist';
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

	public static function getTutorialList()
	{
		$key = 'magic:tutoriallist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadTutorialList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getTutorialInfo($id)
	{
		$list = self::getTutorialList();
		if (isset($list[$id])) {
			return $list[$id];
		}

		return null;
	}

	public static function loadTutorialList()
	{
		$db = Hapyfish2_Magic_Dal_BasicInfo::getDefaultInstance();
		$list = $db->getTutorialList();
		if ($list) {
			$key = 'magic:tutoriallist';
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

	public static function getAvatarList()
	{
		$key = 'magic:avatarlist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadAvatarList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getAvatarInfo($id)
	{
		$list = self::getAvatarList();
		if (isset($list[$id])) {
			return $list[$id];
		}

		return null;
	}

	public static function loadAvatarList()
	{
		$db = Hapyfish2_Magic_Dal_BasicInfo::getDefaultInstance();
		$list = $db->getAvatarList();
		if ($list) {
			$key = 'magic:avatarlist';
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

	public static function getNpcList()
	{
		$key = 'magic:npclist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadNpcList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getNpcInfo($id)
	{
		$list = self::getNpcList();
		if (isset($list[$id])) {
			return $list[$id];
		}

		return null;
	}

	public static function loadNpcList()
	{
		$db = Hapyfish2_Magic_Dal_BasicInfo::getDefaultInstance();
		$list = $db->getNpcList();
		if ($list) {
			$key = 'magic:npclist';
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

	public static function getStudentList()
	{
		$key = 'magic:studentlist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadStudentList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getStudentInfo($id)
	{
		$list = self::getStudentList();
		if (isset($list[$id])) {
			return $list[$id];
		}

		return null;
	}

	public static function loadStudentList()
	{
		$db = Hapyfish2_Magic_Dal_BasicInfo::getDefaultInstance();
		$list = $db->getStudentList();
		if ($list) {
			$key = 'magic:studentlist';
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

	public static function getOneStory($storyId)
	{
		$key = 'magic:story:' . $storyId;
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$story = $localcache->get($key);
		if (!$story) {
			$cache = self::getBasicMC();
			$story = $cache->get($key);
			if (!$story) {
				$story = self::loadOneStory($storyId);
			}
			if ($story) {
				$localcache->set($key, $story);
			}
		}

		return $story;
	}

	public static function loadOneStory($storyId)
	{
		$db = Hapyfish2_Magic_Dal_BasicInfo::getDefaultInstance();
		$story = $db->getOneStory($storyId);
		if ($story) {
			$key = 'magic:story:' . $storyId;
			$cache = self::getBasicMC();
			$cache->set($key, $story);
		}

		return $story;
	}

	public static function getTaskTypeList()
	{
		$key = 'magic:tasktypelist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadTaskTypeList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getTaskTypeInfo($id)
	{
		$list = self::getTaskTypeList();
		if (isset($list[$id])) {
			return $list[$id];
		}

		return null;
	}

	public static function loadTaskTypeList()
	{
		$db = Hapyfish2_Magic_Dal_BasicInfo::getDefaultInstance();
		$list = $db->getTaskTypeList();
		if ($list) {
			$key = 'magic:tasktypelist';
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

    public static function getDailyAwardList()
	{
		$key = 'magic:dailyaward';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadDailyAward();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getDailyAwardInfo($id)
	{
		$list = self::getDailyAwardList();
		if ($list && isset($list[$id])) {
			return $list[$id];
		}

		return null;
	}

	public static function loadDailyAward()
	{
		$db = Hapyfish2_Magic_Dal_BasicInfo::getDefaultInstance();
		$list = $db->getDailyAwardList();
		if ($list) {
			$key = 'magic:dailyaward';
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

    public static function getActivityList()
	{
		$key = 'magic:activity';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadActivity();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getActivityInfo($id)
	{
		$list = self::getActivityList();
		if ($list && isset($list[$id])) {
			return $list[$id];
		}

		return null;
	}

	public static function loadActivity()
	{
		$db = Hapyfish2_Magic_Dal_BasicInfo::getDefaultInstance();
		$list = $db->getActivityList();
		if ($list) {
			$key = 'magic:activity';
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

    public static function getCharacterList()
	{
		$key = 'magic:character';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadCharacter();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getCharacterInfo($id)
	{
		$list = self::getCharacterList();
		if ($list && isset($list[$id])) {
			return $list[$id];
		}

		return null;
	}

	public static function loadCharacter()
	{
		$db = Hapyfish2_Magic_Dal_BasicInfo::getDefaultInstance();
		$list = $db->getCharacterList();
		if ($list) {
			$key = 'magic:character';
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

    /* map basic */
	public static function getMapAllList()
	{
	    $data1 = self::getMapCopyList();
		$data2 = self::getMapSceneList();
		$list = $data1 + $data2;
		return $list;
	}

    public static function getMapAllInfo($id)
	{
		$list = self::getMapAllList();
		if ($list && isset($list[$id])) {
			return $list[$id];
		}
		return null;
	}

    public static function getMapSceneList()
	{
		$key = 'magic:mapscenelist';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadMapSceneList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}
		return $list;
	}

	public static function getMapSceneInfo($id)
	{
		$list = self::getMapSceneList();
		if ($list && isset($list[$id])) {
			return $list[$id];
		}
		return null;
	}

	public static function loadMapSceneList()
	{
		$db = Hapyfish2_Magic_Dal_BasicInfo::getDefaultInstance();
		$list = $db->getMapSceneList();
		if ($list) {
			$key = 'magic:mapscenelist';
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}
		return $list;
	}

    public static function getMapCopyList()
	{
		$key = 'magic:mapcopy';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadMapCopy();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getMapCopyInfo($id)
	{
		$list = self::getMapCopyList();
		if ($list && isset($list[$id])) {
			return $list[$id];
		}

		return null;
	}

	public static function loadMapCopy()
	{
		$db = Hapyfish2_Magic_Dal_BasicInfo::getDefaultInstance();
		$list = $db->getMapCopyList();
		if ($list) {
			$key = 'magic:mapcopy';
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

    public static function getMapBuildingList()
	{
		$key = 'magic:mapbuilding';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadMapBuilding();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getMapBuildingInfo($id)
	{
		$list = self::getMapBuildingList();
		if ($list && isset($list[$id])) {
			return $list[$id];
		}

		return null;
	}

	public static function loadMapBuilding()
	{
		$db = Hapyfish2_Magic_Dal_BasicInfo::getDefaultInstance();
		$list = $db->getMapBuildingList();
		if ($list) {
			$key = 'magic:mapbuilding';
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

    public static function getMapMonsterList()
	{
		$key = 'magic:mapmonster';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadMapMonster();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getMapMonsterInfo($id)
	{
		$list = self::getMapMonsterList();
		if ($list && isset($list[$id])) {
			return $list[$id];
		}

		return null;
	}

	public static function loadMapMonster()
	{
		$db = Hapyfish2_Magic_Dal_BasicInfo::getDefaultInstance();
		$list = $db->getMapMonsterList();
		if ($list) {
			$key = 'magic:mapmonster';
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

    public static function getMapAnimationList()
	{
		$key = 'magic:mapanimation';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadMapAnimation();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

    public static function getMapAnimationInfo($id)
	{
		$list = self::getMapAnimationList();
		if ($list && isset($list[$id])) {
			return $list[$id];
		}

		return null;
	}

    public static function loadMapAnimation()
	{
		$db = Hapyfish2_Magic_Dal_BasicInfo::getDefaultInstance();
		$list = $db->getMapAnimationList();
		if ($list) {
			$key = 'magic:mapanimation';
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

	public static function getMapTaskListByPMapId($pMapId)
	{
        $key = 'magic:maptask:'.$pMapId;
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$list = $localcache->get($key);
		if (!$list) {
		    $allList = self::getMapTaskList();
		    $list = array();
		    foreach ($allList as $id=>$data) {
		        if ($data['map_parent_id'] == $pMapId) {
                    $list[$id] = $data;
		        }
		    }
    		if ($list) {
    			$localcache->set($key, $list);
    		}
		}

        return $list;
	}

    public static function getMapTaskList()
	{
		$key = 'magic:maptask';
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadMapTask();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

    public static function getMapTaskInfo($id)
	{
		$list = self::getMapTaskList();
		if ($list && isset($list[$id])) {
			return $list[$id];
		}

		return null;
	}

    public static function loadMapTask()
	{
		$db = Hapyfish2_Magic_Dal_BasicInfo::getDefaultInstance();
		$list = $db->getMapTaskList();
		if ($list) {
			$key = 'magic:maptask';
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}
	/* -map basic */

	/* map copy related transcritp */
    public static function getMapCopyTranscriptList($mapId)
	{
		$key = 'magic:maptranscript:'.$mapId;
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadMapCopyTranscript($mapId);
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getMapCopyTranscript($mapId, $listName)
	{
		$list = self::getMapCopyTranscriptList($mapId);
		return $list[$listName];
	}

    public static function loadMapCopyTranscript($mapId)
	{
		$db = Hapyfish2_Magic_Dal_BasicInfo::getDefaultInstance();
		$list1 = $db->getMapCopyDecorList($mapId);
		$list2 = $db->getMapCopyPortalList($mapId);
		$list3 = $db->getMapCopyFloorList($mapId);
		$list4 = $db->getMapCopyGhostList($mapId);
		$list5 = $db->getMapCopyMineList($mapId);
		$list = array(
            'decorList' => $list1,
            'portalList' => $list2,
            'floorList' => $list3,
            'ghostList' => $list4,
            'mineList' => $list5
		);

		$key = 'magic:maptranscript:'.$mapId;
		$cache = self::getBasicMC();
		$cache->set($key, $list);

		return $list;
	}
	/* -map copy related transcritp */
}