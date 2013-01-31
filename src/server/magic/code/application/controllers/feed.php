<?php defined ( 'SYSPATH' ) or die ( 'No direct access allowed.' );

/**
 * feed controller
 *
 * @copyright  Copyright (c) 2010 HapyFish Inc. (http://www.hapyfish.com)
 * @create     2011/01/04    Nick
 */
class Feed_Controller extends Role_Controller {
	
	protected function getParam($param)
	{
		$result = $this->input->post($param);
		if ( !$result ) {
			$result = $this->input->get($param);
		}
		return $result;
	}
	
	/*
	 * read feed
	 * 
	 */
	public function readfeed()
	{
		$role_id = Role::getOwnRoleId();
		
		$pageSize1= 50;
		$pageSize2= 50;
		$pageSize3= 50;
		
		$feed = Feed::instance($role_id);
        $result = $feed->readFeed($role_id, $pageSize1, $pageSize2, $pageSize3);

		//Network::buffer('result', common::result());
		Network::buffer('feed', $result);
	}

	/*
	 * read feed count
	 * 
	 */
	public function readfeedcount()
	{
		$role_id = Role::getOwnRoleId();
		
		$feed = Feed::instance($role_id);
        $result = $feed->readFeedCount($role_id);

		Network::buffer('feedCount', $result);
	}
	
	public function addfeed()
	{
		$role_id = Role::getOwnRoleId();
		
        $minifeed = array(
        	'role_id' => $role_id,
			'id' => 1,  //模板id:
			'actor' => $role_id,
			'target' => $role_id,
			'title' => array('shipName' => 2, 'visitorNum' => 2),
			'type' => 3, //类型：系统，好友，
        	'icon' => 1, //图标: 开心，不开心
			'create_time' => PEAR::getStaticProperty('_APP', 'timestamp')
        );
        $feed = Feed::instance($role_id);
        $feed->insertMiniFeed($minifeed);
        
        //$feed = Feed::instance();
        //$feed->addMiniFeed($role_id, $actor, $target, $template_id, $type, $icon, $title = null);
	}
	
	public function phpinfo()
	{
		echo phpinfo();
	}

}