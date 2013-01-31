<?php

/**
 * logic's Operation
 *
 * @package    Happyfish_Magic_Bll
 * @copyright  Copyright (c) 
 * @create      2010/10/08    zhangxin
 */
class Happyfish_Magic_Bll_FormatVo
{

	//const 
	const OUTPUT_ERRCODE = -1;
	const OUTPUT_NORMAL = 1;
	protected static $_doorLimit = array(25=>6,15=>5,10=>4,5=>3,2=>2,0=>1);
	
	/**
	 * init game static data info
	 *
	 * @param null
	 * @return array
	 */
	public static function initGame()
	{
		$aryData = array();
		
		//decor info
    	$lstNbBuilding = Happyfish_Magic_Bll_Cache_NbBasicInfo::listNbBuilding();
    	$aryNbBuilding = array();
    	$idx = 0;
		foreach ($lstNbBuilding as $key=>$data) {
			$aryNbBuilding[$idx]['d_id'] = $data['bid'];
			$aryNbBuilding[$idx]['type'] = $data['type'];
			$aryNbBuilding[$idx]['type_show'] = $data['type_show'];
			$aryNbBuilding[$idx]['class_name'] = $data['class_name'];
			$aryNbBuilding[$idx]['magic_type'] = $data['magic_type'];
			$aryNbBuilding[$idx]['name'] = $data['name'];
			$aryNbBuilding[$idx]['size_x'] = $data['size_x'];
			$aryNbBuilding[$idx]['size_y'] = $data['size_y'];
			$aryNbBuilding[$idx]['size_z'] = $data['size_z'];
			$aryNbBuilding[$idx]['door_refresh_time'] = $data['door_cooldown'];
			$aryNbBuilding[$idx]['door_guest_limit'] = $data['door_guest_limit'];
			$aryNbBuilding[$idx]['max_magic'] = $data['effect_mp'];
			$idx++;
		}
		$aryData['decorClass'] = $aryNbBuilding;
		
		//level info 
		$lstNbLevel = Happyfish_Magic_Bll_Cache_NbBasicInfo::listNbLevel();
		$aryNbLevel = array();
		$idx = 0;
		foreach ($lstNbLevel as $key=>$data) {
			$aryNbLevel[$idx]['level'] = $data['level'];
			$aryNbLevel[$idx]['max_exp'] = $data['exp'];
			$aryNbLevel[$idx]['desk_limit'] = $data['limit_seat'];
			$aryNbLevel[$idx]['student_limit'] = $data['limit_person'];
			$aryNbLevel[$idx]['magic_limit'] = $data['max_mp'];
			$aryNbLevel[$idx]['items'] = array($data['levup_card'], $data['levup_card_count']);
			$aryNbLevel[$idx]['tile_x_length'] = $data['house_size'];
			$aryNbLevel[$idx]['tile_z_length'] = $data['house_size'];
			$aryNbLevel[$idx]['gem'] = $data['levup_money'];
			$idx++;
		}
		$aryData['levelInfos'] = $aryNbLevel;
	
		//magic info
		$lstNbMagicA = Happyfish_Magic_Bll_Cache_NbBasicInfo::listNbMagicA();
		$aryNbMagic = array();
		$idx = 0;
		foreach ($lstNbMagicA as $key=>$data) {
			$aryNbMagic[$idx]['magic_id'] = $data['id'];
			$aryNbMagic[$idx]['name'] = $data['name'];
			$aryNbMagic[$idx]['magic_type'] = $data['type'];
			$aryNbMagic[$idx]['class_name'] = $data['class_name1'];
			$aryNbMagic[$idx]['actMovie'] = $data['class_name2'];
			$aryNbMagic[$idx]['mp'] = $data['need_mp'];
			$aryNbMagic[$idx]['exp'] = $data['gain_exp'];
			$aryNbMagic[$idx]['time'] = $data['spend_time'];
			$aryNbMagic[$idx]['need_level'] = $data['level'];
			if (1 == $data['type']) {
				$crystalType = 'red';
			}
			else if (2 == $data['type']) {
				$crystalType = 'blue';
			}
			else {
				$crystalType = 'green';
			}
			$aryNbMagic[$idx]['crystal'] = $data['gain_'.$crystalType];
			$aryNbMagic[$idx]['learn_crystal'] = $data[$crystalType];
			$idx++;
		}
		$aryData['magicClass'] = $aryNbMagic;
		
		//card & item info
		$lstNbCard = Happyfish_Magic_Bll_Cache_NbBasicInfo::listNbCard();
		$aryNbCard = array();
		$idx = 0;
		foreach ($lstNbCard as $key=>$data) {
			$aryNbCard[$idx]['i_id'] = $data['cid'];
			$aryNbCard[$idx]['name'] = $data['name'];
			$aryNbCard[$idx]['class_name'] = $data['class_name'];
			$aryNbCard[$idx]['type'] = $data['type'];
			$aryNbCard[$idx]['add_mp'] = $data['effect'];
			$aryNbCard[$idx]['red'] = $data['red'];
			$aryNbCard[$idx]['blue'] = $data['blue'];
			$aryNbCard[$idx]['green'] = $data['green'];
			$aryNbCard[$idx]['gem'] = $data['money'];
			$idx++;
		}
		$lstNbItem = Happyfish_Magic_Bll_Cache_NbBasicInfo::listNbItem();
		$aryNbItem = array();
		foreach ($lstNbItem as $key=>$data) {
			$aryNbItem[$idx]['i_id'] = $data['mid'];
			$aryNbItem[$idx]['name'] = $data['name'];
			$aryNbItem[$idx]['class_name'] = $data['class_name'];
			$aryNbItem[$idx]['type'] = 3;
			$idx++;
		}
		$aryData['itemClss'] = $aryNbCard + $aryNbItem;
		
		//other basic info
		$aryOtherBasic = array();
		$aryOtherBasic['interrupt_exp'] = Happyfish_Magic_Bll_GuestService::RESCUE_BREAK_GAIN_EXP;
		$aryOtherBasic['interrupt_mp'] = Happyfish_Magic_Bll_GuestService::RESCUE_BREAK_NEED_MP;
		$aryOtherBasic['door_exp'] = Happyfish_Magic_Bll_GuestService::OPEN_DOOR_GAIN_EXP;
		$aryOtherBasic['steal_need_mp'] = Happyfish_Magic_Bll_GuestService::STEAL_CRYSTAL_NEED_MP;
		$aryData['basicClass'] = $aryOtherBasic;
		
		return $aryData;
	}
	
	
	/**
	 * format to front decor Vo
	 *
	 * @param array   $lstUserBuilding
	 * @param integer $mode 0-in bag 1-in room
	 * @return array
	 */
	public static function decorVo($lstUserBuilding, $mode = 1, $lstDoor=null)
	{
		//door info
		$now = time();
		
		$aryRst = $aryUserBuilding = array();
        foreach ($lstUserBuilding['buildingList'] as $key=>$data) {
        	$aryUserBuilding[$key]['id'] = $data['id'];
        	$aryUserBuilding[$key]['x'] = $data['pos_x'];
        	$aryUserBuilding[$key]['y'] = $data['pos_y'];
        	$aryUserBuilding[$key]['z'] = $data['pos_z'];
        	$aryUserBuilding[$key]['mirror'] = $data['mirror'];
        	$aryUserBuilding[$key]['bag_type'] = $data['status'];
        	$aryUserBuilding[$key]['d_id'] = $data['building_id'];
        	$aryUserBuilding[$key]['num'] = 1;
        	//door status info
			if (2 == $data['building_type'] && $data['status'] && $lstDoor) {
				foreach ($lstDoor as $door) {
					if ($door['door_id'] == $data['id']) {
						$aryUserBuilding[$key]['door_left_students_num'] = count($door['wait_guest_ary']);
						$goneTime = (int)($now - $door['last_open_time']);
        				$aryUserBuilding[$key]['door_left_time'] = ($goneTime>=(int)$door['door_cooldown'] ? 0 : ((int)$door['door_cooldown'] - $goneTime));
						break;
					}
				}
			}
        }
        if ($mode) {
        	$aryRst['decorList'] = $aryUserBuilding;
	        $aryRst['floorList'] = $lstUserBuilding['floorList'];
	        $aryRst['wallList'] = $lstUserBuilding['wallList'];
        }
        else {
        	$aryUserFloor = $aryUserWall = array();
        	foreach ($lstUserBuilding['floorList'] as $key=>$data) {
        		$aryUserFloor[$key]['id'] = 0;
        		$aryUserFloor[$key]['x'] = 0;
        		$aryUserFloor[$key]['y'] = 0;
        		$aryUserFloor[$key]['z'] = 0;
        		$aryUserFloor[$key]['mirror'] = 0;
	        	$aryUserFloor[$key]['bag_type'] = 0;
	        	$aryUserFloor[$key]['d_id'] = $data['floor_id'];
	        	$aryUserFloor[$key]['num'] = $data['quantity'];
        	}
        	foreach ($lstUserBuilding['wallList'] as $key=>$data) {
        		$aryUserWall[$key]['id'] = 0;
        		$aryUserWall[$key]['x'] = 0;
        		$aryUserWall[$key]['y'] = 0;
        		$aryUserWall[$key]['z'] = 0;
        		$aryUserWall[$key]['mirror'] = 0;
	        	$aryUserWall[$key]['bag_type'] = 0;
	        	$aryUserWall[$key]['d_id'] = $data['wall_id'];
	        	$aryUserWall[$key]['num'] = $data['quantity'];
        	}
        	$aryRst['decorList'] = array_merge($aryUserBuilding, $aryUserFloor, $aryUserWall);
        }
        
        return $aryRst;
	}
	
	/**
	 * format to front door Vo
	 *
	 * @param array   $lstDoor
	 * @return array
	 */
	public static function doorVo($lstDoor)
	{
		$aryDoor = array();
        $now = time();
        foreach ($lstDoor as $key=>$data) {
        	$aryDoor[$key]['id'] = $data['door_id'];
        	//$aryDoor[$key]['x'] = $data['pos_x'];
        	//$aryDoor[$key]['y'] = $data['pos_y'];
        	//$aryDoor[$key]['z'] = $data['pos_z'];
        	//$aryDoor[$key]['mirror'] = $data['mirror'];
        	//$aryDoor[$key]['bag_type'] = $data['status'];
        	$aryDoor[$key]['d_id'] = $data['building_id'];
        	//$aryDoor[$key]['num'] = 1;
        	$aryDoor[$key]['door_left_students_num'] = count($data['wait_guest_ary']);
        	$goneTime = (int)($now - $data['last_open_time']);
        	$aryDoor[$key]['door_left_time'] = ($goneTime>=(int)$data['door_cooldown'] ? 0 : ((int)$data['door_cooldown'] - $goneTime));
        }
        return $aryDoor;
	}
	
	/**
	 * format to front user Vo
	 *
	 * @param array   $userInfo
	 * @return array
	 */
	public static function userVo($userInfo)
	{
		$aryRst = array();
        $aryRst['uid'] = $userInfo['uid'];
        $aryRst['name'] = $userInfo['name'];
        $aryRst['face'] = $userInfo['headurl'];
        $aryRst['level'] = $userInfo['level'];
        $aryRst['exp'] = $userInfo['exp'];
        $aryRst['max_exp'] = $userInfo['next_lev_exp'];
        $aryRst['magic_type'] = $userInfo['major_magic'];
        $aryRst['red'] = $userInfo['red'];
        $aryRst['blue'] = $userInfo['blue'];
        $aryRst['green'] = $userInfo['green'];
        $aryRst['gem'] = $userInfo['money'];
        $aryRst['mp'] = $userInfo['mp'];
        $aryRst['max_mp'] = (int)$userInfo['nbLevInfo']['max_mp'] + (int)$userInfo['mp_addition'];
        $cntFriend = count($userInfo['fids']);
        $aryRst['door_limit'] = 1;
        foreach (self::$_doorLimit as $key=>$value) {
        	if ($cntFriend>=$key) {
        		$aryRst['door_limit'] = $value;
        		break;
        	}
        }
        
        //$aryRst['desk_limit'] = $userInfo['nbLevInfo']['limit_seat'];
        $aryRst['tile_x_length'] = $userInfo['nbLevInfo']['house_size'];
        $aryRst['tile_z_length'] = $userInfo['nbLevInfo']['house_size'];
        $aryRst['eat_limit'] = (Happyfish_Magic_Bll_Shop::EAT_RECOVERMP_FOODLIMIT - $userInfo['mgInfo']['eat_count']) > 0 ? (Happyfish_Magic_Bll_Shop::EAT_RECOVERMP_FOODLIMIT - $userInfo['mgInfo']['eat_count']) : 0;
        return $aryRst;
	}
	
	/**
	 * format to front user guest Vo
	 *
	 * @param array   $lstDesk
	 * @param array   $aryWalk
	 * @param integer   $majorMagic
	 * @return array
	 */
	public static function studentVo($lstDesk, $aryWalk, $majorMagic)
	{
		$aryStudent = array();
		$now = time();
        foreach ($lstDesk as $key=>$data) {
        	if ($data['status']) {
				$studentVo = array();
				$studentVo['avatar_id'] = (int)$data['guest_id'];
				$studentVo['decor_id'] = (int)$data['desk_id'];
				$studentVo['state'] = (int)$data['status']; //0闲逛中 1未教 2学习中 3已教完 4中断
				$studentVo['magic_id'] = (int)$data['magic_id'];
				$studentVo['time'] = 0;
				$studentVo['event_time'] = 0;
				$studentVo['stone_time'] = 0;
				//learning or completed
				if (2 == $data['status'] && !empty($data['start_time'])) {
					$studentVo['state'] = 2;
					//no break event
					if ($data['break_time'] == -1) {
						$spentTime = $now - $data['start_time'];
					}
					//break event
					else {
						//has dealt break event   ****************
						if ($data['rescue_time']) {
							$spentTime = $data['break_time'] - $data['start_time'] + ($now - $data['rescue_time']);
						}
						//has not deal break
						else {
							//spenttime = break time if break time is arrive
							$spentTime = ($now-$data['start_time']) < ($data['break_time']-$data['start_time']) ? ($now-$data['start_time']) : ($data['break_time']-$data['start_time']);
						}
					}
					//remain time && stone time				
					$studentVo['time'] = ($data['spend_time'] - $spentTime) > 0 ? ($data['spend_time'] - $spentTime) : 0;					
					$studentVo['stone_time'] = 0;
					//completed status
					if (0 == $studentVo['time']) {
						$studentVo['state'] = 3;
						$studentVo['stone_time'] = (Happyfish_Magic_Bll_GuestService::BE_STONE_TIME - $spentTime) > 0 ? (Happyfish_Magic_Bll_GuestService::BE_STONE_TIME - $spentTime) : 0;
					}
					//break time if need
					$studentVo['event_time'] = ($data['break_time'] == -1 ? -1 : ($data['break_time'] - $data['start_time']));
					//is break time is arrived
					if ($data['break_time'] > 0) {
						//remain ? second in break event
						$studentVo['event_time'] = ($data['break_time'] - $now) > 0 ? ($data['break_time'] - $now) : 0;
						if (0 == $studentVo['event_time'] && empty($data['rescue_time'])) {
							$studentVo['state'] = 4;//中断
						}
					}
					if (1 == $majorMagic) {
						$studentVo['crystal'] = $data['red'];
					}
					else if (2 == $majorMagic) {
						$studentVo['crystal'] = $data['blue'];
					}
					else {
						$studentVo['crystal'] = $data['green'];
					}
				}
				
				$studentVo['can_steal'] = false;
				$aryStudent[] = $studentVo;
			}
        }
        
        foreach ($aryWalk as $key=>$data) {
        	$studentVo = array();
			$studentVo['avatar_id'] = (int)$data['guestType'];
			$studentVo['decor_id'] = 0;
			$studentVo['state'] = 0; //0闲逛中 1未教 2学习中 3已教完
			$studentVo['magic_id'] = 0;
			$studentVo['time'] = 0;
			$studentVo['event_time'] = 0;
			$studentVo['stone_time'] = 0;
			$studentVo['crystal'] = 0;
			$studentVo['can_steal'] = false;
			$aryStudent[] = $studentVo;
        }
        return $aryStudent;
	}
	
	
	/**
	 * format to front user magic Vo
	 *
	 * @param  array   $rowUser
	 * @param  array   $lstMagic
	 * @return array
	 */
	public static function magicVo($rowUser, $lstMagic)
	{
		$aryRst = array();
		
        foreach ($lstMagic as $data) {
        	$type = substr($data['magic_id'],0,1);
        	if ( $data['status'] && ($type == $rowUser['major_magic']) ) {
        		$aryRst[] = $data['magic_id'];
        	}
        }
        return $aryRst;
	}
	
	/**
	 * format to front user items Vo
	 *
	 * @param  array   $lstCard
	 * @param  array   $lstItem
	 * @return array
	 */
	public static function itemVo($lstCard, $lstItem)
	{
		$aryRst = array();
		
        foreach ($lstCard as $data) {
        	if ($data['card_count']) {
        		$aryRst[] = array('i_id' => $data['cid'], 'num' => $data['card_count']);
        	}
        }
        
        foreach ($lstItem as $data) {
        	if ($data['item_count']) {
        		$aryRst[] = array('i_id' => $data['mid'], 'num' => $data['item_count']);
        	}
        }
        
        return $aryRst;
	}
	
	
	/**
	 * format to front result Vo
	 *
	 * @param  array   $info
	 * @param  array   $info
	 * @return array
	 */
	public static function resultVo($info, $content='')
	{
		$aryRst = array();
		if (empty($content)) {
			$aryRst = $info;
			$aryRst['status'] = self::OUTPUT_NORMAL;
		}
		else {
			$aryRst['status'] = self::OUTPUT_ERRCODE;
			$aryRst['content'] = $content;
		}
		/*
		$resultVo = array();
		$resultVo['content'] = '';
		$resultVo['levelUp'] = false;
		$resultVo['red'] = 0;
		$resultVo['blue'] = 0;
		$resultVo['green'] = 0;
		$resultVo['gem'] = 0;
		$resultVo['exp'] = $rowNbTask['gain_exp'];
		*/
        return $aryRst;
	}
	
}