<?php defined ( 'SYSPATH' ) or die ( 'No direct access allowed.' );
class Event_Controller extends Role_Controller {
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * 领奖
	 */
	public function award()
	{
		$event_id = $this->input->post('t_id');
		//$event_type = $this->input->post('event_type');
		
		$role_id = Role::getOwnRoleId();
		//判断是否存在
    	$event_model = Event_Model::instance($role_id);
    	$event_intance_data = $event_model->getDataByRoleId();
    	
    	$trunk = json_decode($event_intance_data['trunk']);
    	$branch = json_decode($event_intance_data['branch']);
    	$daily = json_decode($event_intance_data['daily']);
    	$newbie = json_decode($event_intance_data['newbie']);
    
    	if (in_array($event_id, $trunk)) {
    		$event_type = EventType::TRUNK;
    		$event_array = $trunk;
    		$event_type_str = 'trunk';
    	} elseif (in_array($event_id, $branch)) {
    		$event_type = EventType::BRANCH;
    		$event_array = $branch;
    		$event_type_str = 'branch';
    	} elseif (in_array($event_id, $daily)) {
    		$event_type = EventType::DAILY;
    		$event_array = $daily;
    		$event_type_str = 'daily';
    	} elseif (in_array($event_id, $newbie)) {
    		$event_type = EventType::NEWBIE;
    		$event_array = $newbie;
    		$event_type_str = 'newbie';
    	} else {
    		Network::buffer_error('no_this_event');
    	}
		
		$game_event = GameEvent::instance($role_id);
		$event_basic_data = $game_event->getEventBasicData($event_id, $event_type);
		
		//判断玩家身上是否有此任务
		
		//判断玩家是不是完成了TODO
		$ret_event = $game_event->judgeOkByEventId($event_id);
		if (empty($ret_event)) {
			Network::buffer_error('no_this_event');
		}
		
		if ($ret_event[0]['state'] === 0) {
			Network::buffer_error('event_not_finished');
		}
		
		//发奖励
		$game_event->award($event_id);
		
		/**
		if ($event_type == EventType::TRUNK) {
			
		} elseif ($event_type == EventType::BRANCH) {
			
		} elseif ($event_type == EventType::DAILY) {
			
		} elseif ($event_type == EventType::NEWBIE) {
			
		}
		*/
		//删除任务
		$index = array_search($event_id, $event_array);
		unset($event_array[$index]);
		$event_model->update($role_id, array(
			$event_type_str => json_encode(array_values($event_array)),
		));
		$game_event->removeRoleEvent($event_id);
		
		//如果是主线任务,插入子任务
		if ($event_type == EventType::TRUNK) {
			$game_event->addRoleEvent($event_basic_data['child_id'], $event_type);
		}
		
		//判断新手任务是否全部完成,插入第一条主线
		if ($event_type == EventType::NEWBIE) {
			//判断是否为空
			if (empty($event_array)) {
				//XXX 写死了
				$game_event->addRoleEvent(11, EventType::TRUNK);
			}
		}
		
		Network::buffer('result', common::result());
	}
}