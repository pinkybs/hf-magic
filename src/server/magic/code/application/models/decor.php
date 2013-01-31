<?php
class Decor_Model extends Model_Core
{
	protected $table_pre = 'role_decor';
	
	public function getDecorByRoleId()
	{
		$result = $this->db_cache->select($this->table, array('role_id' => $this->role_id));
		
		return $result;
	}
	
	public function update($id, $data)
	{
		$result = $this->db_cache->update($this->table, $data, array('id' => $id));
		
		return $result;
	}
}