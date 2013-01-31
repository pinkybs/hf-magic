<?php

class Hapyfish2_Magic_Bll_Student
{
	//CONST FIDDLE = 0;//闲逛
	//CONST NOTEACH = 1;//站在桌子上
	//CONST STUDYING = 2;//学习
	//CONST TEACHOVER = 3;//教学完成
	//CONST INTERRUPT = 4;//中断

	public static function checkState($uid, $sid, &$studentInfo, $t = null)
	{
		if ($studentInfo['state'] != 2) {
			return false;
		}

		if ($t == null) {
			$t = time();
		}

		$state = $studentInfo['state'];

		if ($studentInfo['event'] == 0 && $studentInfo['end_time'] != 0 && $studentInfo['end_time'] <= $t) {
			//TEACHOVER
			$state = 3;
		}

		if ($studentInfo['event'] > 0 && $studentInfo['event_time'] < $t) {
			//INTERRUPT
			$state = 4;
		}

		if ($state != $studentInfo['state']) {
			//update
			$studentInfo['state'] = $state;
			Hapyfish2_Magic_HFC_Student::updateOne($uid, $sid, $studentInfo);

			return true;
		}

		return false;
	}

	public static function updateOneFiddle($uid, &$desk)
	{
		$students = Hapyfish2_Magic_HFC_Student::getAll($uid);
		foreach ($students as &$std) {
			//FIDDLE
			if ($std['state'] == 0) {
				$std['state'] = 1;
				$std['desk_id'] = $desk['id'];
if ($std['desk_id'] == 0) {
    info_log('Hapyfish2_Magic_Bll_Student::updateOneFiddle:'.json_encode($std), 'testfor1');
}
				Hapyfish2_Magic_HFC_Student::updateOne($uid, $std['sid'], $std);

				//
				$desk['student_id'] = $std['sid'];
				Hapyfish2_Magic_HFC_Desk::updateOne($uid, $desk['id'], $desk);
				return $std;
			}
		}

		return null;
	}

	public static function getInScene($uid)
	{
		$studentList = array();
		$students = Hapyfish2_Magic_HFC_Student::getAll($uid);
		if (empty($students)) {
			return $studentList;
		}

		$t = time();
		$deskList = array();
		foreach ($students as &$student) {
			if ($student['state'] >= 0 && $student['state'] != 3) {
				self::checkState($uid, $student['sid'], $student, $t);
				if ($student['state'] != 3) {
				    if ($student['state'] != 0) {
    				    if (isset($deskList[$student['desk_id']])) {
    				        info_log($uid.' repair 2 student in 1 desk:'.json_encode($student), 'repairstudent');
    						$student['state'] = 3;
    						$student['desk_id'] = 0;
    						Hapyfish2_Magic_HFC_Student::updateOne($uid, $student['sid'], $student);
    						continue;
    					}
    					$deskList[$student['desk_id']] = 1;
				    }
					$std = array(
						'sid' => $student['sid'],
						'decor_id' => $student['desk_id'],
						'state' => $student['state'],
						'magic_id' => $student['magic_id'],
						'coin' => $student['coin'],
						'can_steal' => 1
					);
					if ($student['event'] > 0) {
						$std['event_time'] = $student['event_time'] - $t;
						if ($std['event_time'] < 0) {
							$std['event_time'] = 0;
						}
						//剩余收钱时间 必须大于发生异常状态时间
						$std['time'] = $student['spend_time'] + $std['event_time'];
					} else {
						$std['time'] = $student['end_time'] - $t;
						$std['event_time'] = -1;
					}
					$std['stone_time'] = $student['stone_time'] - $t;
					if ($std['time'] < 0) {
						$std['time'] = 0;
					}
					if ($std['stone_time'] < 0) {
						$std['stone_time'] = 0;
					}

					$studentList[] = $std;
				}
			}
		}

		return $studentList;
	}

	public static function getAllInfo($uid)
	{
		$students = Hapyfish2_Magic_HFC_Student::getAll($uid);
		$t = time();
		$student0 = array();
		$student1 = array();
		foreach ($students as &$student) {
			if ($student['state'] >= 0) {
				self::checkState($uid, $student['sid'], $student, $t);
				//学习完成也算空闲
				if ($student['state'] == 3) {
					$student0[] = array(
						'sid' => $student['sid']
					);
					continue;
				}

				$std = array(
					'sid' => $student['sid'],
					'decor_id' => $student['sid'],
					'state' => $student['state'],
					'magic_id' => $student['magic_id'],
					'coin' => $student['coin'],
				);
				if ($student['event'] > 0) {
					$std['time'] = $student['spend_time'] - ($student['event_time'] - $student['start_time']);
					$std['event_time'] = $student['event_time'] - $t;
					if ($std['event_time'] < 0) {
						$std['event_time'] = 0;
					}
				} else {
					$std['time'] = $student['end_time'] - $t;
					$std['event_time'] = -1;
				}
				$std['stone_time'] = $student['stone_time'] - $t;
				if ($std['time'] < 0) {
					$std['time'] = 0;
				}
				if ($std['stone_time'] < 0) {
					$std['stone_time'] = 0;
				}

				$student1[] = $std;
			} else {
				$student0[] = array(
					'sid' => $student['sid']
				);
			}
		}

		return array('student0' => $student0, 'student1' => $student1);
	}

	public static function getStudentStateList($uid)
	{
		$students = Hapyfish2_Magic_HFC_Student::getAll($uid);
		$unlockList = array();
		foreach ($students as &$student) {
			$unlockList[] = array(
				'sid' => $student['sid'],
				'exp' => $student['exp'],
				'level' => $student['level'],
				'needAward' => $student['award_flg'],
				'unLock' => 1
			);
		}

		return $unlockList;
	}

	public static function unlockStudent($uid, $oldLevel, $newLevel)
	{
		$students = Hapyfish2_Magic_HFC_Student::getAll($uid);
		$studentInfoList = Hapyfish2_Magic_Cache_BasicInfo::getStudentList();
		foreach ($studentInfoList as $std) {
			//if ($std['unlock_level'] > $oldLevel && $std['unlock_level'] <= $newLevel) {
			if ($std['unlock_level'] <= $newLevel) {
				$sid = $std['id'];
				if (!isset($students[$sid])) {
					Hapyfish2_Magic_HFC_Student::addOne($uid, $sid);
				}
			}
		}
	}

	public static function studyMagic($uid, $sid)
	{
		$std = Hapyfish2_Magic_HFC_Student::getOne($uid, $sid);
		if (!$std) {
			return Hapyfish2_Magic_Bll_UserResult::Error('student_id_error');
		}

		$mid = $std['magic_id'];
		if ($mid == 0) {
			return Hapyfish2_Magic_Bll_UserResult::Error('magic_id_error');
		}

		//判断魔法值是否足够
		$magicInfo = Hapyfish2_Magic_Cache_BasicInfo::getMagicStudyInfo($mid);
		if (!$magicInfo) {
			return Hapyfish2_Magic_Bll_UserResult::Error('magic_info_error');
		}

		$userMpInfo = Hapyfish2_Magic_HFC_User::getUserMp($uid);
		if ($userMpInfo['mp'] < $magicInfo['need_mp']) {
			return Hapyfish2_Magic_Bll_UserResult::Error('mp_not_enough');
		}

		$t = time();

		//消耗魔法值
		//$userMpInfo['mp'] -= $magicInfo['need_mp'];
		//Hapyfish2_Magic_HFC_User::updateUserMp($uid, $userMpInfo);
		Hapyfish2_Magic_HFC_User::decUserMp($uid, $magicInfo['need_mp']);

		//奖励经验
		Hapyfish2_Magic_HFC_User::incUserExp($uid, $magicInfo['gain_exp']);

		//新手时间
		$isFirst = false;
		$helpInfo = Hapyfish2_Magic_Cache_UserHelp::getHelpInfo($uid);
		if ($helpInfo) {
			$isFirst = ($helpInfo['helpList'][2] == 0);
		}
		if ($isFirst === true) {
			$spend_time = SPEED_NEWBIE_STUDY_TIME;
		} else {
			$spend_time = floor($magicInfo['spend_time']/SPEED_BASE/SPEED_STUDY_TIME);
		}

		//是否有中断
		if (mt_rand(1, 100) < $magicInfo['abnormal_percent'] || $isFirst === true) {
			$state = 2; //STUDYING
			$event = 1;
			if ($isFirst === true) {
				$event_time = $t + round(mt_rand(1, $spend_time - 1));
			} else {
				$event_time = $t + mt_rand(5, $spend_time - 10);
			}
		} else {
			$state = 2;
			$event = 0;
			$event_time = -1;

			//
			$deskId = $std['desk_id'];
			$desk = Hapyfish2_Magic_HFC_Desk::getOne($uid, $deskId);
			if ($desk) {
				$desk['magic_id'] = $magicInfo['id'];
				$desk['coin'] = $magicInfo['gain_coin'];
				$desk['end_time'] = $t + $spend_time;
				$desk['stone_time'] = $t + $spend_time + floor(STONE_TIME/SPEED_BASE/SPEED_STONE_TIME);
				Hapyfish2_Magic_HFC_Desk::updateOne($uid, $deskId, $desk);
			}
		}
		$std['state'] = $state;
		$std['start_time'] = $t;
		$std['end_time'] = $t + $spend_time;
		$std['event'] = $event;
		$std['event_time'] = $event_time;
		$std['spend_time'] = $spend_time;
		$std['coin'] = $magicInfo['gain_coin'];
		$std['stone_time'] = $t + $spend_time + floor(STONE_TIME/SPEED_BASE/SPEED_STONE_TIME);

		$levelUp = self::updateExp($uid, $std);

		$changeStudent = array(
			'sid' => $sid,
			'decor_id' => $std['desk_id'],
			'state' => $std['state'],
			'time' => $std['spend_time'],
			'magic_id' => $std['magic_id'],
			'event_time' => $event > 0 ? ($std['event_time'] - $t) : -1,
			'coin' => $std['coin'],
			'stone_time' => STONE_TIME,//$std['stone_time'] - $t,
			'can_steal' => 1
		);
		Hapyfish2_Magic_Bll_UserResult::addField($uid, 'changeStudent', $changeStudent);

		$changeStudentState = array(
			'sid' => $sid,
			'exp' => $levelUp ? 0 : STUDENT_EXP, //这里是变化值
			'level' => $levelUp ? 1 : 0, //这里是变化值
			'needAward' => $std['award_flg'],
			'student_state' => $std['state']
		);
		Hapyfish2_Magic_Bll_UserResult::addField($uid, 'changeStudentState', $changeStudentState);

		//派发事件
		$event = array('uid' => $uid, 'student' => $std, 'magicInfo' => $magicInfo);
		Hapyfish2_Magic_Bll_Event::teachStudent($event);

		return Hapyfish2_Magic_Bll_UserResult::all();
	}

	public static function updateExp($uid, &$student)
	{
		$levelUp = false;
		$studentLevelInfo = Hapyfish2_Magic_Cache_BasicInfo::getStudentLevelInfo($student['level']);
		$oldExp = $student['exp'];
		if ($student['exp'] + STUDENT_EXP >= $studentLevelInfo['exp']) {
			// level up
			$student['level']++;
			$student['exp'] = 0;
			$student['award_flg'] = 1;
			$levelUp = true;
		} else {
			$student['exp'] += STUDENT_EXP;
			$student['award_flg'] = 0;
		}

		$ok = Hapyfish2_Magic_HFC_Student::updateOne($uid, $student['sid'], $student, $levelUp);
		if ($ok && $levelUp) {

		}

		return $levelUp;
	}

	public static function pickup($uid, $fid, $deskIds)
	{
		if ($uid != $fid) {
			$isFriend = Hapyfish2_Platform_Bll_Friend::isFriend($uid, $fid);
			if (!$isFriend) {
				return Hapyfish2_Magic_Bll_UserResult::Error('not_friend');
			}
		}

	    $count = count($deskIds);
		if ($count == 1) {
			$res = self::pickupOne($uid, $fid, $deskIds[0], true);
			$results = array($res['result']);
		} else {
    		foreach ($deskIds as $deskId) {
        		self::pickupOne($uid, $fid, $deskId, true);
        	}
        	$results = array(Hapyfish2_Magic_Bll_UserResult::result(true));
		}

    	$result = array('results' => $results);
    	Hapyfish2_Magic_Bll_UserResult::field($result);

    	if (!isset($result['changeStudents'])) {
    		$result['changeStudents'] = array();
    	}

    	return $result;
	}

	public static function pickupOne($uid, $fid, $deskId, $batch = false)
	{
		$desk = Hapyfish2_Magic_HFC_Desk::getOne($fid, $deskId);
		if (!$desk) {
			return Hapyfish2_Magic_Bll_UserResult::Error('desk_id_error');
		}

		$sid = $desk['student_id'];

		$std = Hapyfish2_Magic_HFC_Student::getOne($fid, $sid);
		if (!$std) {
			return Hapyfish2_Magic_Bll_UserResult::Error('student_id_error');
		}

		$t = time();

		if ($std['desk_id'] == $deskId) {
			//检查状态
			self::checkState($fid, $sid, $std, $t);
			//TEACHOVER
			if ($std['state'] != 3) {
				return Hapyfish2_Magic_Bll_UserResult::Error('student_state_error');
			}
		}
	    else {
		    return Hapyfish2_Magic_Bll_UserResult::Error('student_state_error');
		}

		if ($desk['end_time'] > $t) {
			return Hapyfish2_Magic_Bll_UserResult::Error('end_time_not_right');
		}

		$coinChange = 0;
		$clear = true;

		//在变石头前
		if ($t < $desk['stone_time']) {
			//自己家
			if ($uid == $fid) {
				$coinChange = $desk['coin'];
				$ok = Hapyfish2_Magic_HFC_User::incUserCoin($uid, $coinChange);

				//event
				Hapyfish2_Magic_Event_Bll_Collection::rndDrop('201112Xmas', $uid);
			} else {
				$mid = $desk['magic_id'];
				$magicInfo = Hapyfish2_Magic_Cache_BasicInfo::getMagicStudyInfo($mid);
				if (!$magicInfo) {
					return Hapyfish2_Magic_Bll_UserResult::Error('magic_info_error');
				}

				//判断剩余金币是否足够
				if  (!empty($magicInfo['gain_coin']) && $desk['coin'] <= $magicInfo['gain_coin'] * $magicInfo['steal_rate_limit']/100) {
					return Hapyfish2_Magic_Bll_UserResult::Error('coin_not_enough');
				}

				//判断是否自己mp是否足够
				$userMpInfo = Hapyfish2_Magic_HFC_User::getUserMp($uid);
				if ($userMpInfo['mp'] < $magicInfo['need_mp']) {
					return Hapyfish2_Magic_Bll_UserResult::Error('mp_not_enough');
				}

				//判断是否偷过
				$moochInfo = Hapyfish2_Magic_Cache_Mooch::getMoochDesk($fid, $deskId);
				if (!empty($moochInfo) && in_array($uid, $moochInfo)) {
					return Hapyfish2_Magic_Bll_UserResult::Error('has_pickup');
        		}

        		//记录偷过
        		$moochInfo[] = $uid;
				Hapyfish2_Magic_Cache_Mooch::moochDesk($fid, $deskId, $moochInfo);

				//好友获得,被偷获得
				$steal_friend_coin = mt_rand($magicInfo['steal_friend_low'], $magicInfo['steal_friend_high']);
				Hapyfish2_Magic_HFC_User::incUserCoin($fid, $steal_friend_coin);

				$steal_coin = mt_rand($magicInfo['steal_low'], $magicInfo['steal_high']);
				Hapyfish2_Magic_HFC_User::incUserCoin($uid, $steal_coin);
				//info_log($steal_coin, 'a1');

				//消耗魔法值
				$userMpInfo['mp'] -= $magicInfo['need_mp'];
				Hapyfish2_Magic_HFC_User::updateUserMp($uid, $userMpInfo);

				//减去金币数量
				$desk['coin'] = $desk['coin'] - $steal_friend_coin - $steal_coin;

				$ok = Hapyfish2_Magic_HFC_Desk::updateOne($fid, $deskId, $desk);

				$coinChange = $steal_coin;
				$clear = false;

			    //insert minifeed
		        $rowFriend = Hapyfish2_Platform_Bll_User::getUser($fid);
		        if ($rowFriend) {
            		$feed = array(
            			'uid' => $fid,
            			'template_id' => 1,
            			'actor' => $uid,
            			'target' => $fid,
            			'type' => 1,//1好友 2系统
            			'icon' => 2,//1笑脸 2哭脸
            			'title' => array('actor' => $rowFriend['name'], 'num1' => $steal_friend_coin, 'num2' => $steal_coin),
            			'create_time' => time()
            		);
            		Hapyfish2_Magic_Bll_Feed::insertMiniFeed($feed);
		        }
			}
		} else {
			if ($uid != $fid) {
				return Hapyfish2_Magic_Bll_UserResult::Error('no_coin_pickup');
			}

			//insert minifeed
			$feed = array(
				'uid' => $uid,
				'template_id' => 7,
				'actor' => $uid,
				'target' => $uid,
				'type' => 2,//1好友 2系统
				'icon' => 2,//1笑脸 2哭脸
				'title' => array('num1' => $desk['coin']),
				'create_time' => time()
			);
			Hapyfish2_Magic_Bll_Feed::insertMiniFeed($feed);
		}

		//清空
		if ($clear) {
			$desk['student_id'] = 0;
			$desk['magic_id'] = 0;
			$desk['coin'] = 0;
			$desk['end_time'] = 0;
			$desk['stone_time'] = 0;
			$ok = Hapyfish2_Magic_HFC_Desk::updateOne($uid, $deskId, $desk);
			if ($ok) {
			    $students = Hapyfish2_Magic_HFC_Student::getAll($uid);
		        foreach ($students as $tmp) {
		            if ($tmp['desk_id'] == $deskId) {
		                //把占据该位置学生设置成空闲状态
		                $tmp['desk_id'] = 0;
		                $tmp['state'] = 3;
		                Hapyfish2_Magic_HFC_Student::updateOne($uid, $tmp['sid'], $tmp);
		                break;
		            }
		        }
			}

			//如果有闲逛学生，取一个
			$fiddleStd = self::updateOneFiddle($uid, $desk);
			if ($fiddleStd) {
				$changeStudent = array(
						'sid' => $fiddleStd['sid'],
						'decor_id' => $fiddleStd['desk_id'],
						'state' => $fiddleStd['state'],
						'time' => 0,
						'magic_id' => $fiddleStd['magic_id'],
						'event_time' => -1,
						'coin' => 0,
						'stone_time' => 0,
						'can_steal' => 0
				);

				Hapyfish2_Magic_Bll_UserResult::addField($uid, 'changeStudents', array($changeStudent));
			}
		}

		//派发事件
		$event = array('uid' => $uid, 'coin' => $coinChange);
		Hapyfish2_Magic_Bll_Event::pickup($event);

		if ($batch) {
			return Hapyfish2_Magic_Bll_UserResult::result();
		} else {
			return Hapyfish2_Magic_Bll_UserResult::all();
		}
	}

	public static function award($uid, $sid)
	{
		$std = Hapyfish2_Magic_HFC_Student::getOne($uid, $sid);
		if (!$std) {
			return Hapyfish2_Magic_Bll_UserResult::Error('student_id_error');
		}

		if ($std['award_flg'] != 1) {
			return Hapyfish2_Magic_Bll_UserResult::Error('no_award');
		}

		$awardInfo = Hapyfish2_Magic_Cache_BasicInfo::getStudentAwardInfo($sid, $std['level'] - 1);

		if (!$awardInfo) {
			return Hapyfish2_Magic_Bll_UserResult::Error('award_info_error');
		}

		$std['award_flg'] = 0;
		$ok = Hapyfish2_Magic_HFC_Student::updateOne($uid, $sid, $std, true);

		if ($ok) {
			$awardRot = new Hapyfish2_Magic_Bll_Award();
			$prop = $awardInfo['prop'];
			if (!empty($prop)) {
				$awardRot->setProp($prop);
			}

			$items = $awardInfo['items'];
			if (!empty($items)) {
				$awardRot->setItemList($items);
			}

			$decors = $awardInfo['decors'];
			if (!empty($decors)) {
				$awardRot->setDecorList($decors);
			}

			$awardRot->sendOne($uid);
		}

		return Hapyfish2_Magic_Bll_UserResult::all();
	}

	public static function help($uid, $fid, $deskIds)
	{
		if ($uid != $fid) {
			$isFriend = Hapyfish2_Platform_Bll_Friend::isFriend($uid, $fid);
			if (!$isFriend) {
				return Hapyfish2_Magic_Bll_UserResult::Error('not_friend');
			}
		}

		$count = count($deskIds);
		if ($count == 1) {
			$res = self::helpOne($uid, $fid, $deskIds[0]);
			$results = array($res['result']);
		} else {
			foreach ($deskIds as $deskId) {
	    		self::helpOne($uid, $fid, $deskId, true);
	    	}
	    	$results = array(Hapyfish2_Magic_Bll_UserResult::result(true));
		}

    	$result = array('results' => $results);
    	Hapyfish2_Magic_Bll_UserResult::field($result);

    	return $result;
	}

	public static function helpOne($uid, $fid, $deskId, $batch = false)
	{
		$desk = Hapyfish2_Magic_HFC_Desk::getOne($fid, $deskId);
		if (!$desk) {
			return Hapyfish2_Magic_Bll_UserResult::Error('desk_id_error');
		}

		$sid = $desk['student_id'];
		$std = Hapyfish2_Magic_HFC_Student::getOne($fid, $sid);
		if (!$std) {
			return Hapyfish2_Magic_Bll_UserResult::Error('desk_or_student_error');
		}

		$t = time();

		if ($std['event'] == 0) {
			return Hapyfish2_Magic_Bll_UserResult::Error('student_state_error');
		}

		if ($std['event_time'] > $t) {
			return Hapyfish2_Magic_Bll_UserResult::Error('student_state_time_error');
		}

		//判断魔法值是否足够
		$mpChange = FIX_INTERRUPT_MP;
		$userMpInfo = Hapyfish2_Magic_HFC_User::getUserMp($uid);
		if ($userMpInfo['mp'] < $mpChange) {
			return Hapyfish2_Magic_Bll_UserResult::Error('mp_not_enough');
		}

		//消耗魔法值
		Hapyfish2_Magic_HFC_User::decUserMp($uid, $mpChange);

		$end_time = $t + $std['spend_time'] - ($std['event_time'] - $std['start_time']);

		$std['state'] = 2;
		$std['end_time'] = $end_time;
		$std['event'] = 0;
		$std['event_time'] = -1;
		$std['stone_time'] = $end_time + floor(STONE_TIME/SPEED_BASE/SPEED_STONE_TIME);

		Hapyfish2_Magic_HFC_Student::updateOne($fid, $std['sid'], $std);

		//更新desk
		$desk['magic_id'] = $std['magic_id'];
		$desk['coin'] = $std['coin'];
		$desk['end_time'] = $std['end_time'];
		$desk['stone_time'] = $std['stone_time'];
		Hapyfish2_Magic_HFC_Desk::updateOne($fid, $deskId, $desk);

		$expChange = FIX_INTERRUPT_EXP;
		//奖励经验
		Hapyfish2_Magic_HFC_User::incUserExp($uid, $expChange);

		//派发事件
		$event = array('uid' => $uid, 'student' => $std);
		Hapyfish2_Magic_Bll_Event::helpStudent($event);

		//返回studengVO
		$changeStudent = array(
						'sid' => $std['sid'],
						'decor_id' => $std['desk_id'],
						'state' => $std['state'],
						'time' => $end_time - $t,
						'magic_id' => $std['magic_id'],
						'event_time' => -1,
						'coin' => $std['coin'],
						'stone_time' => STONE_TIME,//$std['stone_time'] - $t,
						'can_steal' => 1
				);

		Hapyfish2_Magic_Bll_UserResult::addField($uid, 'changeStudents', array($changeStudent));

		if ($uid != $fid) {
    	    //insert minifeed
            $rowFriend = Hapyfish2_Platform_Bll_User::getUser($fid);
            if ($rowFriend) {
        		$feed = array(
        			'uid' => $fid,
        			'template_id' => 2,
        			'actor' => $uid,
        			'target' => $fid,
        			'type' => 1,//1好友 2系统
        			'icon' => 1,//1笑脸 2哭脸
        			'title' => array('actor' => $rowFriend['name'], 'name' => '吞噬'),
        			'create_time' => time()
        		);
        		Hapyfish2_Magic_Bll_Feed::insertMiniFeed($feed);
            }
		}

		if ($batch) {
			return Hapyfish2_Magic_Bll_UserResult::result();
		} else {
			return Hapyfish2_Magic_Bll_UserResult::all();
		}
	}
}