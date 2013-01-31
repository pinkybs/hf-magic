<?php defined('SYSPATH') or die('No direct script access.');
class Unittest_Controller extends Controller {
	public function __construct()
	{
		Event::add_before('system.shutdown', array('Kohana', 'shutdown'), array('GameEvent', 'storyRet'));
		
		Event::add_before('system.shutdown', array('Kohana', 'shutdown'), array('DbCache', 'updates'));
		Event::add_before('system.shutdown', array('Kohana', 'shutdown'), array('DbCache', 'sync'));

		Event::add('system.shutdown', array('Network', 'send'));
		$timestamp = &PEAR::getStaticProperty('_APP', 'timestamp');
		$timestamp = time();
		parent::__construct();
	}
	
	public function cache()
	{
		$test = Cache::instance('mc_0');
		$test = Cache::instance('mc_1');
	}
	
	public function building()
	{
		$building_model = Building_Model::instance(267);
		$newBuilding = array('role_id' => 267,
							 'x'=>6,
							 'y'=>0,
							 'z'=>2,
	                         'mirror'=>0,
	                         'bag_type'=>0);
		$building_model->updateBuildingById($newBuilding,1586);
		
		echo 'change';
	}
	
	public function get()
	{
		$prop = new Profiler;
		$building_model = Building_Model::instance(267);
		$building_list = $building_model->getDataByRoleIdBagType(0);
		
		foreach ($building_list as $vl) {
			if ($vl[0]['id'] == 1586) {
				var_dump($vl[0]);
			}
		}
	}
	
	public function newbie()
	{
		$role = Role::create(Role::getOwnRoleId());
		$newbie = json_decode($role->get('newbie'), true);
		
		$newbie[0][2-1][1] = 1;
		
		$role->set('newbie', json_encode($newbie));
		$role->increment('exp', 2);
		echo Kohana::debug(json_decode($role->get('newbie'), true));
		//var_dump(json_encode($role->get('newbie'), true));
	}
	
	public function exp()
	{
		$role = Role::create(Role::getOwnRoleId());
		$role->increment('exp', 100);
		
		var_dump($role->getData());
	}
	
	public function castest()
	{
	$role_id = Role::getOwnRoleId();
		$role = Role::create($role_id);
		$role->increment('exp', 2);
	}
	
	public function cas()
	{
		$m = new Memcached();
		$m->addServer('127.0.0.1', 11211);
		
		do {
		    /* 获取ip列表以及它的标记 */
		    $ips = $m->get('ip_block', null, $cas);
		    /* 如果列表不存在， 创建并进行一个原子添加（如果其他客户端已经添加， 这里就返回false）*/
		    if ($m->getResultCode() == Memcached::RES_NOTFOUND) {
		        $ips = array($_SERVER['REMOTE_ADDR']);
		        $m->add('ip_block', $ips);
		    /* 其他情况下，添加ip到列表中， 并以cas方式去存储， 这样当其他客户端修改过， 则返回false */
		    } else { 
		        $ips[] = $_SERVER['REMOTE_ADDR'];
		        $m->cas($cas, 'ip_block', $ips);
		    }   
		} while ($m->getResultCode() != Memcached::RES_SUCCESS);
	}
	
	public function student()
	{
		
		$student_model = Student_Task_Model::instance(658);
		
		$timestamp = time();
		$spend_time = 5;
		$event_time = $timestamp;
		$data = array(
			'avatar_id' => 1,
			'start_time' => $timestamp,
			'end_time' => $timestamp + $spend_time,
			'state' => 2,
			'event_time' => $event_time,
			'spend_time' => $spend_time,
			'red' => 5,
			'green' => 5,
			'blue' => 5,
			'stone_time' => $timestamp + $spend_time,
		);
		
		$student_model->update(6515, $data);
		
		$student_model = Student_Task_Model::instance(658);
		$task_data = $student_model->getDataById(6514);
		//var_dump($task_data);
		
		$task_data = $student_model->getDataById(6515);
		//var_dump($task_data);
		
		$students = $student_model->getDataByRoleId();
		
		//var_dump($students);
	}
	
	public function delete()
	{
		//role_event_condition_role_id_658_event_id_702
		$event_con = Event_Condition_Model::instance(658);
		$event_con->delete(2155);
	}
	
	public function testdecor()
	{
		$role_id = Role::getOwnRoleId();
		$role = Role::create($role_id);
		
		//取出此建筑信息
		$decor = Decor::instance($role_id);
		$left_desk = $decor->getEmptyDesk();
		
		$builing_model = Building_Model::instance($role_id);
		$builing_model->updateBuildingById(array('bag_type' => 1), 9735);
	}
	
	public function event()
	{
		$event_list = &PEAR::getStaticProperty('_APP', 'event_list');
		$role_id = Role::getOwnRoleId();
		$game_event = GameEvent::instance($role_id);
		$event_list = $game_event->getRoleEvent();
		
		//echo Kohana::debug($event_list);
		
		Event::add('system.shutdown', array('Network', 'send'));
		
		Event::add_before('system.shutdown', array('Kohana', 'shutdown'), array('GameEvent', 'staticGetRoleEvent'));
		
		//插入一条event
		$game_event->addRoleEvent(27, EventType::TRUNK);
	}
	
	public function avatar()
	{
		$basic_model = new Basic_Model ();
		$avatar_list = $basic_model->getAvatarList();
		
		//$avatar_list = $basic_model->getAvatarListByType(AvatarType::STUDENT);
		var_dump($avatar_list);
	}
	
	public function studenttask()
	{
		$role_id = Role::getOwnRoleId();
		$student_model = Student_Task_Model::instance($role_id);
		$data = array(
						'id' => 10462,
						'avatar_id' => 8301,
						'magic_id' => 2001,
						'event_time' => -1,
						'start_time' => 0,
						'end_time' => 0,
						'state' => StudentState::NOTEACH,
						'spend_time' => 0,
						'role_id' => $role_id,
						'coin' => 0,
						'stone_time' => 0,
		);
		
		$student_model->update(10462, $data);
	}
	
	public function testmem()
	{
		$role = Role::create(Role::getOwnRoleId());
		$role->increment('exp', 1);
	}
}