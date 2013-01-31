<?php
class Student_Task_Model extends Model_Core
{
	protected $table_pre = 'role_student_task';
	
	public function getDataByRoleId()
	{
		$result = $this->db_cache->select($this->table, array('role_id' => $this->role_id));
		
		if (!$result->count()) {
			return array();
		}
		return $result;
	}
	
	public function getDataById($id)
	{
		$result = $this->db_cache->select($this->table, array('id' => $id));
		
		if (!$result->count()) {
			return array();
		}
		return current($result->current());
	}
	
	public function insert($data)
	{
		$this->db_cache->insert($this->table, $data, $this->table_pre);
	}
	
	public function update($id, $data)
	{
		$this->db_cache->update($this->table, $data, array('id' => $id));
	}
	
	public function increment($id, $data)
	{
		$this->db_cache->increment($this->table, $data, array('id' => $id)); 
	}
}