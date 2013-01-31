<?php
class Admin_Controller extends Role_Controller {
	public function __construct()
	{
		parent::__construct();
	}
	
	public function prop()
	{
		$name = $this->input->get('name');
		$value = $this->input->get('value');
		$role_id = $this->input->get('role_id');
		
		$role = Role::create($role_id);
		$role->set($name, $value);
		
		$role_name = $role->get('name');
		
		echo "将用户 $role_name 的属性 $name 改为 $value 成功";
	}
	
	/**
	 * 删除用户
	 * 
	 * 主要是删除uid_map
	 */
	public function du()
	{
		$platform_uid = $this->input->get('uid');
		
		$uid_map_model = new Uid_Map_Model($platform_uid);
		
		//取出role_id
		$basic = Basic::instance($platform_uid);
		$role_id = $basic->getRoleId();
		
		if (empty($role_id)) {
			die('删除用户失败');
		}
		
		$uid_map_model->delete($role_id);
		
		echo '删除用户成功';
	}
}