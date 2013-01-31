<?php
class Door_Task_Model extends Model_Core
{
	protected $table_pre = 'role_door_task';
	
	public function getDataById($id)
	{
		$result = $this->db_cache->select($this->table, array('id' => $id));
		
		if (!$result->count()) {
			return array();
		}
		return current($result->current());
	}
	
	public function insertDoorTask($data)
	{
		return $this->db_cache->insert($this->table, $data, 'role_door_task');
	}

	public function deleteDoorTask($id)
	{
		$this->db_cache->delete($this->table, array('id' => $id));
	}
	
}