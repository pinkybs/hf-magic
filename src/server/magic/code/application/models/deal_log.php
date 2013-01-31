<?php
class Deal_Log_Model extends Model_Core
{
	protected $table_pre = 'role_deal_log';
	
	public function getUserActorNewDeal()
	{
		$result = $this->db_cache->select($this->table, array('actor' => $this->role_id, 'status' => 1));
		
		if (!$result->count()) {
			return array();
		}
		return $result;
	}

	public function getUserTargetNewDeal()
	{
		$result = $this->db_cache->select($this->table, array('role_id' => $this->role_id, 'status' => 1));
		
		if (!$result->count()) {
			return array();
		}
		return $result;
	}

	public function insertDealLog($data)
	{
		$this->db_cache->insert($this->table, $data, 'role_deal_log');
	}
	
	public function updateDealLogById($data, $id)
	{
		$this->db_cache->update($this->table, $data, array('id' => $id));
	}
	

	
}