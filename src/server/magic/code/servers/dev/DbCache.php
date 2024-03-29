<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * @author Beck
 * 
 * TODO 
 * 1.delete的方式更改,改为被动式,主动删缓存效率低
 * 
 * XXX 目前此类使用的一些注意事项
 * 1.insert需要写入数据库中的全部字段
 * 2.主意传入的where顺序问题,也会导致索引不一致
 * 3.避免更改索引的值,比如bag_type这种十分不推荐
 * 
 * @version 2.0
 */
class DbCache {
	//db
	protected $db;
	
	//缓存类实例
	protected $cache;
	
	//单例
	protected static $instance;
	
	public $data_list = array();
	public $change_data_list = array();
	public $insert_data_list = array();
	//改变的索引key
	public $change_keys_list = array();
	//缓存key列表
	public $cache_keys_list = array();
	
	//维护table
	private $table_keys = array();
	
	//需要同步的数据
	public $sync_data = array();
	
	public static $register_dbs = array();
	public $role_id;
	
	public $db_config_name;
	
	//内存缓存开关
	/**
	 * 当本机没有配置缓存环境时,可以暂时关闭
	 * @var Boolean
	 */
	const CACHE_ON = 1;
	
	public static $timestamp;
	public $role_token;
	public $role_cas_flg = true;

	/**
	 * Loads the database instance, if the database is not already loaded.
	 *
	 * @return  void
	 */
	public function __construct($db_config_name, $role_id = null)
	{
		if ( ! is_object($this->db))
		{
			// Load the default database
			$this->db = Database::instance($db_config_name);
			array_push(self::$register_dbs, $this);

			$this->role_id = $role_id;
			$this->db_config_name = $db_config_name;
			
			self::$timestamp = PEAR::getStaticProperty('_APP', 'timestamp');
		}
		
		if (1 || self::CACHE_ON) {
			if ($role_id === null) {
				$this->cache = Cache::instance('config_db');
			} else {
				$this->cache = Cache::instance('mc_'.$role_id%Kohana::config('base.cut_memcached_num'));
			}
		}
	}
	
	public static function instance($db_config_name, $role_id = null)
	{
		if (!isset(self::$instance[$db_config_name.$role_id]))
		{
			// Create a new instance
			self::$instance[$db_config_name.$role_id] = new DbCache($db_config_name, $role_id);
		}

		return self::$instance[$db_config_name.$role_id];
	}
	
	/**
	 * 获取数据并放入缓存
	 * 键值索引为表名_where条件
	 * XXX 新版本的where目前只支持role_id或者uid
	 * @param String $table
	 * @param Array $where
	 */
	public function getDataCondition($table, $where = array(), $order_by = array(), $limit = 0)
	{
		$cache_key = $this->getCacheKey($table, $where);

		//先从类属性取
		if (isset($this->cache_keys_list[$cache_key.'_keys'])) {
			$data = array();
			foreach ($this->cache_keys_list[$cache_key.'_keys'] as $c_v) {
				$data[$c_v] = $this->data_list[$c_v];
			}
		}
		
		//再从缓存取
		if (empty($data) && self::CACHE_ON) { 
			if (!empty($this->role_id)) {
				$role_key = 'role_role_id_'.$this->role_id.'_id_'.$this->role_id;
				if ($cache_key == $role_key) {
					$role_data =  $this->cache->getCas($role_key, $this->role_token);
					
					if ($this->cache->getResultCode() == Cache::RES_NOTFOUND) {
						$this->role_cas_flg = false;
					}
				}
			}
			
			$cache_keys = $this->cache->get($cache_key.'_keys');
			if ($cache_keys !== null) {
				$data = $this->cache->getMulti($cache_keys);
			}
			
			//设置类属性
			if (isset($data) && $data !== false) {
				foreach ($data as $key => $vl) {
					if (!isset($this->data_list[$key])) {
						$this->data_list[$key] = $vl;
					} else {
						$data[$key] = $this->data_list[$key];
					}
				}
				$this->cache_keys_list[$cache_key.'_keys'] = $cache_keys;
			} else {
				$data = NULL;
			}
//			//hack当数据为空,但是存储过内存的时候
//			if ($cache_keys === array()) {
//				$data = array();
//			}
		}

		//再从数据库取
		if (!isset($data)) {
			//从数据库中取出数据,直接取出数组XXX等待重构数据结构
			if ($limit == 0) {
				$result = $this->db->where($where)->orderby($order_by)->get($table);
			} else {
				$result = $this->db->where($where)->orderby($order_by)->limit($limit)->get($table);
			}
			
			/**
			 * ===============================================================================
			 * 设置缓存
			 * ===============================================================================
			 */
			$mysql_cache_data = array();
			$mysql_cache_keys = array();
			$timestamp = PEAR::getStaticProperty('_APP', 'timestamp');
			//循环,赋值memcache
			foreach ($result as $key => $vl) {
				//数据, 更新标记, 改变时间戳, extra冗余数据
				$data_tmp = array($vl, 0, $timestamp, array());
				if (isset($where['id'])) {
					$mysql_key = $this->getCacheKeyForIndex($table, $where);
				} else {
					$mysql_key = $this->getCacheKeyForIndex($table, $where).'_id_'.$data_tmp[0]['id'];
				}
				$mysql_cache_data[$mysql_key] = $data_tmp;
				array_push($mysql_cache_keys, $mysql_key);
				
				if (!isset($this->data_list[$mysql_key])) {
					$this->data_list[$mysql_key] = $data_tmp;
				} else {
					$mysql_cache_data[$mysql_key] = $this->data_list[$mysql_key];
				}
			}
			
			/**
			 * XXX TODO 及其丑陋,请更改!!!!
			 */
			if ($table == 'uid_map' && empty($mysql_cache_data)) {
				
			} else {
				$this->cache->setMulti($mysql_cache_data);
				$this->cache->set($cache_key.'_keys', $mysql_cache_keys);
				$this->cache_keys_list[$cache_key.'_keys'] = $mysql_cache_keys;
			}
			
			$data = $mysql_cache_data;
		}
		
		//where方式的索引排查TODO 可以优化
		foreach ($data as $k => $v) {
			$where_ok_flg = true;
			foreach ($where as $key => $vl) {
				//XXX 注意0的判断问题
				if ($v[0][$key] != (string)$vl) {
					$where_ok_flg = false;
				}
			}
			if ($where_ok_flg === false) {
				unset($data[$k]);
			}
		}

		//返回array_iterator迭代器
		$arrayobject = new ArrayObject($data);
		$arrayobject->setIteratorClass('MyArrayIterator');
		$result = $arrayobject->getIterator();
		
		return $result;
	}
	
	/**
	 * //用于统一的索引,比如有需要desk_id的索引的数据,那么就需要在存储的数据中排除此desk_id
	 */
	private function getCacheKeyForIndex($table, $where)
	{
		$cache_key_for_index = null;
		if ($cache_key_for_index === null) {
			$cache_key_for_index = $table;//mysql_field_table($this->result, 0);
			foreach ($where as $key => $vl) {
				if ($key != 'role_id' && $key != 'id') {
					continue;
				}
				$cache_key_for_index .= "_".$key.'_'.$vl;
			}
		}
		
		return $cache_key_for_index;
	}
	
	/**
	 * 获取cache_key, 可以考虑静态缓存
	 * @param unknown_type $table
	 * @param unknown_type $where
	 */
	public function getCacheKey($table, &$where)
	{
		$cache_key = $table;//$this->db->config['table_prefix'].
		
		if ($this->role_id != null && !isset($where['role_id'])) {
			$where = array('role_id' => $this->role_id) + $where;
		}
		
		if (isset($where['role_id'])) {
			$cache_key .= "_".'role_id'.'_'.$where['role_id'];
		}
		if (isset($where['id'])) {
			$cache_key .= "_".'id'.'_'.$where['id'];
		}
		return $cache_key;
	}
	
	/**
	 * 新的封装,推荐使用
	 * @param unknown_type $table
	 * @param unknown_type $where
	 * @param unknown_type $order_by
	 * @param unknown_type $limit
	 */
	public function select($table, $where = array(), $order_by = array(), $limit = 0)
	{
		return $this->getDataCondition($table, $where, $order_by, $limit);
	}
	
	/**
	 * 1 更新
	 * @param String $table
	 * @param Array $data
	 * @param Array $where
	 */
	public function update($table, $data = array(), $where = array(), $increment = false)
	{
		//仅仅是改变标记

		//先取出数据
		$result = $this->select($table, $where);
		$new_data = $result->current();
		
		$old_data = $new_data;
		
		$cache_key = $this->getCacheKey($table, $where); 
		
		$new_data[1] = 1;

		foreach ($data as $key => $vl) {
			if (empty($increment)) {
				$new_data[0][$key] = $vl;
			} else {
				$new_data[0][$key] += $vl;
			}
		}
		
		$cache_key_db = $this->getCacheKeyForDB($table, $where);
		//同步数据
		if ($new_data[2] + Kohana::config('base.sync_time') < self::$timestamp || isset($this->sync_data[$cache_key_db])) {
			$new_data[2] = self::$timestamp;
			
			$db_data = array();
			foreach ($data as $key => $vl) {
				if (empty($increment)) {
					$db_data[$key] = $vl;
				} else {
					$db_data[$key] = $new_data[0][$key];
				}
			}

			//XXX role_id //$db_data + $this->sync_data[$cache_key][1]
			$this->sync_data[$cache_key_db] = array($table, $new_data[0], $where, $this->db_config_name);
		}
		
		//最新的争取支持自动索引TODO
		//更改id的
		$where = array('id' => $new_data[0]['id']);
		$key_cache = $this->getCacheKey($table, $where);
		$this->change_data_list[$key_cache] = $new_data;
		$this->data_list[$key_cache] = $new_data;
	}
	
	public function getCacheKeyForDB($table, $where)
	{
		$cache_key = $table;//$this->db->config['table_prefix'].
		
		if ($this->role_id != null && !isset($where['role_id'])) {
			$where = array('role_id' => $this->role_id) + $where;
		}
		
		foreach ($where as $key => $vl) {
			$cache_key .= "_".$key.'_'.$vl;
		}
		return $cache_key;
	}
	
	/**
	 * 增长
	 * @param unknown_type $table
	 * @param unknown_type $data
	 * @param unknown_type $where
	 * @param unknown_type $increment
	 */
	public function increment($table, $data = array(), $where = array(), $increment = true)
	{
		$this->update($table, $data, $where, $increment);
	}
	
	/**
	 * 2 插入
	 * @param unknown_type $table
	 * @param unknown_type $data
	 */
	public function insert($table, $data = array(), $table_pre)
	{
		if (!isset($data['id'])) {
			$db = Database::instance('basic');
			//同步,需要+key TODO
			$result = $db->query(
							"UPDATE ".
								"seq_id ".
							"SET ".
								"id = LAST_INSERT_ID(id + 1) ".
							"WHERE ".
								"name = '$table_pre'"
					);
			
			$seq_data = $db->query(
							"SELECT ".
								"LAST_INSERT_ID(id) ".
							"FROM ".
								"seq_id ".
							"WHERE ".
								"name = '$table_pre'"
					)->current();
	
			$id =  $seq_data['LAST_INSERT_ID(id)'];
		} else {
			$id = $data['id'];
		}
		//插入数据库
		$data += array('id' => $id);
		$this->db->replace($table, $data);
		
		//改变原始数据
		$where = array('id' => $id);
		$cache_key = $this->getCacheKey($table, $where);
		$data_insert_format = array($data, 0, self::$timestamp, array());
		$this->cache->set($cache_key, $data_insert_format);
		//改类变量中的
		$this->data_list[$cache_key] = $data_insert_format;
		
		//插入内存id索引的
		$where = array('id' => $id);
		$this->insertCacheKeys($table, $where, $id);
		
		//=========================插入table的======================================
		//改变role_id索引的
		if (!empty($this->role_id) ) {
			$where = array('role_id' => $this->role_id);
			$this->insertCacheKeys($table, $where, $id);
		}
		//============================================================================
		
		return $id;
	}
	
	/**
	 * 添加keys
	 * @param $table
	 * @param $where
	 */
	private function insertCacheKeys($table, $where, $id)
	{
		$cache_key = $this->getCacheKey($table, $where);
		//插入缓存keys
		$cache_keys = $this->cache->get($cache_key.'_keys');
		if (empty($cache_keys)) {
			$cache_keys = array();
		}
		
		$where = array('id' => $id);
		$cache_key_id = $this->getCacheKey($table, $where);
		array_push($cache_keys, $cache_key_id);
		
		//可以放入类变量
		if (isset($this->cache_keys_list[$cache_key.'_keys'])) {
			$this->cache_keys_list[$cache_key.'_keys'] = $cache_keys;
		}
		
		$this->cache->set($cache_key.'_keys', $cache_keys);
	}
	
	/**
	 * 3 删除
	 * 目前只支持where[id]
	 * @param unknown_type $table
	 * @param unknown_type $where
	 */
	public function delete($table, $where = array())
	{
		$cache_key = $this->getCacheKey($table, $where);
		
		//删除内存
		$this->cache->delete($cache_key);
		
		//删除类变量中的
		unset($this->data_list[$cache_key]);
		
		//删除keys
		$where_keys = array('id' => $where['id']);
		$this->deleteKeys($table, $where_keys, $where['id']);
		
		if (!empty($this->role_id) ) {
			$where_keys = array('role_id' => $this->role_id);
			$this->deleteKeys($table, $where_keys, $where['id']);
		}
		
		//删除数据库
		$this->db->delete($table, $where);
	}
	
	/**
	 * 删除keys
	 * @param unknown_type $table
	 * @param unknown_type $where
	 */
	private function deleteKeys($table, $where, $id)
	{
		$cache_key = $this->getCacheKey($table, $where);
		
		$cache_keys = $this->cache->get($cache_key.'_keys');
		
		if (!empty($cache_keys)) {
			$where = array('id' => $id);
			$cache_key_id = $this->getCacheKey($table, $where);
			$key_c = array_search($cache_key_id, $cache_keys);
			
			if ($key_c !== false) {
				unset($cache_keys[$key_c]);
			}
			$this->cache->set($cache_key.'_keys', $cache_keys);
		}
		
		if (isset($this->cache_keys_list[$cache_key.'_keys'])) {
			$this->cache_keys_list[$cache_key.'_keys'] = $cache_keys;
		}
	}
	
	/**
	 * 更新内存
	 */
	public static function updates()
	{
		foreach (self::$register_dbs as $vl) {
			$ok = false;
			$try = 5;
			while($try > 0) {
				$role_key = 'role_role_id_'.$vl->role_id.'_id_'.$vl->role_id;
				if (isset($vl->change_data_list[$role_key])) {
					if ($vl->role_cas_flg === true) {
						$vl->cache->cas($vl->role_token, $role_key, $vl->change_data_list[$role_key]);
					} else {
						break;
					}
				}
				
				if ($vl->cache->getResultCode() == Cache::RES_SUCCESS) {
					$ok = true;
					unset($vl->change_data_list[$role_key]);
					break;
				}
				$try--;
			}
			
			$vl->cache->setMulti($vl->change_data_list);
			$vl->cache->setMulti($vl->change_keys_list);
		}
	}
	
	/**
	 * 同步DB
	 * 
	 * 同步方式待改进,比如实现真正的15分钟更新一次db(对于某个人)
	 * 
	 * XXX sync+上role_id
	 */
	public static function sync()
	{
		foreach (self::$register_dbs as $vl) {
			if ($vl->role_id) {
				$sync_data = $vl->sync_data;
				if (is_array($sync_data)) {
					foreach ($sync_data as $sync_vl) {
						$db = Database::instance($sync_vl[3]);
						$db->update($sync_vl[0], $sync_vl[1], $sync_vl[2]);
					}
				}
			}
		}
	}
}