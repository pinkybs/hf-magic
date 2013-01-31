<?php
class Student_Model extends Model_Core
{
	protected $table_pre = 'role_student';
	
	public function getDataByRoleId()
	{
		$result = $this->db_cache->select($this->table, array('role_id' => $this->role_id));
		
		if (!$result->count()) {
			return array();
		}
		return $result;
	}
	
	public function getDataByRoleIdStudentId($student_id)
	{
		$result = $this->db_cache->select($this->table, array('role_id' => $this->role_id, 'student_id' => $student_id));
		
		if (!$result->count()) {
			return array();
		}
		return current($result->current());
	}
	
	public function getDataByTypeCount($type)
	{
		$result = $this->db_cache->select($this->table, array('role_id' => $this->role_id, 'student_state' => $type));

		return $result->count();
	}
	
	public function getDataByType($type)
	{
		$result = $this->db_cache->select($this->table, array('role_id' => $this->role_id, 'student_state' => $type));
		if (!$result->count()) {
			return array();
		}
		return current($result->current());
	}
	
	public function getDataByTypeSome($type)
	{
		$result = $this->db_cache->select($this->table, array('role_id' => $this->role_id, 'student_state' => $type));
		if (!$result->count()) {
			return array();
		}
		return $result;
	}
	
	public function updateExp($student_id, $exp)
	{
		$this->db_cache->update($this->table, array('exp' => $exp), array('role_id' => $this->role_id, 'student_id' => $student_id));
	}
	
	public function updateState($student_id, $state)
	{
		$this->db_cache->update($this->table, array('student_state' => $state), array('role_id' => $this->role_id, 'student_id' => $student_id));
	}
	
	public function update($id, $data)
	{
		$this->db_cache->update($this->table, $data, array('id' => $id));
	}
}