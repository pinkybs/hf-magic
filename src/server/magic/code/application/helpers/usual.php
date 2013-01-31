<?php defined ( 'SYSPATH' ) or die ( 'No direct access allowed.' );
class usual {
	public static function removeEvent($event_id)
	{
		$role_id = Role::getOwnRoleId();
		
		$rolevent_model = new Rolevent_Model($role_id);
		$rolevent_model->removeDataByRoleIdEventId($event_id);
	}
	
    /**
     * 可以防止多个item数据冲突
     * @param Integer $item_id
     */
    public static function getPlanDataById($plan_id)
    {
    	static $plan_data = array();
		//这里可能也会有多个item的问题
		if (!isset($plan_data[$plan_id])) {
    		$plan_model = new Plan_Model();
			$plan_data[$plan_id] = $plan_model->getPlanById($plan_id);
    	}
    	
    	return $plan_data[$plan_id];
    }
    
    /**
     * 返回当前等级
     * 
     * @param unknown_type $level
     */
    public static function getLevelConfig($level = null)
    {
    	static $role_config = array();
    	
    	if (empty($level)) {
    		$role = Role::create(Role::getOwnRoleId());
    		$level = $role->get('level');
    	}
    	
		//这里可能也会有多个item的问题
		if (!isset($role_config[$level])) {
			$role_config[$level] = Basic::getCurLevelData($level);
    	}
    	//var_dump($role_config[$level]);
    	return $role_config[$level];
    }
    
	public static function updateEventRecord($type, $id, $num = 1)
	{
		$role_id = Role::getOwnRoleId();
		//查找event_record表,如果有此种物品则同时增加,目前是如果有多个相同的则同时算
		//FIXME 有bug,现在完全没有判断value的where原始值,但问题不大
		$event_record_type = GameEvent::getEventRecordTypeId($type);
		$event_record_model = new Event_Record_Model($role_id);
		$event_record_model->updateRecordValue($event_record_type, $id, $num);
	}
	
	/**
	 * buff效果
	 * @param unknown_type $type
	 */
	public static function getBuffByType($type)
	{
		$role_id = Role::getOwnRoleId();
		$role_buff_model = new Role_Buff_Model($role_id);
		$buff_data =  $role_buff_model->getDataByType($type);
		
		if (empty($buff_data)) {
			return array();
		}
		
		$buff_effcet = arr::_serial_to_array($buff_data['effect']);
		return $buff_effcet[$type];
	}
	
	/**
	 * 实际buff效果
	 * @param unknown_type $msg
	 * @param unknown_type $fid
	 */
	public static function buffEffect(&$vl, $type)
	{
		$item_buff = usual::getBuffByType($type);
		
		if (!empty($item_buff)) {
			$vl = eval('return '.$vl.$item_buff.';');
		}
		
		return $vl;
	}
	
	public static function addNotice($msg, $fid)
	{
		$cache = new Cache(Kohana::config('cache.notice_tt'));
		$timestamp = PEAR::getStaticProperty('_APP', 'timestamp');

		$cache_key = 'role_notice_'.$fid;
		$notice_data = $cache->get($cache_key);
		
		if (is_array($notice_data)) {
			foreach ($notice_data as $key => $vl) {
				if ($key >= 20) {
					unset($notice_data[$key]);
				}
			}
		} else {
			$notice_data = array();
		}
		
		$msg_new = array('m' => $msg, 't' => $timestamp);
		array_unshift($notice_data, $msg_new);
		
		$cache->set($cache_key, $notice_data);
		$role = Role::create($fid); 
		//更新标记
		$role->set('notice_flg', 1);
	}
	
	public static function getNotice($fid)
	{
		$cache = new Cache(Kohana::config('cache.notice_tt'));
		
		$cache_key = 'role_notice_'.$fid;
		$notice_data = $cache->get($cache_key);
		
		return $notice_data;
	}
}