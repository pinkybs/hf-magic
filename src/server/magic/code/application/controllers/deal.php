<?php defined ( 'SYSPATH' ) or die ( 'No direct access allowed.' );

/**
 * magic controller
 *
 * @copyright  Copyright (c) 2010 HapyFish Inc. (http://www.hapyfish.com)
 * @create     2011/01/27    Nick
 */
class Deal_Controller extends Role_Controller {
	
	public function getParam($param)
	{
		$result = $this->input->post($param);
		if ( !$result ) {
			$result = $this->input->get($param);
		}
		return $result;
	}
	
	/*
	 * add deal
	 * 
	 */
	public function adddeal()
	{
		$num = $this->getParam('num');
		$role_id = Role::getOwnRoleId();
		
		$deal = Deal::instance($role_id);
        $result = $deal->addDeal($num);
        
        if ( $result['status'] == 1 ) {
			Network::buffer('result', common::result());
        }
        else {
        	Network::buffer_error($result['content']);
        }
	}

	/*
	 * do deal
	 * 
	 */
	public function dodeal()
	{
		$ownerUid = $this->getParam('uid');
		$num = $this->getParam('num');
		$dealType = $this->getParam('type');
		$role_id = Role::getOwnRoleId();
		
		$deal = Deal::instance($role_id);
        $result = $deal->doDeal($ownerUid, $num, $dealType);
        
        if ( $result['status'] == 1 ) {
			Network::buffer('result', common::result());
        }
        else {
        	Network::buffer_error($result['content']);
        }
	}
	
	/*
	 * get deal
	 * 
	 */
	public function getdeal()
	{
		$role_id = Role::getOwnRoleId();
		
		$deal = Deal::instance($role_id);
        $result = $deal->getDeal();
        
        if ( $result['status'] == 1 ) {
			Network::buffer('result', common::result());
        }
        else {
        	Network::buffer_error($result['content']);
        }
	}
	
	/*
	 * read deal list
	 * 
	 */
	public function readdeal()
	{
		$role_id = Role::getOwnRoleId();
		
		$deal = Deal::instance($role_id);
        $result = $deal->readDeal();
        
		Network::buffer('switchVo', $result);
	}
	
	public function upgrade()
	{
		$role_id = Role::getOwnRoleId();
		
		$deal = Deal::instance($role_id);
        $result = $deal->upgradeDeal();
        
        if ( $result['status'] == 1 ) {
			Network::buffer('result', common::result());
        }
        else {
        	Network::buffer_error($result['content']);
        }
	}
	

	
}