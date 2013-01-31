<?php defined ( 'SYSPATH' ) or die ( 'No direct access allowed.' );
/**
 * mac test
 * Enter description here ...
 * @author beck
 *
 */
class Init_Controller extends Role_Controller {
	public function __construct()
	{
		parent::__construct();
	}
	
	public function test()
	{
		$role = Role::create(Role::getOwnRoleId());
		//$role->increment('exp', 1);
		//GameEvent::processRoleEvent(EventConditionType::TEACH_MAGIC_TIMES, 0, 1);
		
		//测试memcached
		$cache = new Cache('friends_memcache');
		
		$cache->set('test1', array('dfssdf'=> 'dfsdfs'));
		$dd = $cache->get('test1');
		$cache->setMutil('test1', array('dfssdf'=> 'dfsdfs'));
		var_dump($dd);
		
		$cache = new Cache('feed_cache');
		
		$cache->set('test1', array('dfssdf'=> 'dfsdfs'));
		$dd = $cache->get('test1');
		var_dump($dd);

		$cache->set('test1', array('dfssdf'=> 'dfsdfs'));
	}
	
	public function benchmark()
	{
		$prop = new Profiler;
		$starttime = microtime(true);
		$this->index();
		echo microtime(true) - $starttime;
	}
	
	public function newbie()
	{
		$gid = $this->input->post('gid');
		
		$post = new Validation($_POST);
		$post->add_rules('gid', 'required', 'numeric');
		if(!$post->validate())
		{
		   Network::buffer_error(Kohana::lang('base.valid_error'));
		}
		
		$role_id = Role::getOwnRoleId();
		$role = Role::create($role_id);
		
		$newbie = json_decode($role->get('newbie'), true);
		
		$newbie[0][$gid-1][1] = 2;
		
		$role->set('newbie', json_encode($newbie));
		
		//完成奖励,判断是否都完成了TODO
		$newbie_finish_flg = 1;
		$i = 0; 
		foreach ($newbie[0] as $key => $vl) {
			$newbie_data[$key] = array('gid' => $vl[0], 'state' => $vl[1]);
			
			if ($vl[1] == 1) {
				$newbie_finish_flg = 0;
			}
			$i++;
		}
		
		if ($newbie_finish_flg === 1) {
			//取出静态数据
			$basic_model = new Basic_Model();
			$newbie_static_data = $basic_model->getNewbieDataById($i);

			//奖励道具
	    	$items = json_decode($newbie_static_data['items']);

	    	$item = Item::instance($role_id);
	    	$item->addItems($items);
	    	
			//奖励人民币
			$role->increment('gmoney', $newbie_static_data['gem']);
		}
		
		//TODO 加一个领奖标记,防止刷
		//Network::buffer('newbie', $role->get('newbie'));
		
		Network::buffer('result', common::result());
	}
	
	public function index()
	{
		$role_id = Role::getOwnRoleId();

		$role = Role::create($role_id);
		
		//此用户场景信息
		$decor = Decor::instance($role_id);
		$scene_data = $decor->getSceneData();
		
		Network::buffer('scene', $scene_data);
		
		//用户信息
		$user_data = Role::getRoleFormat($role_id);
		Network::buffer('userInfo', $user_data);
		
		//学会的魔法列表
		$magic_model = Magic_Model::instance($role_id);
		$role_magic_data = $magic_model->getDataByRoleId($role_id);
		Network::buffer('magics', json_decode($role_magic_data['study_ids']));
		Network::buffer('transMagics', json_decode($role_magic_data['trans_ids']));
		
		//道具
		$item_model = Item_Model::instance($role_id);
		$item_list = $item_model->getRoleItems();
		$items = array();
		foreach ($item_list as $vl) {
			$vl = current($vl);
			if (empty($vl['count'])) {
				continue;
			}
			array_push($items, array((int)$vl['item_id'], (int)$vl['count'], (int)$vl['id']));
		}
		Network::buffer('items', $items);
		
		//任务
		$event = GameEvent::instance($role_id);
		$event_list = $event->getRoleEvent();
		Network::buffer('tasks', $event_list);
		
		//交易
//		$deal = Deal::instance($role_id);
//        $deal_list = $deal->getSwitchVo($role_id);
//        Network::buffer('switchVo', $deal_list);
        
		$decor = Decor::instance($role_id);
		$decor_list_format = $decor->getBag();
		Network::buffer('decorBagList', $decor_list_format);
		
		//日志
		$pageSize1= 50;
		$pageSize2= 50;
		$pageSize3= 50;
		$feed = Feed::instance($role_id);
        $diarys = $feed->readFeed($role_id, $pageSize1, $pageSize2, $pageSize3);
        Network::buffer('diarys', $diarys);
		
		//场景
		$scene_model = Scene_Model::instance($role_id);
		$scene_data = $scene_model->getData();
		
		$scenes = json_decode($scene_data['scenes']);
		
		//新手引导
		$newbie = json_decode($role->get('newbie'));
		$newbie_data = array();
		$newbie_finish_flg = 1;
		foreach ($newbie[0] as $key => $vl) {
			$newbie_data[$key] = array('gid' => $vl[0], 'state' => $vl[1]);
			
			if ($vl[1] == 1) {
				$newbie_finish_flg = 0;
			}
		}
		if ($newbie_finish_flg === 1) {
			$newbie_data = array();
		}
		Network::buffer('guides', $newbie_data);
		
		//取出静态
		$basic_model = new Basic_Model();
		$init_scene = $basic_model->getSceneList();
		$scene_format = array();
		foreach ($init_scene as $value) {
			$value = current($value);
			if (in_array($value['id'], $scenes)) {
				array_push($scene_format, array((int)$value['id'], 1));
			} else {
				array_push($scene_format, array((int)$value['id'], $value['state']));
			}
		}
		Network::buffer('sceneState', $scene_format);
	}
	
	/**
	 * 创建人物
	 */
	public function createrole()
	{
		$role_id = Role::getOwnRoleId();
		$role = Role::create($role_id);
		
		$aid = $this->input->post('avatarId');
		
		//预判段TODO
		
		$role->set('avatar_id', $aid);
		
		//初始化数据
		$basic_model = new Basic_Model();
		$init_data = $basic_model->getInitDataById();
		
		//初始化magic表
		$magic_data = array(
			'id' => $role_id,
			'role_id' => $role_id,
			'study_ids' => $init_data['magics'],
			'trans_ids' => $init_data['trans'],
		);
		$magic_model = Magic_Model::instance($role_id);
		$magic_model->insert($magic_data);
		
		Network::buffer('result', common::result());
	}
}