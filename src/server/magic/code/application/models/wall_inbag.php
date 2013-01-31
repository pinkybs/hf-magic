<?php
class Wall_Inbag_Model extends Model_Core
{
	protected $table_pre = 'role_wall_inbag';

	/*
	 * wall
	 * 
	 */
	public function getUserWallInBag()
	{
		$result = $this->db_cache->select($this->table, array('role_id' => $this->role_id));
		if (!$result->count()) {
			return array();
		}
		return $result;
	}
	
	public function getUserWallInBagById($wallId)
	{
		$result = $this->db_cache->select($this->table, array('role_id' => $this->role_id, 'wall_id' => $wallId));
		if (!$result->count()) {
			return array();
		}
		return current($result->current());
	}
	
	
	public function insertUserWallInBag($data)
	{
		$this->db_cache->insert($this->table, $data, 'role_wall_inbag');
	}
	
	public function updateUserWallInBagByField($role_id, $fid, $changeArray)
	{
		$this->db_cache->increment($this->table, $changeArray, array('role_id' => $role_id, 'wall_id' => $fid));
	}

	public function addUserWallInBag($data)
	{
		$result = $this->db_cache->select($this->table, array('role_id' => $data['role_id'], 'wall_id' => $data['wall_id']));
		if (!$result->count()) {
			$id = $this->db_cache->insert($this->table, $data, 'role_wall_inbag');
		}
		else {
			$this->db_cache->increment($this->table, array('quantity' => $data['quantity']), array('role_id' => $data['role_id'], 'wall_id' => $data['wall_id']));
			$wall = current($result->current());
			$id = $wall['id'];
		}
		
		//返回
		if ($data['quantity'] > 0) {
			Network::bufferAdd('addDecorBag', array($id, $data['quantity'], $data['wall_id']));
		} else {
			Network::bufferAdd('removeDecorBag', array($id, $data['quantity'], $data['wall_id']));
		}
		
		return array('id' => $id);
	}
}