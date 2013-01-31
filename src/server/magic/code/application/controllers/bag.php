<?php defined ( 'SYSPATH' ) or die ( 'No direct access allowed.' );
class Bag_Controller extends Role_Controller {
	
	public function __construct()
	{
		parent::__construct();//mac
	}
	
	/**
	 * 背包
	 */
	public function decorlist()
	{
		$role_id = Role::getOwnRoleId();
		
		$decor = Decor::instance($role_id);
		$decor_list_format = $decor->getBag();
		Network::buffer('decorList', $decor_list_format);
	}
	
	/**
	 * 玩家魔法列表
	 */
	public function magicstudylist()
	{
		$role_id = Role::getOwnRoleId();
		
		//取出建筑列表信息
		$building_model = Building_Model::instance($role_id);
		$decor_list = $building_model->getDataByRoleIdType(1);
		
		$decor_list_format = common::transform('decorList', $decor_list);
		
		Network::bufferArray($decor_list_format);
	}
}