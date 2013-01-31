<?php defined ( 'SYSPATH' ) or die ( 'No direct access allowed.' );
class script {
	public static function removeEvent($event_id)
	{
		$role_id = Role::getOwnRoleId();
		
		$rolevent_model = new Rolevent_Model($role_id);
		$rolevent_model->removeDataByRoleIdEventId($event_id);
	}
	
	/**
	 * 获得称号
	 * @param unknown_type $title_id
	 */
	public function prizeTitle($title_id)
	{
		$role_id = Role::getOwnRoleId();
		$role = Role::create($role_id);
		//取出此title
		$role_title_model = new Role_Title_Model($role_id);
		
		$usual_model = new Usual_Model();
		$title_data = $usual_model->getTitleByID($title_id);
		
		$level = $role->get('level');
		$grade = $role->get('grade');
		
		if ($level + 5 > $grade*10) {
			$term = 2;
		} else {
			$term = 1;
		}
		
		//插入title
		$data = array(
			'role_id' => $role_id,
			'role_name' => $role->get('name'),
			'title_id' => $title_id,
			'title_name' => $title_data['name'],
			'grade' => $grade,
			'term' => $term,
		);
		$role_title_model->insertData($data);
		
		common::prize('title', array('key' => $title_id, 'name' => $title_data['name'], 'num' => 1));
	}
	
	public static function setHomeworkPrizeTime()
	{
		$timestamp = PEAR::getStaticProperty('_APP', 'timestamp');
		$date = date("Ymd", $timestamp);
		
		$role_id = Role::getOwnRoleId();
		$role = Role::create($role_id);
		$role->set('homework_prize_time', $date);
	}
	
	/*
	 * 零花钱
	 */
	public static function setPinMoneyTime()
	{
		$timestamp = PEAR::getStaticProperty('_APP', 'timestamp');
		$date = date("Ymd", $timestamp);
		
		$role_id = Role::getOwnRoleId();
		$role = Role::create($role_id);
		$role->set('pin_money_time', $date);
	}
	/**
	 * 升年级
	 */
	public static function upgrade()
	{
		$role_id = Role::getOwnRoleId();
		$role = Role::create($role_id);
		$role->increment('grade');
	}
	
	/**
	 * 添加下一条事件脚本
	 * @param unknown_type $event_id
	 */
	public static function addEvent($event_id)
	{
		$role_id = Role::getOwnRoleId();
		$game_event = GameEvent::instance($role_id);
		
		$game_event->addRoleEvent($event_id);
		
		$ret = array(
				'key' => 'add_event',
				'event_id' => $event_id,
		);
		
		return $ret;
	}
	
	/**
	 * 什么也不做
	 */
	public static function nothing()
	{
		return;
	}
	
	/**
	 * 增加玩家属性
	 */
	public function addProp($key, $vl)
	{
		$role_id = Role::getOwnRoleId();
		$role = Role::create($role_id);
		$role->increment($key, $vl);

		common::prize('role_prop', array('key' => $key, 'num' => $vl));
	}
	
	/**
	 * 强制删除事件
	 * @param unknown_type $event_id
	 */
	public static function removeRoleEvent($event_id) 
	{
		$role_id = Role::getOwnRoleId();
		
		$role_event_model = new Rolevent_Model($role_id);
		$role_event_model->removeDataByRoleIdEventId($event_id);		
	}
	
	public static function giveBuildingItems($building_id)
	{
		$role_id = Role::getOwnRoleId();
		
		$role_building_item_data = array();
		
		//判断时间.默认2小时
		$role = Role::create($role_id);
		$last_building_item = $role->get('last_building_item');
		
		$base_config = Kohana::config_load('base');
		$refresh_building_item_time = $base_config['refresh_building_item_time'];
		
		//获取当前时间
		$timestamp = PEAR::getStaticProperty('_APP', 'timestamp');

		//XXX BUG 一个场景生成了,则另外的场景无法继续生成,可以考虑缩短时间到15分钟
		if ($timestamp > $last_building_item + $refresh_building_item_time) {
			//重新生成//获取item通过building_id
			$item_model = new Item_Model();
			$building_item_data = $item_model->getItemByBuildingId($building_id);
			
			//当前之掉落一个
			$data = common::random($building_item_data);
			
			if (empty($data)) {
				return $role_building_item_data;
			}

			//放入背包
			$role_item = RoleItem::instance($role_id);
			$role_item->putItemToPackage($data['item_id'], $data['num']);
			
			common::prize('item', array('key' => $data['item_id'], 'num' => $data['num']));
			
			//更改building_timeXXX时间并未改变
			$role->set('last_building_item', $timestamp);
			$role_building_item_data = $data;
		}
		$ret = array(
				'key' => 'giveBuildingItems',
				'data' => $role_building_item_data,
		);
		
		return $ret;
	}
	
	/**
	 * 增加好感度脚本
	 * @param unknown_type $npc_id
	 * @param unknown_type $num
	 */
	public static function addFavor($npc_id, $num)
	{
		$role_id = Role::getOwnRoleId();
		
		$role_npc_model = new Role_Npc_Model($role_id);
		$npc_data = $role_npc_model->getData($npc_id);
		
		if (empty($npc_data)) {
			if ($num < 0) {
				$num = 0;
			}
			
			//没有数据则插入
			$data = array(
				'role_id' => $role_id,
				'npc_id' => $npc_id,
				'favor' => $num,
			);
			$role_npc_model->insertData($data);
		} else {
			$favor = $npc_data['favor'] + $num;
			if ($favor < 0) {
				$favor = 0;
			}
			//有数据则更新
			$role_npc_model->updateFavor($npc_data['id'], $favor);
		}
		
		$ret = array(
				'key' => 'favor',
				'npc_id' => $npc_id,
				'num' => $num,
		);
		
		return $ret;
	}
}