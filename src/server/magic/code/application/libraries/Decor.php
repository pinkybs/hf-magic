<?php defined ( 'SYSPATH' ) or die ( 'No direct access allowed.' );
class Decor extends SingleBase {
	
	public function addDecors($decors)
	{
	    $decor_model = Building_Model::instance($this->role_id);
        foreach ($decors as $vl) {
        	$decor_id = (string)$vl[0];
        	//根据id判断类型
        	$type = $decor_id[2];
        	
        	if ($type == DecorType::WALL) {
        		$wall_inbag_model = Wall_Inbag_Model::instance($this->role_id);
        		$wall_inbag_model->addUserWallInBag(array(
        			'role_id' => $this->role_id,
        			'wall_id' => $vl[0],
        			'quantity' => $vl[1],
        		));
        		continue;
        	}
        	
            if ($type == DecorType::FLOOR) {
        		$floor_inbag_model = Floor_Inbag_Model::instance($this->role_id);
        		$floor_inbag_model->addUserFloorInBag(array(
        			'role_id' => $this->role_id,
        			'floor_id' => $vl[0],
        			'quantity' => $vl[1],
        		));
        		continue;
        	}
        	
        	for ($i = $vl[1]; $i > 0; $i--) {
    			$decor_model->insertById($vl[0]);
        	}
    	}
	}
	
	/**
	 * 获取场景信息
	 */
	public function getSceneData()
	{
		//获取building
		$building_model = Building_Model::instance($this->role_id);
		$building_list = $building_model->getDataByRoleIdBagType(0);
		
		$role = Role::create($this->role_id);
		
		//获取地板
		$floor_model = Floor_Model::instance($this->role_id);
		$floor_data = $floor_model->getDataByRoleId();
		
		//获取墙壁
		$wall_model = Wall_Model::instance($this->role_id);
		$wall_data = $wall_model->getDataByRoleId();
		
		//获取学生
		$student_model = Student_Task_Model::instance($this->role_id);
		$students = $student_model->getDataByRoleId();
		
		//设置完成了的学生状态
		$timestamp = PEAR::getStaticProperty('_APP', 'timestamp');
		foreach ($students as $key => $vl) {
			$vl = current($vl);
			/**
			if ($vl['end_time'] < $timestamp && $vl['end_time'] != 0 && $vl['state'] != StudentState::INTERRUPT) {
				$data = array(
					'state' => StudentState::TEACHOVER,
				);
			}
			
			$student_model->update($vl['id'], $data);
			*/
			$student_class = Student::instance($this->role_id);
			$student_class->changeState($vl['id']);
		}
		
		$students = $student_model->getDataByRoleId();
		$s_f = array();
		foreach ($students as $key => $vl) {
			if ($vl[0]['state'] != StudentState::NOSTUDENT && $vl[0]['state'] != StudentState::NODESK) {
				//var_dump($vl[0]);
				//中断状态,改变时间
				if ($vl[0]['state'] == StudentState::INTERRUPT) {
					$vl[0]['end_time'] = $timestamp + $vl[0]['spend_time'] - ($vl[0]['event_time'] - $vl[0]['start_time']);
				}
				$s_f[$key] = $vl;
			}
		}

		$student_format = common::transform('student', $s_f);
		
		foreach ($student_format['student'] as $key => $vl) {
			if ($student_format['student'][$key]['event_time'] != 0 && $student_format['student'][$key]['event_time'] != -1) {
				$student_format['student'][$key]['event_time'] -= $timestamp;
				
				//中断状态,改变时间
			}
			$student_format['student'][$key]['time'] -= $timestamp;
			$student_format['student'][$key]['stone_time'] -= $timestamp;
			
			if ($student_format['student'][$key]['time'] <= 0) {
				$student_format['student'][$key]['time'] = 0;
			}
			
			if ($student_format['student'][$key]['stone_time'] <= 0) {
				$student_format['student'][$key]['stone_time'] = 0;
			}
			
			if ($student_format['student'][$key]['event_time'] < 0 && $student_format['student'][$key]['event_time'] != -1) {
				$student_format['student'][$key]['event_time'] = 0;
			}
			$crystal = Kohana::config('base.crystal');
			$student_format['student'][$key]['coin'] = $student_format['student'][$key]['coin'];
		}
		
		//闲逛学生
		$role = Role::create($this->role_id);
		$fiddle_students = $role->get('fiddle_students');
		$basic_model = new Basic_Model();
		$avatar_static_data = $basic_model->getAvatarListByType(AvatarType::STUDENT);
		
		//取出闲逛的学生
		$student_state_model = Student_Model::instance($this->role_id);
		$data = $student_state_model->getDataByTypeSome(StudentType::FIDDLE);
		
		foreach ($data as $vl) {
			$avatar_data = arr::_array_rand($avatar_static_data);
			$data = array(
				'id' => 0,
				'student_id' => $vl[0]['student_id'],
				'magic_id' => 0,
				'event_time' => -1,
				'start_time' => 0,
				'end_time' => 0,
				'state' => StudentState::FIDDLE,
				'spend_time' => 0,
				'role_id' => $this->role_id,
				'coin' => 0,
				'stone_time' => 0,
			);
			$student_tf = common::transform('student', $data);
			array_push($student_format['student'], $student_tf['student']);
		}
		
		$student_list = array('students' => $student_format['student']);
		
		//获取已经激活的学生
		$student_model = Student_Model::instance($this->role_id);
		$student_states_data = $student_model->getDataByRoleId();
		$student_states = common::transform('studentStates', $student_states_data);
		
		
		$decorClass =  common::transform('decorList', $building_list);
		$floorClass = array('floorList' => json_decode($floor_data['data']));
		$wallClass = array('wallList' => json_decode($wall_data['data']));
		
		//重构decorClass,加上门的倒计时时间
		$decor_format = array();
		foreach ($decorClass['decorList'] as $key => $vl) {
			if ($vl['type'] == DecorType::DOOR) {
				//取出此门倒计时数据
				//$role_id = Role::getOwnRoleId();
				$door_task_model = Door_Task_Model::instance($this->role_id);
				$door_task_data = $door_task_model->getDataById($vl['id']);
				
				$timestamp = PEAR::getStaticProperty('_APP', 'timestamp');
				$vl['door_left_students_num'] = $door_task_data['left_students_num'];
				$vl['door_left_time'] = $door_task_data['end_time'] - $timestamp;
				$decor_format['decorList'][$key] = $vl;
			} else {
				$decor_format['decorList'][$key] = $vl;
			}
		}
		
		//怪物数据
		$monster = Monster::instance($this->role_id);
		$monster_data = $monster->getMonster();
		
		$user_data = Role::getRoleFormat($this->role_id);
		
		$scene = $decor_format + $student_states + $floorClass + $wallClass + $student_list + array('user' => $user_data) + $monster_data;
		
		return $scene;
	}
	
	/**
	 * 获取某一建筑的信息
	 * @param unknown_type $id
	 */
	public function getDataById($id)
	{
		$building_model = Building_Model::instance($this->role_id);
		$building_data = $building_model->getDataById($id);
		
		return $building_data;
	}
	
	/**
	 * 返回所有可用的桌子
	 */
	public function getEmptyDesk()
	{
		//取出所有桌子
		$building_model = Building_Model::instance($this->role_id);
		$desk_list = $building_model->getDataByRoleIdType(DecorType::DESK);
		//echo Kohana::debug($desk_list);
		//取出在桌子上的学生
		$student_task_model = Student_Task_Model::instance($this->role_id);
		$student_task_list = $student_task_model->getDataByRoleId();
		
		$student_format = array();
		foreach ($student_task_list as $value) {
			if ($value[0]['state'] != StudentState::NOSTUDENT && $value[0]['state'] != StudentState::NODESK) {
				$student_format[$value[0]['id']] = 1;
			}
		}

		$desk_format = array();
		foreach ($desk_list as $vl) {
			if (!isset($student_format[$vl[0]['id']])) {
				$desk_format[$vl[0]['id']] = $vl[0];
			}
		}
		
		return $desk_format;
	}
	
	public function getStaticDecorDataById($id)
	{
		$basic_model = new Basic_Model();
		$building_data = $basic_model->getBuildingDataById($id);
		
		return $building_data;
	}
	
	public function getBag()
	{
		$role_id = Role::getOwnRoleId();
		
		//取出建筑列表信息
		$building_model = Building_Model::instance($role_id);
		$decor_list = $building_model->getDataByRoleIdBagType(1);
		
		//取出地板背包
		$floor_inbag_model = Floor_Inbag_Model::instance($role_id);
		$floor_list = $floor_inbag_model->getUserFloorInBag();
		
		$floor_list_format = array();
		foreach ($floor_list as $vl) {
				$vl = current($vl);
				if ($vl['quantity'] > 0) {
					array_push($floor_list_format, 
						array('id' => $vl['id'],
						'x' => 0,
						'y' => 0,
						'z' => 0,
						'mirror' => 0,
						'bag_type' => 1,
						'd_id' =>  $vl['floor_id'],
						'type' => DecorType::FLOOR,
						'num' => $vl['quantity'],
						)
					);
				}
		}
		
		//取出墙纸背包
		$wall_inbag_model = Wall_Inbag_Model::instance($role_id);
		$wall_list = $wall_inbag_model->getUserWallInBag();
		
		$wall_list_format = array();
		foreach ($wall_list as $vl) {
				$vl = current($vl);
				if ($vl['quantity'] > 0) {
					array_push($wall_list_format, 
						array('id' => $vl['id'],
						'x' => 0,
						'y' => 0,
						'z' => 0,
						'mirror' => 0,
						'bag_type' => 1,
						'd_id' =>  $vl['wall_id'],
						'type' => DecorType::WALL,
						'num' => $vl['quantity'],
						)
					);
				}
		}
		$decor_list_format = common::transform('decorList', $decor_list);

		$decor_all_list_format = array_merge($decor_list_format['decorList'], $wall_list_format, $floor_list_format);
		
		return $decor_all_list_format;
	}
}