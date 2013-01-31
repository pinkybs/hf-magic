<?php
class Building_Model extends Model_Core
{
	protected $table_pre = 'role_building';

	public function getDataByRoleId()
	{
		$result = $this->db_cache->getDataCondition($this->table, array('role_id' => $this->role_id, 'bag_type' => 0));
		
		if (!$result->count()) {
			return array();
		}
		return $result;
	}
	
	/**
	 * 
	 * @param 建筑id $id
	 */
	public function getDataById($id)
	{
		$result = $this->db_cache->select($this->table, array('id' => $id));
		
		if (!$result->count()) {
			return array();
		}
		return current($result->current());
	}
	
	public function getDataByRoleIdType($type = 2)
	{
		$result = $this->db_cache->select($this->table, array('role_id' => $this->role_id, 
						'building_type' => $type, 'bag_type' => 0));
		
		return $result;
	}

	public function getDataByRoleIdBagType($bag_type = 1)
	{
		$result = $this->db_cache->select($this->table, array('role_id' => $this->role_id, 'bag_type' => $bag_type));
		
		return $result;
	}
	
/***************************************************************************/
	
	public function insertBuilding($data)
	{
		$id = $this->db_cache->insert($this->table, $data, 'role_building');
		return $id;
	}
	
	public function insertById($building_id)
	{
		$basic_model = new Basic_Model();
		$buildingBasic = $basic_model->getBuildingDataById($building_id);
		
		$newBuilding = array('role_id' => $this->role_id,
							 'building_id' => $building_id,
							 'building_type' => $buildingBasic['type'],
							 'effect_mp' => $buildingBasic['effect_mp'],
							 'x' => 0,
							 'y' => 0,
							 'z' => 0,
							 'mirror' => 0,
							 'bag_type' => 1);
		
		$id = $this->insertBuilding($newBuilding);
		Network::bufferAdd('addDecorBag', array($id, 1, $building_id));
	}
	
	public function updateBuildingById($data, $id)
	{
		$this->db_cache->update($this->table, $data, array('id' => $id));
	}
	
	public function deleteBuildingById($id)
	{
		//取出
		$data = $this->getDataById($id);
		
		$this->db_cache->delete($this->table, array('id' => $id));
		Network::bufferAdd('removeDecorBag', array($id, 1, $data['building_id']));
	}
	
	public function getUserBuildingInBagByBid($bid)
	{
		$result = $this->db_cache->select($this->table, array('role_id' => $this->role_id, 'building_id' => $bid, 'bag_type' => 1));
	
		if (!$result->count()) {
			return array();
		}
		return $result;
	}
	
	public function getUserBuildingInBagByBidCount($bid)
	{
		$result = $this->db_cache->select($this->table, array('role_id' => $this->role_id, 'building_id' => $bid, 'bag_type' => 1));
	
		if (!$result->count()) {
			return 0;
		}
		return $result->count();
	}
	
	public function getUserBuildingList()
	{
		$result = $this->db_cache->getDataCondition($this->table, array('role_id' => $this->role_id));
		
		if (!$result->count()) {
			return array();
		}
		return $result;
	}
	
}