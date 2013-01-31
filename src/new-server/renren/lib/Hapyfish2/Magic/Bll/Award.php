<?php

class Hapyfish2_Magic_Bll_Award
{
	protected $_coin;
	
	protected $_exp;
	
	protected $_gold;
	
	protected $_items;
	
	protected $_decors;
	
	public function __construct()
	{
		$this->_coin = 0;
		$this->_gold = 0;
		$this->_items = array();
		$this->_decors = array();
	}
	
	public function setCoin($coin)
	{
		$this->_coin = $coin;
	}
	
	public function setExp($exp)
	{
		$this->_exp = $exp;
	}

	public function setGold($gold, $type=0)
	{
		$this->_gold = $gold;
		$this->_goldAddType = $type;
	}
	
	public function setProp($prop)
	{
		foreach ($prop as $k => $v) {
			if ($k == 'coin') {
				$this->setCoin($v);
			} else if ($k == 'exp') {
				$this->setExp($v);
			} else  if ($k == 'gold') {
				$this->setGold($v);
			}
		}
	}
	
	public function setItem($cid, $count)
	{
		if (isset($this->_items[$cid])) {
			$this->_items[$cid] += $count;
		} else {
			$this->_items[$cid] = $count;
		}
	}
	
	public function setDecor($cid, $count)
	{
		if (isset($this->_decors[$cid])) {
			$this->_decors[$cid] += $count;
		} else {
			$this->_decors[$cid] = $count;
		}
	}
	
	public function setItemList($items)
	{
		foreach ($items as $v) {
			$cid = $v[0];
			$count = $v[1];
			if (isset($this->_items[$cid])) {
				$this->_items[$cid] += $count;
			} else {
				$this->_items[$cid] = $count;
			}
		}
	}
	
	public function setDecorList($decors)
	{
		foreach ($decors as $v) {
			$cid = $v[0];
			$count = $v[1];
			if (isset($this->_decors[$cid])) {
				$this->_decors[$cid] += $count;
			} else {
				$this->_decors[$cid] = $count;
			}
		}
	}
	
	public function addDecor($uid, $cid, $num)
	{
		$type = substr($cid, -4, 1);
		
		if ($type == 1) { //desk
			$desk = array(
				'uid' => $uid,
				'cid' => $cid,
				'item_type' => $type,
				'status' => 0
			);
			for($i = 0; $i < $num; $i++) {
				$ok = Hapyfish2_Magic_HFC_Desk::addOne($uid, $desk);
			}
		} else if ($type == 2) { //door
			$door = array(
				'uid' => $uid,
				'cid' => $cid,
				'item_type' => $type,
				'status' => 0
			);
			for($i = 0; $i < $num; $i++) {
				$ok = Hapyfish2_Magic_HFC_Door::addOne($uid, $door);
			}
		} else if ($type == 3) { //floor
			$ok = Hapyfish2_Magic_HFC_FloorBag::addUserFloor($uid, $cid, $num);
		} else if ($type == 4) { //wall
			$ok = Hapyfish2_Magic_HFC_WallBag::addUserWall($uid, $cid, $num);
		} else { //other building
			$building = array(
				'uid' => $uid,
				'cid' => $cid,
				'item_type' => $type,
				'status' => 0
			);
			for($i = 0; $i < $num; $i++) {
				$ok = Hapyfish2_Magic_HFC_Building::addOne($uid, $building);
			}
		}
		
		return $ok;
	}
	
	public function sendOne($uid)
	{
		if ($this->_coin > 0) {
			$ok = Hapyfish2_Magic_HFC_User::incUserCoin($uid, $this->_coin);
			if ($ok) {
				
			}
		}
		
		if ($this->_exp > 0) {
			$ok = Hapyfish2_Magic_HFC_User::incUserExp($uid, $this->_exp);
			if ($ok) {
				
			}
		}

		if ($this->_gold > 0) {
			$goldInfo = array('gold' => $this->_gold, 'type' => $this->_goldAddType);
			$ok = Hapyfish2_Magic_Bll_Gold::add($uid, $goldInfo);
			if ($ok) {
				
			}
		}
		
		foreach ($this->_items as $cid => $count) {
			$ok = Hapyfish2_Magic_HFC_Item::addUserItem($uid, $cid, $count);
	    	if ($ok) {
	    	}
		}
		
		foreach ($this->_decors as $cid => $count) {
			$ok = $this->addDecor($uid, $cid, $count);
	    	if ($ok) {
	    	}
		}
	}
}