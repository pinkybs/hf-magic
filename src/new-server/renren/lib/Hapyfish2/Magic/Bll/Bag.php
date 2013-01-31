<?php

class Hapyfish2_Magic_Bll_Bag
{
	public static function getData($uid)
	{
		$buildingData = Hapyfish2_Magic_HFC_Building::getInBag($uid);
		$doorData = Hapyfish2_Magic_HFC_Door::getInBag($uid);
		$deskData = Hapyfish2_Magic_HFC_Desk::getInBag($uid);
		$decorList = array();
		if (!empty($buildingData)) {
			foreach ($buildingData as $b) {
				$decorList[] = array(
					'id' => $b['id'],
					'bag_type' => 1,
					'd_id' => $b['cid'],
					'type' => $b['item_type']
				);
			}
		}
		if (!empty($doorData)) {
			foreach ($doorData as $d) {
				$decorList[] = array(
					'id' => $d['id'],
					'bag_type' => 1,
					'd_id' => $d['cid'],
					'type' => $d['item_type']
				);
			}
		}
		if (!empty($deskData)) {
			foreach ($deskData as $d) {
				$decorList[] = array(
					'id' => $d['id'],
					'bag_type' => 1,
					'd_id' => $d['cid'],
					'type' => $d['item_type']
				);
			}
		}

		$floorData = Hapyfish2_Magic_HFC_FloorBag::getUserFloor($uid);
		if (!empty($floorData)) {
			foreach ($floorData as $cid => $d) {
				if ($d['count'] > 0) {
					$decorList[] = array(
						'id' => $cid,
						'bag_type' => 1,
						'd_id' => $cid,
						'type' => 3,
						'num' => $d['count']
					);
				}
			}
		}

		$wallData = Hapyfish2_Magic_HFC_WallBag::getUserWall($uid);
		if (!empty($wallData)) {
			foreach ($wallData as $cid => $d) {
				if ($d['count'] > 0) {
					$decorList[] = array(
						'id' => $cid,
						'bag_type' => 1,
						'd_id' => $cid,
						'type' => 3,
						'num' => $d['count']
					);
				}
			}
		}

		return $decorList;
	}

	public static function getDecorCount($uid, $cid, $more = false)
	{
		$type = substr($cid, -4, 1);
		$num = 0;
		$idList = array();
		//desk
		if ($type == 1) {
			$deskData = Hapyfish2_Magic_HFC_Desk::getInBag($uid);
			if ($deskData) {
    			foreach ($deskData as $d) {
    				if ($d['cid'] == $cid) {
    					$num++;
    					if ($more) {
    						$idList[] = $d['id'];
    					}
    				}
    			}
			}
		} else if ($type == 2) { //door
			$doorData = Hapyfish2_Magic_HFC_Door::getInBag($uid);
			if ($doorData) {
    			foreach ($doorData as $d) {
    				if ($d['cid'] == $cid) {
    					$num++;
    					if ($more) {
    						$idList[] = $d['id'];
    					}
    				}
    			}
			}
		} else if ($type == 3) { //floor
			$floorData = Hapyfish2_Magic_HFC_FloorBag::getUserFloor($uid);
			if (isset($floorData[$cid])) {
				$num = $floorData[$cid]['count'];
			}
		} else if ($type == 4) { //wall
			$wallData = Hapyfish2_Magic_HFC_WallBag::getUserWall($uid);
			if (isset($wallData[$cid])) {
				$num = $wallData[$cid]['count'];
			}
		} else {
			$buildingData = Hapyfish2_Magic_HFC_Building::getInBag($uid);
			if ($buildingData) {
    			foreach ($buildingData as $d) {
    				if ($d['cid'] == $cid) {
    					$num++;
    					if ($more) {
    						$idList[] = $d['id'];
    					}
    				}
    			}
			}

		}

		if ($more) {
			return array('num' => $num, 'cid' => $cid, 'type' => $type, 'idList' => $idList);
		} else {
			return $num;
		}
	}

	public static function removeDecor($uid, $cid, $type, $idList, $num)
	{
		//desk
		if ($type == 1) {
			for($i = 0; $i < $num; $i++) {
				Hapyfish2_Magic_HFC_Desk::delOne($uid, $idList[$i], 0, $cid);
			}
		} else if ($type == 2) { //door
			for($i = 0; $i < $num; $i++) {
				Hapyfish2_Magic_HFC_Door::delOne($uid, $idList[$i], 0, $cid);
			}
		} else if ($type == 3) { //floor
			Hapyfish2_Magic_HFC_FloorBag::useUserFloor($uid, $cid, $num);
		} else if ($type == 4) { //wall
			Hapyfish2_Magic_HFC_WallBag::useUserWall($uid, $cid, $num);
		} else {
			for($i = 0; $i < $num; $i++) {
				Hapyfish2_Magic_HFC_Building::delOne($uid, $idList[$i], 0, $cid);
			}
		}
	}

}