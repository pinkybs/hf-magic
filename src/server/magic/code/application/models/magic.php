<?php
class Magic_Model extends Model_Core
{
	protected $table_pre = 'role_magic';
	
	public function getDataByRoleId()
	{
		$result = $this->db_cache->select($this->table, array('role_id' => $this->role_id));
		
		if (!$result->count()) {
			return array();
		}
		return current($result->current());
	}
	
	/******************************************/
	
	/*
	 * update user magic
	 * 
	 * @param array $data
	 * @return void
	 */
	public function updateUserMagic($data)
	{
		$this->db_cache->update($this->table, $data, array('role_id' => $this->role_id));
	}
	
	
}