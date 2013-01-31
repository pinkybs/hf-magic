<?php
class Item_Model extends Model_Core
{
	protected $table_pre = 'role_item';

	public function getUserItem($bid)
	{
		$result = $this->db_cache->select($this->table, array('role_id' => $this->role_id, 'item_id' => $bid));
	
		if (!$result->count()) {
			return array();
		}
		return current($result->current());
	}
	
	public function getRoleItems()
	{
		$result = $this->db_cache->select($this->table, array('role_id' => $this->role_id));
		
		if (!$result->count()) {
			return array();
		}
		
		return $result;
	}
	
	public function incrementUserItem($role_id, $item_id, $changeArray)
	{
		$itemData = self::getUserItem($item_id);
		if ( empty($itemData) ) {
			$newItem = array('role_id' => $this->role_id, 'item_id' => $item_id, 'count' => $changeArray['count']
			,'today_use_count'=> 0,	'last_use_time' => 0);
			$id = $this->db_cache->insert($this->table, $newItem, 'role_item');
		}
		else {
			$this->db_cache->increment($this->table, $changeArray, array('role_id' => $role_id, 'item_id' => $item_id));
			$id = 0;
		}
		
		if ($changeArray['count'] > 0) {
			Network::bufferAdd('addItem', array($item_id, $changeArray['count'], $id));
		} else {
			Network::bufferAdd('removeItems', array($item_id, abs($changeArray['count']), $id));
		}
	}

	public function updateUserItem($role_id, $item_id, $changeArray)
	{
		$itemData = self::getUserItem($item_id);
		if ( empty($itemData) ) {
			$newItem = array('role_id' => $this->role_id, 'item_id' => $item_id, 'count' => $changeArray['count']
			,'today_use_count'=> 0,	'last_use_time' => 0);
			$this->db_cache->insert($this->table, $newItem, 'role_item');
		}
		else {
			$this->db_cache->update($this->table, $changeArray, array('role_id' => $role_id, 'item_id' => $item_id));
		}
	}
	
	
}