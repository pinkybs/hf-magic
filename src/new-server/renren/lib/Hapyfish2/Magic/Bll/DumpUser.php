<?php

class Hapyfish2_Magic_Bll_DumpUser
{
	public static function restore($uid)
	{
		$file = TEMP_DIR . '/dump.' . $uid . '.cache';
		if (is_file($file)) {
			return file_get_contents($file);
		} else {
			return self::dump($uid);
		}
	}

	public static function dump($uid, $asGM = false)
	{
		$scene = self::get($uid, $asGM);
		$result = array('scene' => $scene);
		$file = TEMP_DIR . '/dump.' . $uid . '.cache';
		$data = json_encode($result);
		file_put_contents($file, $data);
		return $data;
	}


	public static function get($uid, $asGM = false)
    {
        $isHome = true;

		$buildingData = Hapyfish2_Magic_HFC_Building::getInScene($uid);
		$doorData = Hapyfish2_Magic_HFC_Door::getInScene($uid);
		$deskData = Hapyfish2_Magic_HFC_Desk::getInScene($uid);
		$t = time();
		$decorList = array();
		if (!empty($buildingData)) {
			foreach ($buildingData as $b) {
				$decorList[] = array(
					'id' => $b['id'],
					'x' => $b['x'],
					'y' => $b['y'],
					'z' => $b['z'],
					'mirror' => $b['mirro'],
					'bag_type' => ($b['status'] == 0 ? 1 : 0),
					'd_id' => $b['cid'],
					'type' => $b['item_type']
				);
			}
		}
		if (!empty($doorData)) {
			foreach ($doorData['doors'] as $d) {
				$decorList[] = array(
					'id' => $d['id'],
					'x' => $d['x'],
					'y' => $d['y'],
					'z' => $d['z'],
					'mirror' => $d['mirro'],
					'bag_type' => ($d['status'] == 0 ? 1 : 0),
					'd_id' => $d['cid'],
					'type' => $d['item_type'],
					'door_left_students_num' => $d['left_student_num'],
					'door_left_time' => $d['end_time'] - $t
				);
			}
		}

		if (!empty($deskData)) {
			foreach ($deskData['desks'] as $d) {
				$decorList[] = array(
					'id' => $d['id'],
					'x' => $d['x'],
					'y' => $d['y'],
					'z' => $d['z'],
					'mirror' => $d['mirro'],
					'bag_type' => ($d['status'] == 0 ? 1 : 0),
					'd_id' => $d['cid'],
					'type' => $d['item_type']
				);
			}
		}

		$floorList = Hapyfish2_Magic_Cache_Floor::getInScene($uid);
		$wallList = Hapyfish2_Magic_Cache_Wall::getInScene($uid);

		//学生
		$studentList = array();

		//解锁学生信息
		$studentStateList = Hapyfish2_Magic_Bll_Student::getStudentStateList($uid);

		//get user info
        $userVo = Hapyfish2_Magic_Bll_User::getUserInit($uid);
        if ($asGM) {
        	$userVo['uid'] = GM_UID_LELE;
        	$userVo['name'] = GM_NAME_LELE;
        	$userVo['face'] = STATIC_HOST . '/img/magic/' . GM_FACE_LELE;
        	$userVo['currentSceneId'] = HOME_SCENE_ID;
        	$userVo['trans_mid'] = 0;
        	$userVo['isfans'] = true;
        	$userVo['avatar'] = GM_AVATAR_LELE;
        }

		//怪物
		$monsterList = array();

		$scene = array(
			'decorList' => $decorList,
			'studentStates' => $studentStateList,
			'floorList' => $floorList,
			'wallList' => $wallList,
			'students' => $studentList,
			'user' => $userVo,
			//'enemys' => $monsterList,
			'mineList' => array(),
			'monsterList' => array(),
			'portalList' => array()
		);

		return $scene;
    }

}