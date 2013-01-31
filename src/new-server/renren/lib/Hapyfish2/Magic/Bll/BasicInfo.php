<?php

class Hapyfish2_Magic_Bll_BasicInfo
{
	public static function getInitVoData($v = '1.0', $compress = false)
	{
		if (!$compress) {
			return self::restore($v);
		} else {
			return self::restoreCompress($v);
		}
	}

	public static function removeDumpFile($v = '1.0', $compress = false)
	{
	    $file = TEMP_DIR . '/initvo.' . $v . '.cache';
	    if ($compress) {
	        $file .= '.zip';
	    }
	    if (is_file($file)) {
            $rst = @unlink($file);
	    }
	    return $rst;
	}

	public static function dump($v = '1.0', $compress = false)
	{
		$resultInitVo = self::getInitVo();
		$file = TEMP_DIR . '/initvo.' . $v . '.cache';
		$data = json_encode($resultInitVo);
		if ($compress) {
			$data = gzcompress($data, 9);
			$file .= '.zip';
		}

		file_put_contents($file, $data);
		return $data;
	}

	public static function restore($v = '1.0')
	{
		$file = TEMP_DIR . '/initvo.' . $v . '.cache';
		if (is_file($file)) {
			return file_get_contents($file);
		} else {
			return self::dump($v);
		}
	}

	public static function restoreCompress($v = '1.0')
	{
		$file = TEMP_DIR . '/initvo.' . $v . '.cache.zip';
		if (is_file($file)) {
			return file_get_contents($file);
		} else {
			return self::dump($v, true);
		}
	}

	public static function getInitVo()
	{
        $resultInitVo = array();
        $resultInitVo['decorClass'] = self::getBuildingList();
        $resultInitVo['levelInfos'] = self::getUserLevelList();
        $resultInitVo['magicClass'] = self::getMagicStudyList();
        $resultInitVo['guideClass'] = self::getTutorialList();
        $resultInitVo['enemyClass'] = self::getMonsterList();
        $resultInitVo['mixMagicClass'] = self::getMagicMixList();
        $resultInitVo['transMagicClass'] = self::getMagicTransList();
        $resultInitVo['itemClass'] = self::getItemList();
        $taskClass1 = self::getTaskTrunkList();
        $taskClass2 = self::getTaskBranchList();
        $taskClass3 = self::getTaskDailyList();
        $taskClass4 = self::getTaskTutorialList();
        $taskClass5 = self::getMapTaskList();
        $resultInitVo['taskClass'] = array_merge($taskClass1, $taskClass2, $taskClass3, $taskClass4, $taskClass5);

        $resultInitVo['avatarClass'] = self::getAvatarList();
        $resultInitVo['npcClass'] = self::getNpcList();
        //$resultInitVo['sceneClass'] = self::getSceneList();
        $resultInitVo['sceneClass'] = self::getMapAllList();
        $resultInitVo['roomSizeClass'] = self::getSceneSizeList();
        $resultInitVo['roomLevelClass'] = self::getHouseLevelList();
        $resultInitVo['studentLevelClass'] = self::getStudentLevelList();
        $resultInitVo['studentClass'] = self::getStudentList();
        $resultInitVo['taskTips'] = self::getTaskTypeList();
        $resultInitVo['feedClass'] = self::getActivityList();

        $resultInitVo['portalClass'] = self::getMapPortalList();
        $resultInitVo['monsterClass'] = self::getMapMonsterList();
        $resultInitVo['mineClass'] = self::getMapMineList();
        $resultInitVo['dungeonDecorClass'] = self::getMapDecorList();
        //$resultInitVo['dungeonSceneClass'] = self::getMapCopyList();
        $resultInitVo['actScriptClass'] = self::getMapAnimationList();
        return $resultInitVo;
	}

	public static function getBuildingList()
	{
		$info = array();
		$data = Hapyfish2_Magic_Cache_BasicInfo::getBuildingList();
		foreach ($data as $item) {
			$info[] = array(
				'd_id' => $item['id'],
				'name' => $item['name'],
				'type' => $item['type'],
				'type_show' => '',
				'class_name' => $item['class_name'],
				'magic_type' => '',
				'size_x' => $item['size_x'],
				'size_y' => $item['size_y'],
				'size_z' => $item['size_z'],
				'door_refresh_time' => $item['door_cooldown'],
				'door_guest_limit' => $item['door_guest_limit'],
				'max_magic' => $item['effect_mp']
			);
		}

		return $info;
	}

	public static function getUserLevelList()
	{
		$info = array();
		$data = Hapyfish2_Magic_Cache_BasicInfo::getUserLevelList();
		foreach ($data as $item) {
			$info[] = array(
				'level' => $item['level'],
				'max_exp' => $item['exp'],
				'desk_limit' => $item['limit_desk'],
				'student_limit' => $item['limit_student'],
				'magic_limit' => $item['max_mp'],
				'tile_x_length' => $item['tile_size'],
				'tile_z_length' => $item['tile_size'],
				'gem' => $item['levelup_gmoney'],
				'coin' => $item['coin'],
				'items' => json_decode($item['levelup_item'], true),
				'decors' => json_decode($item['levelup_decors'], true)
			);
		}

		return $info;
	}

	public static function getMagicStudyList()
	{
		$info = array();
		$data = Hapyfish2_Magic_Cache_BasicInfo::getMagicStudyList();
		foreach ($data as $item) {
			$info[] = array(
				'magic_id' => $item['id'],
				'name' => $item['name'],
				'magic_type' => $item['type'],
				'class_name' => $item['class_name1'],
				'actMovie' => $item['class_name2'],
				'mp' => $item['need_mp'],
				'exp' => $item['gain_exp'],
				'time' => $item['spend_time'],
				'need_level' => $item['level'],
				'coin' => $item['gain_coin'],
				'learn_coin' => $item['coin'],
				'learn_gem' => $item['gold'],
				'content' => $item['content']
			);
		}

		return $info;
	}

	public static function getTutorialList()
	{
		$info = array();
		$data = Hapyfish2_Magic_Cache_BasicInfo::getTutorialList();
		foreach ($data as $item) {
			$info[] = array(
				'gid' => $item['id'],
				'name' => $item['name'],
				'icon' => $item['icon'],
				'index' => $item['index'],
				'eventType' => $item['event_type'],
				'chats' => $item['chats'],
				'actTips' => $item['act_tips'],
				'contact' => $item['contact'],
				'contactevent' => $item['contactevent']
			);
		}

		return $info;
	}

	public static function getMonsterList()
	{
		$info = array();
		$data = Hapyfish2_Magic_Cache_BasicInfo::getMonsterList();
		foreach ($data as $item) {
			$info[] = array(
				'enemyCid' => $item['id'],
				'name' => $item['name'],
				'avatarId' => $item['avatar_id'],
				'hp' => $item['hp'],
				'heal' => $item['heal']
			);
		}

		return $info;
	}

	public static function getMagicMixList()
	{
		$info = array();
		$data = Hapyfish2_Magic_Cache_BasicInfo::getMagicMixList();
		foreach ($data as $item) {
			$info[] = array(
				'mix_mid' => $item['id'],
				'name' => $item['name'],
				'type' => $item['type'],
				'mixType' => ($item['type'] == 8 ? 2 : 1),//装饰类1 ，道具类2
				'd_id' => $item['building'],
				'coin' => $item['coin'],
				'decorId' => json_decode($item['need_building'], true),
				'itemId' => json_decode($item['need_item'], true),
				'needLevel' => $item['level'],
				'class_name' => $item['class_name'],
				'gem' => $item['gold']
			);
		}

		return $info;
	}

	public static function getMagicTransList()
	{
		$info = array();
		$data = Hapyfish2_Magic_Cache_BasicInfo::getMagicTransList();
		foreach ($data as $item) {
			$info[] = array(
				'trans_mid' => $item['id'],
				'name' => $item['name'],
				'class_name' => $item['class_name'],
				'mp' => $item['need_mp'],
				'needLevel' => $item['level'],
				'itemId' => json_decode($item['gain_items'], true),
				'coin' => $item['coin'],
				'exp' => $item['gain_exp'],
				'gem' => $item['gold'],
				'content' => $item['content'],
				'time' => $item['magic_time']
			);
		}

		return $info;
	}

	public static function getItemList()
	{
		$info = array();
		$data = Hapyfish2_Magic_Cache_BasicInfo::getItemList();
		foreach ($data as $item) {
			$info[] = array(
				'i_id' => $item['id'],
				'name' => $item['name'],
				'content' => $item['content'],
				'type' => $item['type'],
				'add_mp' => $item['add_mp'],
				'gem' => $item['gold'],
				'coin' => $item['coin'],
				'class_name' => $item['class_name'],
				'sale' => (int)$item['canbuy']
			);
		}

		return $info;
	}

	private static function _getTaskAwards(&$eventItem)
	{
		//符合前端格式化
		$awards = array();

		if (!empty($eventItem['award_items'])) {
			$t = json_decode($eventItem['award_items']);
			foreach ($t as $value) {
				$awards[] = array(
					'type' => 1,
					'id' => $value[0],
					'num' => $value[1]
				);
			}
		}

		if (!empty($eventItem['award_prop'])) {
			$t = json_decode($eventItem['award_prop']);
			$award = array();
			foreach ($t as $key => $value) {
				$awards[] = array(
					'type' => 3,
					'id' => $key,
					'num' => $value
				);
			}
		}

		if (!empty($eventItem['award_decors'])) {
			$t = json_decode($eventItem['award_decors']);
			$award = array();
			foreach ($t as $value) {
				$awards[] = array(
					'type' => 2,
					'id' => $value[0],
					'num' => $value[1]
				);
			}
		}

		return $awards;
	}

	public static function getTaskTrunkList()
	{
		$info = array();
		$data = Hapyfish2_Magic_Cache_BasicInfo::getTaskTrunkList();
		foreach ($data as $item) {
			$info[] = array(
				't_id' => $item['id'],
				'index' => '',
				'type' => 1,
				'taskType' => $item['type'],
				'name' => $item['name'],
				'content' => $item['intro'],
				'icon_class' => $item['icon_class'],
				'sceneId' => array($item['start_scene_id']),
				'npcId' => $item['start_npc_id'],
				'finishNpcId' => $item['finish_npc_id'],
				'finishSceneId' => array($item['finish_scene_id']),
				'quest_str' => $item['condition_intro'] . '<!1@>/' . $item['num'],
				'finish_condition' => json_decode($item['icon_condition'],true),
				'awards' => self::_getTaskAwards($item)
			);
		}

		return $info;
	}

	public static function getTaskBranchList()
	{
		$info = array();
		$data = Hapyfish2_Magic_Cache_BasicInfo::getTaskBranchList();
		foreach ($data as $item) {
			$info[] = array(
				't_id' => $item['id'],
				'index' => '',
				'type' => 2,
				'taskType' => $item['type'],
				'name' => $item['name'],
				'content' => $item['intro'],
				'icon_class' => $item['icon_class'],
				'sceneId' => array($item['start_scene_id']),
				'npcId' => $item['start_npc_id'],
				'finishNpcId' => $item['finish_npc_id'],
				'finishSceneId' => array($item['finish_scene_id']),
				'quest_str' => $item['condition_intro'] . '<!1@>/' . $item['num'],
				'finish_condition' => json_decode($item['icon_condition'],true),
				'awards' => self::_getTaskAwards($item)
			);
		}

		return $info;
	}

	public static function getTaskDailyList()
	{
		$info = array();
		$data = Hapyfish2_Magic_Cache_BasicInfo::getTaskDailyList();
		foreach ($data as $item) {
			$info[] = array(
				't_id' => $item['id'],
				'index' => '',
				'type' => 3,
				'taskType' => $item['type'],
				'name' => $item['name'],
				'content' => $item['intro'],
				'icon_class' => $item['icon_class'],
				'sceneId' => array(HOME_SCENE_ID),
				'npcId' => '',
				'finishNpcId' => '',
				'finishSceneId' => array(HOME_SCENE_ID),
				'quest_str' => $item['condition_intro'] . '<!1@>/' . $item['num'],
				'finish_condition' => json_decode($item['icon_condition'],true),
				'awards' => self::_getTaskAwards($item)
			);
		}

		return $info;
	}

	public static function getTaskTutorialList()
	{
		$info = array();
		$data = Hapyfish2_Magic_Cache_BasicInfo::getTaskTutorialList();
		foreach ($data as $item) {
			$info[] = array(
				't_id' => $item['id'],
				'index' => '',
				'type' => 0,
				'taskType' => $item['type'],
				'name' => $item['name'],
				'content' => $item['intro'],
				'icon_class' => $item['icon_class'],
				'sceneId' => array($item['start_scene_id']),
				'npcId' => $item['start_npc_id'],
				'finishNpcId' => $item['finish_npc_id'],
				'finishSceneId' => array($item['finish_scene_id']),
				'quest_str' => $item['condition_intro'] . '<!1@>/' . $item['num'],
				'finish_condition' => json_decode($item['icon_condition'],true),
				'awards' => self::_getTaskAwards($item)
			);
		}

		return $info;
	}

	public static function getAvatarList()
	{
		$info = array();
		$data = Hapyfish2_Magic_Cache_BasicInfo::getAvatarList();
		foreach ($data as $item) {
			$info[] = array(
				'avatarId' => $item['id'],
				'name' => $item['name'],
				'className' => $item['classname'],
				'type' => $item['type']
			);
		}

		return $info;
	}

	public static function getNpcList()
	{
		$info = array();
		$data = Hapyfish2_Magic_Cache_BasicInfo::getNpcList();
		foreach ($data as $item) {
			$info[] = array(
				'sceneId' => $item['scene_id'],
				'npcId' => $item['id'],
				'avatarId' => $item['avatar_id'],
				'name' => $item['name'],
				'x' => $item['x'],
				'y' => $item['y'],
				'z' => $item['z'],
				'clickType' => $item['click_type'],
				'clickValue' => $item['click_value'],
				'chats' => $item['talks'],
				'shop' => $item['shop'],
				'faceX' => $item['face_x'],
				'faceY' => $item['face_y']
			);
		}

		return $info;
	}

	public static function getSceneSizeList()
	{
		$info = array();
		$data = Hapyfish2_Magic_Cache_BasicInfo::getSceneSizeList();
		foreach ($data as $item) {
			$info[] = array(
				'id' => $item['id'],
				'sizeX' => $item['size'],
				'sizeZ' => $item['size'],
				'coin' => $item['coin'],
				'gem' => $item['gold'],
				'needLevel' => $item['level'],
				'needFriendNum' => $item['friend_num']
			);
		}

		return $info;
	}

	public static function getHouseLevelList()
	{
		$info = array();
		$data = Hapyfish2_Magic_Cache_BasicInfo::getHouseLevelList();
		foreach ($data as $item) {
			$info[] = array(
				'level' => $item['id'],
				'needMaxMp' => $item['mp'],
				'student_limit' => $item['student_limit'],
				'desk_limit' => $item['desk_limit'],
				'coin' => $item['coin'],
				'gem' => $item['gold'],
				'items' => json_decode($item['items'],true),
				'decors' => json_decode($item['decors'],true)
			);
		}

		return $info;
	}

	public static function getStudentLevelList()
	{
		$info = array();
		$data = Hapyfish2_Magic_Cache_BasicInfo::getStudentLevelList();
		foreach ($data as $item) {
			$info[] = array(
				'level' => $item['id'],
				'exp' => $item['exp']
			);
		}

		return $info;
	}

	public static function getStudentList()
	{
		$info = array();
		$data = Hapyfish2_Magic_Cache_BasicInfo::getStudentList();
		foreach ($data as $item) {
			$info[] = array(
				'sid' => $item['id'],
				'avatar_id' => $item['avatar_id'],
				'unLockMp' => $item['unlock_mp'],
				'content' => $item['content']
			);
		}

		return $info;
	}

	public static function getTaskTypeList()
	{
		$info = array();
		$data = Hapyfish2_Magic_Cache_BasicInfo::getTaskTypeList();
		foreach ($data as $item) {
			$info[] = array(
				'taskType' => $item['id'],
				'content' => $item['content']
			);
		}

		return $info;
	}

    public static function getActivityList()
	{
		$info = array();
		$data = Hapyfish2_Magic_Cache_BasicInfo::getActivityList();
		foreach ($data as $item) {
		    unset($item['awards']);
			$info[] = array(
				'id' => $item['id'],
				'value' => json_encode($item)
			);
		}

		return $info;
	}


    public static function getCharacterList()
	{
		$info = array();
		$data = Hapyfish2_Magic_Cache_BasicInfo::getCharacterList();
		foreach ($data as $item) {
			$info[] = array(
				'avatarId' => $item['id'],
				'className' => $item['classname'],
				'name' => $item['name'],
				'type' => $item['price_type'],
				'num' => $item['price']
			);
		}

		return $info;
	}

    /* map basic */
    public static function getMapAllList()
	{
		$info = array();
		$data1 = Hapyfish2_Magic_Cache_BasicInfo::getMapCopyList();
		$data2 = Hapyfish2_Magic_Cache_BasicInfo::getMapSceneList();
		$data = $data1 + $data2;
		foreach ($data as $item) {
			$info[] = array(
				'sceneId' => $item['id'],
				'type' => $item['type'],
				'name' => $item['name'],
				'content' => $item['content'],
				'needLevel' => $item['need_level'],
				'needs1' => json_decode($item['condition1'], true),
				'needs2' => json_decode($item['condition2'], true),
				'mp' => $item['mp'],
				'className' => $item['icon_classname'],
				'bg' => $item['bg'],
				'x' => $item['x'],
				'y' => $item['y'],
				'bgSound' => $item['bgsound'],
				//'enemy_xy' => json_decode($item['monster_xy'], true),
				'nodeStr' => $item['node_str'],
				//'entrances' => json_decode($item['entrances'], true),
				'numCols' => $item['size_x'],
				'numRows' => $item['size_y'],
				'isoStartX' => $item['isostart_x'],
				'isoStartZ' => $item['isostart_y'],
				'parentSceneId' => $item['parent_scene_id'],
				'entrances' => json_decode($item['default_pos'], true)
			);
		}

		return $info;
	}

    public static function getMapPortalList()
	{
		$info = array();
		$data = Hapyfish2_Magic_Cache_BasicInfo::getMapBuildingList();
		foreach ($data as $item) {
		    if ($item['type'] == 12) {
    			$info[] = array(
    				'd_id' => $item['id'],
    				'class_name' => $item['class_name'],
    				'name' => $item['name'],
    				'level' => $item['level'],
    				'size_x' => $item['size_x'],
    				'size_y' => $item['size_y'],
    				'size_z' => $item['size_z'],
    				'type' => $item['type']
    			);
		    }
		}

		return $info;
	}

    public static function getMapDecorList()
	{
		$info = array();
		$data = Hapyfish2_Magic_Cache_BasicInfo::getMapBuildingList();
		foreach ($data as $item) {
		    if ($item['type'] != 12) {
    			$info[] = array(
    				'd_id' => $item['id'],
    				'class_name' => $item['class_name'],
    				'name' => $item['name'],
    				'level' => $item['level'],
    				'size_x' => $item['size_x'],
    				'size_y' => $item['size_y'],
    				'size_z' => $item['size_z'],
    				'type' => $item['type']
    			);
		    }
		}

		return $info;
	}

    public static function getMapMonsterList()
	{
		$info = array();
		$data = Hapyfish2_Magic_Cache_BasicInfo::getMapMonsterList();
		foreach ($data as $item) {
		    if ($item['type'] == 1) {
    			$info[] = array(
    				'cid' => $item['id'],
    				'name' => $item['name'],
    				'avatarId' => $item['avatar_id'],
    				'maxHp' => $item['hp'],
    				'size_x' => $item['size_x'],
    				'size_z' => $item['size_z'],
    				'conditions' => json_decode($item['need_conditions'], true)
    			);
		    }
		}

		return $info;
	}

    public static function getMapMineList()
	{
		$info = array();
		$data = Hapyfish2_Magic_Cache_BasicInfo::getMapMonsterList();
		foreach ($data as $item) {
		    if ($item['type'] == 2) {
    			$info[] = array(
    				'cid' => $item['id'],
    				'name' => $item['name'],
    				'avatarId' => $item['avatar_id'],
    				'maxHp' => $item['hp'],
    				'size_x' => $item['size_x'],
    				'size_z' => $item['size_z'],
    				'conditions' => json_decode($item['need_conditions'], true)
    			);
		    }
		}

		return $info;
	}

    public static function getMapAnimationList()
	{
		$info = array();
		$data = Hapyfish2_Magic_Cache_BasicInfo::getMapAnimationList();
		foreach ($data as $item) {
			$info[] = array(
				//'id' => $item['id'],
				'cid' => $item['cid'],
				'type' => $item['type'],
				'label' => $item['label'],
				'time' => $item['ptime'],
				'coverLabel' => $item['cover_label'],
				'coverDelay' => $item['cover_delay'],
				'coverTimes' => $item['cover_times']
			);
		}

		return $info;
	}

    public static function getMapTaskList()
	{
		$info = array();
		$data = Hapyfish2_Magic_Cache_BasicInfo::getMapTaskList();
		foreach ($data as $item) {
			$info[] = array(
				't_id' => $item['id'],
				'index' => '',
				'type' => 4,
				'taskType' => $item['type'],
				'name' => $item['name'],
				'content' => $item['intro'],
				'icon_class' => $item['icon_class'],
				'sceneId' => json_decode($item['start_map_id'], true),
				'npcId' => $item['start_npc_id'],
				'finishNpcId' => $item['finish_npc_id'],
				'finishSceneId' => json_decode($item['finish_map_id'], true),
				'quest_str' => $item['condition_intro'] . '<!1@>/' . $item['num'],
				'finish_condition' => json_decode($item['icon_condition'], true),
				'awards' => json_decode($item['award_conditions'], true)
			);
		}

		return $info;
	}
	/* -map basic */

	/* map copy related transcritp */
    public static function getMapCopyTranscriptList()
	{
		$info = array();
		//$data = Hapyfish2_Magic_Cache_BasicInfo::getMapCopyTranscriptList();

		return $info;
	}

	/* -map copy related transcritp */

}