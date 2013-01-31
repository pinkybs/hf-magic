<?php defined ( 'SYSPATH' ) or die ( 'No direct access allowed.' );
class Student extends SingleBase {
	public function changeState($desk_id)
	{
		$student_model = Student_Task_Model::instance($this->role_id);
		$task_data = $student_model->getDataById($desk_id);
		
		$timestamp = PEAR::getStaticProperty('_APP', 'timestamp');
		$state = $task_data['state'];

		//闲逛,未教学,学习中,教学完成,中断,桌子上没学生
		if ($task_data['end_time'] <= $timestamp && $task_data['end_time'] != 0 && $task_data['event_time'] == -1) {
			$state = StudentState::TEACHOVER;
		}

		if ($task_data['event_time'] != -1 && $task_data['event_time'] < $timestamp)
		{
			$state = StudentState::INTERRUPT;
		}
		
		if ((int)$task_data['state'] !== (int)$state) {
			$data = array('state' => $state);
			
			$student_model->update($desk_id, $data);
		}
		
		//取出这个学生的状态
		
		//取出这个桌子上学生的状态,如果是教学完成
		if ($state == StudentState::TEACHOVER && $task_data['student_id'] != 0) {
			//将此桌子的student_id改为0
			$data = array('student_id' => 0);
			$student_model->update($desk_id, $data);
			
			$this->setStudentType($task_data['student_id'], StudentType::ACG);
		}
	}
	
	/**
	 * 随机取出一个学生
	 * 1.闲逛2.桌子上
	 */
	public function randomStudent($type = 1)
	{
		//取出学生数据
		$student_model = Student_Task_Model::instance($this->role_id);
		$student_result = $student_model->getDataByRoleId();
		
		$students_data = array();
		//然后才能计算task
		foreach ($student_result as $vl) {
			//等待拾取的水晶不算人头
			if ($vl[0]['state'] == StudentState::NOSTUDENT || $vl[0]['state'] == StudentState::NODESK || $vl[0]['state'] == StudentState::TEACHOVER) {
				$students_data[$vl[0]['id']] = $vl[0];
			}
		}
		
		$student_state_model = Student_Model::instance($this->role_id);
		$student_state_result = $student_state_model->getDataByRoleId();
		
		foreach ($student_state_result as $vl) {
			$vl = $vl[0];
			if ($vl['student_state'] == StudentType::ACG) {
				$random = $vl;
				break;
			}
		}
		
		if (!isset($random)) {
			return false;
		}
		$student_state_model->update($random['id'], array('student_state' => $type));
		
		return $random;
	}
	
	/*
	 * 设置学生状态类型
	 */
	public function setStudentType($sid, $type = 0)
	{
		if ($sid == 0) {
			return;
		}
		
		$student_state_model = Student_Model::instance($this->role_id);
		$student_state_model->updateState($sid, $type);
	}
	
	/**
	 * 根据状态类型获取学生
	 * @param unknown_type $desk_id
	 */
	public function getStudentByTypeCount($type = 1)
	{
		$student_state_model = Student_Model::instance($this->role_id);
		$count = $student_state_model->getDataByTypeCount($type);
		
		return $count;
	}
	
	/**
	 * 取出一个闲逛的学生
	 * @param  $desk_id
	 */
	public function getFiddleStudent()
	{
		$student_state_model = Student_Model::instance($this->role_id);
		$data = $student_state_model->getDataByType(StudentType::FIDDLE);
		
		if (empty($data)) {
			return false;
		} else {
			$student_state_model->update($data['id'], array('student_state' => StudentType::ONDESK));
		}
		
		return $data;
	}
	
	public function updateExp($sid)
	{
		$student_exp = common::basic('student_exp');
		//取出学生数据
		$student_model  = Student_Model::instance($this->role_id);
		$student_data = $student_model->getDataByRoleIdStudentId($sid);
		
		//取出此等级数据
		$basic_model = new Basic_Model();
		$student_level_data = $basic_model->getStudentLevelDataByLevel($student_data['level']);
		$old_exp = $student_data['exp'];
		if ($student_data['exp'] >= $student_level_data['exp'] - 1) {
			// level up
			$student_data['level']++;
			$student_data['exp'] = 0;
			$student_data['award_flg'] = 1; 
		} else {
			$student_data['exp'] += $student_exp;
			$student_data['award_flg'] = 0; 
		}
		
		//update data
		$student_model->updateExp($sid, $student_data['exp']);
		
		$student_model->update($student_data['id'], array('award_flg' => $student_data['award_flg']));
		$student_model->update($student_data['id'], array('level' => $student_data['level']));
		
		//return student_state_vo
		if ($old_exp >= $student_level_data['exp'] - 1) {
			$student_data['exp'] = 0;
			$student_data['level'] = 1;
		} else {
			$student_data['exp'] = $student_exp;
			$student_data['level'] = 0;
		}
		
		$change_states = common::transform('studentStates', $student_data);
		Network::buffer('changeStudentState', $change_states['studentStates']);
	}
	
	public function addDesk($desk_id)
	{
		$role_id = $this->role_id;
		$role = Role::create($role_id);
		$student_model = Student_Task_Model::instance($role_id);
		$task = $student_model->getDataById($desk_id);
		
		$students = Network::get('changeStudents');
		$student_class = Student::instance($role_id);
		$left_students = $student_class->getFiddleStudent();
		if (!empty($left_students)) {
			$magic_model = Magic_Model::instance($role_id);
			$role_magic_data = $magic_model->getDataByRoleId();
			$study_ids = json_decode($role_magic_data['study_ids']);
			$magic_id = arr::_array_rand($study_ids);
			$basic_model = new Basic_Model();
			
			$avatar_static_data = $basic_model->getAvatarListByType(AvatarType::STUDENT);
			$avatar_data = arr::_array_rand($avatar_static_data);
			
			if (empty($task)) {
				//插入task
				$data = array(
					'id' => $desk_id,
					'avatar_id' => $avatar_data['id'],
					'magic_id' => $magic_id,
					'event_time' => -1,
					'start_time' => 0,
					'end_time' => 0,
					'spend_time' => 0,
					'state' => StudentState::NOTEACH,
					'role_id' => $role_id,
					'coin' => 0,
					'student_id' => $left_students['student_id'],
					'stone_time' => 0,
				);
	
				$student_model->insert($data);
				$student_format = common::transform('student', $data);
			} else {
				$data = array(
					'id' => $desk_id,
					'avatar_id' => $avatar_data['id'],
					'magic_id' => $magic_id,
					'event_time' => -1,
					'start_time' => 0,
					'end_time' => 0,
					'state' => StudentState::NOTEACH,
					'role_id' => $role_id,
					'coin' => 0,
					'student_id' => $left_students['student_id'],
				);
				$student_model = Student_Task_Model::instance($role_id);
				$student_model->update($desk_id, $data);
				
				$student_format = common::transform('student', $data + $task);
			}

			
			array_push($students, $student_format['student']);
			Network::buffer('changeStudents', $students);
			$role->increment('fiddle_students', -1);
		}
	}
	
	/**
	 * $student = Student::instance($role_id);
	 * $student->moveOutDesk($desk_id);
	 * @param unknown_type $desk_id
	 */
	public function moveOutDesk($desk_id)
	{
		$role_id = $this->role_id;
		$student_model = Student_Task_Model::instance($role_id);
		
		//直接删掉
		//$student_model->delete($desk_id);
		
		$task = $student_model->getDataById($desk_id);
		
		if (!empty($task)) {
			$data = array(
				'id' => $desk_id,
				'avatar_id' => 1,
				'magic_id' => 1001,
				'event_time' => -1,
				'start_time' => 0,
				'end_time' => 0,
				'state' => StudentState::NODESK,
				'role_id' => $role_id,
				'coin' => 0,
			);
			$student_model = Student_Task_Model::instance($role_id);
			$student_model->update($desk_id, $data);
		}
	}
}