<?php
class Sns_Xiaonei_Driver extends Sns_Driver {
	public $rest;
	public function __construct($api_key, $secret, $app_id, $app_name, $extend_pramas) {
		$this->rest = new Sns_Xiaonei_Rest_Driver($api_key, $secret);
	}
	
	/**
	 * 获取玩家信息
	 * @return 
	 * uid tinyurl vip sex name star headurl money
	 */
	public function getUserInfo()
	{
		$user_data = $this->rest->client->usersGetInfo();
		
		$user_data[0]['money'] = $user_data[0]['zidou'];
		unset($user_data[0]['zidou']);
		return $user_data[0];
	}
	
	/**
	 * 判断玩家是否在这个平台内
	 * return true/false
	 */
	public function usersIsAppUser()
	{
		$ret = $this->rest->client->usersIsAppUser();
		
		return $ret;
	}
	
	/**
	 * 获得用户在此平台,同时玩这个游戏的好友信息
	 * @return 二维ARRAY
	 * uid,name,tinyurl,headurl
	 */
	public function getAppFriends()
	{
		$ret = $this->rest->client->friendsGetAppFriends();

		return $ret;
	}
}