<?php
class Friends extends SingleBase {
	public static function getFriendsData()
	{
		$base_conf = Kohana::config('base');
		//先从平台取
		$api_key = $base_conf['api_key'];
		$secret = $base_conf['secret_key'];
		$site = $base_conf['site'];
		
		$sns = Sns::factory($site, $api_key, $secret);
		$friends = $sns->getAppFriends();

		$friends_list = array();
		foreach ($friends as $vl) {
			//根据平台uid获取游戏role_id
			$basic = Basic::instance($vl);
			$role_id = $basic->getRoleId();
			//取出此人数据
			if (!empty($role_id)) {
				$role = Role::create($role_id);
				$role_data = $role->getData();
				$user_data = common::transform('user', $role_data);
				array_push($friends_list, $user_data['user']);
			}
		}
		return $friends_list;
	}
}