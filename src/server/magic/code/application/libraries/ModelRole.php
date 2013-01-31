<?php defined('SYSPATH') OR die('No direct access allowed.');
class ModelRole extends Model_Core
{
	protected $table_name;
	
	public function getTableName($role_id)
	{
		$table_name = $this->table_name.'_'.$role_id % Kohana::config('base.cut_table_num');
		//$table_name = $this->table_name;
		return $table_name;
	}
	
	public function insertData($data)
	{
		//$data['role_id'] = $this->role_id;
		
		//重置缓存
		$db_cache = DbCache::instance();
		$db_cache->removeTable($this->role_id, $this->table);
		
		$this->db->insert($this->table, $data);
	}
	
	/**
	 * 删除数据
	 * @param unknown_type $id
	 */
	public function removeData($id)
	{
		$this->db->delete($this->table, array('id' => $id));
		
		//重置缓存
		$db_cache = DbCache::instance();
		$db_cache->removeTable($this->role_id, $this->table);
	}
}