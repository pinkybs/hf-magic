<?php
class Uid_Map_Model
{
	protected $db;
	private $uid;
	private $table_pre = 'uid_map';
	
	protected $db_cache;
	private $table;
	
	/**
	 * Loads the database instance, if the database is not already loaded.
	 *
	 * @return  void
	 */
	public function __construct($uid, $role_id = null)
	{
		if ( ! is_object($this->db))
		{
			$this->uid = $uid;
			
			$this->table = common::getTableName($this->table_pre, $this->uid);
			
			$db_config_name = '';
			// Load the default database
			if (Kohana::config('base.cut_database_num_by_platform_id') == 0) {
				$db_config_name = 'map';
				$this->db = Database::instance($db_config_name);
			} else {
				$db_config_name = 'map_'.$uid % Kohana::config('base.cut_database_num_by_platform_id');
				$this->db = Database::instance($db_config_name);
			}
			
			$this->db_cache = DbCache::instance($db_config_name, $role_id);
		}
	}
	
	/**
	 * 获取uid
	 */
	public function getRoleIdByUid()
	{
		$result = $this->db->select('*')->where(array('uid' => $this->uid))->get($this->table);
		if (!$result->count()) {
			return array();
		}
		$data = array($result->current());
		return $data;
	}
	
	public function insertData($uid, $role_id)
	{
		$this->db_cache->insert($this->table, array('uid' => $uid, 'role_id' => $role_id, 'id' => $role_id), $this->table_pre);
	}
	
	public function delete($id)
	{
		$this->db_cache->delete($this->table, array('id' => $id));
	}
}