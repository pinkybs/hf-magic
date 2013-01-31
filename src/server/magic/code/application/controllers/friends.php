<?php defined ( 'SYSPATH' ) or die ( 'No direct access allowed.' );
class Friends_Controller extends Role_Controller {
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$role_id = Role::getOwnRoleId();
		//取出所有的好友信息
		$cache = Cache::instance('friends');
		
		$friends = $cache->get('friends_'.$role_id);
		
		if ($friends === null) {
			//取出数据
			$friends = Friends::getFriendsData();
			
			//缓存
			$cache->set('friends_'.$role_id, $friends);
		}
		
		Network::buffer('friends', $friends);
		/**
		$role_model = Role_Model::instance(null);
		$data = $role_model->getAllData();
		
		$friends = array();
		foreach ($data as $vl) {
			$vl = $vl[0];
			
			if ($vl['id'] < 638) {
				continue;
			}
			
			$user_data = common::transform('user', $vl);
			array_push($friends, $user_data['user']);
		}
		Network::buffer('friends', $friends);
		*/
	}
	
	public function scene()
	{
		$role_id = $this->input->post('uid');
		
		$decor = Decor::instance($role_id);
		$scene_data = $decor->getSceneData();
		
		Network::buffer('scene', $scene_data);
		
		//任务,访问好友
		GameEvent::processRoleEvent(EventConditionType::VISIT_FRIENDS, 0, 1);
		
		//交易
		$deal = Deal::instance($role_id);
        $deal_list = $deal->getSwitchVo($role_id);
        Network::buffer('switchVo', $deal_list);
	}
}