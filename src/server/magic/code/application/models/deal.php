<?php
class Deal_Model extends Model_Core
{
	protected $table_pre = 'role_deal';
	
	public function getUserDeal()
	{
		$result = $this->db_cache->select($this->table, array('role_id' => $this->role_id));
		
		if (!$result->count()) {
			return array();
		}
		return current($result->current());
	}

	public function insertDeal($data)
	{
		$this->db_cache->insert($this->table, $data, 'role_deal');
	}

	public function updateUserDeal($data)
	{
		$this->db_cache->update($this->table, $data, array('role_id' => $this->role_id));
	}
	
	
}