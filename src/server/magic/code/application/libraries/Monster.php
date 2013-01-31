<?php defined ( 'SYSPATH' ) or die ( 'No direct access allowed.' );
class Monster extends SingleBase {
    protected static $instace = null;
    
    public function __construct($role_id)
    {
    	parent::__construct($role_id);
    }
    
    public function getMonster()
    {
    	//return array(array('enemyId' => 1, 'enemyCid' => 7006), array('enemyId' => 2, 'enemyCid' => 7006));
    	$cache = Cache::instance('monster');
    	
    	$own_role_id = Role::getOwnRoleId();
    	
    	//是否在好友家
    	$is_in_friends_home = false;
    	if ($own_role_id != $this->role_id) {
    		//在好友家
    		$is_in_friends_home = true;
    	}
    	$role_id = $this->role_id;
    	
    	$role = Role::create($role_id);
    	
    	$cur_scene_id = $role->get('cur_scene_id');
    	
    	//取出所在场景数据
    	$basic_model = new Basic_Model();
    	$scene_static_data = $basic_model->getSceneDataById($cur_scene_id);
    	//var_dump($scene_static_data['monster_xy']);
    	
    	//刷新时间
    	$monster_refresh_time = common::basic('friends_monster_num');
    	
    	//取出静态monster数据
    	$monster_static_data = $basic_model->getMonsterListBySceneId($cur_scene_id);
    	if (empty($monster_static_data)) {
    		return array('enemys' => array());
    	}
    	$timestamp = PEAR::getStaticProperty('_APP', 'timestamp');
    	//取出此用户的怪物数据
    	$cache = Cache::instance('monster');
    	if ($is_in_friends_home === false) {
    		//自己家
    		$cache_key = 'monster_role_id_'.$role_id.'_'.$cur_scene_id;
    		
    		$monsters = $this->setMonsters($cache_key, $scene_static_data, $monster_static_data);
    	} else {
    		//好友家
			$cache_key = 'monster_role_id_'.$own_role_id.'friends_id_'.$role_id;
    		
			$monsters = $this->setMonsters($cache_key, $scene_static_data, $monster_static_data);
    	}
    	
    	$monster_format = array();
    	foreach ($monsters as $key => $value) {
    		if ($value['flg'] == true) {
    			array_push($monster_format, array($value));
    		}
    	}
    	
    	$monster_data = common::transform('enemys', $monster_format);
    	
    	return $monster_data;
    }
    
    private function setMonsters($cache_key, &$scene_static_data, &$monster_static_data)
    {
    	$timestamp = PEAR::getStaticProperty('_APP', 'timestamp');
    	$cache = Cache::instance('monster');
    	$monsters = $cache->get($cache_key);
        if (empty($monsters)) {
    		//插入
    		$i = 0;
    		$monsters = array();
    		$monster_xy = json_decode($scene_static_data['monster_xy']);
    		foreach ($monster_xy as $vl) {
    			$edata = arr::_array_rand($monster_static_data);
    			$monster = array(
    				'id' => $i,//mt_rand().'_'.$i,
    				'eid' =>  $edata['id'],
    				't' => $timestamp,
    				'flg' => true,//是否活着
    			);
    			$monsters[$i] = $monster;
    			$i++;
    		}
    		$cache->set($cache_key, $monsters);
    	} else {
    		$change = false;
    		foreach ($monsters as $key => $vl) {
    			if ($vl['t'] + common::basic('monster_refresh_time') < $timestamp && $vl['flg'] === false) {
    				$edata = arr::_array_rand($monster_static_data);
    				$monster = array(
    					'id' => $vl['id'],
    					'eid' =>  $edata['id'],
    					't' => $timestamp,
    					'flg' => true,//是否活着
    				);
    				$monsters[$key] = $monster;
    				$change = true;
    			}
    		}
    		if ($change === true) {
    			$cache->set($cache_key, $monsters);
    		}
    	}
    	
    	return $monsters;
    }
}