<?php
class Role_Model extends Model_Core
{
	protected $table_pre = 'role';
	
	public function getData()
	{
		$result = $this->db_cache->getDataCondition($this->table, array('id' => $this->role_id));
		
		return current($result->current());
	}
	
	public function getAllData()
	{
		$result = $this->db_cache->getDataCondition($this->table, array());
		
		return $result;
	}
	
	public function update($key, $vl)
	{
		$result = $this->db_cache->update($this->table, array($key => $vl), array('id' => $this->role_id));
		
		return $result;
	}
}