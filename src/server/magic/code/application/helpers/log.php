<?php defined ( 'SYSPATH' ) or die ( 'No direct access allowed.' );
class log {
	/**
	 * 购买日志
	 *
	 * @param unknown_type $uid
	 * @param unknown_type $xmoney
	 * @param unknown_type $prop
	 * @param unknown_type $type 1,正常购买 2,系统赠送, 4,宝箱开出
	 */
	public static function buyLog($use_rmoney, $remain_rmoney, $item_id, $item_name, $num, $type = 1)
	{
		$role_id = Role::getOwnRoleId();
		$role = Role::create($role_id);
		$data = array(
			'role_name' => $role->get('name'),
			'use_rmoney' => $use_rmoney,
			'remain_rmoney' => $remain_rmoney,
			'item_id' => $item_id,
			'item_name' => $item_name,
			'num' => $num,
			'type' => $type,
		);
		$model_log = new Role_Log_Model($role_id);
		$model_log->insertBuyLog($data);
	}
	
	/**
	 * 道具使用日志
	 *
	 * @param unknown_type $uid
	 * @param unknown_type $xmoney
	 * @param unknown_type $prop
	 * @param unknown_type $type 1,鱼竿 2鱼饵,4道具
	 */
	public static function useLog($item_id, $item_name, $num = 1, $type = 1)
	{
		$role_id = Role::getOwnRoleId();
		$role = Role::create($role_id);
		$data = array(
			'role_name' => $role->get('name'),
			'item_id' => $item_id,
			'item_name' => $item_name,
			'num' => $num,
			'type' => $type,//1.使用,2.丢弃
		);
		$model_log = new Role_Log_Model($role_id);
		$model_log->insertUseLog($data);
	}
}