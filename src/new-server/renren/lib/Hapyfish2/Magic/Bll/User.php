<?php

class Hapyfish2_Magic_Bll_User
{
	public static function getUserInit($uid)
	{
		$user = Hapyfish2_Platform_Bll_User::getUser($uid);
		$userVO = Hapyfish2_Magic_HFC_User::getUserVO($uid);

		$t = time();
		$trans_time = $userVO['trans_start_time'];
		$endtime = Hapyfish2_Magic_Bll_Magic::getTransEndTime($uid, $userVO['trans_type'], $trans_time);
		$trans_time = $endtime - $t;
		if ($trans_time < 0) {
			$trans_time = 0;
		}

		return array(
			'uid' => $userVO['uid'],
			'name' => $user['name'],
			'face' => $user['figureurl'],
			'exp' => $userVO['exp'],
		    'max_exp' => $userVO['next_level_exp'],
			'level' => $userVO['level'],
			'coin' => $userVO['coin'],
			'gem' => $userVO['gold'],
		    'mp' => $userVO['mp'],
		    'max_mp' => $userVO['max_mp'],
			'replyMp_time' => $userVO['mp_set_time'] + MP_RECOVERY_TIME - $t,
			'replyMpPer' => MP_RECOVERY_MP,//MP_RECOVERY_RATE
			'replyMpTime' => MP_RECOVERY_TIME,
			'avatar' => $userVO['avatar'],
			'roomLevel' => $userVO['house_level'],
		    'tile_x_length' => $userVO['tile_x_length'],
            'tile_z_length' => $userVO['tile_z_length'],
			'currentSceneId' => $userVO['cur_scene_id'],
			'trans_mid' => $userVO['trans_type'],
			'trans_time' => $trans_time
		);
	}

	public static function joinUser($uid)
	{
		$user = Hapyfish2_Platform_Bll_User::getUser($uid);
		if (empty($user)) {
			return false;
		}

		$step = 0;
		$today = date('Ymd');
		try {
			$dalUser = Hapyfish2_Magic_Dal_User::getDefaultInstance();
			$dalUserSequence = Hapyfish2_Magic_Dal_UserSequence::getDefaultInstance();
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
			$dalTaskOpen= Hapyfish2_Magic_Dal_TaskOpen::getDefaultInstance();

			$dalAchievement = Hapyfish2_Magic_Dal_Achievement::getDefaultInstance();
			$dalAchievementDaily = Hapyfish2_Magic_Dal_AchievementDaily::getDefaultInstance();

			//开始
			//
			$dalUser->init($uid);
			$step++;

			$dalUserSequence->init($uid);
			$step++;

			$dalFloor->init($uid, INIT_USER_FLOOR_ID, 8);
			$step++;

			$dalWall->init($uid, INIT_USER_WALL_ID, 8);
			$step++;

			$listFloor = array();
			if (!empty($listFloor)) {
				$dalFloorBag->init($uid, $listFloor);
			}
			$step++;

			$listWall = array();
			if (!empty($listWall)) {
				$dalWallBag->init($uid, $listWall);
			}
			$step++;

			$dalBuilding->init($uid);
			$step++;

			$dalDoor->init($uid);
			$step++;

			$dalDesk->init($uid);
			$step++;

			$itemList = array('21' => 3, '22' => 3, '8301' => 10, '8302' => 10, '8311' => 10, '8312' => 10, '8313' => 10);
			if (!empty($itemList)) {
				$dalItem->init($uid, $itemList);
			}
			$step++;

			$dalStudent->init($uid);
			$step++;

			$dalMagic->init($uid);
			$step++;

			$dalTaskOpen->init($uid);
			$step++;

			$dalAchievement->init($uid);
			$step++;

			$dalAchievementDaily->init($uid, $today);
			$step++;

		}
		catch (Exception $e) {
			info_log('[' . $step . ']' . $e->getMessage(), 'magic.user.init');
            return false;
		}

		Hapyfish2_Magic_Cache_User::setAppUser($uid);

		return true;
	}

	public static function initAvatar($uid, $avatarId)
	{
		$allowIds = array(801, 802, 803, 804);
		if (!in_array($avatarId, $allowIds)) {
			return Hapyfish2_Magic_Bll_UserResult::Error('avatar_id_error');
		}

		$avatarInfo = Hapyfish2_Magic_HFC_User::getUserAvatar($uid);
		if ($avatarInfo['avatar_edit'] != 0) {
			return Hapyfish2_Magic_Bll_UserResult::Error('avatar_can_not_edit');
		}

		$avatarInfo['avatar_id'] = $avatarId;
		$avatarInfo['avatar_edit'] = 1;

		$ok = Hapyfish2_Magic_HFC_User::updateUserAvatar($uid, $avatarInfo, true);
		if (!$ok) {
			return Hapyfish2_Magic_Bll_UserResult::Error('avatar_edit_error');
		}

		return Hapyfish2_Magic_Bll_UserResult::all();
	}

	public static function changehelp($uid, $help)
	{
        if (!in_array($help, array('1','2','3','4','5','6','7')) ) {
        	return Hapyfish2_Magic_Bll_UserResult::Error('id_error');
        }

        //get user help info
        $userHelp = Hapyfish2_Magic_Cache_UserHelp::getHelpInfo($uid);
        $helpList = $userHelp['helpList'];
        $comCount = $userHelp['completeCount'];

        if ($comCount >= 7 || $helpList[$help] == 1) {
        	return Hapyfish2_Magic_Bll_UserResult::Error('help_has_completed');
        }

        if ( $comCount < 6 && $help == 7 ) {
        	return Hapyfish2_Magic_Bll_UserResult::Error('help_has_not_all_completed');
        }

        //report tutorial log
		$logger = Hapyfish2_Util_Log::getInstance();
		$userInfo = Hapyfish2_Platform_Cache_User::getUser($uid);
		$joinTime = $userInfo['create_time'];
		$gender = $userInfo['gender'];
		$logger->report('tutorial', array($uid, $help, $joinTime, $gender));

		$helpList[$help] = 1;
		Hapyfish2_Magic_Cache_UserHelp::updateHelp($uid, $helpList);

		$ret = array();
		$goldChange = 0;

        if ( $help == 7 ) {
        	$t = time();
        	$tutorialInfo = Hapyfish2_Magic_Cache_BasicInfo::getTutorialInfo($help);

			//奖励道具
			if (!empty($tutorialInfo['items'])) {
				$addItem = array();
	    		$items = json_decode($tutorialInfo['items']);
	    		foreach ($items as $v) {
	    			$ok = Hapyfish2_Magic_HFC_Item::addUserItem($uid, $v[0], $v[1]);
	    			if ($ok) {
	    				$addItem[] = array($v[0], $v[1], 0);
	    			}
	    		}
	    		$ret['addItem'] = $addItem;
			}

			//奖励宝石
			if (!empty($tutorialInfo['gold']) && $tutorialInfo['gold'] > 0) {
				$goldChange = $tutorialInfo['gold'];
				$goldInfo = array('gold' => $goldChange, 'type' => 4, 'time' => $t);
				Hapyfish2_Magic_Bll_Gold::add($uid, $goldInfo);
			}
        }

        //新手任务奖励
        if ($help == 2 || $help == 3 || $help == 5 || $help == 6) {
        	$taskId = 1000 + $help;
        	Hapyfish2_Magic_Bll_Task_Tutorial_Base::trigger($uid, $taskId);
        }

		return Hapyfish2_Magic_Bll_UserResult::all();
	}

	public static function checkLevelUp($uid)
	{
		$user = Hapyfish2_Magic_HFC_User::getUser($uid, array('exp' => 1, 'level' => 1));
		if (!$user) {
			return false;
		}

		$userLevel = $user['level'];
		$nextLevelInfo = Hapyfish2_Magic_Cache_BasicInfo::getUserLevelInfo($userLevel + 1);
		if (!$nextLevelInfo) {
			return false;
		}

		if ($user['exp'] < $nextLevelInfo['exp']) {
			return false;
		}

		$user['level'] += 1;
		$userLevelInfo = array('level' => $user['level'], 'house_level' => $user['house_level']);

		//
		$ok = Hapyfish2_Magic_HFC_User::updateUserLevel($uid, $userLevelInfo);
		if ($ok) {
			Hapyfish2_Magic_Bll_UserResult::setLevelUp($uid, true);
			$now = time();
			//升级日志记录
			Hapyfish2_Magic_Bll_LevelUpLog::add($uid, $userLevel, $user['level']);

			//最大魔法值增加，并且魔法值恢复满
			$userMPInfo = Hapyfish2_Magic_HFC_User::getUserMp($uid);
			$userMPInfo['max_mp'] += $nextLevelInfo['max_mp_add'];
			$userMPInfo['mp'] = $userMPInfo['max_mp'];
			$userMPInfo['mp_set_time'] = $now;
			Hapyfish2_Magic_HFC_User::updateUserMp($uid, $userMPInfo, true);
			Hapyfish2_Magic_Bll_Scene::checkRoomLevelUp($uid);

			//升级奖励
			$awardRot = new Hapyfish2_Magic_Bll_Award();
			//coin
			if ($nextLevelInfo['coin'] > 0) {
				$awardRot->setCoin($nextLevelInfo['coin']);
			}
			//gold
			if ($nextLevelInfo['levelup_gmoney'] > 0) {
				$awardRot->setGold($nextLevelInfo['levelup_gmoney'], 7);
			}
			//items
			$items = json_decode($nextLevelInfo['levelup_item'], true);
			if (!empty($items)) {
				$awardRot->setItemList($items);
			}
			//decors
			$decors = json_decode($nextLevelInfo['levelup_decors'], true);
			if (!empty($decors)) {
				$awardRot->setDecorList($decors);
			}

			$awardRot->sendOne($uid);

			//派发事件
			$event = array('uid' => $uid, 'level' => $user['level']);
			Hapyfish2_Magic_Bll_Event::levelUp($event);

			return true;
		}

		return false;
	}

	public static function updateLoginTime($uid)
	{
	    $loginInfo = Hapyfish2_Magic_HFC_User::getUserLoginInfo($uid);
		if (!$loginInfo) {
			return null;
		}

		$isSaveDb = true;
		$now = time();
		$todayTm = strtotime(date('Ymd'));
		$newLoginInfo = array();
		$newLoginInfo['last_login_time'] = $loginInfo['last_login_time'];
		if ($loginInfo['last_login_time'] < $now) {
		    $newLoginInfo['last_login_time'] = $now;
		}
		$newLoginInfo['today_login_count'] = $loginInfo['today_login_count'] + 1;
		$newLoginInfo['all_login_count'] = $loginInfo['all_login_count'] + 1;
		$newLoginInfo['active_login_count'] = $loginInfo['active_login_count'];
		$newLoginInfo['max_active_login_count'] = $loginInfo['max_active_login_count'];

        //info_log(json_encode($loginInfo), 'aa');

		//new day come
		if ($todayTm > $loginInfo['last_login_time']) {
		    $isSaveDb = true;
		    $newLoginInfo['today_login_count'] = 1;
		    if ($todayTm - $loginInfo['last_login_time'] > 86460) {
		        $newLoginInfo['active_login_count'] = 0;
		    }
		    else {
		        $newLoginInfo['active_login_count'] = $loginInfo['active_login_count'] + 1;
		        if ($newLoginInfo['active_login_count'] > $loginInfo['max_active_login_count']) {
                    $newLoginInfo['max_active_login_count'] = $newLoginInfo['active_login_count'];
		        }
		    }
		    //add log
			$logger = Hapyfish2_Util_Log::getInstance();
			$userInfo = Hapyfish2_Platform_Bll_User::getUser($uid);
			$joinTime = $userInfo['create_time'];
			$gender = $userInfo['gender'];
			$userLevelInfo = Hapyfish2_Magic_HFC_User::getUserLevel($uid);
			$userLevel = $userLevelInfo['level'];
			$logger->report('101', array($uid, $joinTime, $gender, $userLevel));
		}

		foreach ($newLoginInfo as $key=>$value) {
		    $newLoginInfo[$key] = (int)$value;
		}

        Hapyfish2_Magic_HFC_User::updateUserLoginInfo($uid, $newLoginInfo, $isSaveDb);
        return $newLoginInfo;
	}

}