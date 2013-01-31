<?php
class Wall_Model extends Model_Core
{
	protected $table_pre = 'role_wall';
	
	public function getDataByRoleId()
	{
		$result = $this->db_cache->getDataCondition($this->table, array('id' => $this->role_id));
		
		if (!$result->count()) {
			return array();
		}
		return current($result->current());
	}
	
	public function updateWallByUid($data, $role_id)
	{
		$this->db_cache->update($this->table, $data, array('id' => $role_id));
	}
	
	
	
}