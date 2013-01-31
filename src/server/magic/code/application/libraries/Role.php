<?php defined('SYSPATH') OR die('No direct access allowed.');
class Role {
	private static $roles = array();
	
	private $id;

	//------各种属性---------------------
	private $exp;
	private $level;
	private $max_exp;
	private $max_mp_add;
	private $mp;
	private $max_mp;
	private $mp_set_time;
	private $mp_recovery_rate_plus;
	private $coin;
	private $gmoney;
	private $major_magic;
	private $deal_level;
	private $trans_type;
	private $trans_start_time;
	private $fiddle_students;
	private $name;
	private $tile_x_length;
	private $tile_z_length;
	private $avatar_id;
	private $cur_scene_id;
	private $popularity;
	private $newbie;
	private $house_level;
	private $story_exp;
	//-----------------------------------
	
	//记录改变的差值
	private $prop_diff_data;
	//记录改变
	private $prop_change_data;
	
	private $change_hp_mina_flg = 0;
	
	//原始数值
	private $origin_data;
	
	//变更后的数据
	private $data;
	
	protected static $own_role_id;
	
	//调试开关
	const DEBUG = 1;
	
	public static function create($role_id)
	{
		if (!isset(self::$roles[$role_id])) {
			self::$roles[$role_id] = new role($role_id);
		}
		
		return self::$roles[$role_id];
	}
	
	public function __construct($role_id)
	{
		$this->role_id = $role_id;
		
		//hack方式,一开始就取出这个玩家的数据
		$this->get('exp');
		
		//按时间恢复气血和精力
		$this->resumeHpMina();
	}
	
	public function get($name)
	{
		//获取属性,如果没有此属性,直接返回false
		$class_vars = get_class_vars(get_class($this));
		if (!array_key_exists($name, $class_vars)) {
			return 0;
		}
		
		//当此值已经有了时候直接返回
		if ($this->$name !== null) {
			if (self::DEBUG) {
				//echo "class var";
			}
			return $this->$name;
		}
		
		$role_model = Role_Model::instance($this->role_id);
		$data = $role_model->getData();

		if (empty($data)) {
			return 0;
		}

		//原始数据
		$this->data = $data;
		$this->origin_data = $data;
		
		foreach ($data as $key => $vl) {
			$this->$key = $vl;
			
			//---------------这里是额外的加成属性-------------------------------------
			if ($key == 'max_mp') {
				$this->max_mp += $data['max_mp_add'];
				$this->data['max_mp'] = $this->max_mp;
			}
			//----------------------------------------------------------------------
		}
		
		return $this->$name;
	}
	
	public function cacheDataReset()
	{
		
	}
	
	/**
	 * 有待优化,实际上当非玩家消耗,或者主动增加mp.只是单纯的时间恢复可以不去更改数据库数据的
	 */
	public function resumeHpMina()
	{
		$mp = $this->get('mp');
		
		$resume_mp = json_decode(common::basic('mp_recovery'));
		$resume_mp_time = $resume_mp[0];
		$resume_mp_rate = $resume_mp[1]/100;
		
		$mp_set_time = $this->get('mp_set_time');
		$max_mp = $this->get('max_mp');
		
		//取出当前时间
		$timestamp = PEAR::getStaticProperty('_APP', 'timestamp');
		
		if ($mp_set_time + $resume_mp_time <= $timestamp) {
			$rate = floor(($timestamp - $mp_set_time)/$resume_mp_time);
			//进一取整
			$this->increment('mp', ceil($max_mp*$resume_mp_rate)*$rate);
			$this->set('mp_set_time', $timestamp - ($timestamp - $mp_set_time)%$resume_mp_time);
		}
		
		$this->change_hp_mina_flg = 0;
	}
	
	public function set($name, $vl) 
	{
		if (!isset($this->$name)) {
			$this->get($name);
		}
		
		if ($this->$name == $vl) {
			return;
		}
		
		$this->$name = $vl;
		$this->data[$name] = $this->$name;

		//记录变化的值
		$role_model = Role_Model::instance($this->role_id);
		$role_model->update($name, $vl);
	}
	
	/**
	 * 返回所有数据,待优化TODO
	 */
	public function getData()
	{
		$this->get('name');

		$data = $this->data;
		
		return $data;
	}
	
	public function getOriginData()
	{
		$this->get('name');
		
		$data = $this->origin_data;
		
		return $data;
	}
	
	public function increment($name, $add = 1)
	{	
		//体力精力,处理
		if ($name == 'hp' || $name == 'mp') {
			$hm = $this->get('max_'.$name);
			if ($this->$name + $add >= $hm) {
				$add = $hm - $this->$name;
			}
		}
		
		//判断是否<0
		if ($this->$name + $add < 0 || $add == 0) {
			return;
		}
		
		
		$this->$name = $this->$name + $add;
		$this->data[$name] = $this->$name;
		
		if ($name == 'max_mp_add') {
			$this->max_mp += $add;
			$this->data['max_mp'] = $this->max_mp;
		}
		
		//记录增量变化的值
		/**
		if (isset($this->data[$name])) {
			$this->data[$name] += $add;
			
			if ($name == 'hp' || $name == 'mp') {
				$this->change_hp_mina_flg = 1;
			}
		}
		*/
		
		/*
		 * 房间升级
		 */
		if ($name == 'max_mp' || $name == 'max_mp_add') {
			$cur_level_data = Basic::getHouseLevelData($this->get('house_level'));
			
			$max_mp = $this->get('max_mp');
			if ($max_mp >= $cur_level_data['mp']) {
				//house level up
				$this->increment('house_level');
				
				//扩展桌子和学生数量，直接取了

				//gift
				//升级赠送
				$this->increment('gmoney', $cur_level_data['gem']);
				$this->increment('coin', $cur_level_data['coin']);
		    	//奖励道具
		    	$items = json_decode($cur_level_data['items']);
		    	$item = Item::instance($this->role_id);
		    	$item->addItems($items);
		    	
		    	//奖励装饰
		    	$decors = json_decode($cur_level_data['decors']);
		    	$decor = Decor::instance($this->role_id);
		    	$decor->addDecors($decors);
		    	
		    	//学生自动解锁
				//根据等级取出学生数据
				$basic_model = new Basic_Model();
				$student_level_conf = $basic_model->getStudentListByLevel($this->get('house_level'));
				
				$student_model = Student_Model::instance($this->role_id);
				foreach ($student_level_conf as $vl) {
					//插入
					$student_model->insert(
						array(
							'role_id' => $this->role_id,
							'student_id' => $vl[0]['id'],
							'exp' => 0,
							'level' => 1,
							'award_flg' => 0,
							'student_state' => 0,
						)
					);
				}
			}
		}
		
		/*
		 * 升级处理
		 */
		if ($name == 'exp') {
			if ($this->get($name) >= $this->get('max_'.$name)) {
				//记录升级时剩余的经验
				//$left_exp = $this->get($name) - $this->get('max_'.$name);
				
				//升级
				$this->increment('level');
				
				$level_config = usual::getLevelConfig($this->get('level'));
				
				$this->set('exp', $this->$name);
				$this->set('max_exp', $level_config['exp']);
				//$this->set('max_mp', $level_config['max_mp']);
				$this->increment('max_mp', $level_config['max_mp']-($this->get('max_mp') - $this->get('max_mp_add')));
				
				//升级满气血
				$this->set('mp', $level_config['max_mp']);
				//unset($this->prop_diff_data[$name]);
				
				$level_config = usual::getLevelConfig($this->get('level') - 1);
				
				//升级赠送
				$this->increment('gmoney', $level_config['levelup_gmoney']);
				$this->increment('coin', $level_config['coin']);
		    	//奖励道具
		    	$items = json_decode($level_config['levelup_item']);
		    	$item = Item::instance($this->role_id);
		    	$item->addItems($items);
		    	
		    	//奖励装饰
		    	$decors = json_decode($level_config['levelup_decors']);
		    	$decor = Decor::instance($this->role_id);
		    	$decor->addDecors($decors);
		    	
		    	//扩充场景
		    	//$this->set('tile_x_length', $level_config['tile_size']);
		    	//$this->set('tile_z_length', $level_config['tile_size']);
				
				//升级返回场景数据
				//$decor = Decor::instance($this->role_id);
				//$scene_data = $decor->getSceneData();
				
				//Network::buffer('levelupScene', $scene_data);

				return;
			}
		}
		
		$role_model = Role_Model::instance($this->role_id);
		$role_model->update($name, $this->$name);
	}
	
	/**
	 * 更新所有变更的
	 * 话说这里应该在更新时加上where条件防止并发
	 */
	public function update()
	{
		if ($this->change_hp_mina_flg === 0) {
			unset($this->prop_diff_data['hp']);
			unset($this->prop_diff_data['mina']);
			unset($this->prop_diff_data['hp_mina_set_time']);
			unset($this->prop_change_data['hp']);
			unset($this->prop_change_data['mina']);
			unset($this->prop_change_data['hp_mina_set_time']);
		}
		
		$update_str = '';
		if (!empty($this->prop_change_data)) {
			foreach ($this->prop_change_data as $key => $vl) {
				$update_str .= ' '.$key.' = "'.$vl.'",';  
			}
		}
		
		if (!empty($this->prop_diff_data)) {
			foreach ($this->prop_diff_data as $key => $vl) {
				$update_str .= ' '.$key." = GREATEST(0, $key+".$vl.'),';  
			}
		}

		if (!empty($update_str)) {
			$update_str = substr($update_str, 0, -1);

			$db = Database::instance('default');
			$sql = 'UPDATE '.$db->table_prefix().'role_'.$this->role_id % Kohana::config('base.cut_table_num')
			.' SET '.$update_str.' where role_id = '.$this->role_id;
			//echo $sql;
			$result = $db->query($sql);

			//Kohana::log('error', var_export($sql, 1));
			//如果只是切换场景,则不清除缓存,只是更新
			if (empty($this->prop_diff_data) && (isset($this->prop_change_data['at_scene_id']) || isset($this->prop_change_data['at_role_id'])) 
				&& count($this->prop_change_data) == 1) {
				//取出原始数据
				$role_model = new Role_Model($this->role_id);
				$data = $role_model->getData();
				
				$data['at_scene_id'] = $this->at_scene_id;
				$data['at_role_id'] = $this->at_role_id;
				$db_cache = DbCache::instance();
				$db_cache->setDataCondition('role_'.$this->role_id % Kohana::config('base.cut_table_num'), 
							array($data), array('role_id' => $this->role_id));
				return;
			}
			
			//清空缓存
			$db_cache = DbCache::instance();
			$db_cache->removeTable($this->role_id, 'role_'.$this->role_id % Kohana::config('base.cut_table_num'));
/**
			if ($this->role_id == '225457568') {
				Kohana::log('error', var_export($this->prop_change_data, 1));
				Kohana::log('error', var_export($this->prop_diff_data, 1));
				Kohana::log('error', var_export('role_'.$this->role_id % Kohana::config('base.cut_table_num'), 1));
			} 
*/
		}
	}
	
	public static function updates()
	{
		foreach (self::$roles as $role) {
			$role->update();
		}
	}
	
	/**
	 * 返回当前玩家的自己的role_id
	 */
	public static function getOwnRoleId()
	{
		if (!isset(self::$own_role_id)) {
			$session = Session::instance();
			self::$own_role_id = $session->get('role_id');

			if (empty(self::$own_role_id)) {
				self::$own_role_id = 48;
			}
		}
		return self::$own_role_id;
	}
	
	public static function getAtRoleId()
	{
		$role_id = Role::getOwnRoleId();
		//判断时间.默认2小时
		$role = Role::create($role_id);
		
		return $role->get('at_role_id');
	}
	
	public static function getRoleFormat($role_id)
	{
		static $user_data = null;
		
		if (!empty($user_data)) {
			return $user_data['user'];
		}
		
		$role = Role::create($role_id);
		$role_data = $role->getData();

		$user_data = common::transform('user', $role_data);
		
		$timestamp = PEAR::getStaticProperty('_APP', 'timestamp');
		$endtime = Magic::getTransEndTime($role_id);

		$user_data['user']['trans_time'] = $endtime - $timestamp;
		
		if ($user_data['user']['trans_time'] <= 0) {
			$user_data['user']['trans_time'] = 0;
		}
		
		$resume_mp = json_decode(common::basic('mp_recovery'));
		$resume_mp_time = $resume_mp[0];
		
		$user_data['user']['replyMp_time'] = $user_data['user']['replyMp_time'] + $resume_mp_time - $timestamp;
		$user_data['user']['replyMpPer'] = $resume_mp[1];
		$user_data['user']['replyMpTime'] = $resume_mp_time;
		
		return $user_data['user'];
	}
}