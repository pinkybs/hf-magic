<?php defined ( 'SYSPATH' ) or die ( 'No direct access allowed.' );
class Student_Controller extends Role_Controller {
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function opendoor()
	{
		$role_id = Role::getOwnRoleId();
		$role = Role::create($role_id);
		
		//获取门的id
		$door_id = $this->input->post('decor_id');
		
		//取出此建筑信息
		$decor = Decor::instance($role_id);
		$door_data = $decor->getDataById($door_id);

		if (empty($door_data)) {
			//返回错误信息
			Network::buffer_error('door_not_exsit');
		}
		
		//取出task,如果为空,返回error 测试输入中文
		$door_task_model = Door_Task_Model::instance($role_id);
		$door_task_data = $door_task_model->getDataById($door_id);
		
		if (empty($door_task_data)) {
			Network::buffer_error('door_task_not_exsit');
		}
		
		$left_students_num = $door_task_data['left_students_num'];
		
		$timestamp = PEAR::getStaticProperty('_APP', 'timestamp');
		
		//判断倒计时是否结束,计时未结束,返回错误信息,计时结束,放人
		if ($door_task_data['end_time'] > $timestamp) {
			Network::buffer_error('not_end_time');
		}
		
		
		//判断场景内人数是否已满
		$basic = Basic::instance($role_id);
		$level_data = $basic->getLevelData();
		
		//取出解锁的学生
		$student_state_model = Student_Model::instance($role_id);
		$student_state_result = $student_state_model->getDataByRoleId();
		
//		if (empty($student_state_result)) {
//			$limit_student = 0;
//		} else {
//			$limit_student = $student_state_result->count();
//		}
		
		$cur_level_data = Basic::getHouseLevelData($role->get('house_level'));
		$limit_student = $cur_level_data['student_limit'];
		//$limit_student = $level_data[$role->get('level')]['limit_student'];
		$student_model = Student_Task_Model::instance($role_id);
		$student_result = $student_model->getDataByRoleId();
		
		$students_data = array();
		$students_on_desk = 0;
		$student_class = Student::instance($role_id);
		
		//先改变状态
		foreach ($student_result as $vl) {
			$students_data[$vl[0]['id']] = $vl[0];
			
			//改变状态XXX
			$student_class->changeState($vl[0]['id']);
		}
		
		//水晶算人头,所以改变已经变成水晶的学生状态为acg
		
		//然后才能计算task
		foreach ($student_result as $vl) {
			$students_data[$vl[0]['id']] = $vl[0];
			//等待拾取的水晶不算人头
			if ($vl[0]['state'] != StudentState::NOSTUDENT && $vl[0]['state'] != StudentState::NODESK && $vl[0]['state'] != StudentState::TEACHOVER) {
				$students_on_desk++;
			}
		}
		
		$role = Role::create($role_id);
	
		$fiddle_students = $student_class->getStudentByTypeCount(StudentType::FIDDLE);

		if ($students_on_desk + $fiddle_students >= $limit_student) {
			Network::buffer_error('out_of_limit_student');
		}
		
		//判断student表的数据,acg
		$acg_count = $student_class->getStudentByTypeCount(StudentType::ACG);
		if (empty($acg_count)) {
			Network::buffer_error('out_of_limit_student');
		}
		//插入教室内的学生 1.空闲课桌 先插入空闲课桌,剩余人数更改至
		//取出此用户所有课桌
		$left_desk = $decor->getEmptyDesk();
		
		//判断空闲桌子
		//if (empty($left_desk)) {
			//Network::buffer_error('no_empty_desk');
		//}
		
		//取出玩家已经学会的魔法
		$magic_model = Magic_Model::instance($role_id);
		$magic_data = $magic_model->getDataByRoleId();
		$study_ids = json_decode($magic_data['study_ids']);
		
		//取出所有magic静态数据
		//$basic = Basic::instance($role_id);
		//$magic_static_data = $basic->getMagicData();
		
		//取出avatar数据
		$basic_model = new Basic_Model();
		$avatar_static_data = $basic_model->getAvatarListByType(AvatarType::STUDENT);
		
		$students = array();
		//更改task
		foreach($left_desk as $key => $vl) {
			//如果有数据,则更改,无则插入
			if (isset($students_data[$vl['id']])) {
				$student_task_data = $students_data[$vl['id']];
			} else {
				$student_task_data = false;
			}
			
			//取出此课桌的基本信息
			$desk_static_data = $decor->getStaticDecorDataById($vl['building_id']);
			
			//随机一个魔法
			$magic_id = arr::_array_rand($study_ids);
			//随机一个学生
			$avatar_data = arr::_array_rand($avatar_static_data);
			$student_random_data = $student_class->randomStudent(StudentType::ONDESK);
			
			if ($student_random_data === false) {
				Network::buffer_error('empty_limit_student');
			}
			
			//$study_magic_data = $magic_static_data[$magic_id];
			if (empty($student_task_data)) {
				//插入task
				$data = array(
					'id' => $vl['id'],
					'student_id' => $student_random_data['student_id'],
					'avatar_id' => $avatar_data['id'],
					'magic_id' => $magic_id,
					'event_time' => -1,
					'start_time' => 0,
					'end_time' => 0,
					'spend_time' => 0,
					'state' => StudentState::NOTEACH,
					'role_id' => $role_id,
					'coin' => 0,
					'stone_time' => 0,
				);
	
				$student_model->insert($data);
			} else {
				if ($student_task_data['state'] == StudentState::NOSTUDENT || $student_task_data['state'] == StudentState::NODESK) {
					$data = array(
						'id' => $vl['id'],
						'avatar_id' => $avatar_data['id'],
						'student_id' => $student_random_data['student_id'],
						'magic_id' => $magic_id,
						'event_time' => -1,
						'start_time' => 0,
						'end_time' => 0,
						'state' => StudentState::NOTEACH,
						'spend_time' => 0,
						'role_id' => $role_id,
						'coin' => 0,
						'stone_time' => 0,
					);
					$student_model->update($vl['id'], $data);
				}
			}
			
			$student_format = common::transform('student', $data);
			array_push($students, $student_format['student']);
			
			$left_students_num--;
			
			if ($left_students_num == 0) {
				break;
			}
		}
		
		for ($index = 0; $index < $left_students_num; $index++) {
			$avatar_data = arr::_array_rand($avatar_static_data);
			$student_random_data = $student_class->randomStudent(StudentType::FIDDLE);
			$data = array(
				'id' => 0,
				'avatar_id' => $avatar_data['id'],
				'student_id' => $student_random_data['student_id'],
				'magic_id' => 0,
				'event_time' => -1,
				'start_time' => 0,
				'end_time' => 0,
				'state' => StudentState::FIDDLE,
				'spend_time' => 0,
				'role_id' => $role_id,
				'coin' => 0,
				'stone_time' => 0,
			);
			$student_format = common::transform('student', $data);
			array_push($students, $student_format['student']);
		}
		
		//修改教室内闲逛学生的数量
		$role = Role::create($role_id);
		$role->increment('fiddle_students', $left_students_num);
		
		$speed = Kohana::config('base.speed');
		//如果门内的人数为空,插入新的
		if ($left_students_num <= 0) {
			//取出门的基础信息
			$door_static_data = $decor->getStaticDecorDataById($door_data['building_id']);
			//$door_guest_limit = $door_data['door_guest_limit'];
			
			//修改门的task信息
			$door_change_data = array(
				'left_students_num' => $door_static_data['door_guest_limit'],
				'start_time' => $timestamp,
				'end_time' => $timestamp + $door_static_data['door_cooldown']/$speed['base']/$speed['door_time'],
				'role_id' => $role_id,
			);
		} else {
			//修改剩余学生数量
			$door_change_data = array('left_students_num' => $left_students_num);
		}
		
		$door_task_model->update($door_task_data['id'], $door_change_data);
		
		Network::buffer('students', $students);
	}
	
	public function study()
	{
		//获取的传输的desk_id
		$desk_id = $this->input->post('decor_id');
		
		$role_id = Role::getOwnRoleId();
		$role = Role::create($role_id);
		//开始各种判断
		
		$decor = Decor::instance($role_id);
		//取出此物品
		$desk_data = $decor->getDataById($desk_id);
		
		if (empty($desk_data)) {
			Network::buffer_error('desk_not_exsit');
		}
		
		$student_class = Student::instance($role_id);
		$student_class->changeState($desk_id);
		
		$desk_static_data = $decor->getStaticDecorDataById($desk_data['building_id']);
		
		//取出此task信息,获得magic_id
		$student_model = Student_Task_Model::instance($role_id);
		$student_task_data = $student_model->getDataById($desk_id);
		
		//取出此magic信息,静态表
		$basic_model = new Basic_Model();
		$magic_data = $basic_model->getMagicStudyDataById($student_task_data['magic_id']);
		
		//判断魔法值是否足够
		$mp = $role->get('mp');
		if ($magic_data['need_mp'] > $mp) {
			Network::buffer_error('mp_not_enough');
		}
		
		//消耗魔法值
		$role->increment('mp', -$magic_data['need_mp']);
		//奖励经验
		$role->increment('exp', $magic_data['gain_exp']);
		
		$timestamp = PEAR::getStaticProperty('_APP', 'timestamp');
		$speed = Kohana::config('base.speed');
		
		//第一次取出newbie数据,缩短时间,并必定有中断
		$newbie_flag = false;
		$newbie = json_decode($role->get('newbie'));
		if ($newbie[1][0] == 1) {
			//新手
			$newbie_flag = true;
			
			//修改newbie_flg
			$newbie[1][0] = 0;
			$role->set('newbie', json_encode($newbie));
		}
		
		if ($newbie_flag === true) {
			$spend_time = Kohana::config('base.newbie_study_time');
		} else {
			$spend_time = floor($magic_data['spend_time']/$speed['base']/$speed['study_time']);
		}
		
		//是否有中断
		if (mt_rand(1, 100) < $magic_data['abnormal_percent'] || $newbie_flag === true) {
			$state = StudentState::STUDYING;

			if ($newbie_flag === true) {
				$event_time = $timestamp + round(mt_rand(1, $spend_time - 1));
			} else {
				$event_time = $timestamp + mt_rand(5, $spend_time - 10);
			}
		} else {
			$state = StudentState::STUDYING;
			$event_time = -1;
		}
		
		$avatar_static_data = $basic_model->getAvatarListByType(AvatarType::STUDENT);
		$avatar_data = arr::_array_rand($avatar_static_data);
	
		//改变task
		$data = array(
			'avatar_id' => $avatar_data['id'],
			'start_time' => $timestamp,
			'end_time' => $timestamp + $spend_time,
			'state' => $state,
			'event_time' => $event_time,
			'spend_time' => $spend_time,
			'coin' => $magic_data['gain_coin'],
			'stone_time' => $timestamp + $spend_time + floor(common::basic('stone_time')/$speed['base']/$speed['stone_time']),
		);
		
		$student_model->update($desk_id, $data);
		
		//返回一个新的学生数据
		$student_format = common::transform('student', $data + $student_task_data);
		//Network::buffer('data', $data);
		
		if ($student_format['student']['event_time'] != -1) {
			$student_format['student']['event_time'] -= $timestamp;
		}
		$crystal = Kohana::config('base.crystal');
		$student_format['student']['coin'] = $data['coin'];
		$student_format['student']['time'] = $data['end_time'] - $timestamp;
		$student_format['student']['stone_time'] = $data['stone_time'] - $timestamp;
		Network::buffer('changeStudent', $student_format['student']);
		
		//升级学生经验
		$student_class = Student::instance($role_id);
		$student_class->updateExp($student_task_data['student_id']);
		
		//任务,传授魔法次数
		GameEvent::processRoleEvent(EventConditionType::TEACH_MAGIC_TIMES, 0, 1);
		GameEvent::processRoleEvent(EventConditionType::TEACH_MAGIC_TIMES, $student_task_data['magic_id'], 1);
		
		Network::buffer('result', common::result());
	}
	
	public function award()
	{
		$sid = $this->input->post('sid');
		
		$role_id = Role::getOwnRoleId();
		
		//取出基础数据
		$student_model  = Student_Model::instance($role_id);
		$student_data = $student_model->getDataByRoleIdStudentId($sid);
		
		if ($student_data['award_flg'] != 1) {
			Network::buffer_error('not_need_award');
		}
		
		//get student award data
		$basic_model = new Basic_Model();
		$student_basic_data = $basic_model->getStudentAwardsData($sid, $student_data['level'] - 1);
		
    	//奖励属性
    	$props = arr::_serial_to_array($student_basic_data['award_prop']);
    	$role = Role::create($role_id);
    	foreach ($props as $key => $vl) {
    		$role->increment($key, $vl);
    	}
    	
    	//奖励道具
    	$items = json_decode($student_basic_data['award_items']);
    	$item = Item::instance($role_id);
    	$item->addItems($items);
    	
    	//奖励装饰
    	$decors = json_decode($student_basic_data['award_decors']);
    	$decor = Decor::instance($role_id);
    	$decor->addDecors($decors);
		
		$student_model = Student_Model::instance($role_id);
		$student_model->update($student_data['id'], array('award_flg' => 0));
		
		Network::buffer('result', common::result());
	}
	
	public function pickup()
	{
		//获取uid
		$decor_ids = $this->input->post('decor_ids');
		$role_id = $this->input->post('uid');
		$own_role_id = Role::getOwnRoleId();
		
		if (empty($role_id)) {
			$role_id = Role::getOwnRoleId();
		}
		
		//转成数组
		$desk_ids = json_decode($decor_ids);
		
		//各种预先判断 判断是否是自己的好友
		
		$timestamp = PEAR::getStaticProperty('_APP', 'timestamp');
		
		$results = array();
		$students = array();
		//拾取
		foreach ($desk_ids as $desk_id) {
			$student_class = Student::instance($role_id);
			$student_class->changeState($desk_id);
			
			//取出此桌子
			$student_model = Student_Task_Model::instance($role_id);
			$student_task_data = $student_model->getDataById($desk_id);
			
			$magic_id = $student_task_data['magic_id'];
			//取出水晶
			$basic_model = new Basic_Model();
			$magic_data = $basic_model->getMagicStudyDataById($magic_id);
			
			if ($student_task_data['state'] != StudentState::TEACHOVER) {
				array_push($results, common::result_error('student_state_error'));
				continue;
			}
			
			//判断是否到时间了
			if ($student_task_data['end_time'] > $timestamp) {
				array_push($results, common::result_error('end_time_not_right'));
				continue;
			}
			
			//加水晶 TODO 判断变石头,则不加
			$role = Role::create($role_id);
			$crystal_task = 0;
			if ($timestamp < $student_task_data['stone_time']) {
				if ($role_id === $own_role_id) {
					$crystal_task = $student_task_data['coin'];
					
					//自己家
					$role->increment('coin', $student_task_data['coin']);
					
					GameEvent::processRoleEvent(EventConditionType::GAIN_CRYSTAL, 0, $crystal_task);
				} else {
					//好友家
					
					//判断水晶是否足够
					if  (!empty($magic_data['gain_coin']) && $student_task_data['coin'] <= $magic_data['gain_coin'] * $magic_data['steal_rate_limit']/100) {
						array_push($results, common::result_error('not_enouth_cystal'));
						continue;
					}
					
					//判断是否自己mp是否足够
					$role_self = Role::create($own_role_id);
					if ($role_self->get('mp') < common::basic('help_friend_mp')) {
						array_push($results, common::result_error('not_enouth_mp'));
						continue;
					}
					
					//判断是否偷过
					$cache = Cache::instance('steal_friends');
					$day = date("Ymd", $timestamp);
					$steal_cache_key = 'steal_friends_role_id_'.$own_role_id.'friends_id'.$role_id.'_t'.$student_task_data['start_time'];
					$steal_friends = $cache->get($steal_cache_key);
					
					if (!empty($steal_friends) && $day <= $steal_friends[0]) {
						array_push($results, common::result_error('stealed_this_friend'));
						continue;
					}
					
					//记录偷过
					//time
					$steal_friends= array($day);
					$cache->set($steal_cache_key, $steal_friends);
					
					//好友获得,被偷获得
					//比率
					$steal_friend_rate = mt_rand($magic_data['steal_friend_low'], $magic_data['steal_friend_high']);
					$steal_friend_coin = ceil($magic_data['gain_coin']*$steal_friend_rate/100);
					
					$role->increment('coin', $steal_friend_coin);
					
					//自己获得.偷取者获得
					$role_self = Role::create($own_role_id);
					$steal_rate = mt_rand($magic_data['steal_low'], $magic_data['steal_high']);
					$steal_coin = ceil($magic_data['gain_coin']*$steal_rate/100);
					
					$role_self->increment('coin', $steal_coin);
					$mp = common::basic('help_friend_mp');
					$role_self->increment('mp', -$mp);
					
					$crystal_task = $steal_coin;
					
					//减去水晶数量
					$student_model = Student_Task_Model::instance($role_id);
					$student_model->increment($desk_id, 
						array(
							'coin' => -$steal_coin - $steal_friend_coin,
						)
					);
					
					GameEvent::processRoleEvent(EventConditionType::GAIN_CRYSTAL, 0, $crystal_task);
					array_push($results, common::result());
					//在好友家其余操作作废
					continue;
				}
			}
			//任务,收取水晶数量
			/**
			if (!empty($crystal_task)) {
				GameEvent::processRoleEvent(EventConditionType::GAIN_CRYSTAL, 0, $crystal_task);
			} else {
				array_push($results, common::result_error('server_error'));
				continue;
			}
			*/

			//将学习完成的学生转换成异次元空间ACG
			$student_class = Student::instance($role_id);
			$student_class->setStudentType($student_task_data['student_id'], StudentType::ACG);
			
			//如果还有闲逛的人,取出一个XXX闲逛学生
			$left_students = $student_class->getFiddleStudent();
			
			//$left_students = $role->get('fiddle_students');
			if (!empty($left_students)) {
				$magic_model = Magic_Model::instance($role_id);
				$role_magic_data = $magic_model->getDataByRoleId();
				$study_ids = json_decode($role_magic_data['study_ids']);
				$magic_id = arr::_array_rand($study_ids);
				
				$avatar_static_data = $basic_model->getAvatarListByType(AvatarType::STUDENT);
				$avatar_data = arr::_array_rand($avatar_static_data);
				
				$data = array(
					'id' => $desk_id,
					'avatar_id' => $avatar_data['id'],
					'student_id' => $left_students['student_id'],
					'magic_id' => $magic_id,
					'event_time' => -1,
					'start_time' => 0,
					'end_time' => 0,
					'state' => StudentState::NOTEACH,
					'role_id' => $role_id,
					'coin' => 0,
				);
				$student_model = Student_Task_Model::instance($role_id);
				$student_model->update($desk_id, $data);

				$student_format = common::transform('student', $data + $student_task_data);
				array_push($students, $student_format['student']);
				$role->increment('fiddle_students', -1);
			} else {
				$data = array(
					'id' => $desk_id,
					'avatar_id' => 1,
					'magic_id' => 1001,
					'event_time' => -1,
					'start_time' => 0,
					'end_time' => 0,
					'state' => StudentState::NOSTUDENT,
					'role_id' => $role_id,
					'coin' => 0,
				);
				$student_model = Student_Task_Model::instance($role_id);
				$student_model->update($desk_id, $data);
			}
			
			array_push($results, common::result());
		}
		Network::buffer('results', $results);
		Network::buffer('changeStudents', $students);
	}
	
	public function interrupt()
	{
		//获取uid
		$decor_ids = $this->input->post('decor_ids');
		$friend_id = $this->input->post('uid');
		
		$role_id = Role::getOwnRoleId();
		$role = Role::create($role_id);
		
		if (empty($friend_id)) {
			$friend_id = $role_id;
		}
		
		//转成数组
		$desk_ids = json_decode($decor_ids);
		
		$timestamp = PEAR::getStaticProperty('_APP', 'timestamp');
		
		$results = array();
		$students = array();
		foreach ($desk_ids as $desk_id) {
			$student_class = Student::instance($friend_id);
			$student_class->changeState($desk_id);
			
			//取出此桌子
			$student_model = Student_Task_Model::instance($friend_id);
			$student_task_data = $student_model->getDataById($desk_id);
			
			if ($student_task_data['state'] != StudentState::INTERRUPT) {
				array_push($results, common::result_error('state_error'));
				continue;
			}
			
			//判断mp是否足够
			$need_mp = common::basic('interrupt_mp');
			$role_mp = $role->get('mp');
			
			if ($role_mp < $need_mp) {
				array_push($results, common::result_error('mp_not_enough'));
				continue;
			}
			
			//处理中断
			if ($student_task_data['event_time'] == 0) {
				//非中断状态,返回error
				array_push($results, common::result_error('not_event'));
				continue;
			}
			
			//判断中断时间是否到了
			if ($student_task_data['event_time'] >= $timestamp) {
				//返回error
				array_push($results, common::result_error('not_event'));
				continue;
			}
			
			$speed = Kohana::config('base.speed');
			$end_time = $timestamp + $student_task_data['spend_time'] - ($student_task_data['event_time'] - $student_task_data['start_time']);
			$data = array(
				'end_time' => $end_time,
				'state' => StudentState::STUDYING,
				'event_time' => -1,
				'stone_time' => $end_time + floor(common::basic('stone_time')/$speed['base']/$speed['stone_time']),
			);
			
			$student_model->update($desk_id, $data);
			
			//扣除mp
			$role->increment('mp', -$need_mp);
			//增加经验
			$role->increment('exp', common::basic('interrupt_exp'));
			
			$student_format = common::transform('student', $data + $student_task_data);
			array_push($students, $student_format['student']);
			array_push($results, common::result());
			
			//任务,解除异常
			GameEvent::processRoleEvent(EventConditionType::REMOVE_MAGIC_EVENT, 0, 1);
		}
		Network::buffer('results', $results);
		//Network::buffer('students', $students);
	}
}