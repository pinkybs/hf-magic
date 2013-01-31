<?php defined ( 'SYSPATH' ) or die ( 'No direct access allowed.' );
class Scene_Controller extends Role_Controller {
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * 打怪
	 */
	public function killmonster()
	{
		$enemy_id = $this->input->post('enemyId');
		$f_id = Role::getOwnRoleId();
		
    	$own_role_id = Role::getOwnRoleId();
    	$role_id = $own_role_id;

    	//是否在好友家
    	$is_in_friends_home = false;
    	if ($own_role_id != $f_id) {
    		//在好友家
    		$is_in_friends_home = true;
    		$role_id = $f_id;
    	}
    	
    	$role = Role::create($role_id);
    	$cur_scene_id = $role->get('cur_scene_id');
    	if ($is_in_friends_home === true) {
    		$cache_key = 'monster_role_id_'.$own_role_id.'friends_id_'.$role_id;
    	} else {
    		$cache_key = 'monster_role_id_'.$role_id.'_'.$cur_scene_id;
    	}
    	
    	$cache = Cache::instance('monster');
    	$monsters = $cache->get($cache_key);
    	
    	if (empty($monsters)) {
    		Network::buffer_error('no_this_id');
    	}
		
		//清除这个怪物
		$monsters[$enemy_id]['flg'] = false;
		
		$cache->set($cache_key, $monsters);
		
		//取出此怪物的静态数据
		$basic_model = new Basic_Model();
		$monster_static_data = $basic_model->getMonsterDataById($monsters[$enemy_id]['eid']);
		
		//消耗mp
		$role = Role::create($own_role_id);
		$role->increment('mp', -$monster_static_data['mp']);
		
		//获得奖励
		$role->increment('coin', $monster_static_data['coin']);
		
		Network::buffer('result', common::result());
	}
	
	/**
	 * 切换场景
	 */
	public function change()
	{
		$scene_id = $this->input->post('sceneId');
		
		//取出此条数据
		$basic_model = new Basic_Model();
		$scene_static_data = $basic_model->getSceneDataById($scene_id);
		
		$role_id = Role::getOwnRoleId();
		$role = Role::create($role_id);
		
		//取出玩家开启的场景
		$scene_model = Scene_Model::instance($role_id);
		$scene_data = $scene_model->getData();
		
		if (empty($scene_data)) {
			Network::buffer_error('scene_id_error');
		}
		
		$scenes = json_decode($scene_data['scenes']);
		
		//判断mp
		if (!in_array($scene_id, $scenes)) {
			Network::buffer_error('scene_id_not_open');
		}
		
		if ($role->get('mp') < $scene_static_data['mp']) {
			Network::buffer_error('mp_not_enough');
		} 
		
		$role->increment('mp', -$scene_static_data['mp']);
		$role->set('cur_scene_id', $scene_id);
		
		//返回result
		Network::buffer('result', common::result());
	}
	
	/**
	 * 解锁场景
	 */
	public function unlock()
	{
		$post = new Validation($_POST);
		$post->add_rules('sceneId', 'required', 'numeric');
		$post->add_rules('type', 'required', 'numeric');
		
		if(!$post->validate())
		{
		   Network::buffer_error(Kohana::lang('base.valid_error'));
		}
		
		$scene_id = $this->input->post('sceneId');
		$type = $this->input->post('type');
		
		//取出此条数据
		$basic_model = new Basic_Model();
		$scene_static_data = $basic_model->getSceneDataById($scene_id);
		
		$role_id = Role::getOwnRoleId();
		$role = Role::create($role_id);
		
		//取出玩家开启的场景
		$scene_model = Scene_Model::instance($role_id);
		$scene_data = $scene_model->getData();
		
		$scenes = json_decode($scene_data['scenes']);
		if (in_array($scene_id, $scenes)) {
			Network::buffer_error('scene_id_opened');
		}
		
		$condition = $scene_static_data['condition'.$type];
		$conditions = arr::_multi_serial_to_array($condition);
		
		//判断道具和玩家属性是否足够
		$item_model = Item_Model::instance($role_id);
		foreach ($conditions as $key => $vl) {
			if ($vl['cat'] == 1) {
				//道具
				$item_data = $item_model->getUserItem($vl['id']); 
				if (empty($item_data)) {
					Network::buffer_error('condition_error');
				} else {
					//XXX 删除
					$item_model->incrementUserItem($role_id, $vl['id'], array('count' => -$vl['num']));
				}
			} elseif ($vl['cat'] == 3) {
				//玩家属性
				if ($role->get($vl['id']) < $vl['num']) {
					Network::buffer_error('condition_error');
				} else {
					$role->increment($vl['id'], -$vl['num']);
				}
			}
		}
		
		//将此场景写入
		array_push($scenes, $scene_id);
		
		$scene_model->update($role_id, array('scenes' => json_encode($scenes)));
		
		//返回result
		Network::buffer('result', common::result());
	}
	
	/**
	 * 扩展场景
	 */
	public function resize()
	{
		$post = new Validation($_POST);
		$post->add_rules('id', 'required', 'numeric');
		$post->add_rules('type', 'required', 'numeric');
		
		$id = $this->input->post('id');
		$type = $this->input->post('type');
		
		if(!$post->validate())
		{
		   Network::buffer_error(Kohana::lang('base.valid_error'));
		}
		
		$role_id = Role::getOwnRoleId();
		$role = Role::create($role_id);
		
		$magic_type = $role->get('major_magic');
		if ($magic_type == 1) {
			$crystal_name = 'red';
		} elseif ($magic_type == 2) {
			$crystal_name = 'blue';
		} elseif ($magic_type == 3) {
			$crystal_name = 'green';
		}
		$crystal_name = 'coin';
		
		$basic_model = new Basic_Model();
		$size_data = $basic_model->getSizeSceneById($id);
		
		$level = $role->get('house_level');
		//判断等级
		if ($level < $size_data['level']) {
			Network::buffer_error('level_not_ok');
		}
		
		//判断金币
		if ($type == 1) {
			//游戏币
			if ($role->get('coin') < $size_data['coin']) {
				Network::buffer_error('crystal_not_enough');
			}
		} elseif ($type == 2) {
			//金币
			if ($role->get('gmoney') < $size_data['money']) {
				Network::buffer_error('money_not_enough');
			}
		} else {
			Network::buffer_error('type_not_ok');
		}

		$role->set('tile_x_length', $size_data['size']);
    	$role->set('tile_z_length', $size_data['size']);
    	
		//初始化数据
		$basic_model = new Basic_Model();
		$init_data = $basic_model->getInitDataById();
		
		//减去金币
		if ($type == 1) {
			//游戏币
			$role->increment('coin', -$size_data['coin']);
		} else {
			//金币
			$role->increment('gmoney', -$size_data['money']);
		}
		
		//================改变地板和墙====================================================
		//插入floor表
		$floor_model = Floor_Model::instance($role_id);
		$floor_old_data = $floor_model->getDataByRoleId();
		$floor_format = json_decode($floor_old_data['data'], true);
		for ($j = 0; $j < $size_data['size']-1; $j++) {
			$floor_format[(int)$size_data['size']-2][$j] = $init_data['floor'];
			$floor_format[$j][(int)$size_data['size']-2] = $init_data['floor'];
		}
		$floor_data = array(
			'id' => $role_id,
			'role_id' => $role_id,
			'data' => json_encode($floor_format),
		);
		$floor_model->update($role_id, $floor_data);
		
		//插入墙壁表
		$wall_model = Wall_Model::instance($role_id);
		$wall_old_data = $wall_model->getDataByRoleId();
		$wall_format = json_decode($wall_old_data['data'], true);
		
		$wall_format[0][(int)$size_data['size']-2] = $init_data['wall'];
		$wall_format[1][(int)$size_data['size']-2] = $init_data['wall'];
		
		$wall_data = array(
			'id' => $role_id,
			'role_id' => $role_id,
			'data' => json_encode($wall_format),
		);
		$wall_model->update($role_id, $wall_data);
		//=================================================================================
    	
		//升级返回场景数据
		$decor = Decor::instance($role_id);
		$scene_data = $decor->getSceneData();
		
		Network::buffer('levelupScene', $scene_data);
		
		Network::buffer('result', common::result());
	}
}