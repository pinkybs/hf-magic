<?php defined ( 'SYSPATH' ) or die ( 'No direct access allowed.' );
/**
 * 格式如下
 *  $str = "1/type:5;id:606;num:5;
	2/type:2;id:606;num:5;";
	var_dump(arr::_multi_serial_to_array($str));
 * @author Beck
 *
 */
class GameEvent extends SingleBase {
    protected static $instace = null;
    
    public function __construct($role_id)
    {
    	parent::__construct($role_id);
    }
    
    public static function storyRet()
    {
    	//取出玩家故事经验
    	$role_id = Role::getOwnRoleId();
    	$role = Role::create($role_id);
    	$story_exp = $role->get('story_exp');
    	$role_exp = $role->get('exp');
    	
    	$basic_model = new Basic_Model();
    	$story_data = $basic_model->getStoryByExp($story_exp, $role_exp);

    	if (!empty($story_data)) {
    		$role->set('story_exp', $story_data['exp']);
    		
    		//取出这个故事的情节
    		$story_talk = $basic_model->getStoryTalkListByStoryId($story_data['id']);
    		
    		$actions = common::transform('actions', $story_talk);
    		Network::buffer('story', $actions);
    	}
    }
    
    public static function staticGetRoleEvent()
    {
    	$old_event_list = PEAR::getStaticProperty('_APP', 'event_list');

    	$role_id = Role::getOwnRoleId();
		$event = GameEvent::instance($role_id);
		$event_list = $event->getRoleEvent();

		$new_event_list = array();
		foreach ($old_event_list as $vl) {
			$new_event_list[$vl['t_id']] = $vl;
		}
		
		$ret_events = array();
		foreach ($event_list as $vl) {
			if (isset($new_event_list[$vl['t_id']])) {
				if ($vl['state'] === 1 && $vl['state'] !== $new_event_list[$vl['t_id']]['state']) {
					array_push($ret_events, $vl);
				} else {
					if ($vl['fc_curNums'] != $new_event_list[$vl['t_id']]['fc_curNums']) {
						array_push($ret_events, $vl);
					}
				}
			} else {
				array_push($ret_events, $vl);
			}
		}
		
		if (!empty($ret_events)) {
			Network::buffer('changeTasks', $ret_events);
		}
    }
    
    /**
     * 取出此用户所有的event
     */
    public function getRoleEvent()
    {
    	$role = Role::create($this->role_id);
    	
    	$event_model = Event_Model::instance($this->role_id);
    	$event_data = $event_model->getDataByRoleId();
    	if (empty($event_data)) {
    		return array();
    	}
    	$trunk = json_decode($event_data['trunk']);
    	$branch = json_decode($event_data['branch']);
    	$daily = json_decode($event_data['daily']);
    	$newbie = json_decode($event_data['newbie']);

    	$events = array_merge($newbie, $trunk, $branch, $daily);

    	//取出条件
    	$event_con_model = Event_Condition_Model::instance($this->role_id);
    	$event_con_data = $event_con_model->getEventConditionByRoleId();
		//echo Kohana::debug($event_con_data);
    	$ret_event = $this->retEveneFormat($event_con_data, $events);
    	return $ret_event;
    }
    
    /**
     * 判断某event是否完成
     * @param $event_id
     */
    public function judgeOkByEventId($event_id)
    {
    	//取出条件
    	$event_con_model = Event_Condition_Model::instance($this->role_id);
    	$event_con_data = $event_con_model->getEventConditionByRoleIdEventId($event_id);
    	
    	$ret_event = $this->retEveneFormat($event_con_data, array($event_id));
    	
    	return $ret_event;
    }
    
    private function retEveneFormat($event_con_data, $events, $ret_event = array())
    {
    	$role = Role::create($this->role_id);
    	
        $event_con_format = array();
    	$event_state = array();
    	foreach ($event_con_data as $vl) {
    		//echo Kohana::debug($vl);
    		$vl = current($vl);
    		if (isset($event_con_format[$vl['event_id']])) {
    			$event_con_format[$vl['event_id']] += array($vl['num']);
    		} else {
    			$event_con_format[$vl['event_id']] = array($vl['num']);
    		}
    		
    		if ($vl['num'] >= $vl['target_num']) {
    			if (isset($event_state[$vl['event_id']]) && $event_state[$vl['event_id']] === 0) {
    				$event_state[$vl['event_id']] = 0;
    			} else {
    				$event_state[$vl['event_id']] = 1;
    			}
    		} else {
    			$event_state[$vl['event_id']] = 0;
    		}
    	}
    	
    	//条件2,特殊情况判断
    	foreach ($events as $event_id) {
    		if (!isset($event_state[$event_id]) || (isset($event_state[$event_id]) && $event_state[$event_id] === 1)) {
    			$basic_event_data = $this->getRoleEventBasic($event_id);
    			$condition2 = arr::_multi_serial_to_array($basic_event_data['condition2']);
    			
    			foreach ($condition2 as $vl) {
    				if ($vl['type'] == EventConditionType::GIVE_ITEMS) {
    					//取出此种道具
    					$item_model = Item_Model::instance($this->role_id);
    					$item_data = $item_model->getUserItem($vl['id']);
    					
			    		if ($item_data['count'] >= $vl['num']) {
			    			if (isset($event_state[$event_id]) && $event_state[$event_id] === 0) {
			    				$event_state[$event_id] = 0;
			    			} else {
			    				$event_state[$event_id] = 1;
			    			}
			    		} else {
			    			$event_state[$event_id] = 0;
			    		}
    				} else if ($vl['type'] == EventConditionType::GIVE_BUILDINGS) {
    					//建筑物,装饰物,除了地板和墙纸
    				    $building_model = Building_Model::instance($this->role_id);
    					$building_nums = $building_model->getUserBuildingInBagByBidCount($vl['id']);
    					
			    		if ($building_nums >= $vl['num']) {
			    			if (isset($event_state[$event_id]) && $event_state[$event_id] === 0) {
			    				$event_state[$event_id] = 0;
			    			} else {
			    				$event_state[$event_id] = 1;
			    			}
			    		} else {
			    			$event_state[$event_id] = 0;
			    		}
    				}
    			}
    		}
    	}

    	foreach ($events as $event_id) {
    		//用户属性
    		if (!isset($event_state[$event_id]) || (isset($event_state[$event_id]) && $event_state[$event_id] === 1)) {
    			//当没有任务条件或者任务条件已经完成的情况下判断用户属性
    			$basic_event_data = $this->getRoleEventBasic($event_id);

    			$prop_condition = arr::_multi_serial_to_array($basic_event_data['prop_condition']);
    			
    			foreach ($prop_condition as $vl) {
    				foreach ($vl as $k => $v) {
    					if (eval('return '.$role->get($k)."$v;")) {
    						if (isset($event_state[$event_id]) && $event_state[$event_id] === 0) {
    							$event_state[$event_id] = 0;
    						} else {
    							$event_state[$event_id] = 1;
    						}
    					} else {
    						$event_state[$event_id] = 0;
    					}
    				}
    			}
    		}
    		
    		if (!isset($event_con_format[$event_id])) {
    			$event_con_format[$event_id] = array();
    		}
    		
    		if (!isset($event_state[$event_id])) {
    			$event_state[$event_id] = 1;
    		}
    		
    		array_push($ret_event, array(
    			't_id' => $event_id,
    			'fc_curNums' => $event_con_format[$event_id],
    			'state' => $event_state[$event_id],
    		));
    	}
    	
    	return $ret_event;
    }
    
    public function getEventBasicData($event_id, $event_type)
    {
    	$basic_model = new Basic_Model();
        if ($event_type == EventType::TRUNK) {
    		$event_data = $basic_model->getEventTrunkById($event_id);
    	} elseif ($event_type == EventType::BRANCH) {
    		$event_data = $basic_model->getEventBranchById($event_id);
    	} elseif ($event_type == EventType::DAILY) {
    		$event_data = $basic_model->getEventDailyById($event_id);
    	} elseif ($event_type == EventType::NEWBIE) {
    		$event_data = $basic_model->getEventNewbieById($event_id);
    	}
    
    	return $event_data;
    }
    
    /**
     * 
     * @param $event_id
     */
    public function getRoleEventBasic($event_id) 
    {
    	$event_model = Event_Model::instance($this->role_id);
    	$event_intance_data = $event_model->getDataByRoleId();
    	
    	$trunk = json_decode($event_intance_data['trunk']);
    	$branch = json_decode($event_intance_data['branch']);
    	$daily = json_decode($event_intance_data['daily']);
    	$newbie = json_decode($event_intance_data['newbie']);
    	
    	if (in_array($event_id, $trunk)) {
    		$event_type = EventType::TRUNK;
    		$event_array = $trunk;
    	} elseif (in_array($event_id, $branch)) {
    		$event_type = EventType::BRANCH;
    	} elseif (in_array($event_id, $daily)) {
    		$event_type = EventType::DAILY;
    	} elseif (in_array($event_id, $newbie)) {
    		$event_type = EventType::NEWBIE;
    	}
		
		$event_basic_data = $this->getEventBasicData($event_id, $event_type);
		return $event_basic_data;
    }
    
    /**
     * 
     * @param $event_id
     * @param $event_type
     */
    public function addRoleEvent($event_id, $event_type = 1)
    {
    	$event_model = Event_Model::instance($this->role_id);
    	$event_intance_data = $event_model->getDataByRoleId();
    	if ($event_type == EventType::TRUNK) {
    		$basic_model = new Basic_Model();
    		$event_data = $basic_model->getEventTrunkById($event_id);
    		
    		$trunk = json_decode($event_intance_data['trunk']);
    		array_push($trunk, $event_id);
    		$event_model->update($this->role_id, array('trunk' => json_encode($trunk)));
    	} elseif ($event_type == EventType::BRANCH) {
    		$basic_model = new Basic_Model();
    		$event_data = $basic_model->getEventBranchById($event_id);
    		
    		$branch = json_decode($event_intance_data['branch']);
    		array_push($branch, $event_id);
    		$event_model->update($this->role_id, array('branch' => json_encode($branch)));
    	} elseif ($event_type == EventType::DAILY) {
    		$basic_model = new Basic_Model();
    		$event_data = $basic_model->getEventDailyById($event_id);
    		
    		$daily = json_decode($event_intance_data['daily']);
    		array_push($daily, $event_id);
    		$event_model->update($this->role_id, array('daily' => json_encode($daily)));
    	} elseif ($event_type == EventType::NEWBIE) {
    		
    	}
    	
    	//插入条件
    	$event_condition = arr::_multi_serial_to_array($event_data['condition']);
    	$event_con_model = Event_Condition_Model::instance($this->role_id);
    	foreach ($event_condition as $vl) {
    		//判断任务类型,取出相应的材料数量TODO
    		
    		$data = array(
    			'type' => $vl['type'],
    			'num' => 0,
    			'target_num' => $vl['num'],
    			'type_id' => $vl['id'],
    			'event_id' => $event_data['id'],
    			'role_id' => $this->role_id,
    		);
    		
    		$event_con_model->insert($data);
    	}
    }
    
    public function removeRoleEvent($event_id)
    {
    	//删除event
    	
    	//删除role_event_condition
    	$event_condition_model = Event_Condition_Model::instance($this->role_id);
    	$event_condition_model->delete($event_id);
    }
    
    /**
     * 奖励
     * @param $event_id
     */
    public function award($event_id)
    {
    	$event_basic_data = $this->getRoleEventBasic($event_id);
    	//奖励属性
    	$props = arr::_serial_to_array($event_basic_data['award_prop']);
    	$role = Role::create($this->role_id);
    	foreach ($props as $key => $vl) {
    		$role->increment($key, $vl);
    	}
    	
    	//奖励道具
    	$items = json_decode($event_basic_data['award_items']);
    	$item = Item::instance($this->role_id);
    	$item->addItems($items);
    	
    	//奖励装饰
    	$decors = json_decode($event_basic_data['award_decors']);
    	$decor = Decor::instance($this->role_id);
    	$decor->addDecors($decors);
    }
    
    public function addBranchEvent()
    {
    	
    }
    
    /**
     * 
     * @param $type
     */
    public static function processRoleEvent($type, $id, $change_num, $role_id = null)
    {
    	if (empty($role_id)) {
    		$role_id = Role::getOwnRoleId();
    	}
    	
    	//取出
    	$event_con_model = Event_Condition_Model::instance($role_id);
    	$event_con_data = $event_con_model->getEventConditionByTypeId($type, $id);
    	
    	if (empty($event_con_data)) {
    		return false;
    	}
    	
    	$event_ids = array();
    	$change_flag = false;
    	//TODO 减少数量的情况
    	foreach ($event_con_data as $vl) {
    		$vl = $vl[0];
    		if ($change_num >= 0) {
	    		if ($vl['num'] < $vl['target_num']) {
	    			//更改
	    			$event_con_model->update($vl['id'], $change_num);
	    			$change_flag = true;
	    		}
    		} else {
    			if ($vl['num'] > 0) {
	    			//变更
	    			$event_con_model->update($vl['id'], $change_num);
	    			$change_flag = true;
	    		}
    		}
    		
    		if (!in_array($vl['event_id'], $event_ids)) {
    			array_push($event_ids, $vl['event_id']);
    		}
    	}
    	
    	if ($change_flag === true) {
    		//self::returnTaskVo($event_ids, $type, $id, $role_id);
    	}
    }
    
    public static function returnTaskVo($event_ids, $type, $id, $role_id = null)
    {
        if (empty($role_id)) {
    		$role_id = Role::getOwnRoleId();
    	}
    	
        //取出条件
    	$event_con_model = Event_Condition_Model::instance($role_id);
    	//XXX 传递所有的条件,判断消耗大量资源
    	$event_con_data = $event_con_model->getEventConditionByTypeId($type, $id);

    	$tasks = Network::get('changeTasks');
    	$game_event = GameEvent::instance($role_id);
    	
    	$ret_event = $game_event->retEveneFormat($event_con_data, $event_ids, $tasks);
    	
    	Network::buffer('changeTasks', $ret_event);
    }
    
    /**
     * 支线日常任务处理
     */
	public static function initBranchDailyEvent()
	{
		$role_id = Role::getOwnRoleId();
		$role = Role::create($role_id);
		
		$data = $role->getOriginData();
		
		$role_exp = $role->get('exp');
		if ($data['exp'] != $role_exp) {
			//取出玩家任务数据
	    	$event_model = Event_Model::instance($role_id);
	    	$event_data = $event_model->getDataByRoleId();
	    	$branch = json_decode($event_data['branch']);
			
			//处理支线任务
			$basic_model = new Basic_Model();
			$branch_event_data = $basic_model->getEventBranchExpList($event_data['branch_exp']);
			foreach ($branch_event_data as $vl) {
				$vl = current($vl);
				if ($role_exp < $vl['exp']) {
					break;
				}
				
				$game_event = GameEvent::instance($role_id);
				$game_event->addRoleEvent($vl['id'], EventType::BRANCH);
				
				//设置lastexp
				$event_model->update($role_id, array('branch_exp' => $vl['exp']));
			}
		}
		
		$role_level = $role->get('level');
		if ($data['level'] != $role_level) {
			if (empty($event_data)) {
		    	$event_model = Event_Model::instance($role_id);
		    	$event_data = $event_model->getDataByRoleId();
		    	$daily = json_decode($event_data['daily']);
			}
			
			//处理日常任务
			$basic_model = new Basic_Model();
			$daily_event_data = $basic_model->getEventDailyLevelList($role_level);
			foreach ($daily_event_data as $vl) {
				$vl = current($vl);
				$game_event = GameEvent::instance($role_id);
				$game_event->addRoleEvent($vl['id'], EventType::DAILY);
				
				//$event_model->update($role_id, array('daily_level' => $vl['level']));
			}
		}
	}
}