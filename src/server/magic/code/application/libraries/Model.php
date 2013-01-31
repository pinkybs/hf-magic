<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Model base class.
 *
 * $Id: Model.php 3769 2008-12-15 00:48:56Z zombor $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Model_Core {

	protected $db;
	protected $role_id;
	protected $db_cache;
	protected $table;
	
	protected static $instance;

	/**
	 * Loads the database instance, if the database is not already loaded.
	 *
	 * @return  void
	 */
	public function __construct($role_id)
	{
		if ( ! is_object($this->db))
		{
			$this->role_id = $role_id;
			
			$this->table = common::getTableName($this->table_pre, $this->role_id);
			
			$db_config_name = '';
			// Load the default database
			if (Kohana::config('base.cut_database_num') == 0) {
				$db_config_name = 'main';
				$this->db = Database::instance($db_config_name);
			} else {
				$db_config_name = 'main_'.$role_id % Kohana::config('base.cut_database_num');
				$this->db = Database::instance($db_config_name);
			}
			
			$this->db_cache = DbCache::instance($db_config_name, $this->role_id);
		}
	}
	
	public static function instance($role_id)
	{
		$class_name = get_called_class();
		if (!isset(self::$instance[$class_name.'_'.$role_id]))
		{
			// Create a new instance
			self::$instance[$class_name.'_'.$role_id] = new $class_name($role_id);
		}

		return self::$instance[$class_name.'_'.$role_id];
	}
	
	public function insert($data)
	{
		$insert_id = $this->db_cache->insert($this->table, $data, $this->table_pre);
		
		return $insert_id;
	}
	
	public function update($id, $data)
	{
		$this->db_cache->update($this->table, $data, array('id' => $id));
	}
	
	public function delete($id)
	{
		$this->db_cache->delete($this->table, array('id' => $id));
	}

} // End Model