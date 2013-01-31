<?php defined ( 'SYSPATH' ) or die ( 'No direct access allowed.' );

/**
 * magic controller
 *
 * @copyright  Copyright (c) 2010 HapyFish Inc. (http://www.hapyfish.com)
 * @create     2011/01/04    Nick
 */
class Magic_Controller extends Role_Controller {
	
	public function getParam($param)
	{
		$result = $this->input->post($param);
		if ( !$result ) {
			$result = $this->input->get($param);
		}
		return $result;
	}
	
	public function gettrans()
	{
		$role_id = Role::getOwnRoleId();
		$endtime = Magic::getTransEndTime($role_id);
		echo '<br/>';
		echo $endtime;
		exit;
	}
	
	/*
	 * study teach magic
	 * 
	 */
	public function studyteach()
	{
		//$msid = $this->input->post('magic_id');
		$msid = $this->getParam('magic_id');
		$role_id = Role::getOwnRoleId();
		
		$magic = Magic::instance($role_id);
        $result = $magic->studyteach($role_id, $msid);
        
        if ( $result['status'] == 1 ) {
			Network::buffer('result', common::result());
        }
        else {
        	Network::buffer_error($result['content']);
        }
	}

	/*
	 * study trans magic
	 * 
	 */
	public function studytrans()
	{
		$mtid = $this->input->post('trans_mid');	
		$role_id = Role::getOwnRoleId();
		
		$magic = Magic::instance($role_id);
        $result = $magic->studytrans($role_id, $mtid);
        
        if ( $result['status'] == 1 ) {
			Network::buffer('result', common::result());
        }
        else {
        	Network::buffer_error($result['content']);
        }
	}
	
	/*
	 * use mix magic
	 * 
	 */
	public function mixmagic()
	{
		$mmid = $this->getParam('mix_mid');
		$nums = $this->getParam('nums');
		$role_id = Role::getOwnRoleId();
		
		$magic = Magic::instance($role_id);
        $result = $magic->mixmagic($role_id, $mmid, $nums);
        
        if ( $result['status'] == 1 ) {
			Network::buffer('result', common::result());
        }
        else {
        	Network::buffer_error($result['content']);
        }
	}

	/*
	 * use trans magic
	 * 
	 */
	public function transmagic()
	{
		$mtid = $this->getParam('trans_mid');
		$friend_id = $this->getParam('uid');
		$role_id = Role::getOwnRoleId();
		
		$magic = Magic::instance($role_id);
        $result = $magic->transmagic($role_id, $friend_id, $mtid);
        
        if ( $result['status'] == 1 ) {
			Network::buffer('result', common::result());
			//Network::buffer('addItem', $result['addItem']);
        }
        else {
        	Network::buffer_error($result['content']);
        }
	}
	
	/*
	 * reducer 
	 * 
	 */
	public function reducetrans()
	{
		$role_id = Role::getOwnRoleId();
		
		$magic = Magic::instance($role_id);
        $result = $magic->reducetrans($role_id);
        
        if ( $result['status'] == 1 ) {
			Network::buffer('result', common::result());
        }
        else {
        	Network::buffer_error($result['content']);
        }
	}
	
	/*
	 * change magic type
	 * 
	 */
	public function changetype()
	{
		$type = $this->input->post('type');
		$role_id = Role::getOwnRoleId();
		
		$magic = Magic::instance($role_id);
		$result = $magic->changemagictype($role_id, $type);
	
        if ( $result['status'] == 1 ) {
			Network::buffer('result', common::result());
        }
        else {
        	Network::buffer_error($result['content']);
        }
	}
	
}