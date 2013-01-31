<?php
class Scene_Model extends Model_Core
{
	protected $table_pre = 'role_scene';
	
	public function getData()
	{
		$result = $this->db_cache->select($this->table, array('id' => $this->role_id));
		
		if (!$result->count()) {
			return array();
		}
		return current($result->current());
	}
}