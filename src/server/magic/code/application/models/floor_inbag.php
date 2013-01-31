<?php
class Floor_Inbag_Model extends Model_Core
{
	protected $table_pre = 'role_floor_inbag';

	/*
	 * floor
	 * 
	 */
	public function getUserFloorInBag()
	{
		$result = $this->db_cache->select($this->table, array('role_id' => $this->role_id));
		if (!$result->count()) {
			return array();
		}
		return $result;
	}
	
	public function getUserFloorInBagByFid($fid)
	{
		$result = $this->db_cache->select($this->table, array('role_id' => $this->role_id, 'floor_id' => $fid));
		
		if (!$result->count()) {
			return array();
		}
		return current($result->current());
	}
	
	public function insertUserFloorInBag($data)
	{
		$this->db_cache->insert($this->table, $data, 'role_floor_inbag');
	}
	
	public function updateUserFloorInBagByField($role_id, $fid, $changeArray)
	{
		$this->db_cache->increment($this->table, $changeArray, array('role_id' => $role_id, 'floor_id' => $fid));
	}

	public function addUserFloorInBag($data)
	{
		$result = $this->db_cache->select($this->table, array('role_id' => $data['role_id'], 'floor_id' => $data['floor_id']));
		if (!$result->count()) {
			$id = $this->db_cache->insert($this->table, $data, 'role_floor_inbag');
		}
		else {
			$this->db_cache->increment($this->table, array('quantity' => $data['quantity']), array('role_id' => $data['role_id'], 'floor_id' => $data['floor_id']));
			$floor = current($result->current());
			$id = $floor['id'];
		}
		
		//返回
		if ($data['quantity'] > 0) {
			Network::bufferAdd('addDecorBag', array($id, $data['quantity'], $data['floor_id']));
		} else {
			Network::bufferAdd('removeDecorBag', array($id, $data['quantity'], $data['wall_id']));
		}
		
		return array('id' => $id);
	}
	
}