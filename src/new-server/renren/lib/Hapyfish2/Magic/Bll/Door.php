<?php

class Hapyfish2_Magic_Bll_Door
{
	public static function randofArray(&$arr)
	{
		$key = array_rand($arr);
		return $arr[$key];
	}

	public static function open($uid, $doorId)
	{
    	$result = array();
		$t = time();
		//取出此门信息
    	$door = Hapyfish2_Magic_HFC_Door::getOne($uid, $doorId);

		if (empty($door)) {
			return Hapyfish2_Magic_Bll_UserResult::Error('door_id_error');
		}

		if ($door['end_time'] > $t) {
			return Hapyfish2_Magic_Bll_UserResult::Error('door_endtime_error');
		}

		$left_student_num = $door['left_student_num'];
		if ($left_student_num <= 0) {
			return Hapyfish2_Magic_Bll_UserResult::Error('door_left_student_num_error');
		}

		$userLevelInfo = Hapyfish2_Magic_HFC_User::getUserLevel($uid);
		if (!$userLevelInfo) {
			return Hapyfish2_Magic_Bll_UserResult::Error('user_levelinfo_error');
		}

		$houseLevel = $userLevelInfo['house_level'];
		$houseBasicInfo = Hapyfish2_Magic_Cache_BasicInfo::getHouseLevelInfo($houseLevel);
		$student_limit = $houseBasicInfo['student_limit'];

		$studentInfo = Hapyfish2_Magic_Bll_Student::getAllInfo($uid);
		$curStudentNum = count($studentInfo['student1']);
		$acgStudentNum = count($studentInfo['student0']);

		if ($curStudentNum >= $student_limit) {
info_log($uid.'|'.$door.'|'.$student_limit, 'opendoorstdlimit');
info_log('curStd:'.json_encode($studentInfo['student1']), 'opendoorstdlimit');
info_log('acgStd:'.json_encode($studentInfo['student0']), 'opendoorstdlimit');
			return Hapyfish2_Magic_Bll_UserResult::Error('student_limit');
		}

		if ($acgStudentNum == 0) {
			return Hapyfish2_Magic_Bll_UserResult::Error('no_empty_student');
		}

		$outNum = min($acgStudentNum, $left_student_num);
		$stdIdList = array();
		$acgList = $studentInfo['student0'];
		$magicList = Hapyfish2_Magic_Cache_Magic::getList($uid, true);
		$studyMagicIds = $magicList['study_ids'];
		if($outNum < $acgStudentNum) {
			//随机取学生
			if ($outNum == 1) {
				$key = array_rand($acgList);
				$mid = self::randofArray($studyMagicIds);
				$stdIdList[] = array($acgList[$key]['sid'], $mid);
			} else {
				$keys = array_rand($acgList, $outNum);
				foreach ($keys as $v) {
					$mid = self::randofArray($studyMagicIds);
					$stdIdList[] = array($acgList[$v]['sid'], $mid);
				}
			}
		} else {
			//全部空闲
			foreach ($acgList as $v) {
				$mid = self::randofArray($studyMagicIds);
				$stdIdList[] = array($v['sid'], $mid);
			}
		}

		//当前场景中的桌子
		$deskList = Hapyfish2_Magic_HFC_Desk::getInScene($uid);
		//空闲桌子
		$emptyDesk = array();
		$emptyDeskCount = 0;
		if ($deskList['desks']) {
    		foreach ($deskList['desks'] as $desk) {
    			if ($desk['student_id'] == 0 || ($desk['student_id'] > 0 && $desk['magic_id'] > 0 && $desk['coin'] <= 0)) {
    				$emptyDesk[] = $desk;
    				$emptyDeskCount++;
    			}
    		}
		}

		//开始放人
		//到桌子上
		$outStudents = array();
		$deskStudentNum = min($outNum, $emptyDeskCount);
		$deskIndex = 0;

		for($i = 0; $i < $deskStudentNum; $i++) {
			$sid = $stdIdList[$i][0];
			$mid = $stdIdList[$i][1];
			$desk = $emptyDesk[$i];

			//修改学生状态
			$std = Hapyfish2_Magic_HFC_Student::getOne($uid, $sid);
			$std['state'] = 1; //NOTEACH
			$std['desk_id'] = $desk['id'];
			$std['start_time'] = 0;
			$std['end_time'] = 0;
			$std['spend_time'] = 0;
			$std['event'] = 0;
			$std['event_time'] = 0;
			$std['magic_id'] = $mid;
			$std['coin'] = 0;
			$std['stone_time'] = 0;
if ($desk['id'] == 0) {
    info_log('Hapyfish2_Magic_Bll_Door::open:goemptydesk:'.json_encode($desk), 'testfor1');
    info_log('Hapyfish2_Magic_Bll_Door::open:emptydesks:'.json_encode($emptyDesk), 'testfor1');
}
			Hapyfish2_Magic_HFC_Student::updateOne($uid, $sid, $std);

			//修改对应桌子状态
			$desk['student_id'] = $sid;
			$desk['magic_id'] = 0;
			$desk['coin'] = 0;
			$desk['end_time'] = 0;
			$desk['stone_time'] = 0;
			Hapyfish2_Magic_HFC_Desk::updateOne($uid, $desk['id'], $desk);

			$outNum--;
			$deskIndex++;

			$outStudents[] = array(
				'sid' => $sid,
				'decor_id' => $std['desk_id'],
				'state' => $std['state'],
				'time' => 0,
				'magic_id' => $std['magic_id'],
				'event_time' => -1,
				'coin' => 0,
				'stone_time' => 0,
				'can_steal' => 0
			);
		}

		//有闲逛的学生
		if ($outNum > 0) {
			for($j = 0; $j < $outNum; $j++) {
				$sid = $stdIdList[$deskIndex + $j][0];
				$mid = $stdIdList[$deskIndex + $j][1];
				//修改学生状态
				$std = Hapyfish2_Magic_HFC_Student::getOne($uid, $sid);
				$std['state'] = 0; //FIDDLE
				$std['desk_id'] = 0;
    			$std['start_time'] = 0;
    			$std['end_time'] = 0;
    			$std['spend_time'] = 0;
    			$std['event'] = 0;
    			$std['event_time'] = 0;
    			$std['magic_id'] = $mid;
    			$std['coin'] = 0;
    			$std['stone_time'] = 0;
if ($std['desk_id'] == 0) {
    info_log('Hapyfish2_Magic_Bll_Door::open:stdgoinroom:'.json_encode($std), 'testfor1');
}
				Hapyfish2_Magic_HFC_Student::updateOne($uid, $sid, $std);

				$outStudents[] = array(
					'sid' => $sid,
					'decor_id' => $std['desk_id'],
					'state' => $std['state'],
					'time' => 0,
					'magic_id' => $std['magic_id'],
					'event_time' => -1,
					'coin' => 0,
					'stone_time' => 0,
					'can_steal' => 0
				);
			}
		}

		Hapyfish2_Magic_Bll_UserResult::addField($uid, 'students', $outStudents);

		if ($left_student_num > $outNum) {
			$door['left_student_num'] -= $outNum;
		} else {
			$door['start_time'] = $t;
			$doorBasicInfo = Hapyfish2_Magic_Cache_BasicInfo::getBuildingInfo($door['cid']);
			if ($doorBasicInfo) {
				$door['left_student_num'] = $doorBasicInfo['door_guest_limit'];
				$door['enf_time'] = $t + $doorBasicInfo['door_cooldown']/SPEED_BASE/SPEED_DOOR_TIME;
			} else {
				$door['left_student_num'] = 0;
				$door['enf_time'] = $t;
			}
		}
		//更新门状态
		Hapyfish2_Magic_HFC_Door::updateOne($uid, $door['id'], $door);

		return Hapyfish2_Magic_Bll_UserResult::all();
	}

}