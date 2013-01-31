<?php

class Hapyfish2_Magic_Bll_Tools
{
	public static function loadBasicAll()
	{
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		Hapyfish2_Magic_Cache_BasicInfo::loadAvatarList();
		$key = 'magic:avatarlist';
		$localcache->set($key, $list, false);

		Hapyfish2_Magic_Cache_BasicInfo::loadBuildingList();
		$key = 'magic:buildinglist';
		$localcache->set($key, $list, false);

		Hapyfish2_Magic_Cache_BasicInfo::loadFeedTemplate();
		$key = 'magic:feedtemplate';
		$localcache->set($key, $list, false);

		Hapyfish2_Magic_Cache_BasicInfo::loadHouseLevelList();
		$key = 'magic:houselevellist';
		$localcache->set($key, $list, false);

		Hapyfish2_Magic_Cache_BasicInfo::loadItemList();
		$key = 'magic:itemlist';
		$localcache->set($key, $list, false);

		Hapyfish2_Magic_Cache_BasicInfo::loadMagicMixList();
		$key = 'magic:magicmixlist';
		$localcache->set($key, $list, false);

		Hapyfish2_Magic_Cache_BasicInfo::loadMagicStudyList();
		$key = 'magic:magicstudylist';
		$localcache->set($key, $list, false);

		Hapyfish2_Magic_Cache_BasicInfo::loadMagicTransList();
		$key = 'magic:magictranslist';
		$localcache->set($key, $list, false);

		Hapyfish2_Magic_Cache_BasicInfo::loadMonsterList();
		$key = 'magic:monsterlist';
		$localcache->set($key, $list, false);

		Hapyfish2_Magic_Cache_BasicInfo::loadNpcList();
		$key = 'magic:npclist';
		$localcache->set($key, $list, false);

		Hapyfish2_Magic_Cache_BasicInfo::loadMapSceneList();
		$key = 'magic:mapscenelist';
		$localcache->set($key, $list, false);

		Hapyfish2_Magic_Cache_BasicInfo::loadSceneSizeList();
		$key = 'magic:scenesizelist';
		$localcache->set($key, $list, false);

		Hapyfish2_Magic_Cache_BasicInfo::loadStudentLevelList();
		$key = 'magic:studentlevellist';
		$localcache->set($key, $list, false);

		$list = Hapyfish2_Magic_Cache_BasicInfo::loadStudentList();
		$key = 'magic:studentlist';
		$localcache->set($key, $list, false);
	    foreach ($list as $sid=>$data) {
		    $listSub = Hapyfish2_Magic_Cache_BasicInfo::loadStudentAwardList($sid);
    		$key1 = 'magic:studentawardlist:'.$sid;
    		$localcache->set($key1, $listSub, false);
		}

		Hapyfish2_Magic_Cache_BasicInfo::loadTaskBranchList();
		$key = 'magic:taskbranchlist';
		$localcache->set($key, null, false);

		Hapyfish2_Magic_Cache_BasicInfo::loadTaskDailyList();
		$key = 'magic:taskdailylist';
		$localcache->set($key, null, false);

		Hapyfish2_Magic_Cache_BasicInfo::loadTaskTrunkList();
		$key = 'magic:tasktrunklist';
		$localcache->set($key, null, false);

		Hapyfish2_Magic_Cache_BasicInfo::loadTaskTutorialList();
		$key = 'magic:tasktutoriallist';
		$localcache->set($key, null, false);

		Hapyfish2_Magic_Cache_BasicInfo::loadTaskTypeList();
		$key = 'magic:tasktypelist';
		$localcache->set($key, null, false);

		Hapyfish2_Magic_Cache_BasicInfo::loadTutorialList();
		$key = 'magic:tutoriallist';
		$localcache->set($key, null, false);

		Hapyfish2_Magic_Cache_BasicInfo::loadUserLevelList();
		$key = 'magic:userlevellist';
		$localcache->set($key, null, false);

		for ($storyId=1;$storyId<=24;$storyId++) {
		    Hapyfish2_Magic_Cache_BasicInfo::loadOneStory($storyId);
		    $key = 'magic:story:' . $storyId;
		    $localcache->set($key, null, false);
		}

		Hapyfish2_Magic_Cache_BasicInfo::loadDailyAward();
		$key = 'magic:dailyaward';
		$localcache->set($key, null, false);

		Hapyfish2_Magic_Cache_BasicInfo::loadActivity();
		$key = 'magic:activity';
		$localcache->set($key, null, false);

		Hapyfish2_Magic_Cache_BasicInfo::loadCharacter();
		$key = 'magic:character';
		$localcache->set($key, null, false);

		/* map */
		$list = Hapyfish2_Magic_Cache_BasicInfo::loadMapBuilding();
		$key = 'magic:mapbuilding';
		$localcache->set($key, $list, false);

		$list = Hapyfish2_Magic_Cache_BasicInfo::loadMapMonster();
		$key = 'magic:mapmonster';
		$localcache->set($key, $list, false);

		$list = Hapyfish2_Magic_Cache_BasicInfo::loadMapCopy();
		$key = 'magic:mapcopy';
		$localcache->set($key, $list, false);

		$list = Hapyfish2_Magic_Cache_BasicInfo::loadMapSceneList();
		$key = 'magic:mapscenelist';
		$localcache->set($key, $list, false);

		$list = Hapyfish2_Magic_Cache_BasicInfo::loadMapTask();
		$key = 'magic:maptask';
		$localcache->set($key, $list, false);

	    for ($pMapId=1;$pMapId<=10;$pMapId++) {
		    $key1 = 'magic:maptask:'.$pMapId;
		    $localcache->set($key1, null, false);
		}

		$list = Hapyfish2_Magic_Cache_BasicInfo::loadMapAnimation();
		$key = 'magic:mapanimation';
		$localcache->set($key, $list, false);

		$list = Hapyfish2_Magic_Cache_BasicInfo::getMapAllList();
	    foreach ($list as $mapId=>$data) {
		    $listSub = Hapyfish2_Magic_Cache_BasicInfo::loadMapCopyTranscript($mapId);
    		$key1 = 'magic:maptranscript:'.$mapId;
    		$localcache->set($key1, $listSub, false);
		}
		/* -map */

	}

	public static function reInitUser($uid)
	{
		$dalUser = Hapyfish2_Magic_Dal_User::getDefaultInstance();
		$dalFloor = Hapyfish2_Magic_Dal_Floor::getDefaultInstance();
		$dalWall = Hapyfish2_Magic_Dal_Wall::getDefaultInstance();
		$dalFloorBag = Hapyfish2_Magic_Dal_FloorBag::getDefaultInstance();
		$dalWallBag = Hapyfish2_Magic_Dal_WallBag::getDefaultInstance();
		$dalBuilding = Hapyfish2_Magic_Dal_Building::getDefaultInstance();
		$dalDoor = Hapyfish2_Magic_Dal_Door::getDefaultInstance();
		$dalDesk = Hapyfish2_Magic_Dal_Desk::getDefaultInstance();
		$dalItem = Hapyfish2_Magic_Dal_Item::getDefaultInstance();
		$dalStudent = Hapyfish2_Magic_Dal_Student::getDefaultInstance();
		$dalMagic = Hapyfish2_Magic_Dal_Magic::getDefaultInstance();
		$dalTask = Hapyfish2_Magic_Dal_Task::getDefaultInstance();
		$dalTaskOpen= Hapyfish2_Magic_Dal_TaskOpen::getDefaultInstance();
		$dalTaskDaily = Hapyfish2_Magic_Dal_TaskDaily::getDefaultInstance();
		$dalAchievement = Hapyfish2_Magic_Dal_Achievement::getDefaultInstance();
		$dalAchievementDaily = Hapyfish2_Magic_Dal_AchievementDaily::getDefaultInstance();
		$dalLevelUpLog = Hapyfish2_Magic_Dal_LevelUpLog::getDefaultInstance();

		$today = date('Ymd');

		$cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$cache2 = Hapyfish2_Cache_Factory::getMC($uid);

		//初始化基础数据
		$info = array(
			'coin' => INIT_USER_COIN, 'gold' => INIT_USER_GOLD, 'exp' => 0, 'mp' => INIT_USER_MP, 'max_mp' => INIT_USER_MP,
			'level' => 1, 'house_level' => 1, 'tile_x_length' => 8, 'tile_z_length' => 8, 'study_magic_num' => 1,
			'trans_type' => 0, 'trans_start_time' => 0, 'cur_scene_id' => HOME_SCENE_ID, 'open_scene_list' => HOME_SCENE_ID,
			'help' => '', 'help_completed' => 0, 'avatar_edit' => 0,
			'last_login_time' => 0, 'today_login_count' => 0, 'active_login_count' => 0, 'max_active_login_count' => 0, 'all_login_count' => 0
		);
		$dalUser->update($uid, $info);
		$keys = array(
			'm:u:exp:' . $uid,
			'm:u:coin:' . $uid,
			'm:u:gold:' . $uid,
			'm:u:level:' . $uid,
			'm:u:scene:' . $uid,
			'm:u:avatar:' . $uid,
			'm:u:mp:' . $uid,
			'm:u:trans:' . $uid,
			'm:u:login:' . $uid
		);
		foreach ($keys as $key) {
			$cache->delete($key);
		}
//print_r(Hapyfish2_Magic_Cache_UserHelp::getHelpInfo($uid));exit;
		$key = 'm:u:help:' . $uid;
		$cache2->delete($key);
		$key = 'm:u:dlyvisit:' . $uid;
		$cache2->delete($key);

		//初始化floor
        $tmp = array();
        for($i = 0; $i < 8; $i++) {
        	for($j = 0; $j < 8; $j++) {
        		$tmp[$i][$j] = INIT_USER_FLOOR_ID;
        	}
        }
        $data = json_encode($tmp);
        $dalFloor->update($uid, $data);
		$dalFloorBag->clear($uid);
		$key = 'm:u:floorbag:' . $uid;
		$cache->delete($key);
		$key = 'm:u:floor:' . $uid;
		$cache2->delete($key);

		//初始化wall
        $tmp = array();
       	for($j = 0; $j < 8; $j++) {
       		$tmp[] = INIT_USER_WALL_ID;
       	}
        $data = json_encode(array($tmp, $tmp));
        $dalWall->update($uid, $data);
		$dalWallBag->clear($uid);
		$key = 'm:u:wallbag:' . $uid;
		$cache->delete($key);
		$key = 'm:u:wall:' . $uid;
		$cache2->delete($key);

		//初始化building
		$dalBuilding->clear($uid);
	    $ids = Hapyfish2_Magic_Cache_Building::getInSceneIds($uid);
        if ($ids) {
            foreach ($ids as $id) {
        		$key = 'm:u:bld:' . $uid . ':' . $id;
        		$cache->delete($key);
        	}
        }
        $key = 'm:u:bldids:all:' . $uid;
        $cache2->delete($key);
        $key = 'm:u:bldids:inscn:' . $uid;
        $cache2->delete($key);
        $dalBuilding->init($uid);

        //初始化door
		$dalDoor->clear($uid);
		$ids = Hapyfish2_Magic_Cache_Door::getInSceneIds($uid);
	    if ($ids) {
            foreach ($ids as $id) {
        		$key = 'i:u:door:' . $uid . ':' . $id;
        		$cache->delete($key);
        	}
        }
        $key = 'm:u:drids:all:' . $uid;
        $cache2->delete($key);
        $key = 'm:u:drids:inscene:' . $uid;
        $cache2->delete($key);
        $dalDoor->init($uid);

        //初始化desk
		$dalDesk->clear($uid);
		$ids = Hapyfish2_Magic_Cache_Desk::getInSceneIds($uid);
	    if ($ids) {
            foreach ($ids as $id) {
        		$key = 'i:u:desk:' . $uid . ':' . $id;
        		$cache->delete($key);
        		$key2 = 'i:u:mooch:desk:' . $uid . ':' . $id;
        		$cache2->delete($key);
        	}
        }
        $key = 'm:u:dkids:all:' . $uid;
        $cache2->delete($key);
        $key = 'm:u:dkids:inscene:' . $uid;
        $cache2->delete($key);
        $dalDesk->init($uid);

        //初始化item
		$dalItem->clear($uid);
		$key = 'm:u:item:' . $uid;
		$cache->delete($key);
		$itemList = array('21' => 3, '22' => 3, '8301' => 10, '8302' => 10, '8311' => 10, '8312' => 10, '8313' => 10);
		if (!empty($itemList)) {
			$dalItem->init($uid, $itemList);
		}

		//初始化student
		$dalStudent->clear($uid);
		$ids = Hapyfish2_Magic_Cache_Student::getUnlockStudentIds($uid);
	    if ($ids) {
            foreach ($ids as $id) {
        		$key = 'm:u:student:' . $uid . ':' . $id;
        		$cache->delete($key);
        	}
        }
        $key = 'm:u:unlocksids:' . $uid;
        $cache2->delete($key);
		$dalStudent->init($uid);

		//初始化magiclist
		$magicInfo = array(
			'study_ids' => '[1001]',
			'trans_ids' => '[8001]'
		);
		$dalMagic->update($uid, $magicInfo);
		$key = 'm:u:magiclist:' . $uid;
		$cache2->delete($key);

		//初始化task
		$dalTask->clear($uid);
		$key = 'm:u:alltask:' . $uid;
		$cache2->delete($key);

		//初始化opentask
		$taskOpenInfo = array(
			'trunk' => 0, 'trunk_track_num' => 0, 'trunk_start' => 1, 'branch' => '[]'
		);
		$dalTaskOpen->update($uid, $taskOpenInfo);
		$key = 'm:u:taskopen:' . $uid;
		$cache->delete($key);

		//初始化daily task
		$taskDailyInfo = array(
			'today' => $today, 'tids' => ''
		);
		$dalTaskDaily->update($uid, $taskDailyInfo);
		$key = 'm:u:alltaskdly:' . $uid;
		$cache2->delete($key);

		//初始化achievement
		$achieInfo = array(
			'num_1' => 0, 'num_2' => 0, 'num_3' => 0, 'num_4' => 0, 'num_5' => 0, 'num_6' => 0, 'num_7' => 0, 'num_8' => 0,
			'num_9' => 0, 'num_10' => 0, 'num_11' => 0, 'num_12' => 0, 'num_13' => 0, 'num_14' => 0, 'num_15' => 0, 'num_16' => 0,
			'num_17' => 0, 'num_18' => 0, 'num_19' => 0, 'num_20' => 0, 'num_21' => 0, 'num_22' => 0, 'num_23' => 0, 'num_24' => 0
		);
		$dalAchievement->update($uid, $achieInfo);
		$key = 'm:u:ach:' . $uid;
        $cache->delete($key);

		//初始化daily achievement
		$achieDailyInfo = array(
			'today' => $today,
			'num_1' => 0, 'num_2' => 0, 'num_3' => 0, 'num_4' => 0, 'num_5' => 0, 'num_6' => 0, 'num_7' => 0, 'num_8' => 0
		);
		$dalAchievementDaily->update($uid, $achieDailyInfo);
		$key = 'm:u:achdly:' . $uid;
		$cache->delete($key);

		//删除升级日志
		$dalLevelUpLog->clear($uid);
	}

}