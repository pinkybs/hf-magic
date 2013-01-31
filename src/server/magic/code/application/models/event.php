<?php
class Event_Model extends Model_Core
{
	protected $table_pre = 'role_event';
	
	public function getDataByRoleId()
	{
		$result = $this->db_cache->select($this->table, array('id' => $this->role_id));
		
		if (!$result->count()) {
			return array();
		}
		return current($result->current());
	}
}