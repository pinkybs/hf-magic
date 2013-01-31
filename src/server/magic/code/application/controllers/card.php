<?php defined ( 'SYSPATH' ) or die ( 'No direct access allowed.' );

/**
 * card controller
 *
 * @copyright  Copyright (c) 2010 HapyFish Inc. (http://www.hapyfish.com)
 * @create     2011/02/16    Nick
 */
class Card_Controller extends Role_Controller {
	
	public function getParam($param)
	{
		$result = $this->input->post($param);
		if ( !$result ) {
			$result = $this->input->get($param);
		}
		return $result;
	}
	
	/*
	 * buy item
	 * 
	 */
	public function buyitem()
	{
		$itemId = $this->getParam('i_id');
		$num = $this->getParam('num');
		$role_id = Role::getOwnRoleId();
		
		$item = Item::instance($role_id);
        $result = $item->buyItem($itemId, $num);
        
        if ( $result['status'] == 1 ) {
			Network::buffer('result', common::result());
			//Network::buffer('addItem', $result['addItem']);
        }
        else {
        	Network::buffer_error($result['content']);
        }
	}
	
	/*
	 * use item
	 * 
	 */
	public function useitem()
	{
		$itemId = $this->getParam('id');
		$role_id = Role::getOwnRoleId();
		
		$item = Item::instance($role_id);
        $result = $item->useItem($itemId);
        
        if ( $result['status'] == 1 ) {
			Network::buffer('result', common::result());
			Network::buffer('removeItems', $result['removeItems']);
        }
        else {
        	Network::buffer_error($result['content']);
        }
	}

}